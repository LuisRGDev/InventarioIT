<?php

namespace App\Http\Controllers;

use App\Enums\EmployeeStatus;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(): View
    {
        $employees = Employee::withCount('currentAssignments')
            ->orderBy('name')
            ->paginate(20);

        return view('employees.index', compact('employees'));
    }

    public function create(): View
    {
        $statuses = EmployeeStatus::cases();
        return view('employees.create', compact('statuses'));
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        Employee::create($request->validated());

        return redirect()->route('employees.index')
            ->with('success', 'Empleado creado correctamente.');
    }

    public function show(Employee $employee): View
    {
        $employee->load('currentAssignments.device.category');
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee): View
    {
        $statuses = EmployeeStatus::cases();
        return view('employees.edit', compact('employee', 'statuses'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $employee->update($request->validated());

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        // Verificar que no tenga asignaciones activas antes de eliminar (soft delete)
        if ($employee->currentAssignments()->exists()) {
            return back()->with('error', 'No se puede eliminar un empleado con equipos asignados actualmente.');
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
}
