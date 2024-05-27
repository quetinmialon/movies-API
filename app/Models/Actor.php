<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Actor extends Model
{
    use HasFactory;

    // Met à jour le timestamps updated_at du film si on lui attache/détache des acteurs
    protected $touches = ['movies'];

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class);
    }
}
