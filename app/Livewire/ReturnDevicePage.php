<?php

namespace App\Livewire;

use App\Enums\DeviceCondition;
use App\Enums\DeviceStatus;
use App\Exceptions\NoActiveAssignmentException;
use App\Models\Device;
use App\Services\DeviceAssignmentService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ReturnDevicePage extends Component
{
    public ?int $deviceId = null;

    // Búsqueda (cuando se entra sin equipo, p. ej. desde el menú lateral)
    public string $deviceSearch = '';

    // Formulario
    public string $conditionOnReturn = 'buen_estado';

    public string $newStatus = 'disponible';

    public string $notes = '';

    // UI
    public bool $showConfirm = false;

    public ?string $successMessage = null;

    public ?string $errorMessage = null;

    public function mount(?int $device = null): void
    {
        $this->deviceId = $device;
    }

    #[Computed]
    public function device(): ?Device
    {
        return $this->deviceId
            ? Device::with(['currentAssignment.employee', 'category'])->find($this->deviceId)
            : null;
    }

    /**
     * Equipos con una asignación activa, para elegir cuál devolver cuando
     * la pantalla se abre sin un equipo (menú lateral). Busca por serial,
     * marca, modelo o nombre del empleado que lo tiene.
     */
    #[Computed]
    public function assignedDevices()
    {
        return Device::whereHas('currentAssignment')
            ->with(['currentAssignment.employee', 'category'])
            ->when($this->deviceSearch, fn ($q) => $q->where(fn ($w) => $w->where('serial_number', 'like', "%{$this->deviceSearch}%")
                ->orWhere('brand', 'like', "%{$this->deviceSearch}%")
                ->orWhere('model', 'like', "%{$this->deviceSearch}%")
                ->orWhereHas('currentAssignment.employee', fn ($e) => $e->where('name', 'like', "%{$this->deviceSearch}%"))
            ))
            ->orderBy('brand')
            ->limit(15)
            ->get();
    }

    #[Computed]
    public function conditions()
    {
        return DeviceCondition::cases();
    }

    #[Computed]
    public function returnableStatuses(): array
    {
        return [
            DeviceStatus::Disponible,
            DeviceStatus::EnReparacion,
            DeviceStatus::Obsoleto,
            DeviceStatus::Baja,
        ];
    }

    public function selectDevice(int $id): void
    {
        $this->deviceId = $id;
        $this->deviceSearch = '';
        $this->errorMessage = null;
        $this->showConfirm = false;
    }

    public function clearDevice(): void
    {
        $this->deviceId = null;
        $this->showConfirm = false;
        $this->errorMessage = null;
    }

    public function updatedConditionOnReturn(string $value): void
    {
        // Si el equipo viene dañado, preseleccionar "en reparación"
        if ($value === DeviceCondition::Daniado->value) {
            $this->newStatus = DeviceStatus::EnReparacion->value;
        } elseif ($this->newStatus === DeviceStatus::EnReparacion->value) {
            $this->newStatus = DeviceStatus::Disponible->value;
        }
    }

    public function prepareConfirm(): void
    {
        $this->validate([
            'deviceId' => 'required',
            'conditionOnReturn' => 'required',
            'newStatus' => 'required',
        ]);

        $this->showConfirm = true;
    }

    public function returnDevice(DeviceAssignmentService $service): void
    {
        try {
            $device = Device::findOrFail($this->deviceId);

            $service->returnDevice($device, [
                'condition_on_return' => $this->conditionOnReturn,
                'new_status' => $this->newStatus,
                'notes' => $this->notes,
            ]);

            session()->flash('success', "Equipo [{$device->brand} {$device->model}] devuelto correctamente.");
            $this->redirect(route('assignments.index'), navigate: true);

        } catch (NoActiveAssignmentException $e) {
            $this->errorMessage = $e->getMessage();
            $this->showConfirm = false;
        } catch (\Exception $e) {
            report($e);
            $this->errorMessage = 'Ocurrió un error inesperado. Intenta de nuevo.';
            $this->showConfirm = false;
        }
    }

    public function render()
    {
        return view('livewire.return-device-page')
            ->layout('layouts.app', ['title' => 'Registrar Devolución']);
    }
}
