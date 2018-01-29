<html>
    <body>
	<h2> Confirmación Ingreso de Orden de Trabajo</h2>
	<hr>
	<h3> Hola {{ $name }},</h3>
	<p>Se ha ingresado la OT exitósamente, atenderemos su requerimiento y podrá ver el progreso de la misma por el siguiente link :</p>
	<a href="{{ url('/') }}/buscar_OT/tarea/{{ $issue_id }}/email/{{ $correo }}/seq/{{ $confirmation_token }}">Tarea {{ $issue_id }}</a>

	<p>Si el link no funciona porfavor copie la siguiente url: {{ url('/') }}/buscar_OT/tarea/{{ $issue_id }}/email/{{ $correo }}/seq/{{ $confirmation_token }} y péguela en el navegador

	<p><strong>Nota:</strong> El código <strong>{{ $confirmation_token }}</strong> le permitirá ver su tarea</p>

	Saludos Cordiales<br>
	Ingeniería SVA Regional<br>
	ingenieriasva@claro.com.gt</br>
	<hr>
	{{ url('/') }}
    </body>
</html>
