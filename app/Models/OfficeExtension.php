<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\ExtensionStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfficeExtension extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'status' => ExtensionStatus::class,
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(OfficeExtensionAssignment::class);
    }

    public function currentAssignment()
    {
        return $this->hasOne(OfficeExtensionAssignment::class)->whereNull('returned_at');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', ExtensionStatus::Disponible->value);
    }
}
