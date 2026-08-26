<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OfficeExtensionAssignment extends Pivot
{
    use HasFactory;

    public $incrementing = true;
    public $timestamps = true;

    protected $guarded = [];

    protected $casts = [
        'assigned_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function officeExtension(): BelongsTo
    {
        return $this->belongsTo(OfficeExtension::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
