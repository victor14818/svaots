<html>
  <head>
      <title>Buscar OT</title>      
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
	{{ Html::style('css/bootstrap.min.css') }}
	{{ Html::script('js/jquery.min.js') }}
	{{ Html::script('js/bootstrap.min.js') }}
  </head>
  <body>
      <div class="container">
	<center><h1>Información</h1><hr></center>
	<a href="{{ url('/') }}" class="btn btn-warning">Inicio</a><hr>
	<div class="panel panel-danger">
	  <div class="panel-heading">Datos de la tarea</div>
	  <center>
	  <div class="panel-body">
	    {!! $var !!}
	  </div>
	  </center>
	</div>
    </body>
</html>
