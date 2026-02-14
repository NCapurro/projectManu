<?php

namespace App\Models;

use App\Models\City;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Show extends Model
{
    protected $fillable = [
        'titulo', 'lugar', 'direccion', 'city_id',
        'fecha_hora', 'flyer_path', 'ticket_url', 
        'esta_publicado', 'sold_out'
    ];

    // Para que Laravel maneje la fecha automáticamente como un objeto Carbon
    protected $casts = [
        'fecha_hora' => 'datetime',
        'esta_publicado' => 'boolean',
        'sold_out' => 'boolean',
    ];

    public function city() {
    return $this->belongsTo(City::class);
}

}
