<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTareasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tareas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tarea')->nullable();
            $table->integer('proyecto')->nullable();
            $table->string('proyecto_nombre')->nullable();
            $table->string('proyecto_autor')->nullable();
            $table->string('asunto')->nullable();
            $table->string('descripcion')->nullable();
	    $table->integer('progreso')->nullable();
	    $table->string('nombre_cntct')->nullable();
            $table->string('email_cntct')->nullable();
            $table->string('telefono_cntct')->nullable();
            $table->string('area_cntct')->nullable();
            $table->integer('estado')->nullable();
            $table->string('token_verificacion')->nullable();
	    $table->date('fecha_finalizacion')->nullable();
	    $table->string('status')->nullable();
	    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tareas');
    }
}
