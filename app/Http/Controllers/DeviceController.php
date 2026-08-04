<?php

namespace App\Http\Controllers;

use App\Enums\DeviceStatus;
use App\Enums\DeviceCondition;
use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Models\Device;
use App\Models\DeviceCategory;
use App\Models\DeviceModel;
use App\Models\Employee;
use App\Services\DeviceAssignmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DevicesExport;
use App\Exports\DevicesTemplateExport;
use App\Exports\GeneralInventoryExport;
use App\Exports\GeneralInventoryTemplateExport;
use App\Imports\DevicesImport;
use App\Imports\GeneralInventoryImport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(): View
    {
        return view('devices.index');
    }


    public function create(): View
    {
        $categories = DeviceCategory::orderBy('name')->get();
        $statuses   = DeviceStatus::cases();
        $conditions = DeviceCondition::cases();
        $employees  = Employee::active()->orderBy('name')->get();
        $categoriesJson = $categories->map(fn($c) => ['id' => $c->id, 'isComputer' => $c->isComputer(), 'isSmartphone' => $c->isSmartphone()])->keyBy('id')->toJson();

        $models = DeviceModel::orderBy('brand')->orderBy('model')->orderBy('variant')->get();
        $modelsJson = $models->map(fn($m) => [
            'id' => $m->id,
            'category_id' => $m->device_category_id,
            'brand' => $m->brand,
            'model' => $m->model,
            'variant' => $m->variant,
            'display_name' => $m->display_name,
            'cpu' => $m->cpu ?? '',
            'ram' => $m->ram ?? '',
            'storage' => $m->storage ?? '',
            'os' => $m->os ?? '',
        ])->keyBy('id')->toJson();

        return view('devices.create', compact('categories', 'statuses', 'conditions', 'employees', 'categoriesJson', 'models', 'modelsJson'));
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

        $models = DeviceModel::orderBy('brand')->orderBy('model')->orderBy('variant')->get();
        $modelsJson = $models->map(fn($m) => [
            'id' => $m->id,
            'category_id' => $m->device_category_id,
            'brand' => $m->brand,
            'model' => $m->model,
            'variant' => $m->variant,
            'display_name' => $m->display_name,
            'cpu' => $m->cpu ?? '',
            'ram' => $m->ram ?? '',
            'storage' => $m->storage ?? '',
            'os' => $m->os ?? '',
        ])->keyBy('id')->toJson();

        return view('devices.edit', compact('device', 'categories', 'statuses', 'categoriesJson', 'models', 'modelsJson'));
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

        $device->assignments()->delete();
        $device->delete();

        return redirect()->route('devices.index')
            ->with('success', 'Equipo eliminado permanentemente.');
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

    public function exportGeneral(): BinaryFileResponse
    {
        return Excel::download(new GeneralInventoryExport, 'inventario_global_completo_' . date('Y-m-d') . '.xlsx');
    }

    public function downloadTemplate(): BinaryFileResponse
    {
        return Excel::download(new DevicesTemplateExport, 'plantilla_importacion_equipos.xlsx');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'Debes subir un archivo.',
            'file.mimes'    => 'El archivo debe ser un Excel (.xlsx, .xls o .csv).',
            'file.max'      => 'El archivo no debe pesar más de 5MB.'
        ]);

        try {
            Excel::import(new DevicesImport, $request->file('file'));
            
            return redirect()->route('devices.index')
                ->with('success', 'Importación masiva completada correctamente.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $row = $failure->row();
                $errors = implode(', ', $failure->errors());
                $errorMessages[] = "Fila {$row}: {$errors}";
            }
            return back()->with('error', 'Errores de validación en el archivo: <br>' . implode('<br>', $errorMessages));
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error inesperado al importar el archivo: ' . $e->getMessage());
        }
    }

    public function downloadGeneralTemplate(): BinaryFileResponse
    {
        return Excel::download(new GeneralInventoryTemplateExport, 'plantilla_general_inventario.xlsx');
    }

    public function importGeneral(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'Debes seleccionar un archivo para importar.',
            'file.mimes'    => 'El archivo debe tener formato Excel (.xlsx, .xls o .csv).',
            'file.max'      => 'El archivo supera el límite de 5MB.'
        ]);

        try {
            Excel::import(new GeneralInventoryImport, $request->file('file'));

            return redirect()->route('dashboard')
                ->with('success', '¡Importación general y actualización masiva completadas con éxito!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $row = $failure->row();
                $errors = implode(', ', $failure->errors());
                $errorMessages[] = "Fila {$row}: {$errors}";
            }
            return back()->with('error', 'Errores de validación en el archivo: <br>' . implode('<br>', $errorMessages));
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error al procesar el archivo: ' . $e->getMessage());
        }
    }
}
