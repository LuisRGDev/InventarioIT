<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Employee;
use App\Livewire\Traits\WithSorting;

class EmployeeTable extends Component
{
    use WithPagination;
    use WithSorting;

    public $search = '';
    public $department = '';
    public $status = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingDepartment() { $this->resetPage(); }
    public function updatingStatus() { $this->resetPage(); }

    public function clearFilters()
    {
        $this->reset(['search', 'department', 'status', 'sortBy', 'sortDirection']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Employee::withCount('currentAssignments');

        // Búsqueda por texto
        if (!empty($this->search)) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }

        // Filtro por departamento
        if (!empty($this->department)) {
            $query->where('department', 'like', "%{$this->department}%");
        }

        // Filtro por estatus
        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }

        // Sorting
        $query->orderBy($this->sortBy === 'created_at' ? 'name' : $this->sortBy, $this->sortDirection);

        $employees = $query->paginate(20);

        return view('livewire.employee-table', [
            'employees' => $employees,
        ]);
    }
}
