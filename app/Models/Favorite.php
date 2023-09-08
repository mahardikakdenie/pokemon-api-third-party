<?php

namespace App\Models;

use App\Http\Lib\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    function favorite()
    {
        return $this->hasMany(AbilityFavorite::class, 'favorite_id');
    }

    function scopeEntities($query, $entities)
    {
        Helper::entities($query, $entities);
    }
}
