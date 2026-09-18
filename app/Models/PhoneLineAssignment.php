<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PhoneLineAssignment extends Pivot
{
    use HasFactory;

    protected $table = 'phone_line_assignments';

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
