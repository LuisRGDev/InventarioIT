<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Device;
use App\Models\DeviceCategory;
use App\Models\DeviceModel;
use App\Livewire\Traits\WithSorting;

class DeviceTable extends Component
{
    use WithPagination;
    use WithSorting;

    public $search = '';
    public $category_ids = [];
    public $status = '';
    public $condition = '';
    public $model_id = '';
    public $date_from = '';
    public $date_to = '';

    // Reset pagination when searching/filtering
    public function updatingSearch() { $this->resetPage(); }
    public function updatingCategoryIds() { $this->resetPage(); }
    public function updatingStatus() { $this->resetPage(); }
    public function updatingCondition() { $this->resetPage(); }
    public function updatingModelId() { $this->resetPage(); }
    public function updatingDateFrom() { $this->resetPage(); }
    public function updatingDateTo() { $this->resetPage(); }

    public function clearFilters()
    {
        $this->reset(['search', 'category_ids', 'status', 'condition', 'model_id', 'date_from', 'date_to', 'sortBy', 'sortDirection']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Device::with(['category', 'deviceModel'])->withCount('assignments');

        // Text Search
        if (!empty($this->search)) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('mac_address_ethernet', 'like', "%{$search}%")
                  ->orWhere('mac_address_wifi', 'like', "%{$search}%");
            });
        }

        // Filters
        if (!empty($this->category_ids)) {
            $query->whereIn('device_category_id', $this->category_ids);
        }
        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }
        if (!empty($this->condition)) {
            $query->where('condition', $this->condition);
        }
        if (!empty($this->model_id)) {
            $query->where('device_model_id', $this->model_id);
        }
        if (!empty($this->date_from)) {
            $query->whereDate('created_at', '>=', $this->date_from);
        }
        if (!empty($this->date_to)) {
            $query->whereDate('created_at', '<=', $this->date_to);
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $devices = $query->paginate(20);

        // Required Data for Filters
        $categories = DeviceCategory::orderBy('name')->get();
        $models = DeviceModel::orderBy('brand')->orderBy('model')->get();
        $statsByStatus = Device::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('livewire.device-table', [
            'devices' => $devices,
            'categories' => $categories,
            'models' => $models,
            'statsByStatus' => $statsByStatus,
        ]);
    }
}
