<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $asset): void {
            do {
                $assetCode = 'AST-'.random_int(100, 999);
            } while (self::query()->where('asset_code', $assetCode)->exists());

            $asset->asset_code = $assetCode;
        });
    }

    public $table = 'assets';

    protected $fillable = [
        'category_id',
        'brand_id',
        'classroom_id',
        'building_id',
        'name',
        'serial_number',
        'terminal_number',
        'asset_code',
        'expiry_date',
        'status',
    ];

    protected $casts = [
        'expiry_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function assetTags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'asset_tags', 'asset_id', 'asset_tag_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->assetTags();
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
