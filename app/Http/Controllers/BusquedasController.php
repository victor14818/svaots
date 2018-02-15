<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Encuesta;
use Illuminate\Support\Facades\DB;
use Mail;
use App\Mail\confirmacionCerrarTarea;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Tarea;
use App\Lib\EasyRedmineConn;
use Exception;

class BusquedasController extends Controller
{
    private $key = 'bd20a77b8aae24076246a15b6ef5333fbc58fef8';
    private $ip = '10.98.72.11';

    /*
     * Funciones Buscar Tarea
     */

    public function buscarTarea($tareaId, $email, $confirm_token)
    {
    	$tarea = null;
    	$issue = null;
    	$date = null;
    	$datosTareaRedmine = null;
    	$listaNotas = array();
    	$listaArchivosAdjuntos = array();
    	$redmineConnectionAPI = new EasyRedmineConn();	
    	try
    	{
	    	$tarea = Tarea::where('numeroTarea', $tareaId)->first();
	    	$respuesta = $redmineConnectionAPI->buscarTarea($tareaId);
	    	if($respuesta == Null)
	    	{
	    		throw new Exception('Error: No existe la tarea en redmine');
	    	}
	    	$issue=simplexml_load_string($respuesta);

	    	$datosTareaRedmine = (object) [
	    			'estado' => (string)$issue->status['name']
	    		,	'progreso' => (string)$issue->done_ratio
	    		,	'fechaIngreso' => (string)$issue->start_date
	    		,	'fechaEstimada' => (string)$issue->due_date
	    		,	'nombreResponsable' => (string)$issue->assigned_to['name']
	    	];

	    	$listaNotas = $issue->journals->journal;
	    	$listaArchivosAdjuntos = $issue->attachments->attachment;

    	}catch(\Exception $e)
    	{
    		Log::info($e);
    	}
        return view('aplicacionOTS.busquedaTarea', ['tarea' => $tarea, 'datosTareaRedmine' => $datosTareaRedmine, 'listaNotas' => $listaNotas, 'listaArchivosAdjuntos' => $listaArchivosAdjuntos, 'key' => $redmineConnectionAPI->getKey()]);    		
    }

    public function buscar_tarea(Request $request)
    {
    	$redmineConnectionAPI = new EasyRedmineConn();	
		try{
		    $respuesta = $redmineConnectionAPI->buscarTarea();
		    $issue=simplexml_load_string($respuesta);
		    
		    /*
		    * Validación Token y correo guardados en la descripción
		    */
		    $description_valores_tmp = explode("|",(string)$issue->description);
		    if(count($description_valores_tmp) == 3){
			$description_valores = explode(";",$description_valores_tmp[1]);
	    		if(count($description_valores) == 7){
			    $nombre_clnt = explode(":",$description_valores[0])[1];
			    $correo_clnt = explode(":",$description_valores[1])[1];
			    $token_clnt = explode(":",$description_valores[4])[1];
			    $description = explode(":",$description_valores[5])[1];
			    if($correo_clnt != strtolower($request->cor) || $token_clnt != $request->cod)
			    { 
				throw new \Exception("Código o correo incorrecto"); 
			    }
		    	}else{ throw new \Exception("Mal formato de la descripción en redmine");}
		    }else{ throw new \Exception("Mal formato de la descripción en redmine");}

		    $status = (string)$issue->status['name'];
		    $assigned_to_id = $issue->assigned_to['id'];
		    $assigned_to_name = (string)$issue->assigned_to['name'];
		    $project_name = strtolower((string)$issue->project['name']);
		    $subject = (string)$issue->subject;
		    //$start_date_sp = explode(" ",(string)$tareas[0]->created_at);
		    $start_date = (string)$issue->start_date;
		    $due_date = (string)$issue->due_date;
		    $done_ratio = (string)$issue->done_ratio;

		    //Cálculo de tiempo activo
		    $tiempo_activo = 0;
		    if($status == "Closed" || $status == "Rejected")
		    {
			if($due_date != "")
			{ 
			    $tiempo_activo =ceil( (strtotime($issue->due_date) - strtotime($issue->start_date)) /86400);
			    $tiempo_activo = "<div id=\"div_alert_error\" class=\"alert alert-danger\">".$tiempo_activo."</div>";
			 }
			    $tiempo_activo =ceil( (strtotime($issue->due_date) - strtotime($issue->start_date)) /86400);
			    $tiempo_activo = "<div id=\"div_alert_error\" class=\"alert alert-info\">".$tiempo_activo."</div>";
		    }else
		    {
			$tiempo_activo = ceil( (time() - strtotime($issue->start_date)) / 86400);
			if($tiempo_activo > 0 && $tiempo_activo < 15)
			{
			    $tiempo_activo = "<div id=\"div_alert_error\" class=\"alert alert-success\">".$tiempo_activo."</div>";
			}
			elseif($days >= 15 && $days < 25)
			{
			    $tiempo_activo = "<div id=\"div_alert_error\" class=\"alert alert-warning\">".$tiempo_activo."</div>";
			}
			else
			{
			    $tiempo_activo = "<div id=\"div_alert_error\" class=\"alert alert-danger\">".$tiempo_activo."</div>";
			}
		    }

		    //Notas
		    $str_journals = "";
		    $journals = $issue->journals;
	            $str_journals = "<table class=\"table table-striped table-bordered\"><thead><tr><th>Autor</th><th>Nota</th><th>Fecha</th></tr></thead><tbody>";
		    foreach($journals->journal as $journal)
		    {
			$dt = Carbon::parse($journal->created_on)->timezone('UTC');
			$toDay = $dt->format('d');
			$toMonth = $dt->format('m');
			$toYear = $dt->format('Y');
			$dateUTC = Carbon::createFromDate($toYear, $toMonth, $toDay, 'UTC');
			$datePST = Carbon::createFromDate($toYear, $toMonth, $toDay, 'America/Guatemala');
			$difference = $dateUTC->diffInHours($datePST);
			$date = $dt->addHours($difference);
			if(!empty($journal->notes))
			{
			    $str_journals .= "<tr><td>".$journal->user['name']."</td><td>".$journal->notes."</td><td>".$date."</td></tr>";
			}
		    }
		    $str_journals .= "</tbody></table>";

		    //Generar forms de descarga llamada POST con parametros de link de descarga en Redmine, 
		    //Dentro del post recuperar el archivo, guardarlo temporalemente y luego realizar un response con el archivo que incluya eliminación.

		    $str_rst_links = "";
		    $attachments = $issue->attachments;
		    foreach($attachments->attachment as $attachment)
		    {
			$str_rst_links .= "<form method='POST' action='".url('/')."/download'>".csrf_field()."<input type='hidden' value='".$attachment->filename."' name='fileName'><input type='hidden' value='".$attachment->content_url."?key=".$this->key."' name='fileUrl'><input type='hidden' value='".$attachment->content_type."' name='fileContentType'><input class='btn btn-info' type='submit' value='".$attachment->filename."'></form>";
		    }

	    	return response()->json(array('issue_subject' => $subject, 'issue_description' => $description, 'issue_status' => $status, 'issue_done_ratio' => $done_ratio, 'issue_start_date' => $start_date, 'issue_due_date' => $due_date, 'issue_assigned' => $assigned_to_name, 'issue_project_name' => $project_name, 'tiempo_activo' => $tiempo_activo, 'journals' => $str_journals, 'nombre_clnt' => $nombre_clnt, 'down_links' => $str_rst_links), 200);
	
		}catch(\Exception $e){
		    Log::info($e);
		    App::abort();
		}
    }

