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
	    	$tarea = Tarea::where('numeroTarea', $tareaId)->where('emailCliente', $email)->where('token', $confirm_token)->where('validado',true)->firstOrFail();
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
        return view('aplicacionOTS.busquedaTarea', ['tarea' => $tarea, 'datosTareaRedmine' => $datosTareaRedmine, 'listaNotas' => $listaNotas, 'listaArchivosAdjuntos' => $listaArchivosAdjuntos]);    		
    }

    public function buscarTareap(Request $request)
    {
    		return Redirect::to('buscartarea/tarea/'.$request->tareaId.'/email/'.$request->correo.'/seq/'.$request->token);
    }

}
