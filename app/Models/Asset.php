<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Asset extends Model
{
    use HasFactory;

    public $table = 'assets';

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'serial_number',
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
}