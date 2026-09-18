<?php

namespace App\Http\Controllers;

use App\Enums\DeviceStatus;
use App\Enums\MaintenanceStatus;
use App\Exports\MaintenancesExport;
use App\Http\Requests\CompleteMaintenanceRequest;
use App\Http\Requests\StoreMaintenanceRequest;
use App\Models\Device;
use App\Models\DeviceMaintenance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
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

    public function store(StoreMaintenanceRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request) {
            $maintenance = DeviceMaintenance::create([
                'device_id' => $validated['device_id'],
                'user_id' => Auth::id(),
                'type' => $validated['type'],
                'status' => $validated['status'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'scheduled_at' => $validated['scheduled_at'] ?? null,
                'started_at' => $validated['status'] === 'en_proceso' ? now() : null,
                'next_due_at' => $validated['next_due_at'] ?? null,
            ]);

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

    public function complete(CompleteMaintenanceRequest $request, DeviceMaintenance $maintenance): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $maintenance) {
            $maintenance->update([
                'status' => MaintenanceStatus::Completado,
                'resolution_notes' => $validated['resolution_notes'],
                'completed_at' => now(),
                'next_due_at' => $validated['next_due_at'] ?? $maintenance->next_due_at,
            ]);

            $newStatus = $validated['new_device_status'];
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
        if (! in_array($maintenance->status, [MaintenanceStatus::Programado, MaintenanceStatus::EnProceso])) {
            return back()->with('error', 'Solo se pueden cancelar mantenimientos en estado programado o en proceso.');
        }

        $maintenance->update([
            'status' => MaintenanceStatus::Cancelado,
        ]);

        return redirect()->route('maintenances.index')
            ->with('success', 'El registro de mantenimiento ha sido cancelado.');
    }

    public function export(): BinaryFileResponse
    {
        return Excel::download(new MaintenancesExport, 'bitacora_mantenimientos_'.now()->format('Y-m-d').'.xlsx');
    }
}
