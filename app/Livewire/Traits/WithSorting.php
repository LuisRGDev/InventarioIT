<?php

namespace App\Livewire\Traits;

trait WithSorting
{
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    public function sortByField($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
            $this->sortBy = $field;
        }
    }
}
