<html>
    <body>
	<h3> Hola {{ $name }},</h3>

	@if($action == 'Close')
		<p>Aviso!! OT {{ $issueId }} <striong>cerrada</striong>.</p>
		<br>
		<p>{{ $msg }}</p>
		<br>
		<p>Por favor llene la siguiente encuesta de servicio</p>
		<a href="{{ url('/') }}/encuesta/tarea/{{ $issueId }}/seq/{{ $token }}">Encuesta tarea {{ $issueId }}</a>
		<p>Si el link no funciona porfavor copie la siguiente url: {{ url('/') }}/encuesta/tarea/{{ $issueId }}/seq/{{ $token }} y péguela en el navegador
	@else
		<p>Aviso!! OT {{ $issueId }} <striong>rechazada</striong>.</p>
		<br>
		<p>{{ $msg }}</p>
	@endif
	<br>
	Saludos Cordiales<br>
	Ingeniería SVA Regional<br>
	ingenieriasva@claro.com.gt</br>
	<hr>
	{{ url('/') }}
    </body>
</html>
