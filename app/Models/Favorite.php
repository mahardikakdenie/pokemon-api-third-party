<?php

namespace App\Models;

use App\Http\lib\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    function ability()
    {
        return $this->hasMany(AbilityFavorite::class, 'ability_id');
    }

    function scopeEntities($query, $entities)
    {
        Helper::entities($query, $entities);
    }
}
