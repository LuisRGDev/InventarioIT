<?php

namespace App\Models;

use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeviceMaintenance extends Model
{
    use SoftDeletes;

    protected $table = 'device_maintenances';

    protected $fillable = [
        'device_id',
        'user_id',
        'type',
        'status',
        'title',
        'description',
        'resolution_notes',
        'scheduled_at',
        'started_at',
        'completed_at',
        'next_due_at',
    ];

    protected $casts = [
        'type'           => MaintenanceType::class,
        'status'         => MaintenanceStatus::class,
        'scheduled_at'   => 'date',
        'started_at'     => 'datetime',
        'completed_at'   => 'datetime',
        'next_due_at'    => 'date',
    ];

    // ─── Relaciones ───────────────────────────────────────────

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ───────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->whereIn('status', [MaintenanceStatus::EnProceso, MaintenanceStatus::Programado]);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', MaintenanceStatus::EnProceso);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', MaintenanceStatus::Completado);
    }

    public function scopePreventive($query)
    {
        return $query->where('type', MaintenanceType::Preventivo);
    }
}
