<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Adjunto;
use App\Tarea;
use App\Encuesta;
use Redirect;
use App\Services\PayUService\Exception;
use Illuminate\Support\Facades\Storage;
use App\Lib\Proyecto;   
use App\Lib\EasyRedmineConn;
use App\Proyecto as modelProyecto;
use Route;
use Zip;
use Auth;
use Log;
use Mail;
use App\Mail\confirmacionCerrarTarea;

class FormsTareasController extends Controller
{
    public function show()
    {
        $redmineConnectionAPI = new EasyRedmineConn();
        $tareasOnLocal = Tarea::whereNotNull('numeroTarea')->where('validado',true)->get();
        $listaTareas = array();
        foreach($tareasOnLocal as $tarea)
        {
	    if(!self::seHaLLenadoEncuesta($tarea->numeroTarea))
	    {
                $tareaObj = $redmineConnectionAPI->getTarea($tarea->numeroTarea);
                if($tareaObj->status != "Closed" && $tareaObj->status != "Rejected" && Auth::user()->redmineId == $tareaObj->assignedToId)
                {
                    array_push($listaTareas,$tareaObj);
                }
	    }
        }
    	return view('aplicacionGestion.formsTareas',['listaTareas' => $listaTareas]);
    }

    public function closereject(Request $request)
    {

        $fieldStatus = array();
        try{
            switch($request->tipoAccionTarea)
            {
                case "Close":
                    array_push($fieldStatus, '<status_id>5</status_id>');
                    break;
                case "Reject":
                    array_push($fieldStatus, '<status_id>6</status_id>');
                    break;
            }
            $redmineConnectionAPI = new EasyRedmineConn();
            $respuesta = $redmineConnectionAPI->modificarTarea($request->tareaId,$fieldStatus);
            if(!is_null($respuesta))
            {
                $tarea = Tarea::where('numeroTarea',$request->tareaId)->firstOrFail();
                $data["name"] = $tarea->nombreCliente;
                $data["token"] = $tarea->token;
                $data["issueId"] = $request->tareaId;
                $data["action"] = $request->tipoAccionTarea;
                $data["message"] = $request->messageText;
                Mail::to($tarea->emailCliente)->send(new confirmacionCerrarTarea($data));

                if($request->tipoAccionTarea == "Close")
                {
                    $encuesta = Encuesta::where('tarea',$request->tareaId)->first();
                    if(!isset($encuesta))
                    {
                        $encuesta = new Encuesta;
                    }
                    $encuesta->tarea = $request->tareaId;
                    $encuesta->proyecto = $request->projectName;
                    $encuesta->token = $tarea->token;
                    $encuesta->save();
                }
                //indica que ya se ha cerrado la tarea por lo que no la buscará de nuevo
                $tarea->cerrado = true;
                $tarea->save();
                
                return Redirect::back()->withErrors(['Se completó la acción']);
            }
            else
            {
                return Redirect::back()->withErrors(['No se pudo completar la acción']);                
            }
            
        }catch(\Exception $e){
            Log::info($e);
        }
    }


    private function seHaLLenadoEncuesta($tareaId)
    {
        $encuesta = Encuesta::where('tarea',$tareaId)->first();
        if(isset($encuesta))
        {
            if($encuesta->token == '')
            {
                return true;
            }
        }
        return false;
    }


}
