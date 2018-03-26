<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tarea;
use App\Encuesta;
use Carbon\Carbon;
use Redirect;

class EncuestaController extends Controller
{
    /*
    * Encuesta
    */
    public function showEncuesta(Request $request, $tarea, $seq)
    {
    	try
    	{    		
			$encuesta = Encuesta::where('tarea',$tarea)->where('token',$seq)->firstOrFail();
			$tarea = Tarea::where('numeroTarea',$tarea)->where('token',$seq)->firstOrFail();
			return view('aplicacionOTS.encuesta',['encuesta' => $encuesta, 'tarea' => $tarea]);
	    
		}catch(\Exception $e)
		{
			$request->session()->flash('alert-warning', 'El recurso que busca ya no existe');
			return Redirect::to('/');	
		}
	}


    public function ingresoEncuesta(Request $request)
    {
    	try
    	{
			$encuesta = Encuesta::find($request->id);
			$encuesta->cumplimiento = $request->cumplimiento;
			$encuesta->descTiempoDeEntrega = $request->tiempo;
			$encuesta->calificacion = $request->calificacion;
			$encuesta->observaciones = $request->observaciones;
			$encuesta->token = '';
			$encuesta->save();
			$request->session()->flash('alert-success', 'Encuesta ingresada exitosamente');
			return Redirect::to('/');	
		}catch(\Exception $e)
		{
			$request->session()->flash('alert-warning', 'Ha ocurrido un problema con la encuesta. Por favor cominicarse con Ingeniería SVA (ingenieriasva@claro.com.gt)');
			return Redirect::to('/');	
		}
    }

}
