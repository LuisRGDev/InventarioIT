<?php

namespace App\Models;

use App\Enums\DeviceCondition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceAssignment extends Model
{
    /**
     * Extends Model (not Pivot) so it can be used directly in the Service Layer
     * via DeviceAssignment::create([...]) and queried as a first-class entity.
     * The ->using(DeviceAssignment::class) in BelongsToMany still works correctly.
     */
    protected $table = 'device_assignments';

    public $incrementing = true;
    public $timestamps   = true;

    protected $fillable = [
        'device_id',
        'employee_id',
        'assigned_by_user_id',
        'returned_by_user_id',
        'assigned_at',
        'returned_at',
        'condition_on_delivery',
        'condition_on_return',
        'notes',
    ];

    protected $casts = [
        'assigned_at'          => 'datetime',
        'returned_at'          => 'datetime',
        'condition_on_delivery' => DeviceCondition::class,
        'condition_on_return'   => DeviceCondition::class,
    ];

    // ─── Relaciones ───────────────────────────────────────────

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }

    public function returnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by_user_id');
    }

    // ─── Accessors ────────────────────────────────────────────

    public function getIsActiveAttribute(): bool
    {
        return is_null($this->returned_at);
    }

    public function getDurationAttribute(): ?string
    {
        $end = $this->returned_at ?? now();
        return $this->assigned_at->diffForHumans($end, true);
    }
}
