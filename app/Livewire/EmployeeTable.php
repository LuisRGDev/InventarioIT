<?php

namespace App\Livewire;

use App\Livewire\Traits\WithSorting;
use App\Models\Employee;
use Livewire\Component;
use Livewire\WithPagination;

class EmployeeTable extends Component
{
    use WithPagination;
    use WithSorting;

    protected array $allowedSortColumns = [
        'name', 'email', 'employee_code', 'department', 'status', 'created_at',
    ];

    public string $search = '';

    public string $department = '';

    public string $status = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDepartment()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'department', 'status', 'sortBy', 'sortDirection']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Employee::withCount('currentAssignments');

        // Búsqueda por texto
        if (! empty($this->search)) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }

        // Filtro por departamento
        if (! empty($this->department)) {
            $query->where('department', 'like', "%{$this->department}%");
        }

        // Filtro por estatus
        if (! empty($this->status)) {
            $query->where('status', $this->status);
        }

        // Sorting. getSortBy() revalida contra $allowedSortColumns en vez de
        // usar la propiedad pública $sortBy sin validar (Hallazgo Medio M1).
        $sortBy = $this->getSortBy();
        $query->orderBy($sortBy === 'created_at' ? 'name' : $sortBy, $this->sortDirection);

        $employees = $query->paginate(20);

        return view('livewire.employee-table', [
            'employees' => $employees,
        ]);
    }
}
