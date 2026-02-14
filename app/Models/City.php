<?php

namespace App\Models;

use App\Models\Country;
use App\Models\Province;
use App\Models\Show;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $fillable = ['name', 'province_id'];

    public function province() {
    return $this->belongsTo(Province::class);
}

public function shows() {
    return $this->hasMany(Show::class); 
}
}
