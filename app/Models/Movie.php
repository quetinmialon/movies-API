<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'synopsis',
        'director_id',
        'duration',
        'release',
    ];

    protected $with = [
        'director:id,name',
        'actors:id,name',
    ];

    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class);
    }

    public function director(): BelongsTo
    {
        return $this->belongsTo(Director::class);
    }

    public function scopeList(Builder $builder): void
    {
        $builder->select('id', 'title', 'synopsis', 'release', 'duration');
    }
}
