<?php

namespace App\Livewire;

use App\Enums\DeviceCondition;
use App\Enums\DeviceStatus;
use App\Exceptions\DeviceNotAvailableException;
use App\Exceptions\NoActiveAssignmentException;
use App\Models\Device;
use App\Models\Employee;
use App\Services\DeviceAssignmentService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ReplaceDevicePage extends Component
{
    // Empleado
    public ?int   $employeeId     = null;
    public string $employeeSearch = '';

    // Equipo viejo (se resuelve desde el empleado seleccionado)
    public ?int $oldDeviceId = null;

    // Equipo nuevo
    public ?int   $newDeviceId  = null;
    public string $deviceSearch = '';
    public ?int   $categoryId   = null;

    // Condiciones
    public string $conditionOnReturn   = 'buen_estado';
    public string $conditionOnDelivery = 'buen_estado';
    public string $oldDeviceNewStatus  = 'disponible';

    // UI
    public bool   $showConfirm    = false;
    public ?string $successMessage = null;
    public ?string $errorMessage   = null;


    public function mount(?int $employee = null): void
    {
        if ($employee) {
            $this->employeeId = $employee;
        }
    }

    #[Computed]
    public function employees()
    {
        return Employee::active()
            ->has('currentAssignments') // Solo empleados con equipos activos
            ->when($this->employeeSearch, fn($q) =>
                $q->where('name', 'like', "%{$this->employeeSearch}%")
                  ->orWhere('email', 'like', "%{$this->employeeSearch}%")
            )
            ->orderBy('name')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function selectedEmployee(): ?Employee
    {
        return $this->employeeId ? Employee::with('currentAssignments.device.category')->find($this->employeeId) : null;
    }

    #[Computed]
    public function availableDevices()
    {
        return Device::available()
            ->with('category')
            ->when($this->categoryId, fn($q) => $q->where('device_category_id', $this->categoryId))
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
    public function selectedNewDevice(): ?Device
    {
        return $this->newDeviceId ? Device::with('category')->find($this->newDeviceId) : null;
    }

    #[Computed]
    public function conditions()
    {
        return DeviceCondition::cases();
    }

    public function selectEmployee(?int $id = null): void
    {
        $this->employeeId     = $id ? (int) $id : null;
        $this->employeeSearch = '';
        $this->oldDeviceId    = null;
        $this->newDeviceId    = null;
    }

    public function selectOldDevice(?int $id = null): void
    {
        $this->oldDeviceId = $id ? (int) $id : null;
    }

    public function selectNewDevice(?int $id = null): void
    {
        $this->newDeviceId  = $id ? (int) $id : null;
        $this->deviceSearch = '';
    }

    public function prepareConfirm(): void
    {
        $this->validate([
            'employeeId'  => 'required',
            'oldDeviceId' => 'required',
            'newDeviceId' => 'required|different:oldDeviceId',
        ], [
            'newDeviceId.different' => 'El equipo nuevo debe ser diferente al actual.',
        ]);

        $this->showConfirm = true;
    }

    public function replace(DeviceAssignmentService $service): void
    {
        try {
            $oldDevice = Device::findOrFail($this->oldDeviceId);
            $newDevice = Device::findOrFail($this->newDeviceId);
            $employee  = Employee::findOrFail($this->employeeId);

            $service->replace($oldDevice, $newDevice, $employee, [
                'condition_on_return'   => $this->conditionOnReturn,
                'condition_on_delivery' => $this->conditionOnDelivery,
                'old_device_new_status' => $this->oldDeviceNewStatus,
            ]);

            session()->flash('success', "Reemplazo completado: [{$oldDevice->brand} {$oldDevice->model}] → [{$newDevice->brand} {$newDevice->model}] para [{$employee->name}].");
            $this->redirect(route('assignments.index'), navigate: true);

        } catch (NoActiveAssignmentException | DeviceNotAvailableException $e) {
            $this->errorMessage = $e->getMessage();
            $this->showConfirm  = false;
        } catch (\Exception $e) {
            $this->errorMessage = 'Ocurrió un error inesperado: ' . $e->getMessage();
            $this->showConfirm  = false;
        }
    }

    public function render()
    {
        return view('livewire.replace-device-page')
            ->layout('layouts.app', ['title' => 'Reemplazar Equipo']);
    }
}
