<?php

namespace App\Http\Controllers;

use App\Enums\EmployeeStatus;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeesExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Models\PhoneLine;
use App\Enums\PhoneLineStatus;
use App\Services\PhoneLineAssignmentService;
use App\Models\OfficeExtension;
use App\Enums\ExtensionStatus;
use App\Services\ExtensionAssignmentService;

class EmployeeController extends Controller
{
    public function index(): View
    {
        return view('employees.index');
    }

    public function create(): View
    {
        $statuses = EmployeeStatus::cases();
        $availablePhoneLines = PhoneLine::where('status', PhoneLineStatus::Disponible->value)->get();
        $availableExtensions = OfficeExtension::where('status', ExtensionStatus::Disponible->value)->get();
        return view('employees.create', compact('statuses', 'availablePhoneLines', 'availableExtensions'));
    }

    public function store(StoreEmployeeRequest $request, PhoneLineAssignmentService $phoneService, ExtensionAssignmentService $extensionService): RedirectResponse
    {
        $employee = Employee::create($request->validated());

        if ($request->filled('assign_phone_line_id')) {
            $phoneLine = PhoneLine::findOrFail($request->input('assign_phone_line_id'));
            $phoneService->assign($phoneLine, $employee);
        }

        if ($request->filled('assign_office_extension_id')) {
            $extension = OfficeExtension::findOrFail($request->input('assign_office_extension_id'));
            $extensionService->assign($extension, $employee);
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
        $availablePhoneLines = PhoneLine::where('status', PhoneLineStatus::Disponible->value)->get();
        $currentPhoneLine = $employee->currentPhoneLines()->first();
        
        $availableExtensions = OfficeExtension::where('status', ExtensionStatus::Disponible->value)->get();
        $currentExtension = $employee->currentOfficeExtensions()->first();

        return view('employees.edit', compact('employee', 'statuses', 'availablePhoneLines', 'currentPhoneLine', 'availableExtensions', 'currentExtension'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee, PhoneLineAssignmentService $phoneService, ExtensionAssignmentService $extensionService): RedirectResponse
    {
        $employee->update($request->validated());

        if ($request->has('assign_phone_line_id')) {
            $newPhoneLineId = $request->input('assign_phone_line_id');
            $currentPhoneLineAssignment = $employee->currentPhoneLineAssignments()->first();

            // Si se seleccionó una línea nueva y diferente a la actual
            if ($newPhoneLineId && (!$currentPhoneLineAssignment || $currentPhoneLineAssignment->phone_line_id != $newPhoneLineId)) {
                // Retornar la actual si existe
                if ($currentPhoneLineAssignment) {
                    $phoneService->returnLine($currentPhoneLineAssignment, ['notes' => 'Cambio de línea telefónica por edición de empleado.']);
                }
                
                // Asignar la nueva
                $phoneLine = PhoneLine::findOrFail($newPhoneLineId);
                $phoneService->assign($phoneLine, $employee);
            }
            // Si se deseleccionó la línea (se pasó vacío) y tenía una
            elseif (!$newPhoneLineId && $currentPhoneLineAssignment) {
                $phoneService->returnLine($currentPhoneLineAssignment, ['notes' => 'Línea telefónica removida por edición de empleado.']);
            }
        }

        if ($request->has('assign_office_extension_id')) {
            $newExtensionId = $request->input('assign_office_extension_id');
            $currentExtensionAssignment = $employee->currentOfficeExtensionAssignments()->first();

            if ($newExtensionId && (!$currentExtensionAssignment || $currentExtensionAssignment->office_extension_id != $newExtensionId)) {
                if ($currentExtensionAssignment) {
                    $extensionService->returnExtension($currentExtensionAssignment, ['notes' => 'Cambio de extensión por edición de empleado.']);
                }
                
                $extension = OfficeExtension::findOrFail($newExtensionId);
                $extensionService->assign($extension, $employee);
            }
            elseif (!$newExtensionId && $currentExtensionAssignment) {
                $extensionService->returnExtension($currentExtensionAssignment, ['notes' => 'Extensión removida por edición de empleado.']);
            }
        }

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        // Verificar que no tenga asignaciones activas antes de eliminar
        if ($employee->currentAssignments()->exists() || $employee->currentPhoneLineAssignments()->exists() || $employee->currentOfficeExtensionAssignments()->exists()) {
            return back()->with('error', 'No se puede eliminar un empleado con equipos o líneas/extensiones asignadas actualmente.');
        }

        $employee->assignments()->delete();
        $employee->phoneLineAssignments()->delete();
        $employee->officeExtensionAssignments()->delete();
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Empleado eliminado permanentemente.');
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
        return Excel::download(new EmployeesExport, 'directorio_empleados_' . date('Y-m-d') . '.xlsx');
    }
}
