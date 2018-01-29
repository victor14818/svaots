<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    //
    protected $fillable = ['tarea', 'proyecto', 'nombre_cntct', 'email_cntct', 'telefono_cntct', 'area_cntct', 'asunto', 'descripcion'];

}
