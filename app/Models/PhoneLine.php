<?php

namespace App\Models;

use App\Enums\PhoneLineStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhoneLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'provider',
        'data_plan',
        'plan_cost',
        'status',
        'notes',
    ];

    protected $casts = [
        'status' => PhoneLineStatus::class,
        'plan_cost' => 'decimal:2',
    ];

    /**
     * Get all assignments for this phone line (history).
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(PhoneLineAssignment::class)->orderBy('assigned_at', 'desc');
    }

    /**
     * Get the current active assignment.
     */
    public function currentAssignment()
    {
        return $this->hasOne(PhoneLineAssignment::class)->whereNull('returned_at');
    }
}
