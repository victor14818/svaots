<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Charts;
use App\Tarea;
use App\Encuesta;
use App\Lib\EasyRedmineConn;
use Log;
use DB;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $redmineConnectionAPI = new EasyRedmineConn();
        $tareasOnLocal = Tarea::whereNotNull('numeroTarea')->where('validado',true)->where('cerrado',false)->get();
        $listaTareas = array();
        foreach($tareasOnLocal as $tarea)
        {
    	    if(!self::seHaLLenadoEncuesta($tarea->numeroTarea))
    	    {
                $tareaObj = $redmineConnectionAPI->getTarea($tarea->numeroTarea);
        		if(!is_null($tareaObj))
        		{
                    if( Auth::user()->hasRole('admin') )
                    {
                        if($tareaObj->status != "Rejected")
                        {
                            array_push($listaTareas,$tareaObj);
                        }            
                    }
                    else
                    {
                        if(Auth::user()->redmineId == $tareaObj->assignedToId)
                        {
                            if($tareaObj->status != "Rejected")
                            {
                                array_push($listaTareas,$tareaObj);
                            }
                        }   
                    }
                }
                else 
                {
                    Log::info('El número de tarea '.$tarea->numeroTarea. ' no se encuentra en el sistema');
                }
            }
        }

        $chartOpenTask = Charts::create('pie', 'fusioncharts')
        ->title('Open Tasks')
        ->colors(['#2196F3', '#F44336', '#FFC107','#266011','#E2A900','#1E8989'])
        ->dimensions(0,200);

        $listaTask = array();
        foreach($listaTareas as $tarea)
        {
            if (array_key_exists(''.$tarea->status, $listaTask)) {
                $listaTask["".$tarea->status] += 1;
            }else
            {
                $listaTask["".$tarea->status] = 1;
            }
        }

        $chartOpenTask->labels(array_keys($listaTask));
        $chartOpenTask->values(array_values($listaTask));  

        //Tareas agrupadas por fecha
        $chartDateTask = Charts::create('line', 'fusioncharts')
        ->title('Open Tasks by Date')
        ->dimensions(0,200);

        $listaTask = array();
        foreach($listaTareas as $tarea)
        {
            if (array_key_exists(''.$tarea->startDate, $listaTask)) {
                $listaTask["".$tarea->startDate] += 1;
            }else
            {
                $listaTask["".$tarea->startDate] = 1;
            }
        }
        ksort($listaTask);
        $chartDateTask->labels(array_keys($listaTask));
        $chartDateTask->values(array_values($listaTask));  

        //Encuestas por calificación
        $chartSurveyGrade = Charts::database(Encuesta::where('token','')->get(), 'bar', 'highcharts')
        ->title('¿Cómo calificaría su satisfacción con el servicio?')
        ->elementLabel("Total")
        ->dimensions(0, 200)
        ->responsive(false)
        ->groupBy('calificacion');

        $chartSurveyTime = Charts::database(Encuesta::where('token','')->get(), 'pie', 'highcharts')
        ->title('¿La OT fue solventada en el tiempo establecido?')
        ->elementLabel("Total")
        ->dimensions(0, 200)
        ->responsive(false)
        ->groupBy('descTiempoDeEntrega');

        $chartSurveyExec = Charts::database(Encuesta::select(DB::raw("case when cumplimiento > 0 then 'sí' else 'no' end as cumplimiento"))->where('token','')->get(), 'donut', 'highcharts')
        ->title('¿Se ha cumplido con el requerimiento inicial?')
        ->elementLabel("Total")
        ->dimensions(0, 200)
        ->responsive(false)
        ->groupBy('cumplimiento');

        $listaEncuesta = Encuesta::whereNotNull('observaciones')->orderBy('updated_at','DESC')->take(5)->get();

        return view('aplicacionGestion.home', ['listaTareas' => $listaTareas,'chartOpenTask' => $chartOpenTask, 'chartDateTask' => $chartDateTask, 'esAdmin' => Auth::user()->hasRole('admin'), 'chartSurveyGrade' => $chartSurveyGrade, 'chartSurveyTime' => $chartSurveyTime, 'chartSurveyExec' => $chartSurveyExec, 'listaEncuesta' => $listaEncuesta]);
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
