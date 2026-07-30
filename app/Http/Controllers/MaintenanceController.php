<?php

namespace App\Http\Controllers;

use App\Models\DeviceMaintenance;
use App\Models\Device;
use App\Enums\MaintenanceType;
use App\Enums\MaintenanceStatus;
use App\Enums\DeviceStatus;
use App\Exports\MaintenancesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MaintenanceController extends Controller
{
    public function index(): View
    {
        return view('maintenances.index');
    }

    public function create(Request $request): View
    {
        $selectedDeviceId = $request->get('device_id');
        $devices = Device::with('category', 'currentAssignment.employee')
            ->orderBy('brand')
            ->orderBy('model')
            ->get();

        return view('maintenances.create', compact('devices', 'selectedDeviceId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'device_id'    => 'required|exists:devices,id',
            'type'         => 'required|in:preventivo,correctivo,upgrade',
            'status'       => 'required|in:programado,en_proceso',
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'scheduled_at' => 'nullable|date',
            'next_due_at'  => 'nullable|date|after_or_equal:today',
            'update_device_status_repair' => 'nullable|boolean',
        ], [
            'device_id.required' => 'Debes seleccionar el equipo al que se le aplicará el servicio.',
            'title.required'     => 'Por favor escribe un título corto para identificar el servicio.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $maintenance = DeviceMaintenance::create([
                'device_id'    => $validated['device_id'],
                'user_id'      => Auth::id(),
                'type'         => $validated['type'],
                'status'       => $validated['status'],
                'title'        => $validated['title'],
                'description'  => $validated['description'] ?? null,
                'scheduled_at' => $validated['scheduled_at'] ?? null,
                'started_at'   => $validated['status'] === 'en_proceso' ? now() : null,
                'next_due_at'  => $validated['next_due_at'] ?? null,
            ]);

            // Si solicitaron cambiar estatus a "En Reparación" en inventario
            if ($request->boolean('update_device_status_repair') && $validated['status'] === 'en_proceso') {
                $device = Device::find($validated['device_id']);
                if ($device) {
                    $device->update(['status' => DeviceStatus::EnReparacion]);
                }
            }
        });

        return redirect()->route('maintenances.index')
            ->with('success', 'Registro de mantenimiento abierto exitosamente.');
    }

    public function show(DeviceMaintenance $maintenance): View
    {
        $maintenance->load(['device', 'device.category', 'device.currentAssignment.employee', 'user']);
        return view('maintenances.show', compact('maintenance'));
    }

    public function complete(Request $request, DeviceMaintenance $maintenance): RedirectResponse
    {
        $request->validate([
            'resolution_notes'  => 'required|string',
            'new_device_status' => 'required|in:disponible,asignado,obsoleto,baja,mantener',
            'next_due_at'       => 'nullable|date|after:today',
        ], [
            'resolution_notes.required' => 'Por favor detalla qué solución o intervención se aplicó para cerrar el ticket.'
        ]);

        DB::transaction(function () use ($request, $maintenance) {
            $maintenance->update([
                'status'           => MaintenanceStatus::Completado,
                'resolution_notes' => $request->input('resolution_notes'),
                'completed_at'     => now(),
                'next_due_at'      => $request->input('next_due_at') ?? $maintenance->next_due_at,
            ]);

            $newStatus = $request->input('new_device_status');
            if ($newStatus !== 'mantener') {
                $device = $maintenance->device;
                if ($device) {
                    $device->update(['status' => DeviceStatus::from($newStatus)]);
                }
            }
        });

        return redirect()->route('maintenances.show', $maintenance)
            ->with('success', '¡El mantenimiento se ha completado y cerrado con éxito!');
    }

    public function cancel(DeviceMaintenance $maintenance): RedirectResponse
    {
        $maintenance->update([
            'status' => MaintenanceStatus::Cancelado,
        ]);

        return redirect()->route('maintenances.index')
            ->with('success', 'El registro de mantenimiento ha sido cancelado.');
    }

    public function export(): BinaryFileResponse
    {
        return Excel::download(new MaintenancesExport, 'bitacora_mantenimientos_' . now()->format('Y-m-d') . '.xlsx');
    }
}
