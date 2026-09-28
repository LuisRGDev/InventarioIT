<?php

namespace App\Livewire;

use App\Enums\DeviceCondition;
use App\Enums\EmployeeStatus;
use App\Enums\ExtensionStatus;
use App\Enums\PhoneLineStatus;
use App\Models\Device;
use App\Models\Employee;
use App\Models\JobPosition;
use App\Models\OfficeExtension;
use App\Models\PhoneLine;
use App\Services\DeviceAssignmentService;
use App\Services\ExtensionAssignmentService;
use App\Services\PhoneLineAssignmentService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class EmployeeOnboardingWizard extends Component
{
    public int $step = 1;

    // ─── Empleado (Paso 1) ──────────────────────────────────────────────
    public string $name = '';

    public string $email = '';

    public string $employee_code = '';

    public string $domain_account = '';

    public ?int $job_position_id = null;

    public string $notes = '';

    public ?int $employeeId = null;

    public ?int $assign_phone_line_id = null;

    public ?int $assign_office_extension_id = null;

    public string $errorMessage = '';

    // ─── Cómputo (Paso 2) ───────────────────────────────────────────────
    public ?int $computer_id = null;

    public string $computerSearch = '';

    public string $computer_condition = '';

    // ─── Celular (Paso 3) ───────────────────────────────────────────────
    public ?int $smartphone_id = null;

    public string $smartphoneSearch = '';

    public string $smartphone_condition = '';

    #[Computed]
    public function availableComputers()
    {
        return Device::available()
            ->whereHas('category', fn ($q) => $q->whereIn('slug', ['portatil', 'desktop']))
            ->when($this->computerSearch, fn ($q) => $q->where(fn ($w) => $w->where('serial_number', 'like', "%{$this->computerSearch}%")
                ->orWhere('brand', 'like', "%{$this->computerSearch}%")
                ->orWhere('model', 'like', "%{$this->computerSearch}%")
            ))
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function availableSmartphones()
    {
        return Device::available()
            ->whereHas('category', fn ($q) => $q->where('slug', 'smartphone'))
            ->when($this->smartphoneSearch, fn ($q) => $q->where(fn ($w) => $w->where('serial_number', 'like', "%{$this->smartphoneSearch}%")
                ->orWhere('brand', 'like', "%{$this->smartphoneSearch}%")
                ->orWhere('model', 'like', "%{$this->smartphoneSearch}%")
            ))
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function conditions(): array
    {
        return DeviceCondition::cases();
    }

    #[Computed]
    public function jobPositions()
    {
        return JobPosition::orderBy('direction')->orderBy('area')->orderBy('name')->get();
    }

    #[Computed]
    public function availablePhoneLines()
    {
        return PhoneLine::where('status', PhoneLineStatus::Disponible->value)->get();
    }

    #[Computed]
    public function availableExtensions()
    {
        return OfficeExtension::where('status', ExtensionStatus::Disponible->value)->get();
    }

    public function selectComputer(int $id)
    {
        $this->computer_id = $id;
        $this->computerSearch = '';
    }

    public function clearComputer()
    {
        $this->computer_id = null;
    }

    public function selectSmartphone(int $id)
    {
        $this->smartphone_id = $id;
        $this->smartphoneSearch = '';
    }

    public function clearSmartphone()
    {
        $this->smartphone_id = null;
    }

    public function saveEmployee()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:employees,email',
            'employee_code' => 'nullable|string|max:50|unique:employees,employee_code',
            'domain_account' => 'nullable|string|max:100|unique:employees,domain_account',

            'job_position_id' => 'required|exists:job_positions,id',
            'notes' => 'nullable|string',
            'assign_phone_line_id' => 'nullable|exists:phone_lines,id',
            'assign_office_extension_id' => 'nullable|exists:office_extensions,id',
        ]);

        $jobPosition = JobPosition::findOrFail($this->job_position_id);
        $validated['department'] = $jobPosition->area;
        $validated['position'] = $jobPosition->name;

        $validated['status'] = EmployeeStatus::Activo->value;

        // Quitar campos que no van en el modelo
        $assignPhoneLineId = $validated['assign_phone_line_id'] ?? null;
        $assignExtensionId = $validated['assign_office_extension_id'] ?? null;
        unset($validated['assign_phone_line_id'], $validated['assign_office_extension_id']);

        $employee = Employee::create($validated);
        $this->employeeId = $employee->id;

        // El empleado ya quedó creado arriba: si la línea/extensión elegida
        // ya no está disponible (p. ej. una condición de carrera con otro
        // admin) no debe perderse ese progreso ni dejar al usuario varado
        // en un error sin estilo; se avisa y se puede asignar después desde
        // Asignaciones, igual que en assignComputer()/assignSmartphone().
        try {
            if ($assignPhoneLineId) {
                $phoneService = app(PhoneLineAssignmentService::class);
                $phoneLine = PhoneLine::findOrFail($assignPhoneLineId);
                $phoneService->assign($phoneLine, $employee);
            }

            if ($assignExtensionId) {
                $extensionService = app(ExtensionAssignmentService::class);
                $extension = OfficeExtension::findOrFail($assignExtensionId);
                $extensionService->assign($extension, $employee);
            }
        } catch (\Exception $e) {
            report($e);
            $this->errorMessage = 'El empleado se creó correctamente, pero no se pudo asignar la línea telefónica o extensión seleccionada. Puedes asignarla después desde Asignaciones.';
        }

        $this->step = 2;
    }

    public function skipComputer()
    {
        $this->errorMessage = '';
        $this->computer_id = null;
        $this->computer_condition = '';
        $this->step = 3;
    }

    public function assignComputer(DeviceAssignmentService $service)
    {
        $this->errorMessage = '';

        if ($this->computer_id) {
            $this->validate(['computer_condition' => 'required|string']);

            try {
                $service->assign(
                    Device::findOrFail($this->computer_id),
                    Employee::findOrFail($this->employeeId),
                    ['condition_on_delivery' => $this->computer_condition]
                );
            } catch (\Exception $e) {
                report($e);
                $this->errorMessage = 'No se pudo asignar el equipo. Intenta de nuevo.';

                return;
            }
        }

        $this->step = 3;
    }

    public function skipSmartphone()
    {
        $this->smartphone_id = null;
        $this->smartphone_condition = '';

        session()->flash('success', 'Empleado dado de alta y equipos asignados (si seleccionaste alguno).');
        $this->redirect(route('employees.show', $this->employeeId), navigate: true);
    }

    public function assignSmartphone(DeviceAssignmentService $service)
    {
        $this->errorMessage = '';

        if ($this->smartphone_id) {
            $this->validate(['smartphone_condition' => 'required|string']);

            try {
                $service->assign(
                    Device::findOrFail($this->smartphone_id),
                    Employee::findOrFail($this->employeeId),
                    ['condition_on_delivery' => $this->smartphone_condition]
                );
            } catch (\Exception $e) {
                report($e);
                $this->errorMessage = 'No se pudo asignar el celular. Intenta de nuevo.';

                return;
            }
        }

        session()->flash('success', 'Empleado dado de alta y equipos asignados (si seleccionaste alguno).');
        $this->redirect(route('employees.show', $this->employeeId), navigate: true);
    }

    public function render()
    {
        return view('livewire.employee-onboarding-wizard')
            ->layout('layouts.app', ['title' => 'Onboarding de Empleado']);
    }
}
