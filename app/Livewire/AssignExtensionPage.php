<?php

namespace App\Livewire;

use App\Exceptions\ExtensionNotAvailableException;
use App\Models\Employee;
use App\Models\OfficeExtension;
use App\Services\ExtensionAssignmentService;
use Livewire\Component;
use Livewire\Attributes\Computed;

class AssignExtensionPage extends Component
{
    // Búsqueda
    public string $employeeSearch = '';
    public string $extensionSearch = '';
    public ?int $selectedEmployeeId = null;
    public ?int $selectedExtensionId = null;

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
    public function availableExtensions()
    {
        return OfficeExtension::where('status', \App\Enums\ExtensionStatus::Disponible->value)
            ->when($this->extensionSearch, fn($q) =>
                $q->where(fn($w) => 
                    $w->where('extension_number', 'like', "%{$this->extensionSearch}%")
                      ->orWhere('direct_number', 'like', "%{$this->extensionSearch}%")
                )
            )
            ->orderBy('extension_number')
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
    public function selectedExtension()
    {
        return $this->selectedExtensionId
            ? OfficeExtension::find($this->selectedExtensionId)
            : null;
    }

    public function selectEmployee(int $id): void
    {
        $this->selectedEmployeeId = $id;
        $this->employeeSearch     = '';
        $this->resetValidation();
    }

    public function selectExtension(int $id): void
    {
        $this->selectedExtensionId = $id;
        $this->extensionSearch     = '';
        $this->resetValidation();
    }

    public function clearEmployee(): void
    {
        $this->selectedEmployeeId = null;
        $this->showConfirm        = false;
    }

    public function clearExtension(): void
    {
        $this->selectedExtensionId = null;
        $this->showConfirm      = false;
    }

    public function prepareConfirm(): void
    {
        $this->validate([
            'selectedEmployeeId'    => 'required',
            'selectedExtensionId'   => 'required',
        ], [
            'selectedEmployeeId.required' => 'Selecciona un empleado.',
            'selectedExtensionId.required'   => 'Selecciona una extensión.',
        ]);

        $this->showConfirm = true;
    }

    public function assign(ExtensionAssignmentService $service): void
    {
        try {
            $extension = OfficeExtension::findOrFail($this->selectedExtensionId);
            $employee  = Employee::findOrFail($this->selectedEmployeeId);

            $assignment = $service->assign($extension, $employee, [
                'notes' => $this->notes,
            ]);

            $this->lastAssignmentId = $assignment->id;
            $this->successMessage = "Extensión [{$extension->extension_number}] asignada a [{$employee->name}] correctamente.";
            $this->reset(['selectedEmployeeId', 'selectedExtensionId', 'notes', 'showConfirm']);
            $this->errorMessage = null;

        } catch (ExtensionNotAvailableException $e) {
            $this->errorMessage   = $e->getMessage();
            $this->showConfirm    = false;
        } catch (\Exception $e) {
            $this->errorMessage   = 'Ocurrió un error inesperado. Intenta de nuevo.';
            $this->showConfirm    = false;
        }
    }

    public function render()
    {
        return view('livewire.assign-extension-page')
            ->layout('layouts.app', ['title' => 'Asignar Extensión Telefónica']);
    }
}
