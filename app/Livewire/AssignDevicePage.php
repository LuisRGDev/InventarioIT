<?php

namespace App\Livewire;

use App\Enums\DeviceCondition;
use App\Enums\DeviceStatus;
use App\Exceptions\DeviceAlreadyAssignedException;
use App\Exceptions\DeviceNotAvailableException;
use App\Models\Device;
use App\Models\Employee;
use App\Services\DeviceAssignmentService;
use Livewire\Component;
use Livewire\Attributes\Computed;

class AssignDevicePage extends Component
{
    // Búsqueda
    public string $employeeSearch = '';
    public string $deviceSearch   = '';
    public ?int $selectedEmployeeId = null;
    public ?int $selectedDeviceId   = null;
    public ?int $selectedCategoryId = null;

    // Formulario
    public string $conditionOnDelivery = 'buen_estado';
    public string $notes               = '';

    // Estado UI
    public bool  $showConfirm = false;
    public ?string $successMessage = null;
    public ?string $errorMessage   = null;

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
    public function availableDevices()
    {
        return Device::available()
            ->with('category')
            ->when($this->selectedCategoryId, fn($q) =>
                $q->where('device_category_id', $this->selectedCategoryId)
            )
            ->when($this->deviceSearch, fn($q) =>
                $q->where(fn($w) => 
                    $w->where('serial_number', 'like', "%{$this->deviceSearch}%")
                      ->orWhere('brand', 'like', "%{$this->deviceSearch}%")
                      ->orWhere('model', 'like', "%{$this->deviceSearch}%")
                )
            )
            ->orderBy('brand')
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
    public function selectedDevice()
    {
        return $this->selectedDeviceId
            ? Device::with('category')->find($this->selectedDeviceId)
            : null;
    }

    #[Computed]
    public function conditions()
    {
        return DeviceCondition::cases();
    }

    public function selectEmployee(int $id): void
    {
        $this->selectedEmployeeId = $id;
        $this->employeeSearch     = '';
        $this->resetValidation();
    }

    public function selectDevice(int $id): void
    {
        $this->selectedDeviceId = $id;
        $this->deviceSearch     = '';
        $this->resetValidation();
    }

    public function clearEmployee(): void
    {
        $this->selectedEmployeeId = null;
        $this->showConfirm        = false;
    }

    public function clearDevice(): void
    {
        $this->selectedDeviceId = null;
        $this->showConfirm      = false;
    }

    public function prepareConfirm(): void
    {
        $this->validate([
            'selectedEmployeeId'    => 'required',
            'selectedDeviceId'      => 'required',
            'conditionOnDelivery'   => 'required',
        ], [
            'selectedEmployeeId.required' => 'Selecciona un empleado.',
            'selectedDeviceId.required'   => 'Selecciona un equipo.',
        ]);

        $this->showConfirm = true;
    }

    public function assign(DeviceAssignmentService $service): void
    {
        try {
            $device   = Device::findOrFail($this->selectedDeviceId);
            $employee = Employee::findOrFail($this->selectedEmployeeId);

            $service->assign($device, $employee, [
                'condition_on_delivery' => $this->conditionOnDelivery,
                'notes'                 => $this->notes,
            ]);

            $this->successMessage = "Equipo [{$device->brand} {$device->model}] asignado a [{$employee->name}] correctamente.";
            $this->reset(['selectedEmployeeId', 'selectedDeviceId', 'conditionOnDelivery', 'notes', 'showConfirm']);
            $this->errorMessage = null;

        } catch (DeviceNotAvailableException | DeviceAlreadyAssignedException $e) {
            $this->errorMessage   = $e->getMessage();
            $this->showConfirm    = false;
        } catch (\Exception $e) {
            $this->errorMessage   = 'Ocurrió un error inesperado. Intenta de nuevo.';
            $this->showConfirm    = false;
        }
    }

    public function render()
    {
        return view('livewire.assign-device-page')
            ->layout('layouts.app', ['title' => 'Asignar Equipo']);
    }
}
