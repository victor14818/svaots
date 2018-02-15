@extends('layouts.master')

@section('content')
<center>
		<h1>Información</h1>
		<hr>
	</center>
	<a href="{{ url('/') }}" class="btn btn-warning">Inicio</a>
	<hr>
	
	<div class="listaProyectos">
		<table class="table table-striped">
			<thead>
				<th>Proyecto</th>
				<th>Tiempo estimado (días)</th>
			</thead>
			<tbody>
			@foreach($listaProyectos as $proyecto)
				<tr>
					<td>{{ $proyecto->name }}</td>
					<td>{{ $proyecto->tiempoEstimado }}</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
	  	
@stop

	
    