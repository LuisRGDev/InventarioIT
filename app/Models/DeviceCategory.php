<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DeviceCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'description'];

    /**
     * Auto-generate slug from name if not provided.
     */
    protected static function booted(): void
    {
        static::creating(function (DeviceCategory $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function isComputer(): bool
    {
        return in_array($this->slug, ['portatil', 'desktop']);
    }

    public function isSmartphone(): bool
    {
        return $this->slug === 'smartphone';
    }
}
