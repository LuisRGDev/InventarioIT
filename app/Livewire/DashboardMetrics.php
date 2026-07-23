<?php

namespace App\Livewire;

use App\Enums\DeviceStatus;
use App\Models\Device;
use Livewire\Attributes\Computed;
use Livewire\Component;

class DashboardMetrics extends Component
{
    #[Computed]
    public function metrics(): array
    {
        $total       = Device::count();
        $available   = Device::where('status', DeviceStatus::Disponible)->count();
        $assigned    = Device::where('status', DeviceStatus::Asignado)->count();
        $inRepair    = Device::where('status', DeviceStatus::EnReparacion)->count();
        $obsolete    = Device::where('status', DeviceStatus::Obsoleto)->count();
        $lowWarranty = Device::warrantyExpiringSoon(30)->count();

        return compact('total', 'available', 'assigned', 'inRepair', 'obsolete', 'lowWarranty');
    }

    #[Computed]
    public function recentAssignments()
    {
        return \App\Models\DeviceAssignment::with(['device.category', 'employee', 'assignedBy'])
            ->whereNull('returned_at')
            ->orderByDesc('assigned_at')
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function expiringWarranties()
    {
        return Device::warrantyExpiringSoon(30)
            ->with(['category', 'currentAssignment.employee'])
            ->orderBy('warranty_expires_at')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard-metrics');
    }
}
