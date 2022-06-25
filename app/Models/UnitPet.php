<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitPet extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_unit',
        'name',
        'race',
    ];


    public $timestamps = false;
    public $table = 'unitpets';
}
