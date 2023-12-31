<?php

namespace App\Models;

use App\Http\Lib\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ability extends Model
{
    use HasFactory;

    function scopeEntities($query, $entities)
    {
        Helper::entities($query, $entities);
    }

    function ability()
    {
        return $this->hasMany(AbilityFavorite::class, 'ability_id');
    }
}
