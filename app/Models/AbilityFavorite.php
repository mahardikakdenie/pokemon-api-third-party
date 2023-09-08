<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbilityFavorite extends Model
{
    use HasFactory;
    protected $table = 'ability_have_favorites';

    function pokemon()
    {
        return $this->belongsTo(Favorite::class, 'favorite_id');
    }

    function ability()
    {
        return $this->belongsTo(Ability::class, 'ability_id');
    }
}
