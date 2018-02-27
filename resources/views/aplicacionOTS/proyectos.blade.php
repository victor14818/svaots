@extends('layouts.master')

@section('content')
<style>
	tr:hover {
	    color:white;
	    background-color: #EF3729;
	}
</style>

	<!-- Main component for a primary marketing message or call to action -->
	<!-- <div class="jumbotron" style="background-image: url('img/pp1.png'); background-size: 100% 100%;"> -->
	<div class="jumbotron" style="background-color: #EF3729; padding-top:15px; padding-buttom:15px; position:relative;">
		<div class="container" style="width: 125px; position: absolute; right: 10px;">
			<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 193.5 69.9"  enable-background="0 0 193.5 69.9">
				<g>
					<path fill="#FFFFFF" d="M144.4,31.2c-5.2,0-9.7,1.9-13.4,5.7c-3.8,3.8-5.6,8.4-5.6,13.7c0,5.3,1.8,9.9,5.6,13.6
						c3.7,3.8,8.2,5.7,13.4,5.7c5.2,0,9.8-1.9,13.6-5.7c3.7-3.7,5.6-8.3,5.6-13.6c0-5.3-1.9-9.9-5.6-13.7
						C154.2,33.1,149.7,31.2,144.4,31.2z M151.6,57.7c-1.9,2-4.3,3-7.1,3c-2.7,0-5.1-1-7.1-3c-1.9-2-3-4.4-3-7.2c0-2.9,1.1-5.3,3-7.2
						c1.9-2,4.3-3,7.1-2.9c2.8-0.1,5.2,0.9,7.2,2.9c1.9,1.9,2.8,4.3,2.9,7.2C154.5,53.3,153.5,55.7,151.6,57.7z"/>
					<path fill="#FFFFFF" d="M23.6,30.8c2.8,0,5.4,0.8,7.9,2.4c2.2,1.5,4,3.6,5.2,6.1h9.6c-1.4-5.1-4.1-9.4-8.3-12.6
						c-4.2-3.4-9.1-5-14.4-5C17,21.7,11.6,24,7,28.7C2.3,33.4,0,39,0,45.6c0,6.5,2.3,12.3,7,17c4.6,4.6,10.1,6.9,16.7,6.9
						c5.3,0,10.2-1.6,14.4-4.9c4.1-3.4,6.9-7.5,8.3-12.6h-9.6c-1.2,2.5-3,4.5-5.2,6.1c-2.5,1.5-5,2.3-7.9,2.3c-4.1,0-7.5-1.5-10.3-4.3
						c-2.9-2.9-4.3-6.3-4.2-10.4c-0.1-4.1,1.3-7.5,4.2-10.4C16.1,32.3,19.6,30.8,23.6,30.8z"/>
					<path fill="#FFFFFF" d="M92.6,33.5c-3.2-1.7-6.7-2.5-10.7-2.4c-6.2-0.1-10.5,1.6-13,4.9c-1.6,2-2.4,4.7-2.6,8h9.2
						c0.2-1.6,0.7-2.7,1.3-3.4c1-1.2,2.6-1.9,4.9-1.8c1.9-0.1,3.4,0.3,4.5,0.9c1.1,0.5,1.5,1.6,1.6,3.1c0,1.2-0.7,2.2-2.1,2.8l-7.1,1.1
						c-3.3,0.4-5.9,1.2-7.9,2.4c-3.4,2-5.1,5.3-5,10c-0.1,3.3,1,5.9,3.1,7.9c1.9,1.6,4.4,2.5,7.5,2.6c5-0.1,9-1.3,11.8-3.8v3.8h9.2v-27
						C97.2,38.2,95.6,35.1,92.6,33.5z M87.5,54.6c0,3.2-0.9,5.4-2.6,6.6c-1.8,1.2-3.6,1.9-5.7,1.9c-1.3,0-2.4-0.4-3.3-1.1
						c-0.9-0.8-1.3-1.9-1.3-3.6c0-1.9,0.7-3.2,2.2-4.2c0.9-0.5,2.4-1,4.3-1.2l4.6-1.2l1.8-1V54.6z"/>
					<rect x="51" y="21.7" fill="#FFFFFF" width="9.2" height="47.9"/>
					<path fill="#FFFFFF" d="M122.1,31.5l-3.5,1c-2.5,1-4.7,2.7-6.5,5.2V33l-8.7-0.1v36.8h9.1V52.1c0-3,0.4-5.1,1.2-6.5
						c0.6-1.4,1.8-2.6,3.2-3.5c1.6-1.1,3.3-1.6,5-1.6l2.8,0.2l-0.1-9.4L122.1,31.5z"/>
					<polygon fill="#FFFFFF" points="181.5,4.6 159.4,26.8 165.2,32.6 187.3,10.4 	"/>
					<rect x="141.4" y="0" fill="#FFFFFF" width="8.2" height="23.8"/>
					<rect x="169.2" y="41.8" fill="#FFFFFF" width="24.3" height="8.2"/>
				</g>
			</svg>
		</div>
		<center><h1 style="color: #ffffff">Ingeniería SVA</h1></center>
	</div>

	<div class="flash-message">
	    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
	      @if(Session::has('alert-' . $msg))

	      <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
	      @endif
	    @endforeach
  	</div>

    <div>
	    <center><h3>Proyectos disponibles</h3><hr></center>
		<br>
		<!-- <table class="table table-striped"> -->
		<div class="listaProyectos">
			<table class="table">
				<thead>
					<th>Proyecto</th>
					<th>Descripción</th>
					<th></th>
				</thead>
				<tbody>
				@foreach($listaProyectos as $proyecto)
					<tr>
						<td class="elegible" style="font-family:'Courier New', Courier, monospace; font-style: italic;"> {{ $proyecto->name }}</td>
						<td class="elegible" style="font-family:'Courier New', Courier, monospace; font-style: italic;"> {{ $proyecto->description }}</td>
						<td>
							{{ Form::open([ 'url' => 'nuevatarea', 'id' => trim(strtolower($proyecto->name)), 'method' => 'GET' ]) }}
								{{ Form::token() }}
								{{ Form::hidden('proyectoId',$proyecto->id) }}
								{{ Form::hidden('proyectoNombre',$proyecto->name) }}
								{{ Form::hidden('proyectoAutor',$proyecto->author) }}
								{{ Form::hidden('proyectoDescripcion',$proyecto->description) }}
								{{ Form::hidden('proyectoTiempoEstimado',$proyecto->tiempoEstimado) }}
								{{ Form::submit('Nueva OT',[ 'class' => 'btn btn-danger' ]) }}
							{{ Form::close() }}
							@if(isset($proyecto->archivoFormularioGenerico))
								<a href='{{ url("/") }}/descargararchivoproyecto/{{ $proyecto->archivoFormularioGenerico }}' class='btn btn-info'>Descargar</a>
							@endif
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
    </div>

 @stop

@section('scripts_extras')
<script>
	/*$("div.listaProyectos > table > tbody > tr > td.elegible").click(function() {
        var row = $(this).text();
  		alert('You clicked ' + row + ' ' + row.toLowerCase().trim());
    });*/
</script>
@stop
