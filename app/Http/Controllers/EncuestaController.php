<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tarea;
use App\Encuesta;
use Carbon\Carbon;
use Redirect;

class EncuestaController extends Controller
{

    private $key = 'bd20a77b8aae24076246a15b6ef5333fbc58fef8';
    private $ip = '10.98.72.11';

    /*
    * Encuesta
    */

    public function ingreso_encuesta(Request $request)
    {
    	try
    	{
			$encuesta = Encuesta::find($request->id);
			$encuesta->satisfaccion = $request->cumplimiento;
			$encuesta->satisfaccion_tiempo = $request->tiempo;
			$encuesta->observaciones = $request->observaciones;
			$encuesta->calificacion = $request->calificacion;
			$encuesta->token = '';
			$encuesta->save();
			//return view('resultadoCorreo',['msg' => 'Encuesta ingresada exitosamente', 'issue_id' => '', 'flag' => 1 ]);
			$request->session()->flash('alert-success', 'Encuesta ingresada exitosamente');
			return Redirect::to('/');	
		}catch(\Exception $e)
		{
			$request->session()->flash('alert-warning', 'Ha ocurrido un problema con la encuesta. Por favor cominicarse con Ingeniería SVA (ingenieriasva@claro.com.gt)');
			return Redirect::to('/');	
		}
    }

    public function showencuesta(Request $request, $tarea, $seq)
    {
    	try
    	{    		
			$encuesta = Encuesta::where('tarea',$tarea)->where('token',$seq)->firstOrFail();
			
			$str_request = "http://".$this->ip."/issues/".$tarea.".xml?key=".$this->key."&include=journals";
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
			$issue_subject = (string)$issue->subject;

			$description_valores_tmp = explode("|",(string)$issue->description);
			if(count($description_valores_tmp) == 3){
			    $description_valores = explode(";",$description_valores_tmp[1]);
		    	    if(count($description_valores) == 7){
				$nombre_clnt = explode(":",$description_valores[0])[1];
				$correo_clnt = explode(":",$description_valores[1])[1];
				$token_clnt = explode(":",$description_valores[4])[1];
				$description = explode(":",$description_valores[5])[1];
			    }
			}
			return view('encuesta',['encuesta' => $encuesta, 'tarea' => $tarea, 'asunto' => $issue_subject, 'descripcion' => $description]);
	    
		}catch(\Exception $e)
		{
			$request->session()->flash('alert-warning', 'Ha ocurrido un problema con la encuesta. Por favor cominicarse con Ingeniería SVA (ingenieriasva@claro.com.gt)');
			return Redirect::to('/');	
		}
	}
}