    public function cerrar_tarea(Request $request)
    {
	try{
	    $str_request = "http://".$this->ip."/issues/".$request->tarea_id.".xml?key=".$this->key."&include=journals";
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $str_request);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch, CURLOPT_HEADER, FALSE);

	    $respuesta = curl_exec($ch);
	    if ($respuesta == false)
	    {
		$respuesta = curl_error($ch);
	    }	
	    curl_close($ch);
	    $issue=simplexml_load_string($respuesta);
	    
	    /*
	    * Validación Token y correo guardados en la descripción
	    */
	    $description_valores_tmp = explode("|",(string)$issue->description);
	    if(count($description_valores_tmp) == 3){
		$description_valores = explode(";",$description_valores_tmp[1]);
    		if(count($description_valores) == 7){
		    $nombre_clnt = explode(":",$description_valores[0])[1];
		    $correo_clnt = explode(":",$description_valores[1])[1];
		    $telefono_clnt = explode(":",$description_valores[2])[1];
		    $area_clnt = explode(":",$description_valores[3])[1];
		    $token_clnt = explode(":",$description_valores[4])[1];
		    $description = explode(":",$description_valores[5])[1];
		    $cerrado = explode(":",$description_valores[6])[1];
	    	}else{ throw new \Exception("mal formato de la descripción en redmine");}
	    }else{ throw new \Exception("mal formato de la descripción en redmine");}

	    if($cerrado == 0){
	    /*
	    * Actualizar parámetro cerrado
	    */
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, "http://".$this->ip."/issues/".$request->tarea_id.".xml?key=".$this->key);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch, CURLOPT_HEADER, FALSE);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	    curl_setopt($ch, CURLOPT_POSTFIELDS, "<issue>
	  	<description>|Cliente:".$nombre_clnt.";Correo:".$correo_clnt.";Telefono:".$telefono_clnt.";Area:".$area_clnt.";Token:".$token_clnt.";Descripcion:".$description.";Cerrado:1|</description>
		</issue>");

	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	  	"Content-Type: application/xml"
	    ));

	    $response = curl_exec($ch);
	    curl_close($ch);
	    }else{ throw new \Exception("Ya ha sido cerrada la tarea");}

	    /*
	    * Enviar Correo de aviso
	    */
	    $data["name"] = $nombre_clnt;
	    $data["token"] = $token_clnt;
	    $data["issue_id"] = $request->tarea_id;
	    Mail::to($correo_clnt)->send(new confirmacionCerrarTarea($data));

	    $encuesta = new Encuesta;
   	    $encuesta->tarea = $issue->id;
	    $encuesta->proyecto = $issue->project["name"];
	    $encuesta->token = $token_clnt;
	    $encuesta->save();
	    
	    return response()->json(array('correo' => '', 'cerrado' => ''), 200);
	}catch(\Exception $e){
	    Log::info($e);
	    App::abort(403);
	}
    }

}
