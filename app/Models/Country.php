<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Country extends Model
{
    // 1. Falta el fillable para permitir la carga masiva (Mass Assignment)
    protected $fillable = ['name', 'code'];

    // 2. La relación está perfecta
    public function provinces(): HasMany
    {
        return $this->hasMany(Province::class);
    }

    public function cities(): HasManyThrough
{
    return $this->hasManyThrough(City::class, Province::class);
}
} 