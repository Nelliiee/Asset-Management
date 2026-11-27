<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetGroup extends Model
{
    protected $fillable = [
        'classroom_id',
        'name',
        'code',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $assetGroup): void {
            do {
                $code = 'ASG-'.random_int(100000, 999999);
            } while (self::query()->where('code', $code)->exists());

            $assetGroup->code = $code;
        });
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}
