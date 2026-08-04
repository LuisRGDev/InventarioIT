<?php

namespace App\Livewire;

use App\Exceptions\PhoneLineNotAvailableException;
use App\Models\Employee;
use App\Models\PhoneLine;
use App\Services\PhoneLineAssignmentService;
use Livewire\Component;
use Livewire\Attributes\Computed;

class AssignPhoneLinePage extends Component
{
    // Búsqueda
    public string $employeeSearch = '';
    public string $phoneSearch   = '';
    public ?int $selectedEmployeeId = null;
    public ?int $selectedPhoneId   = null;

    // Formulario
    public string $notes = '';

    // Estado UI
    public bool  $showConfirm = false;
    public ?string $successMessage = null;
    public ?string $errorMessage   = null;
    public ?int $lastAssignmentId  = null;

    #[Computed]
    public function employees()
    {
        return Employee::active()
            ->when($this->employeeSearch, fn($q) =>
                $q->where('name', 'like', "%{$this->employeeSearch}%")
                  ->orWhere('email', 'like', "%{$this->employeeSearch}%")
                  ->orWhere('employee_code', 'like', "%{$this->employeeSearch}%")
            )
            ->orderBy('name')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function availablePhoneLines()
    {
        return PhoneLine::where('status', \App\Enums\PhoneLineStatus::Disponible->value)
            ->when($this->phoneSearch, fn($q) =>
                $q->where(fn($w) => 
                    $w->where('number', 'like', "%{$this->phoneSearch}%")
                      ->orWhere('provider', 'like', "%{$this->phoneSearch}%")
                )
            )
            ->orderBy('number')
            ->limit(15)
            ->get();
    }

    #[Computed]
    public function selectedEmployee()
    {
        return $this->selectedEmployeeId
            ? Employee::find($this->selectedEmployeeId)
            : null;
    }

    #[Computed]
    public function selectedPhoneLine()
    {
        return $this->selectedPhoneId
            ? PhoneLine::find($this->selectedPhoneId)
            : null;
    }

    public function selectEmployee(int $id): void
    {
        $this->selectedEmployeeId = $id;
        $this->employeeSearch     = '';
        $this->resetValidation();
    }

    public function selectPhoneLine(int $id): void
    {
        $this->selectedPhoneId = $id;
        $this->phoneSearch     = '';
        $this->resetValidation();
    }

    public function clearEmployee(): void
    {
        $this->selectedEmployeeId = null;
        $this->showConfirm        = false;
    }

    public function clearPhoneLine(): void
    {
        $this->selectedPhoneId = null;
        $this->showConfirm      = false;
    }

    public function prepareConfirm(): void
    {
        $this->validate([
            'selectedEmployeeId'    => 'required',
            'selectedPhoneId'      => 'required',
        ], [
            'selectedEmployeeId.required' => 'Selecciona un empleado.',
            'selectedPhoneId.required'   => 'Selecciona una línea.',
        ]);

        $this->showConfirm = true;
    }

    public function assign(PhoneLineAssignmentService $service): void
    {
        try {
            $phoneLine   = PhoneLine::findOrFail($this->selectedPhoneId);
            $employee = Employee::findOrFail($this->selectedEmployeeId);

            $assignment = $service->assign($phoneLine, $employee, [
                'notes'                 => $this->notes,
            ]);

            $this->lastAssignmentId = $assignment->id;
            $this->successMessage = "Línea [{$phoneLine->number}] asignada a [{$employee->name}] correctamente.";
            $this->reset(['selectedEmployeeId', 'selectedPhoneId', 'notes', 'showConfirm']);
            $this->errorMessage = null;

        } catch (PhoneLineNotAvailableException $e) {
            $this->errorMessage   = $e->getMessage();
            $this->showConfirm    = false;
        } catch (\Exception $e) {
            $this->errorMessage   = 'Ocurrió un error inesperado. Intenta de nuevo.';
            $this->showConfirm    = false;
        }
    }

    public function render()
    {
        return view('livewire.assign-phone-line-page')
            ->layout('layouts.app', ['title' => 'Asignar Línea Telefónica']);
    }
}
