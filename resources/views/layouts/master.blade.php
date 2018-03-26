<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
	{{ Html::style('css/jquery.treetable.css') }}
  {{ Html::style('css/bootstrap.min.css') }}
  <style>
    .panel-danger>.panel-heading {
          color: #ffffff;
          background-color: #EF3729;
    }
  </style> 

  </head>

  <body>
    <nav class="navbar navbar-default">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="{{ url('/').'/home' }}">Ing. SVA</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="buscartarea/tarea/0/email/0/seq/0">Buscar OT</a></li>
            <li><a href="informacion">Información tiempos estimados</a></li>
          </ul>
        </div>
      </div>
    </nav>


	<div class="container">
	    @yield('content')
	</div>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    {{ Html::script('js/jquery.min.js') }}
	{{ Html::script('js/bootstrap.min.js') }}
	
	@yield('scripts_extras')
	</body>
</html>

