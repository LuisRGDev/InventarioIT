<?php

namespace App\Livewire;

use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Livewire\Traits\WithSorting;
use App\Models\DeviceMaintenance;
use Livewire\Component;
use Livewire\WithPagination;

class MaintenanceTable extends Component
{
    use WithPagination;
    use WithSorting;

    protected array $allowedSortColumns = [
        'title', 'status', 'type', 'created_at', 'started_at', 'completed_at',
    ];

    public string $search = '';

    public string $status = '';

    public string $type = '';

    public string $date_from = '';

    public string $date_to = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'status', 'type', 'date_from', 'date_to', 'sortBy', 'sortDirection']);
        $this->resetPage();
    }

    public function render()
    {
        $query = DeviceMaintenance::with(['device', 'device.category', 'user']);

        // Busqueda por texto
        if (! empty($this->search)) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('resolution_notes', 'like', "%{$search}%")
                    ->orWhereHas('device', function ($dq) use ($search) {
                        $dq->where('serial_number', 'like', "%{$search}%")
                            ->orWhere('computer_name', 'like', "%{$search}%")
                            ->orWhere('brand', 'like', "%{$search}%")
                            ->orWhere('model', 'like', "%{$search}%");
                    });
            });
        }

        // Filtros
        if (! empty($this->status)) {
            $query->where('status', $this->status);
        }
        if (! empty($this->type)) {
            $query->where('type', $this->type);
        }
        if (! empty($this->date_from)) {
            $query->where(function ($q) {
                $q->whereDate('started_at', '>=', $this->date_from)
                    ->orWhereDate('scheduled_at', '>=', $this->date_from);
            });
        }
        if (! empty($this->date_to)) {
            $query->where(function ($q) {
                $q->whereDate('started_at', '<=', $this->date_to)
                    ->orWhereDate('scheduled_at', '<=', $this->date_to);
            });
        }

        // Default sorting. getSortBy() revalida contra $allowedSortColumns
        // en vez de usar la propiedad pública $sortBy sin validar (Hallazgo
        // Medio M1).
        $sortBy = $this->getSortBy();
        if ($sortBy === 'created_at') {
            $query->latest('started_at');
        } else {
            $query->orderBy($sortBy, $this->sortDirection);
        }

        $maintenances = $query->paginate(15);

        // Stats
        $stats = [
            'in_progress' => DeviceMaintenance::where('status', MaintenanceStatus::EnProceso)->count(),
            'scheduled' => DeviceMaintenance::where('status', MaintenanceStatus::Programado)->count(),
            'completed' => DeviceMaintenance::where('status', MaintenanceStatus::Completado)->count(),
            'preventives' => DeviceMaintenance::where('type', MaintenanceType::Preventivo)->count(),
        ];

        return view('livewire.maintenance-table', [
            'maintenances' => $maintenances,
            'stats' => $stats,
        ]);
    }
}
