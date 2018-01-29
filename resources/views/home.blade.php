<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ingeniería SVA</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
	
	{{ Html::script('js/jquery.min.js') }}
	
      <script>
	var estado = '';
	function getIssue(id,codigo,correo){
	  $("#id_adjuntos").html(''); 
	  if(id > 0){
	    var tr_id = id;
	    $.ajax({
 	      type:'POST',
	      url:'{{ url('/') }}/buscar_tarea',
	      data: {_token:"<?php echo csrf_token() ?>", tarea_id: tr_id, cod:codigo,cor:correo},
	      success:function(data){
		estado = data.issue_status;
		$("#id_subject").html(data.issue_subject);
		$("#id_description").html(data.issue_description);
		$("#id_status").html(data.issue_status);
		$("#id_done_ratio").html('<div class="progress"><div class="progress-bar" role="progressbar" aria-valuenow="'+data.issue_done_ratio+'" aria-valuemin="0" aria-valuemax="100" style="width:'+data.issue_done_ratio+'%">'+data.issue_done_ratio+'%</div></div>');
		$("#id_start_date").html(data.issue_start_date);
		$("#id_due_date").html(data.issue_due_date);
		$("#issue_assigned").html(data.issue_assigned);
		$("#id_journals").html(data.journals);
		$("#id_no").html(id);
		document.getElementById("id_no").value = id;
		$("#id_project").html(data.issue_project_name);
		document.getElementById("id_project").value = data.issue_project_name;
		$("#id_dias").html(data.tiempo_activo);
		$("#id_adjuntos").html(data.down_links);
	      },
	      error:function(request, status, error){
		estado = '';
		$("#id_subject").html('');
		$("#id_description").html('');
		$("#id_status").html('');
		$("#id_done_ratio").html('<div class="progress"><div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">0%</div></div>');
		$("#id_start_date").html('');
		$("#id_due_date").html('');
		$("#issue_assigned").html('');
		$("#id_journals").html('');
		$("#id_no").html('');
		document.getElementById("id_no").value = 0;
		$("#id_project").html('');
		document.getElementById("id_project").value = '';
		$("#id_dias").html('');
		$("#id_adjuntos").html('');
		alert("Tarea no encontrada comunicarse con Ingeniería SVA(ingenieriasva@claro.com.gt) ");
		document.getElementById("div_alert_error").style.display = "block";
	      }
	    });
  	  }
	}
	function searchIssue(event){
	  var a = document.getElementById("tarea_id").value;
	  var b = document.getElementById("code_id").value;
	  var c = document.getElementById("correo_id").value;
  	  getIssue(a,b,c);
	}
	function closeIssue(event){
	  if(estado == "Closed"){
	   var tr_id = document.getElementById("tarea_id").value;
	    $.ajax({
 	      type:'POST',
	      url:'{{ url('/') }}/cerrar_tarea',
	      data: {_token:"<?php echo csrf_token() ?>", tarea_id: tr_id},
	      success:function(data){
	    	document.getElementById("msgCloseIssueId").innerHTML= "<div id=\"div_alert_error\" "
								+"class=\"alert alert-success alert-dismissable\"> "
								+"<a href=\"#\" class=\"close\" data-dismiss=\"alert\" "
								+"aria-label=\"close\">&times;</a>"
								+"La tarea ha sido cerrada " + data.correo + " " + data.cerrado
		    						+"</div>";
		
	      },
	      error:function(request, status, error){
	    	document.getElementById("msgCloseIssueId").innerHTML= "<div id=\"div_alert_error\" "
								+"class=\"alert alert-warning alert-dismissable\"> "
								+"<a href=\"#\" class=\"close\" data-dismiss=\"alert\" "
								+"aria-label=\"close\">&times;</a>"
								+"No se pudo cerrar la tarea "
		    						+"</div>";		
	      }
	    });
  	  }else{
	    document.getElementById("msgCloseIssueId").innerHTML= "<div id=\"div_alert_error\" "
								+"class=\"alert alert-warning alert-dismissable\"> "
								+"<a href=\"#\" class=\"close\" data-dismiss=\"alert\" "
								+"aria-label=\"close\">&times;</a>"
								+"La tarea debe estar cerrada"
		    						+"</div>";
	  }
	}
      </script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        Ingeniería SVA
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <!-- <li><a href="{{ route('register') }}">Register</a></li> -->
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
      <div class="container">
	<center><h1>Ingeniería SVA</h1><hr></center>
	<a href="/" class="btn btn-warning">Inicio</a><hr>
	<div class="panel panel-danger">
	  <div class="panel-heading">Datos de la tarea</div>
	  <center>
	  <div class="panel-body">
	    <div class="row">
	      <div class="col-md-6">{{ Form::label('Número de tarea') }}</div>
	      <div class="col-md-6">{{ Form::text('tarea_name', '', array('class' => 'form-control', 'required', 'id' => 'tarea_id')) }}</div>
	    </div>
	    <div class="row">
	      <div class="col-md-6">{{ Form::label('Código') }}</div>
	      <div class="col-md-6">{{ Form::text('codigo_name', '', array('class' => 'form-control', 'id' => 'code_id')) }}</div>
	    </div>
 	    <div class="row">
	      <div class="col-md-6">{{ Form::label('Correo') }}</div>
	      <div class="col-md-6">{{ Form::text('correo_name', '', array('class' => 'form-control', 'id' => 'correo_id')) }}</div>
	    </div>
	    <br>
 	    <div class="row">
    	      <div class="col-md-12">{{ Form::button('Buscar',['onClick'=>'searchIssue(event)', 'class' => 'btn btn-danger form-control']) }}</div>
	    </div>
		<br>
 	    <div class="row">
    	      <div class="col-md-12">{{ Form::button('Cerrar',['onClick'=>'closeIssue(event)', 'class' => 'btn btn-warning form-control']) }}</div>
	    </div>
	  </div>
	  <br>
	  <div id="msgCloseIssueId">
	  </div>
	  </center>
	</div>

	    <!-- Detalle -->
	    <div class="panel panel-danger">
	        <div class="panel-heading">Detalle de la tarea</div>
	        <div class="panel-body">
		    <div id="div_alert_error" class="alert alert-warning alert-dismissable" style="display:none">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			Tarea no encontrada, por favor comuníquese con el personal de Ingeniería SVA (ingenieriasva@claro.com.gt)
		    </div>
		    <table class="table">
		    <tr><td><strong>Número de OT:</strong></td><td><label id="id_no" value=""></label><td></tr>
		    <tr><td><strong>Proyecto:</strong></td><td><label id="id_project" value=""></label></td></tr>
		    <tr><td><strong>Asunto:</strong></td><td><label id="id_subject"></label></td></tr>
		    <tr><td><strong>Descripción:</strong></td><td><label id="id_description"></label></td></tr>
		    <tr><td><strong>Estado:</strong></td><td><label id="id_status"></label></td></tr>
		    <tr><td><strong>Progreso</strong></td><td><div id="id_done_ratio"></div></td></tr>
		    <tr><td><strong>Fecha de inicio:</strong></td><td><label id="id_start_date"></label></td></tr>
		    <tr><td><strong>Fecha de finalización:</strong></td><td><label id="id_due_date"></label></td></tr>
		    <tr><td><strong>Responsable</strong></td><td><label id="issue_assigned"></label></td></tr>
		    <tr><td><strong>Días activo:</strong></td><td><label id="id_dias"></label></td></tr>
		    <tr><td colspan="2"><div id="id_adjuntos"></div></td></tr>
			</table>
		    <h3>Notas</h3>
		    <div id="id_journals"></div>
		</div>
	    </div>
	</div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
