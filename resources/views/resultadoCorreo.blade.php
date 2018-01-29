<html>
    <head>
	<title>Resultado</title>      
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	{{ Html::style('css/bootstrap.min.css') }}
	{{ Html::script('js/jquery.min.js') }}
	{{ Html::script('js/bootstrap.min.js') }}
    </head>
    <body>
	<div class="container">
	    <center><h1>Resultado</h1><hr></center>
            <a href="/" class="btn btn-warning">Inicio</a><hr>

	    @if($flag == 1)
	    <div id="div_alert_correcto" class="alert alert-success alert-dismissable">
		{{ $msg }}
	    </div>
	    @else
	    <div id="div_alert_error" class="alert alert-warning alert-dismissable">
		{{ $msg }}
	    </div>
	    @endif
	</div>
    </body>
</html>
