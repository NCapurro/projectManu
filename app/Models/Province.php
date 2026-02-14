<?php

namespace App\Models;

use App\Models\Country;
use App\Models\City;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Province extends Model
{

    protected $fillable = ['name', 'country_id'];
    
    public function country() {
    return $this->belongsTo(Country::class);
}
    public function cities() {
    return $this->hasMany(City::class);
}
}
