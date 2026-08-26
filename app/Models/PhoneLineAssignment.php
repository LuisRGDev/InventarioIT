<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhoneLineAssignment extends Pivot
{
    use HasFactory;

    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'phone_line_id',
        'employee_id',
        'assigned_at',
        'returned_at',
        'notes',
    ];

    protected $casts = [
        'assigned_at' => 'date',
        'returned_at' => 'date',
    ];

    public function phoneLine(): BelongsTo
    {
        return $this->belongsTo(PhoneLine::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
