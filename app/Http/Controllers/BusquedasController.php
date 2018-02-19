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
use Redirect;

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

    public function buscarTareap(Request $request)
    {
    		return Redirect::to('buscartarea/tarea/'.$request->tareaId.'/email/'.$request->correo.'/seq/'.$request->token);
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
