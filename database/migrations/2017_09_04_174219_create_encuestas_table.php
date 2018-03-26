<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEncuestasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encuestas', function (Blueprint $table) {
            $table->increments('id');
    	    $table->boolean('cumplimiento')->nullable();
    	    $table->string('descTiempoDeEntrega')->nullable();
    	    $table->integer('calificacion')->nullable();
    	    $table->text('observaciones')->nullable();
    	    $table->string('tarea')->nullable();
    	    $table->string('proyecto')->nullable();
    	    $table->string('token')->nullable();
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
        Schema::dropIfExists('encuestas');
    }
}
