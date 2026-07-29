<?php

namespace App\Models;

use App\Enums\DeviceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'device_category_id', 'device_model_id', 'serial_number', 'computer_name',
        'mac_address_ethernet', 'mac_address_wifi', 'imei', 'phone_number', 'data_plan',
        'brand', 'model', 'status', 'purchase_date',
        'warranty_expires_at', 'specs', 'notes',
    ];

    protected $casts = [
        'status'             => DeviceStatus::class,
        'specs'              => 'array',
        'purchase_date'      => 'date',
        'warranty_expires_at' => 'date',
    ];

    // ─── Relaciones ───────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(DeviceCategory::class, 'device_category_id');
    }

    public function deviceModel(): BelongsTo
    {
        return $this->belongsTo(DeviceModel::class, 'device_model_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(DeviceAssignment::class);
    }

    public function currentAssignment(): HasOne
    {
        return $this->hasOne(DeviceAssignment::class)->whereNull('returned_at')->latest('assigned_at');
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(DeviceMaintenance::class)->latest('started_at');
    }

    public function activeMaintenance(): HasOne
    {
        return $this->hasOne(DeviceMaintenance::class)
            ->where('status', \App\Enums\MaintenanceStatus::EnProceso)
            ->latest('started_at');
    }

    public function lastPreventiveMaintenance(): HasOne
    {
        return $this->hasOne(DeviceMaintenance::class)
            ->where('type', \App\Enums\MaintenanceType::Preventivo)
            ->where('status', \App\Enums\MaintenanceStatus::Completado)
            ->latest('completed_at');
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'device_assignments')
            ->using(DeviceAssignment::class)
            ->withPivot([
                'id', 'assigned_at', 'returned_at',
                'condition_on_delivery', 'condition_on_return',
                'assigned_by_user_id', 'returned_by_user_id', 'notes',
            ])
            ->withTimestamps()
            ->orderByPivot('assigned_at', 'desc');
    }

    // ─── Accessors ────────────────────────────────────────────

    public function getCurrentEmployeeAttribute(): ?Employee
    {
        return $this->currentAssignment?->employee;
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->status === DeviceStatus::Disponible;
    }

    public function getWarrantyIsActiveAttribute(): bool
    {
        return $this->warranty_expires_at !== null
            && $this->warranty_expires_at->isFuture();
    }

    public function getWarrantyExpiresSoonAttribute(): bool
    {
        return $this->warranty_expires_at !== null
            && $this->warranty_expires_at->isFuture()
            && $this->warranty_expires_at->diffInDays(now()) <= 30;
    }

    public function getAgeAttribute(): ?int
    {
        if (! $this->purchase_date) {
            return null;
        }
        return (int) $this->purchase_date->diffInYears(now());
    }

    // ─── Scopes ───────────────────────────────────────────────

    public function scopeAvailable($query)
    {
        return $query->where('status', DeviceStatus::Disponible);
    }

    public function scopeWarrantyExpiringSoon($query, int $days = 30)
    {
        return $query->whereBetween('warranty_expires_at', [now(), now()->addDays($days)]);
    }
}
