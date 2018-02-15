<html>
	<style>
		table {
		    border-collapse: collapse;
		    width: 100%;
		}

		th, td {
		    text-align: left;
		    padding: 8px;
		}

		tr:nth-child(even){background-color: #f2f2f2}

		th {
		    background-color: #EF3729;
		    color: white;
		}
	</style>
    <body>
	<h2> Confirmación Ingreso de Orden de Trabajo</h2>
	<hr>
	<h3> Hola {{ $name }},</h3>
	<p>Se ha ingresado la OT exitósamente, atenderemos su requerimiento y podrá ver el progreso de la misma por el siguiente link :</p>
	<a href="{{ url('/') }}/buscartarea/tarea/{{ $issueId }}/email/{{ $email }}/seq/{{ $token }}">Tarea {{ $issueId }}</a>

	<br>
	<br>
	<br>
	<table>
	  <tr>
	    <th>Asunto</th>
	    <td>{{ $subject }}</td>
	  </tr>
	  <tr>
	    <th>Descripción</th>
	    <td>{{ $description }}</td>
	  </tr>
	</table>
	<br>

	<p>Si el link no funciona porfavor copie la siguiente url: {{ url('/') }}/buscartarea/tarea/{{ $issueId }}/email/{{ $email }}/seq/{{ $token }} y péguela en el navegador

	<p><strong>Datos tarea</strong></p>
	
	<br>
	<table>
	  <tr>
	    <th>Número de tarea</th>
	    <td>{{ $issueId }}</td>
	  </tr>
	  <tr>
	    <th>Código</th>
	    <td>{{ $token }}</td>
	  </tr>
	  <tr>
	    <th>Correo</th>
	    <td>{{ $email }}</td>
	  </tr>
	</table>
	<br>

	Saludos Cordiales<br>
	Ingeniería SVA Regional<br>
	ingenieriasva@claro.com.gt<br>
	<hr>
	{{ url('/') }}
    </body>
</html>
