<html>
    <head>
	<title>Ingreso OT</title>      
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<style>
		.panel-danger>.panel-heading {
    			color: #ffffff;
    			background-color: #EF3729;
		}
	</style> 
    </head>
    <body>
	<div class="container">
	    <center><h1>{{ $project_name }}</h1><hr></center>
            <a href="{{ url('/') }}" class="btn btn-warning">Inicio</a><hr>
	    <div id="div_alert_correcto" class="alert alert-success alert-dismissable" style="display:none">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		Tarea ingresada correctamente. Número de tarea => <strong id="alerta_correcto"></strong>
	    </div>

	    <div id="div_alert_error" class="alert alert-warning alert-dismissable" style="display:none">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		No se logró ingresar la tarea, por favor comuniquese con el personal de Ingeniería SVA (ingenieriasva@claro.com.gt)
	    </div>
	    {{ Form::open(array('id' => 'formulario_ingreso', 'url' => 'nvotingreso', 'method' => 'POST','enctype'=>'multipart/form-data')) }}
	    <div class="panel panel-danger">
	        <div class="panel-heading">Datos de Contacto</div>
	        <div class="panel-body">
		    <div class="row">
	    		<div class="col-md-3">
	  		    {{ Form::label('Nombre') }}
			</div>
	    		<div class="col-md-6">
	  		    {{ Form::text('cliente_nombre', '', array('class' => 'form-control', 'required', 'id' => 'idCltNombre')) }}
			</div>
		    </div>
		    <div class="row">
	    		<div class="col-md-3">
		    	    {{ Form::label('E-mail') }} 
			</div>
	    		<div class="col-md-6">
	  		    {{ Form::email('cliente_email', '', array('class' => 'form-control', 'required')) }}
			</div>
		    </div>
		    <div class="row">
	    		<div class="col-md-3">
			    {{ Form::label('Teléfono') }} 
			</div>
	    		<div class="col-md-6">
	  		    {{ Form::text('cliente_telefono', '', array('class' => 'form-control', 'required')) }}
			</div>
		    </div>
		    <div class="row">
	    		<div class="col-md-3">
		     	    {{ Form::label('Área') }} 
			</div>
	    		<div class="col-md-6">
	  		    {{ Form::text('cliente_area', '', array('class' => 'form-control', 'required')) }}
			</div>
		    </div>
		</div>
	    </div>

	<!-- Datos OT-->
	    <div class="panel panel-danger">
	        <div class="panel-heading">Datos Tarea</div>
	        <div class="panel-body">
		    <div class="row">
	    		<div class="col-md-3">
	  		    {{ Form::label('Asunto') }}
			</div>
	    		<div class="col-md-6">
	  		    {{ Form::text('tarea_asunto', '', array('class' => 'form-control', 'required')) }}
			</div>
		    </div>
		    <div class="row">
	    		<div class="col-md-3">
			    {{ Form::label('Descripción') }} 
			</div>
	    		<div class="col-md-6">
	  		    {{ Form::textarea('tarea_descripcion', '', array('class' => 'form-control', 'required', 'rows' => '5')) }}
			</div>
		    </div>
		    <div class="row">
	    		<div class="col-md-3">
			    {{ Form::label('Archivos') }} 
			</div>
	    		<div class="col-md-6">
	  		    {{ Form::file('adjuntos[]', array('multiple')) }}
			</div>
		    </div>
		</div>
	    </div>
	    {{ Form::token() }}
	    {{ Form::hidden('project_id', $project_id) }}
	    {{ Form::hidden('project_name', $project_name) }}
	    {{ Form::hidden('project_author', $project_author) }}
	    {{ Form::submit('Ingresar', array('class' => 'btn btn-danger form-control')) }}
	    {{ Form::close() }}
	</div>
    </body>
</html>
