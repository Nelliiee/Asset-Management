<?php

namespace App\Models;

use Database\Factories\ClassroomFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classroom extends Model
{
    /** @use HasFactory<ClassroomFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'room_number',
        'capacity',
        'code',
        'floor',
        'building_id',
        'is_active',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function assetGroups(): HasMany
    {
        return $this->hasMany(AssetGroup::class);
    }

    public function getAssetsCountAttribute()
    {
        return $this->assets()->count();
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'classroom_id');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (self $classroom) {
            if ($classroom->isDirty('building_id')) {
                $building = Building::find($classroom->building_id);
                if ($building) {
                    $classroom->code = $building->code;
                }
            }
        });
    }
}
