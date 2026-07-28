<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeviceModel extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'device_category_id',
        'brand',
        'model',
        'variant',
        'cpu',
        'ram',
        'storage',
        'os',
        'notes',
    ];

    /**
     * Relación con la categoría a la que pertenece este modelo y estándar.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(DeviceCategory::class, 'device_category_id');
    }

    /**
     * Relación con todos los dispositivos inventariados bajo este estándar.
     */
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    /**
     * Obtener un nombre formateado y legible para menús desplegables y reportes.
     */
    public function getDisplayNameAttribute(): string
    {
        $name = trim("{$this->brand} · {$this->model}");
        
        if (!empty($this->variant)) {
            $name .= " — [{$this->variant}]";
        }

        return $name;
    }

    /**
     * Resumen conciso de especificaciones técnicas para tablas o subtítulos.
     */
    public function getSpecsSummaryAttribute(): string
    {
        $parts = array_filter([
            $this->cpu ? "CPU: {$this->cpu}" : null,
            $this->ram ? "RAM: {$this->ram}" : null,
            $this->storage ? "Disco: {$this->storage}" : null,
            $this->os ? "OS: {$this->os}" : null,
        ]);

        return !empty($parts) ? implode(' · ', $parts) : 'Sin especificaciones técnicas registradas';
    }
}
