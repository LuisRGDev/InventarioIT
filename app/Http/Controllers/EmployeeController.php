<?php

namespace App\Http\Controllers;

use App\Enums\EmployeeStatus;
use App\Enums\ExtensionStatus;
use App\Enums\PhoneLineStatus;
use App\Exports\EmployeesExport;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\OfficeExtension;
use App\Models\PhoneLine;
use App\Services\ExtensionAssignmentService;
use App\Services\PhoneLineAssignmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EmployeeController extends Controller
{
    public function index(): View
    {
        return view('employees.index');
    }

    public function create(): View
    {
        $statuses = EmployeeStatus::cases();
        $availablePhoneLines = PhoneLine::where('status', PhoneLineStatus::Disponible->value)->limit(500)->get();
        $availableExtensions = OfficeExtension::where('status', ExtensionStatus::Disponible->value)->limit(500)->get();

        return view('employees.create', compact('statuses', 'availablePhoneLines', 'availableExtensions'));
    }

    public function store(StoreEmployeeRequest $request, PhoneLineAssignmentService $phoneService, ExtensionAssignmentService $extensionService): RedirectResponse
    {
        $employee = Employee::create($request->validated());

        try {
            if ($request->filled('assign_phone_line_id')) {
                $phoneLine = PhoneLine::findOrFail($request->input('assign_phone_line_id'));
                $phoneService->assign($phoneLine, $employee);
            }

            if ($request->filled('assign_office_extension_id')) {
                $extension = OfficeExtension::findOrFail($request->input('assign_office_extension_id'));
                $extensionService->assign($extension, $employee);
            }
        } catch (\Exception $e) {
            report($e);

            return redirect()->route('employees.index')
                ->with('success', 'Empleado creado correctamente.')
                ->with('warning', 'Hubo un problema al asignar recursos adicionales. Puedes asignarlos manualmente.');
        }

        return redirect()->route('employees.index')
            ->with('success', 'Empleado creado correctamente.');
    }

    public function show(Employee $employee): View
    {
        $employee->load([
            'currentAssignments.device.category',
        ]);

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee): View
    {
        $statuses = EmployeeStatus::cases();
        $availablePhoneLines = PhoneLine::where('status', PhoneLineStatus::Disponible->value)->limit(500)->get();
        $currentPhoneLine = $employee->currentPhoneLines()->first();

        $availableExtensions = OfficeExtension::where('status', ExtensionStatus::Disponible->value)->limit(500)->get();
        $currentExtension = $employee->currentOfficeExtensions()->first();

        return view('employees.edit', compact('employee', 'statuses', 'availablePhoneLines', 'currentPhoneLine', 'availableExtensions', 'currentExtension'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee, PhoneLineAssignmentService $phoneService, ExtensionAssignmentService $extensionService): RedirectResponse
    {
        $employee->update($request->validated());

        // Los cambios de línea/extensión van en try/catch, igual que en
        // store(): si el recurso elegido ya no está disponible (p. ej. otro
        // admin lo tomó primero) o el modelo no existe, el dato del empleado
        // ya guardado arriba no se pierde y el usuario recibe un aviso claro
        // en vez de un error 500 sin manejar.
        try {
            if ($request->has('assign_phone_line_id')) {
                $newPhoneLineId = $request->input('assign_phone_line_id');
                $currentPhoneLineAssignment = $employee->currentPhoneLineAssignments()->first();

                // Si se seleccionó una línea nueva y diferente a la actual.
                // PhoneLineAssignmentService::assign() ya devuelve
                // automáticamente cualquier línea activa previa del empleado
                // antes de asignar la nueva, así que no hace falta
                // retornarla manualmente aquí.
                if ($newPhoneLineId && (! $currentPhoneLineAssignment || $currentPhoneLineAssignment->phone_line_id != $newPhoneLineId)) {
                    $phoneLine = PhoneLine::findOrFail($newPhoneLineId);
                    $phoneService->assign($phoneLine, $employee);
                }
                // Si se deseleccionó la línea (se pasó vacío) y tenía una
                elseif (! $newPhoneLineId && $currentPhoneLineAssignment) {
                    $phoneService->returnLine($currentPhoneLineAssignment, ['notes' => 'Línea telefónica removida por edición de empleado.']);
                }
            }

            if ($request->has('assign_office_extension_id')) {
                $newExtensionId = $request->input('assign_office_extension_id');
                $currentExtensionAssignment = $employee->currentOfficeExtensionAssignments()->first();

                // ExtensionAssignmentService::assign() también devuelve
                // automáticamente la extensión activa previa del empleado.
                if ($newExtensionId && (! $currentExtensionAssignment || $currentExtensionAssignment->office_extension_id != $newExtensionId)) {
                    $extension = OfficeExtension::findOrFail($newExtensionId);
                    $extensionService->assign($extension, $employee);
                } elseif (! $newExtensionId && $currentExtensionAssignment) {
                    $extensionService->returnExtension($currentExtensionAssignment, ['notes' => 'Extensión removida por edición de empleado.']);
                }
            }
        } catch (\Exception $e) {
            report($e);

            return redirect()->route('employees.show', $employee)
                ->with('success', 'Empleado actualizado correctamente.')
                ->with('warning', 'Hubo un problema al actualizar la línea telefónica o extensión. Puedes intentarlo de nuevo desde Asignaciones.');
        }

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        if ($employee->currentAssignments()->exists() || $employee->currentPhoneLineAssignments()->exists() || $employee->currentOfficeExtensionAssignments()->exists()) {
            return back()->with('error', 'No se puede eliminar un empleado con equipos o líneas/extensiones asignadas actualmente.');
        }

        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Empleado eliminado correctamente.');
    }

    public function history(Employee $employee): View
    {
        $assignments = $employee->assignments()
            ->with(['device.category', 'assignedBy', 'returnedBy'])
            ->orderByDesc('assigned_at')
            ->paginate(15);

        return view('employees.history', compact('employee', 'assignments'));
    }

    public function export(): BinaryFileResponse
    {
        return Excel::download(new EmployeesExport, 'directorio_empleados_'.date('Y-m-d').'.xlsx');
    }
}
