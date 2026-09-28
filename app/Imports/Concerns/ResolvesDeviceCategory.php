<?php

namespace App\Imports\Concerns;

use App\Models\DeviceCategory;
use Illuminate\Support\Collection;

/**
 * Consolida el mapa de búsqueda de categorías (nombre/slug/nombre sin
 * acentos → DeviceCategory) que se construía de forma idéntica en el
 * constructor de DevicesImport y de los 3 Imports/Sheets/*ImportSheet
 * (Hallazgo Alto H7 de la auditoría).
 */
trait ResolvesDeviceCategory
{
    protected Collection $categories;

    protected function loadDeviceCategories(): void
    {
        $this->categories = collect();

        foreach (DeviceCategory::all() as $cat) {
            $this->categories->put(mb_strtolower($cat->name, 'UTF-8'), $cat);
            $this->categories->put(mb_strtolower($cat->slug, 'UTF-8'), $cat);
            $this->categories->put(
                str_replace(['á', 'é', 'í', 'ó', 'ú', 'ä', 'ë', 'ï', 'ö', 'ü'], ['a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u'], mb_strtolower($cat->name, 'UTF-8')),
                $cat
            );
        }
    }
}
