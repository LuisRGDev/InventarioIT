<?php

namespace App\Models;

use App\Enums\EmployeeStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{

    protected $fillable = [
        'employee_code', 'domain_account', 'name', 'email', 'phone',
        'department', 'position', 'status', 'notes',
    ];

    protected $casts = [
        'status' => EmployeeStatus::class,
    ];

    // ─── Relaciones ───────────────────────────────────────────

    public function jobPosition(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(JobPosition::class);
    }

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

    public function phoneLineAssignments(): HasMany
    {
        return $this->hasMany(PhoneLineAssignment::class);
    }

    public function currentPhoneLineAssignments(): HasMany
    {
        return $this->hasMany(PhoneLineAssignment::class)->whereNull('returned_at');
    }

    public function phoneLines(): BelongsToMany
    {
        return $this->belongsToMany(PhoneLine::class, 'phone_line_assignments')
            ->using(PhoneLineAssignment::class)
            ->withPivot(['id', 'assigned_at', 'returned_at', 'notes'])
            ->withTimestamps()
            ->orderByPivot('assigned_at', 'desc');
    }

    public function currentPhoneLines(): BelongsToMany
    {
        return $this->phoneLines()->wherePivotNull('returned_at');
    }

    public function officeExtensionAssignments(): HasMany
    {
        return $this->hasMany(OfficeExtensionAssignment::class);
    }

    public function currentOfficeExtensionAssignments(): HasMany
    {
        return $this->hasMany(OfficeExtensionAssignment::class)->whereNull('returned_at');
    }

    public function officeExtensions(): BelongsToMany
    {
        return $this->belongsToMany(OfficeExtension::class, 'office_extension_assignments')
            ->using(OfficeExtensionAssignment::class)
            ->withPivot(['id', 'assigned_at', 'returned_at', 'notes'])
            ->withTimestamps()
            ->orderByPivot('assigned_at', 'desc');
    }

    public function currentOfficeExtensions(): BelongsToMany
    {
        return $this->officeExtensions()->wherePivotNull('returned_at');
    }

    // ─── Scopes ───────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', EmployeeStatus::Activo->value);
    }
}
