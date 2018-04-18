@extends('layouts.master')

@section('content')
	    <center><h1>{{ $proyectoNombre }}</h1><hr></center>
	    @if($tieneFormularios)
	        <a href="{{ url('/').'/projects/'.$proyectoId.'/downloadAttachments' }}" class="btn btn-success pull-right">
	        	<span class="glyphicon glyphicon-download-alt"></span> Descargar Formularios
	        </a>
        @endif
        <a href="{{ url('/') }}" class="btn btn-warning">Inicio</a><hr>
        @if(isset($proyectoDescripcion))
        <center>
        	<div class="alert alert-info">
   				<strong>Información!</strong> {{ $proyectoDescripcion }}
   			</div>
   		<center>
   		@endif
   		<hr>     	
	    <div id="div_alert_correcto" class="alert alert-success alert-dismissable" style="display:none">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		Tarea ingresada correctamente. Número de tarea => <strong id="alerta_correcto"></strong>
	    </div>

	    <div id="div_alert_error" class="alert alert-warning alert-dismissable" style="display:none">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		No se logró ingresar la tarea, por favor comuníquese con el personal de Ingeniería SVA (ingenieriasva@claro.com.gt)
	    </div>

	    {{ Form::open(array('url' => 'tarea/new', 'method' => 'POST', 'enctype'=>'multipart/form-data')) }}
	    <div class="panel panel-danger">
	        <div class="panel-heading">Datos de Contacto</div>
	        <div class="panel-body">
		    <div class="row">
	    		<div class="col-md-3">
	  		    {{ Form::label('Nombre') }}
			</div>
	    		<div class="col-md-6">
	    			<div class="form-group {{ $errors->has('clienteNombre') ? 'has-error' : '' }}">
	  		    		{{ Form::text('clienteNombre', '', array('class' => 'form-control', 'required', 'max' => '250')) }}
	  		    		<span class="text-danger">{{ $errors->first('clienteNombre') }}</span>
                	</div>
				</div>
		    </div>
		    <div class="row">
	    		<div class="col-md-3">
		    	    {{ Form::label('E-mail') }} 
			</div>
	    		<div class="col-md-6">
	    			<div class="form-group {{ $errors->has('clienteEmail') ? 'has-error' : '' }}">
			  		    {{ Form::email('clienteEmail', '', array('class' => 'form-control', 'required', 'max' => '250')) }}
	  		    		<span class="text-danger">{{ $errors->first('clienteEmail') }}</span>
                	</div>
			</div>
		    </div>
		    <div class="row">
	    		<div class="col-md-3">
			    {{ Form::label('Teléfono') }} 
				</div>
	    		<div class="col-md-6">
	    			<div class="form-group {{ $errors->has('clienteTelefono') ? 'has-error' : '' }}">
	  		    		{{ Form::text('clienteTelefono', '', array('class' => 'form-control', 'required', 'max' => '250')) }}
	  		    	<span class="text-danger">{{ $errors->first('clienteTelefono') }}</span>
            		</div>
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
		    			<div class="form-group {{ $errors->has('tareaAsunto') ? 'has-error' : '' }}">
				  		    {{ Form::text('tareaAsunto', '', array('class' => 'form-control', 'required', 'max' => '250')) }}
		  		    	<span class="text-danger">{{ $errors->first('tareaAsunto') }}</span>
	            		</div>
					</div>
			    </div>
			    <div class="row">
		    		<div class="col-md-3">
				    {{ Form::label('Descripción') }} 
					</div>
		    		<div class="col-md-6">
		    			<div class="form-group {{ $errors->has('tareaDescripcion') ? 'has-error' : '' }}">
		  		    		{{ Form::textarea('tareaDescripcion', '', array('class' => 'form-control', 'required', 'rows' => '5')) }}
		  		    	<span class="text-danger">{{ $errors->first('tareaDescripcion') }}</span>
		            	</div>
					</div>
			    </div>
			    <div class="row">
		    		<div class="col-md-3">
				    {{ Form::label('Archivos') }} 
					</div>
					<div class="form-group {{ $errors->has('adjuntos') ? 'has-error' : '' }}" id="addNewAttachment">
                        <div class="row col-md-8 col-md-offset-2">
                            <div id="divAttachments">
                                <input type="file" name="adjuntos[]" class="form-control" onchange="ValidateSize()" />
                            </div>
                            <a href="#addNewAttachment" onclick="addField();">
                                Agregue otro archivo
                            </a>
			  		    	<span class="text-danger">{{ $errors->first('adjuntos') }}</span>
                        </div>
                    </div>
			    </div>
			</div>
	    </div>
	    {{ Form::token() }}
	    {{ Form::hidden('proyectoId', $proyectoId) }}
	    {{ Form::hidden('proyectoNombre', $proyectoNombre) }}
	    {{ Form::hidden('proyectoAutor', $proyectoAutor) }}
	    {{ Form::hidden('proyectoTiempoEstimado', $proyectoTiempoEstimado) }}
	    @php
	    	$listaUsuariosInformados = json_decode($listaProyectoUsuariosInformados,true);
	    @endphp
	    @if( !is_null($listaUsuariosInformados))
	    	@foreach( $listaUsuariosInformados as $informedUsers)
	    		 <input name="proyectoUsuariosInformados[]" type="hidden" value="{{ $informedUsers }}">
	    	@endforeach
	    @endif
	    
	    <div class="row">
		    <div class="pull-right">
		    	{{ Form::submit('Ingresar', array('class' => 'btn btn-danger')) }}
		    </div>
		</div>
		<br>

	    {{ Form::close() }}
<script>
	function addField(){
	  $('form input:file').last().after($('<input type="file" name="adjuntos[]" class="form-control" onchange="ValidateSize()"/>'));
	}

	function ValidateSize() {
		var TotalSize = 0;
		var attachments = document.getElementsByName('adjuntos[]');
		for( var j = 0; j < attachments.length; j++)
		{
			var files = attachments[j].files;
			for (var i = 0; i < files.length; i++)
			{
				TotalSize += files[i].size;
				if( (files[i].size / 1024 / 1024) > 2 )
				{
					alert('El archivo ' + files[i].name + ' excede el tamaño permitido por archivo de 2 MB');
					attachments[j].value = "";
				}
			}
		}

		if( (TotalSize / 1024 / 1024) > 5 )
		{
			alert('El tamaño total de los arhivos no puede exceder 5 MB');
			if(attachments.length > 0)
			{
				attachments[attachments.length-1].value = "";
			}
		}
    }
</script>
 @stop
