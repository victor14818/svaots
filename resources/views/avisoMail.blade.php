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
	<h2> Aviso de ingreso de OT ( proyecto <strong> {{ $project }} ) </strong></h2>
	<hr>
	<p>Se ha ingresado la OT número {{ $issueId }}</p>

	<strong>Datos solicitante</strong>
	<br>
	<br>
	<table>
	  <tr>
	    <th>Nombre</th>
	    <th>Teléfono</th>
	    <th>Correo</th>
	  </tr>
	  <tr>
	    <td>{{ $name }}</td>
	    <td>{{ $phone }}</td>
	    <td>{{ $email }}</td>
	  </tr>
	</table>
	<br>

	<p><strong>Asunto:</strong>  {{ $subject }}</p>
	<p><strong>Descripción:</strong>  {{ $description }}</p>
    </body>
</html>
