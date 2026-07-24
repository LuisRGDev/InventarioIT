<?php

namespace App\Livewire;

use App\Enums\EmployeeStatus;
use App\Enums\ConditionStatus;
use App\Models\Device;
use App\Models\Employee;
use App\Services\DeviceAssignmentService;
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
    public string $phone = '';
    public string $department = '';
    public string $position = '';
    public string $notes = '';
    public ?int $employeeId = null;

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
            ->whereHas('category', fn($q) => $q->whereIn('slug', ['laptop', 'desktop']))
            ->when($this->computerSearch, fn($q) => $q->where(fn($w) =>
                $w->where('serial_number', 'like', "%{$this->computerSearch}%")
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
            ->whereHas('category', fn($q) => $q->where('slug', 'smartphone'))
            ->when($this->smartphoneSearch, fn($q) => $q->where(fn($w) =>
                $w->where('serial_number', 'like', "%{$this->smartphoneSearch}%")
                  ->orWhere('brand', 'like', "%{$this->smartphoneSearch}%")
                  ->orWhere('model', 'like', "%{$this->smartphoneSearch}%")
            ))
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function conditions(): array
    {
        return ConditionStatus::cases();
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
            'phone' => 'nullable|string|max:30',
            'department' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = EmployeeStatus::Activo->value;

        $employee = Employee::create($validated);
        $this->employeeId = $employee->id;
        
        $this->step = 2;
    }

    public function assignComputer(DeviceAssignmentService $service)
    {
        if ($this->computer_id) {
            $this->validate(['computer_condition' => 'required|string']);
            
            $service->assign(
                Device::findOrFail($this->computer_id),
                Employee::findOrFail($this->employeeId),
                ['condition_on_delivery' => $this->computer_condition]
            );
        }

        $this->step = 3;
    }

    public function assignSmartphone(DeviceAssignmentService $service)
    {
        if ($this->smartphone_id) {
            $this->validate(['smartphone_condition' => 'required|string']);
            
            $service->assign(
                Device::findOrFail($this->smartphone_id),
                Employee::findOrFail($this->employeeId),
                ['condition_on_delivery' => $this->smartphone_condition]
            );
        }

        session()->flash('success', 'Empleado dado de alta y equipos asignados (si seleccionaste alguno).');
        $this->redirect(route('employees.show', $this->employeeId), navigate: true);
    }

    public function render()
    {
        return view('livewire.employee-onboarding-wizard');
    }
}
