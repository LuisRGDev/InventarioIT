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

    // Formulario
    public string $conditionOnReturn = 'buen_estado';
    public string $newStatus         = 'disponible';
    public string $notes             = '';

    // UI
    public bool   $showConfirm    = false;
    public ?string $successMessage = null;
    public ?string $errorMessage   = null;

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
            'deviceId'        => 'required',
            'conditionOnReturn' => 'required',
            'newStatus'       => 'required',
        ]);

        $this->showConfirm = true;
    }

    public function returnDevice(DeviceAssignmentService $service): void
    {
        try {
            $device = Device::findOrFail($this->deviceId);

            $service->returnDevice($device, [
                'condition_on_return' => $this->conditionOnReturn,
                'new_status'          => $this->newStatus,
                'notes'               => $this->notes,
            ]);

            $this->successMessage = "Equipo [{$device->brand} {$device->model}] devuelto correctamente.";
            $this->reset(['conditionOnReturn', 'newStatus', 'notes', 'showConfirm', 'deviceId']);
            $this->errorMessage   = null;

        } catch (NoActiveAssignmentException $e) {
            $this->errorMessage = $e->getMessage();
            $this->showConfirm  = false;
        } catch (\Exception $e) {
            $this->errorMessage = 'Error inesperado: ' . $e->getMessage();
            $this->showConfirm  = false;
        }
    }

    public function render()
    {
        return view('livewire.return-device-page')
            ->layout('layouts.app', ['title' => 'Registrar Devolución']);
    }
}
