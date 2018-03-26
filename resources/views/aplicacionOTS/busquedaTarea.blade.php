@extends('layouts.master')

@section('content')

<style>
	.panel-danger>.panel-heading {
			color: #ffffff;
			background-color: #EF3729;
	}
</style> 
<center><h1>Detalle Tarea</h1><hr></center>
<a href="{{ url('/') }}" class="btn btn-warning">Inicio</a><hr>
<div class="panel panel-danger">
	<div class="panel-heading">Datos de la tarea</div>
	<center>
		<div class="panel-body">
			{{ Form::open([ 'url' => 'buscartarea/form' ]) }}
			<div class="row">
				<div class="col-md-6">{{ Form::label('Número de tarea') }}</div>
				<div class="col-md-6">{{ Form::text('tareaId', '', array('class' => 'form-control', 'required', 'id' => 'tareaId')) }}</div>
			</div>
			<div class="row">
				<div class="col-md-6">{{ Form::label('Código') }}</div>
				<div class="col-md-6">{{ Form::text('token', '', array('class' => 'form-control', 'id' => 'token')) }}</div>
			</div>
			<div class="row">
				<div class="col-md-6">{{ Form::label('Correo') }}</div>
				<div class="col-md-6">{{ Form::text('correo', '', array('class' => 'form-control', 'id' => 'correo')) }}</div>
			</div>
			<br>
			<div class="row">
				<div class="col-md-12">
					<center>
						{{ Form::submit('Buscar', [ 'class' => 'btn btn-danger']) }}
					</center>
				</div>
			</div>
			{{ Form::close() }}
		</div>
	</center>
</div>

<div class="panel panel-danger">
	<div class="panel-heading">Detalle de la tarea</div>
	<div class="panel-body">
		@if($tarea != Null)
			<table class="table">
				<tr>
					<td><strong>Número de tarea:</strong></td>
					<td>{{ $tarea->numeroTarea }}<td>
				</tr>
				<tr>
					<td><strong>Proyecto:</strong></td>
					<td>{{ $tarea->nombreProyecto }}</td>
				</tr>
				<tr>
					<td><strong>Asunto:</strong></td>
					<td>{{ $tarea->asunto }}</td>
				</tr>
				<tr>
					<td><strong>Descripción:</strong></td>
					<td>{{ $tarea->descripcion }}</td>
				</tr>
				<tr>
					<td><strong>Estado:</strong></td>
					<td>{{ $datosTareaRedmine->estado }}</td>
				</tr>
				<!-- <tr>
					<td><strong>Progreso</strong></td>
					<td>
						<div class="progress">
							<div class="progress-bar" role="progressbar" aria-valuenow="{{  $datosTareaRedmine->progreso }}" aria-valuemin="0" aria-valuemax="100" style="width:{{ $datosTareaRedmine->progreso }}%">
								{{ $datosTareaRedmine->progreso }}%
							</div>
						</div>
					</td>
				</tr> -->
				<tr>
					<td><strong>Fecha de inicio:</strong></td>
					<td>{{ $datosTareaRedmine->fechaIngreso }}</td>
				</tr>
				<tr>
					<td><strong>Fecha de finalización:</strong></td>
					<td>{{ $datosTareaRedmine->fechaEstimada }}</td>
				</tr>
				<tr>
					<td><strong>Responsable</strong></td>
					<td>{{ $datosTareaRedmine->nombreResponsable }}</td>
				</tr>
				<tr>
					<td><strong>Días activo:</strong></td>
					<td>
						@if($datosTareaRedmine->estado == 'Closed' || $datosTareaRedmine->estado == 'Rejected')
							{{ ceil( (strtotime($datosTareaRedmine->fechaEstimada) - strtotime($datosTareaRedmine->fechaIngreso)) /86400) }}
						@else
							{{ ceil( (time() - strtotime($datosTareaRedmine->fechaIngreso)) / 86400) }}
						@endif
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div id="id_adjuntos">
							@foreach($listaArchivosAdjuntos as $adjunto)
								<form method='POST' action=' {{ url("/") }}/download'>
									{{ csrf_field() }}
									<input type='hidden' value='{{ $adjunto->filename }}' name='fileName'>
									<input type='hidden' value='{{ $adjunto->content_url }}' name='fileUrl'>
									<input type='hidden' value='{{ $adjunto->content_type }}' name='fileContentType'>
									<input class='btn btn-info' type='submit' value='{{ $adjunto->filename }}'>
								</form>		
								<br>
							@endforeach
						</div>
					</td>
				</tr>
			</table>
			<h3>Notas</h3>
			<div id="id_journals">
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>Autor</th>
							<th>Nota</th>
							<th>Fecha</th>
						</tr>
					</thead>
						@foreach($listaNotas as $nota)
							@if(!empty($nota->notes))
							@php
								$dt = \Carbon\Carbon::parse($nota->created_on)->timezone('UTC');
								$toDay = $dt->format('d');
								$toMonth = $dt->format('m');
								$toYear = $dt->format('Y');
								$dateUTC = \Carbon\Carbon::createFromDate($toYear, $toMonth, $toDay, 'UTC');
								$datePST = \Carbon\Carbon::createFromDate($toYear, $toMonth, $toDay, 'America/Guatemala');
								$difference = $dateUTC->diffInHours($datePST);
								$date = $dt->addHours($difference);
							@endphp
								<tr>
									<td>{{ $nota->user['name'] }}</td>
									<td>{{ $nota->notes }}</td>
									<td>{{ $date }}</td>
								</tr>
							@endif
						@endforeach
					<tbody>
			</div>
		@else
			<div id="div_alert_error" class="alert alert-warning alert-dismissable">
				<center>
					No hay datos para mostrar<br>
					Ingeniería SVA (ingenieriasva@claro.com.gt)
				</center>
			</div>
		@endif
	</div>
	</div>
</div>

@stop
