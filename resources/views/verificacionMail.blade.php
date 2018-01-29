<html>
    <body>
	<h2> Validación Ingreso de Orden de Trabajo</h2>
	<hr>
	<h3> Hola {{ $name }},</h3>
	<p>Ha ingresado una OT al área de Ingeniería SVA, validar el requerimiento por medio del siguiente link:</p>
	<a href="{{ url('/') }}/nvotconfirmacion/email/{{ $email }}/seq/{{ $confirmation_token }}">Confirmar Orden de Trabajo</a>

	<p>Si el link no funciona porfavor copie la siguiente url: {{ url('/') }}/nvotconfirmacion/email/{{ $email }}/seq/{{ $confirmation_token }} y péguela en el navegador

	<p><strong>Nota:</strong> Si usted no ha ingresado este requerimiento en el portal de OT's Ingeniería SVA haga caso omiso de éste correo</p>

	Saludos Cordiales<br>
	Ingeniería SVA Regional<br>
	ingenieriasva@claro.com.gt</br>
	<hr>
	{{ url('/') }}
    </body>
</html>
