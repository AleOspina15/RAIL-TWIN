<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;

    protected $table = 'sch_aicedronesdi.project';

    protected $fillable = [
        'id',
        'id_usuario',
        'nombre',
        'inicio',
        'fin',
        'estado',
        'geom',
        'workspace'
    ];

}
