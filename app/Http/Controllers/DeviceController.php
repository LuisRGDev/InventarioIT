<?php

namespace App\Http\Controllers;

use App\Enums\DeviceStatus;
use App\Enums\ConditionStatus;
use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Models\Device;
use App\Models\DeviceCategory;
use App\Models\Employee;
use App\Services\DeviceAssignmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DevicesExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DeviceController extends Controller
{
    public function index(): View
    {
        $query = Device::with('category')->withCount('assignments');

        // Filtro por texto (serial, marca, modelo)
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('mac_address_ethernet', 'like', "%{$search}%")
                  ->orWhere('mac_address_wifi', 'like', "%{$search}%");
            });
        }

        // Filtro por categoría
        if ($category = request('category')) {
            $query->where('device_category_id', $category);
        }

        // Filtro por estatus
        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $devices = $query->orderByDesc('created_at')->paginate(20);

        $statsByStatus = Device::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('devices.index', compact('devices', 'statsByStatus'));
    }


    public function create(): View
    {
        $categories = DeviceCategory::orderBy('name')->get();
        $statuses   = DeviceStatus::cases();
        $conditions = ConditionStatus::cases();
        $employees  = Employee::active()->orderBy('name')->get();
        $categoriesJson = $categories->map(fn($c) => ['id' => $c->id, 'isComputer' => $c->isComputer(), 'isSmartphone' => $c->isSmartphone()])->keyBy('id')->toJson();

        return view('devices.create', compact('categories', 'statuses', 'conditions', 'employees', 'categoriesJson'));
    }

    public function store(StoreDeviceRequest $request, DeviceAssignmentService $assignmentService): RedirectResponse
    {
        $device = Device::create($request->validated());

        if ($request->filled('assign_to_employee_id')) {
            $employee = Employee::findOrFail($request->input('assign_to_employee_id'));
            $assignmentService->assign($device, $employee, [
                'condition_on_delivery' => $request->input('condition_on_delivery')
            ]);
            
            return redirect()->route('devices.show', $device)
                ->with('success', 'Equipo registrado y asignado correctamente a ' . $employee->name);
        }

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
        $categoriesJson = $categories->map(fn($c) => ['id' => $c->id, 'isComputer' => $c->isComputer(), 'isSmartphone' => $c->isSmartphone()])->keyBy('id')->toJson();

        return view('devices.edit', compact('device', 'categories', 'statuses', 'categoriesJson'));
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

    public function export(): BinaryFileResponse
    {
        return Excel::download(new DevicesExport, 'inventario_equipos_' . date('Y-m-d') . '.xlsx');
    }
}
