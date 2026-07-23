<?php

namespace App\Http\Controllers;

use App\Enums\DeviceStatus;
use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Models\Device;
use App\Models\DeviceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DeviceController extends Controller
{
    public function index(): View
    {
        $devices = Device::with('category')
            ->withCount('assignments')
            ->orderByDesc('created_at')
            ->paginate(20);

        $statsByStatus = Device::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('devices.index', compact('devices', 'statsByStatus'));
    }

    public function create(): View
    {
        $categories = DeviceCategory::orderBy('name')->get();
        $statuses   = DeviceStatus::cases();

        return view('devices.create', compact('categories', 'statuses'));
    }

    public function store(StoreDeviceRequest $request): RedirectResponse
    {
        Device::create($request->validated());

        return redirect()->route('devices.index')
            ->with('success', 'Equipo registrado correctamente.');
    }

    public function show(Device $device): View
    {
        $device->load([
            'category',
            'currentAssignment.employee',
            'currentAssignment.assignedBy',
        ]);

        return view('devices.show', compact('device'));
    }

    public function edit(Device $device): View
    {
        $categories = DeviceCategory::orderBy('name')->get();
        $statuses   = DeviceStatus::cases();

        return view('devices.edit', compact('device', 'categories', 'statuses'));
    }

    public function update(UpdateDeviceRequest $request, Device $device): RedirectResponse
    {
        $device->update($request->validated());

        return redirect()->route('devices.show', $device)
            ->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroy(Device $device): RedirectResponse
    {
        if ($device->currentAssignment()->exists()) {
            return back()->with('error', 'No se puede eliminar un equipo con una asignación activa.');
        }

        $device->delete();

        return redirect()->route('devices.index')
            ->with('success', 'Equipo eliminado correctamente.');
    }

    public function history(Device $device): View
    {
        $assignments = $device->assignments()
            ->with(['employee', 'assignedBy', 'returnedBy'])
            ->orderByDesc('assigned_at')
            ->paginate(15);

        return view('devices.history', compact('device', 'assignments'));
    }
}
