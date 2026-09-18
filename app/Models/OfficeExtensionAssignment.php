<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OfficeExtensionAssignment extends Pivot
{
    use HasFactory;

    protected $table = 'office_extension_assignments';

    public $incrementing = true;

    public $timestamps = true;

    protected $fillable = [
        'office_extension_id',
        'employee_id',
        'assigned_at',
        'returned_at',
        'notes',
    ];

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
