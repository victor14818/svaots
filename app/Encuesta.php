<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Encuesta extends Model
{
    //
    protected $fillable = ['satisfaccion','satisfaccion_tiempo','calificacion','observaciones','tarea','proyecto','token'];
}
