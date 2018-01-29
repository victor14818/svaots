<html>
    <head>
	<title>Encuesta</title>      
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<style>
		.panel-danger>.panel-heading {
    			color: #ffffff;
    			background-color: #EF3729;
		}
	</style> 
    </head>
    <body>
	<div class="container">
	    <center><h1>Encuesta de servicio</h1><hr></center>
            <a href="{{ url('/') }}" class="btn btn-warning">Inicio</a><hr>
	    {{ Form::open(array('id' => 'formulario_ingreso_encuesta', 'url' => 'nvencuestaingreso', 'method' => 'POST')) }}
	    <div class="panel panel-danger">
	        <div class="panel-heading">{{ $encuesta->proyecto }}</div>
	        <div class="panel-body">
		<table class="table table-striped">
			    <thead>
				<tr>
				  <th><div class="text-center">Tarea</div></th>
				  <th><div class="text-center">Asunto</div></th>
				  <th><div class="text-center">Descripción</div></th>
				</tr>
			    </thead>
			    <tbody>
				<tr>
				  <td><div class="text-center">{{ $tarea }}</div></td>
				  <td><div class="text-center">{{ $asunto }}</div></td>
				  <td><div class="text-center">{{ $descripcion }}</div></td>
				</tr>
			    </tbody>
			</table>
		<center>
		    <div class="row">
	    		<div class="col-md-12">
	  		    <h4>¿Se ha cumplido con el requerimiento inicial?</h4>
			</div>
	    		<div class="col-md-12">
	  		    {{ Form::radio('cumplimiento', '0', false, array('required')) }}
				No
	  		    {{ Form::radio('cumplimiento', '1', true, array('required')) }}
				Sí 
			</div>
		    </div><br>
		    <div class="row">
	    		<div class="col-md-12">
		    	    <h4>¿La OT fue solventada en el tiempo establecido?</h4>
			</div>
	    		<div class="col-md-12">
	  		    {{ Form::radio('tiempo', '0', false, array('required')) }}
				Atrasado 
	  		    {{ Form::radio('tiempo', '1', true, array('required')) }}
				En tiempo 
	  		    {{ Form::radio('tiempo', '2', false, array('required')) }}
				Antes 
			</div>
		    </div><br>
		    <div class="row">
	    		<div class="col-md-12">
		    	    <h4>¿Cómo calificaría su satisfacción con el servicio?</h4>
			</div>
	    		<div class="col-md-12">
			    {{ Form::label('Malo') }}
	  		    {{ Form::radio('calificacion', '1', false, array('required')) }}
				1 
	  		    {{ Form::radio('calificacion', '2', true, array('required')) }}
				2 
	  		    {{ Form::radio('calificacion', '3', false, array('required')) }}
				3 
	  		    {{ Form::radio('calificacion', '4', false, array('required')) }}
				4 
	  		    {{ Form::radio('calificacion', '5', true, array('required')) }}
				5 
			    {{ Form::label('Bueno') }}
			</div>
		    </div><br>
		    <div class="row">
	    		<div class="col-md-12">
			    <h4>Observaciones</h4>
			</div>
	    		<div class="col-md-12">
	  		    {{ Form::textarea('observaciones', '', array('class' => 'form-control', 'size' => '30x5')) }}
			</div>
		    </div>
		</center>
		</div>
	    </div>
	    {{ Form::token() }}
	    {{ Form::hidden('proyecto', $encuesta->proyecto) }}
	    {{ Form::hidden('id', $encuesta->id) }}
	    {{ Form::submit('Ingresar', array('class' => 'btn btn-danger form-control')) }}
	    {{ Form::close() }}
	</div>
    </body>
</html>
