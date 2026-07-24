<?php

namespace App\Models;

use App\Enums\EmployeeStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_code', 'domain_account', 'name', 'email', 'phone',
        'department', 'position', 'status', 'notes',
    ];

    protected $casts = [
        'status' => EmployeeStatus::class,
    ];

    // ─── Relaciones ───────────────────────────────────────────

    public function assignments(): HasMany
    {
        return $this->hasMany(DeviceAssignment::class);
    }

    public function currentAssignments(): HasMany
    {
        return $this->hasMany(DeviceAssignment::class)->whereNull('returned_at');
    }

    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'device_assignments')
            ->using(DeviceAssignment::class)
            ->withPivot([
                'id', 'assigned_at', 'returned_at',
                'condition_on_delivery', 'condition_on_return',
                'assigned_by_user_id', 'returned_by_user_id', 'notes',
            ])
            ->withTimestamps()
            ->orderByPivot('assigned_at', 'desc');
    }

    public function currentDevices(): BelongsToMany
    {
        return $this->devices()->wherePivotNull('returned_at');
    }

    // ─── Scopes ───────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', EmployeeStatus::Activo->value);
    }
}
