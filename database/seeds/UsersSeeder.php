<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role;
use App\Tarea;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Role::where('name','admin')->first();
        $projectOwner = Role::where('name','owner')->first();

    	$user = new User();
    	$user->name = 'Ingeniería SVA';
    	$user->email = 'ingenieriasva@claro.com.gt';
    	$user->password = bcrypt('ingSVAClaro0105');
        $user->redmineId = '0';
        $user->save();
        $user->attachRole($admin);

    	$user = new User();
    	$user->name = 'Victor';
    	$user->email = 'victor.vela@claro.com.gt';
    	$user->password = bcrypt('1234');
        $user->redmineId = '77';
        $user->save();
        $user->attachRole($projectOwner);

        $user = new User();
        $user->name = 'Boris';
        $user->email = 'boris.enriquez@claro.com.gt';
        $user->password = bcrypt('1234');
        $user->redmineId = '47';
        $user->save();
        $user->attachRole($projectOwner);

        $user = new User();
        $user->name = 'José';
        $user->email = 'lisandro.oroxon@claro.com.gt';
        $user->password = bcrypt('1234');
        $user->redmineId = '46';
        $user->save();
        $user->attachRole($projectOwner);

        $user = new User();
        $user->name = 'Francisco';
        $user->email = 'juan.carrera@claro.com.gt';
        $user->password = bcrypt('1234');
        $user->redmineId = '48';
        $user->save();
        $user->attachRole($projectOwner);

        $user = new User();
        $user->name = 'Carlos';
        $user->email = 'carlos.reyesp@claro.com.gt';
        $user->password = bcrypt('1234');
        $user->redmineId = '55';
        $user->save();
        $user->attachRole($projectOwner);

        $user = new User();
        $user->name = 'Luis';
        $user->email = 'luis.orozco@claro.com.gt';
        $user->password = bcrypt('1234');
        $user->redmineId = '65';
        $user->save();
        $user->attachRole($projectOwner);

        $tarea = new Tarea();
        $tarea->numeroTarea = 4186;
        $tarea->numeroProyecto = 158; 
        $tarea->nombreProyecto = 'APNs VPN'; 
        $tarea->autorProyecto = 47; 
        $tarea->tiempoEstimadoProyecto = 0;
        $tarea->asunto = 'Agregar Nuevo Segmento APN delsur.claro.sv';
        $tarea->descripcion = 'Se solicita agregar nuevo segmento /25 al APN delsur.claro.sv manteniendo la configuración actual, no se debe perder ninguna configuración.';
        $tarea->nombreCliente = 'Sergio Coto';
        $tarea->emailCliente = 'sergio.coto@claro.com.sv';
        $tarea->telefonoCliente = '+50379503271';
        $tarea->validado = 1;
        $tarea->cerrado = 0;
        $tarea->token = '8oukctyABI';
        $tarea->fecha_finalizacion = null;
        $tarea->save();


         $tarea = new Tarea();
        $tarea->numeroTarea = 4190;
        $tarea->numeroProyecto = 24; 
        $tarea->nombreProyecto = 'Costo Cero/Header Enrichment'; 
        $tarea->autorProyecto = 47; 
        $tarea->tiempoEstimadoProyecto = 0;
        $tarea->asunto = 'CONFIGURACION URLS HE Y C0 H TEC SOLUTIONS';
        $tarea->descripcion = 'CONFIGURACION URLS HE Y C0 H TEC SOLUTIONS';
        $tarea->nombreCliente = 'Diana Ortiz';
        $tarea->emailCliente = 'diana.ortiz@claro.com.gt';
        $tarea->telefonoCliente = '58263201';
        $tarea->validado = 1;
        $tarea->cerrado = 0;
        $tarea->token = '88XCxS3feU';
        $tarea->fecha_finalizacion = null;
        $tarea->save();

        $tarea = new Tarea();
        $tarea->numeroTarea = 4201;
        $tarea->numeroProyecto = 85; 
        $tarea->nombreProyecto = 'Otras'; 
        $tarea->autorProyecto = 47; 
        $tarea->tiempoEstimadoProyecto = 0;
        $tarea->asunto = 'Proyecto APP Revtec';
        $tarea->descripcion = 'Se solicita permitir una IP en la regla APPS-CORP para uso de la aplicación del cliente Revtec';
        $tarea->nombreCliente = 'Javier Garcia';
        $tarea->emailCliente = 'javiere.garcia@claro.com.gt';
        $tarea->telefonoCliente = '58260789';
        $tarea->validado = 1;
        $tarea->cerrado = 0;
        $tarea->token = 'AcUM9bR9eF';
        $tarea->fecha_finalizacion = null;
        $tarea->save();

        $tarea = new Tarea();
        $tarea->numeroTarea = 4202;
        $tarea->numeroProyecto = 25; 
        $tarea->nombreProyecto = 'datos.claro'; 
        $tarea->autorProyecto = 47; 
        $tarea->tiempoEstimadoProyecto = 0;
        $tarea->asunto = 'Permitir IP en datos.claro - Cliente Inversiones la Coruña';
        $tarea->descripcion = 'Se solicita permitir la IP 190.85.232.4/32 en el APN datos.claro';
        $tarea->nombreCliente = 'Javier Garcia';
        $tarea->emailCliente = 'javiere.garcia@claro.com.gt';
        $tarea->telefonoCliente = '58260789';
        $tarea->validado = 1;
        $tarea->cerrado = 0;
        $tarea->token = '19dmqJJVZe';
        $tarea->fecha_finalizacion = null;
        $tarea->save();

        ###Closed
        $tarea = new Tarea();
        $tarea->numeroTarea = 4148;
        $tarea->numeroProyecto = 24; 
        $tarea->nombreProyecto = 'Costo Cero/Header Enrichment'; 
        $tarea->autorProyecto = 47; 
        $tarea->tiempoEstimadoProyecto = 0;
        $tarea->asunto = 'URLs HE Febrero 2';
        $tarea->descripcion = 'Favor agregar las URLS en adjunto con HE';
        $tarea->nombreCliente = 'Juan Jose Marin';
        $tarea->emailCliente = 'juan.marin@claro.com.gt';
        $tarea->telefonoCliente = '58264315';
        $tarea->validado = 1;
        $tarea->cerrado = 1;
        $tarea->token = 'GO9CB9YY0p';
        $tarea->fecha_finalizacion = null;
        $tarea->save();

        $tarea = new Tarea();
        $tarea->numeroTarea = 4187;
        $tarea->numeroProyecto = 85; 
        $tarea->nombreProyecto = 'Otras'; 
        $tarea->autorProyecto = 47; 
        $tarea->tiempoEstimadoProyecto = 0;
        $tarea->asunto = 'Proyecto APP Bantrab';
        $tarea->descripcion = 'Se solicita la carga de las IP\'s del cliente Bantrab en la Regla APPs Corp';
        $tarea->nombreCliente = 'Javier Garcia';
        $tarea->emailCliente = 'javiere.garcia@claro.com.gt';
        $tarea->telefonoCliente = '58260789';
        $tarea->validado = 1;
        $tarea->cerrado = 1;
        $tarea->token = 'zVQLe7TJVp';
        $tarea->fecha_finalizacion = null;
        $tarea->save();

        $tarea = new Tarea();
        $tarea->numeroTarea = 4188;
        $tarea->numeroProyecto = 157; 
        $tarea->nombreProyecto = 'APN Enlace'; 
        $tarea->autorProyecto = 65; 
        $tarea->tiempoEstimadoProyecto = 0;
        $tarea->asunto = 'Creacion APN setrans.claro.sv';
        $tarea->descripcion = 'Se solicita la creación de APN setrans.claro.sv el cual se entregará por la red de datos';
        $tarea->nombreCliente = 'Sergio Coto';
        $tarea->emailCliente = 'sergio.coto@claro.com.sv';
        $tarea->telefonoCliente = '+50379503271';
        $tarea->validado = 1;
        $tarea->cerrado = 1;
        $tarea->token = '3RB98SRwrs';
        $tarea->fecha_finalizacion = null;
        $tarea->save();


        $tarea = new Tarea();
        $tarea->numeroTarea = 4189;
        $tarea->numeroProyecto = 24; 
        $tarea->nombreProyecto = 'Costo Cero/Header Enrichment'; 
        $tarea->autorProyecto = 47; 
        $tarea->tiempoEstimadoProyecto = 0;
        $tarea->asunto = 'CONFIGURACION URLS INTERACEL';
        $tarea->descripcion = 'CONFIGURACION URLS INTERACEL CON HE';
        $tarea->nombreCliente = 'Diana Ortiz';
        $tarea->emailCliente = 'diana.ortiz@claro.com.gt';
        $tarea->telefonoCliente = '58263201';
        $tarea->validado = 1;
        $tarea->cerrado = 1;
        $tarea->token = '8G1IIDcjPI';
        $tarea->fecha_finalizacion = null;
        $tarea->save();

        $tarea = new Tarea();
        $tarea->numeroTarea = 4191;
        $tarea->numeroProyecto = 25; 
        $tarea->nombreProyecto = 'datos.claro'; 
        $tarea->autorProyecto = 47; 
        $tarea->tiempoEstimadoProyecto = 0;
        $tarea->asunto = 'Permitir IP en datos.claro - Cliente Transportes Maze';
        $tarea->descripcion = 'Se solicita permitir la IP 213.136.76.172/32 en el APN datos.claro.';
        $tarea->nombreCliente = 'Javier Garcia';
        $tarea->emailCliente = 'javiere.garcia@claro.com.gt';
        $tarea->telefonoCliente = '58260789';
        $tarea->validado = 1;
        $tarea->cerrado = 1;
        $tarea->token = 'gBTsMNiQct';
        $tarea->fecha_finalizacion = null;
        $tarea->save();
    }
}
