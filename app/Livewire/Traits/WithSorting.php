<?php

namespace App\Livewire\Traits;

trait WithSorting
{
    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    public function sortByField(string $field): void
    {
        if (! property_exists($this, 'allowedSortColumns') || ! in_array($field, $this->allowedSortColumns, true)) {
            return;
        }

        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
            $this->sortBy = $field;
        }
    }

    public function getSortBy(): string
    {
        if (property_exists($this, 'allowedSortColumns') && in_array($this->sortBy, $this->allowedSortColumns, true)) {
            return $this->sortBy;
        }

        return 'created_at';
    }
}
