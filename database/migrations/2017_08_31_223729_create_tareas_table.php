><?php

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
            $table->integer('numeroTarea')->unique()->nullable();
            $table->integer('numeroProyecto'); 
            $table->string('nombreProyecto',255); 
            $table->integer('autorProyecto'); 
            $table->integer('tiempoEstimadoProyecto');
            $table->string('asunto',255);
            $table->text('descripcion');
            $table->string('nombreCliente',255);
            $table->string('emailCliente',255);
            $table->string('telefonoCliente',255);
            $table->boolean('validado');
            $table->boolean('cerrado')->default(false);
            $table->string('token',255);
            $table->date('fecha_finalizacion')->nullable();
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
