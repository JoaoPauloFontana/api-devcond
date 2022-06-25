<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitVehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_unit',
        'title',
        'color',
        'plate'
    ];

    public $timestamps = false;
    public $table = 'unitvehicles';
}
