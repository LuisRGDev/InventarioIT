<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPosition extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'direction',
        'area',
        'name',
        'notes',
    ];

    /**
     * Relación con los empleados que ocupan este puesto.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Obtener un nombre formateado para menús desplegables.
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->direction} > {$this->area} — {$this->name}";
    }
}
