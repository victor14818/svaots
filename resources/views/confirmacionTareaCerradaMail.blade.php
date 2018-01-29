<html>
    <body>
	<h2> Confirmación Ingreso de Orden de Trabajo</h2>
	<hr>
	<h3> Hola {{ $name }},</h3>
	<p>Se ha cerrado la OT {{ $issue_id }} exitósamente. Por favor llene la siguiente encuesta de servicio</p>
	<a href="{{ url('/') }}/encuesta/tarea/{{ $issue_id }}/seq/{{ $token }}">Tarea {{ $issue_id }}</a>

	<p>Si el link no funciona porfavor copie la siguiente url: {{ url('/') }}/encuesta/tarea/{{ $issue_id }}/seq/{{ $token }} y péguela en el navegador

	<br>
	Saludos Cordiales<br>
	Ingeniería SVA Regional<br>
	ingenieriasva@claro.com.gt</br>
	<hr>
	{{ url('/') }}
    </body>
</html>
