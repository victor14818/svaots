<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Mail;
use App\Mail\verificacion;
use App\Mail\confirmacion;
use App\Mail\aviso;
use App\Tarea;
use App\Adjunto;
use Redirect;
use App\Lib\EasyRedmineConn;
use Exception;
use Validator;
use Illuminate\Support\Facades\Input;
use GuzzleHttp\Client;

class TareasController extends Controller
{

    /*
    * Funciones Ingresar Tarea
    */

    public function showFormularioGenerico(Request $request)
    {
		$proyectoId = $request->proyectoId;
		$proyectoNombre = $request->proyectoNombre;
		$proyectoAutor = $request->proyectoAutor;
		$proyectoTiempoEstimado = $request->proyectoTiempoEstimado;

		//Se verifica la existencia física archivos en la carpeta del proyectos.
		$tieneFormularios=true;
		$rutaProyectoArchivos = 'formulariosProyectos/'.$request->proyectoId;
		$archivosDirectorioProyecto = Storage::files($rutaProyectoArchivos);
		if (count($archivosDirectorioProyecto) == 0) 
		{
			$tieneFormularios = false;
		}
		return view('aplicacionOTS.formularioGenerico',['proyectoId' => $proyectoId, 'proyectoNombre' => $proyectoNombre, 'proyectoAutor' => $proyectoAutor, 'proyectoTiempoEstimado' => $proyectoTiempoEstimado, 'tieneFormularios' => $tieneFormularios, 'proyectoDescripcion'  => $request->proyectoDescripcion]);
    }

    public function ingresoTarea(Request $request)
    {
		/*
		* Ingreso de Tarea en BD
		*/	
		try
		{
			//validaciones

			$req = $request->all();
			$mensajes = array();
            $reglas = [
            	'clienteNombre' => 'required|max:250'
			,	'clienteEmail' => 'email|max:250'
			,	'clienteTelefono' => 'required|digits:8'
			,	'tareaAsunto' => 'required|max:250'
			,	'tareaDescripcion' => 'required|max:1000'
			,	'adjuntos' => 'required'
            ];
		
			$validator = Validator::make($req,$reglas,$mensajes);

            if ($validator->fails()) {
                return back()
                            ->withErrors($validator)
                            ->withInput(Input::except('adjuntos'));
            }

		    //Se asigna un token para la validación
		    $tokenDeConfirmacion = str_random(100);
		
		    //Se ingresar el registro de la tarea
		    $issue_id = DB::table('tareas')
			    ->insertGetId(['numeroProyecto' => $request->input("proyectoId")
			    	, 'nombreProyecto' => $request->input("proyectoNombre")
			    	, 'autorProyecto' => $request->input("proyectoAutor")
			    	, 'asunto' => $request->input("tareaAsunto")
			    	, 'tiempoEstimadoProyecto' => $request->input("proyectoTiempoEstimado")
			    	, 'descripcion' => $request->input("tareaDescripcion")
			    	, 'nombreCliente' => $request->input("clienteNombre")
			    	, 'emailCliente' => strtolower($request->input("clienteEmail"))
			    	, 'telefonoCliente' => $request->input('clienteTelefono')
			    	, 'validado' => 0
			    	, 'token' => $tokenDeConfirmacion
			    	, 'created_at' => Carbon::now()
			    	, 'updated_at' => Carbon::now()]
			    );


		    //Se guardan archivos adjuntos en el servidor local
		    if($request->hasFile('adjuntos')) 
		    { 
				foreach($request->file('adjuntos') as $adjunto)
				{
				    $file_name_storage = str_replace("?","",$adjunto->getClientOriginalName());
			   	    $relative_path = $adjunto->storeAs('tmp_files',$file_name_storage,'local');
				    //Obtener la rutha completa del archivo
				    $absolute_path = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix().$relative_path;			

				    $file = new Adjunto;
				    $file->name = $file_name_storage;
				    $file->type = Storage::mimeType("tmp_files/" . $file_name_storage);
				    $file->absolute_path = $absolute_path;
				    $file->relative_path = $relative_path;
				    $file->issue = $issue_id;
				    $file->save();					
				}		
		    }

			
		    // Se envía correo de validación
		    $data["name"] = $request->input("clienteNombre");
		    $data["email"] = $request->input("clienteEmail");
		    $data["token"] = $tokenDeConfirmacion;
		    $data["issueId"] = $issue_id;
		    $data["issueSubject"] = $request->input("tareaAsunto");
		    $data["issueDescription"] = $request->input("tareaDescripcion");
		    Mail::to($request->input("clienteEmail"))->send(new verificacion($data));

			$request->session()->flash('alert-success', 'Se ha enviado un correo a la siguiente dirección: '.$request->input("clienteEmail").'. Por favor, siga las instrucciones en el mismo para validar la tarea');
			return Redirect::to('/');
		}
		catch(\Exception $e)
		{
		    if(!empty($issue_id))
		    {
		        Tarea::find($issue_id)->delete();
		        $files = Adjunto::where('issue', $issue_id)->get();
		        self::borrar_uploads_local($files);
		    }
		    Log::info($e);
			$request->session()->flash('alert-warning', 'No se ha podido realizar la acción requerida, por favor comuníquese con el personal de ingeniería SVA (ingenieriasva@claro.com.gt)');
			return Redirect::to('/');
		}
    }


    public function confirmarCorreo($email, $confirm_token)
    {
		$msgError = 'No se ha podido realizar la acción requerdida, por favor comuníquese con el personal de ingeniería SVA (ingenieriasva@claro.com.gt)';	
		$msgIngresoTareaRedmine = "";
		$redmineConnectionAPI = new EasyRedmineConn();	
    	try
    	{
			//Recuperar tarea a confirmarCorreo
			$issues = DB::table('tareas')
						->where('emailCliente', '=', $email)
						->where('token', '=', $confirm_token)
						->get();

			if(count($issues) != 1)
			{
				Log::info('Problema recuperando la tarea a validar / Cantidad de Registros '. count($issues));
		        throw new Exception('Error: '. $msgError);
			}
			else
			{
			    $issue = $issues[0];
			    $flag_tarea = 0;
				
			    //Verificar si la confirmación está dentro del mismo día
			    $fecha_actual = Carbon::now();
		        $fecha_tarea = date_create($issue->created_at);
			    if(date_format($fecha_actual,'Y-m-d') != date_format($fecha_tarea,'Y-m-d'))
			    {		
					$msgError = 'Periodo de validación expirado. Fecha ingreso: '. date_format($fecha_tarea,'Y-m-d') .'. Fecha validación: ' . date_format($fecha_actual,'Y-m-d');
					Log::info($msgError);
		        	throw new Exception('Error: '. $msgError);				
			    }
			    else
			    {		
					// Se recuperar los archivos a adjuntar que están localmente para subirlos a Redmine.
					//El conjunto de tokens generados por cada archivo adjunto se guardan en la variable str_uploads en formato JSON.
					$files = DB::table('adjuntos')
					  ->where('issue', '=', $issue->id)
					  ->get();
					$str_uploads = "";
					$redmineConnectionAPI->ingresar_uploads_redmine($files,$str_uploads); 
					
					if(count($files) > 0 && $str_uploads == '')
					{
						Log::info('No se han podido cargar los archivo locales a Redmine');
						throw new Exception('Error: '. $msgError);
					}
					else
					{
						//Nuevo token (más corto) para búsquedas
						$token_tarea = str_random(10);

						$str_issue=$redmineConnectionAPI->createTarea(
								$issue->numeroProyecto 
							,	"5" //Tracker 7 => OT --- Tracker 5 => Task
							,	"1" //Estatus (New)
							,	"2" //Prioridad (Normal)
							,	"80" //autor usuario 80 => otsingsva
							,	$issue->autorProyecto
							,	$issue->asunto

							,	preg_replace('/[[:space:]]+/',' ',$issue->descripcion)  
							,   date("Y-m-d")
							,	date("Y-m-d", strtotime(date("Y-m-d")." + ".$issue->tiempoEstimadoProyecto." days"))
							,	"0" 
							,	"10"
							,	$str_uploads
						);
							
						if( $str_issue == Null )
						{
							Log::info('No se pudo crear la Tarea en Redmine');
							throw new Exception('Error: '. $msgError);
						}
						else
						{
						    $tarea_str_rst=json_decode($str_issue,true);
						    $msgIngresoTareaRedmine = json_encode($tarea_str_rst);
					   	    $tarea_id_rst = $tarea_str_rst['issue']['id'];
							//Enviar Correo con la confirmación y link para buscar con el nuevo token y número de tarea
							
						    $tarea = Tarea::find($issue->id);
						    $tarea->numeroTarea = $tarea_id_rst;
						    $tarea->token = $token_tarea;
						    $tarea->validado = true;
						    $tarea->save();

						    self::borrar_uploads_local($files);


						    $data["name"] = $issue->nombreCliente;
						    $data["token"] = $token_tarea;
						    $data["issueId"] = $tarea_id_rst;
						    $data["issueSubject"] = $issue->asunto;
						    $data["issueDescription"] = $issue->descripcion;
						    $data["email"] = $issue->emailCliente;
						    Mail::to($issue->emailCliente)->send(new confirmacion($data));


						    $dat["phone"] = $issue->telefonoCliente;
						    $dat["name"] = $issue->nombreCliente;
						    $dat["email"] = $issue->emailCliente;
						    $dat["issueSubject"] = $issue->asunto;
						    $dat["issueDescription"] = $issue->descripcion;
						    $dat["project"] = $issue->nombreProyecto;
						    $dat["issueId"] = $tarea_id_rst;
						    Mail::to("ingenieriasva@claro.com.gt")->send(new aviso($dat));
						    //Mail::to("victor.vela@claro.com.gt")->send(new aviso($dat));

						    return view('resultadoCorreo',['msg' => 'La tarea ha sido validada exitósamente. Se ha ingresado la OT con número de tarea '.$tarea_id_rst, 'issue_id' => $tarea_id_rst, 'flag' => 1]);
						}
					}				
			    }
			}
		}
		catch(\Exception $e)
		{
			Log::info($e . ' - ' . $msgIngresoTareaRedmine);
			return view('resultadoCorreo',['msg' => $msgError, 'issue_id'=>'', 'flag' => 0]);
		}

		
    }
	
    private function borrar_uploads_local($adjuntos)
    {
		foreach($adjuntos as $adjunto)
		{
		    Storage::delete($adjunto->relative_path);
		    $file_adjunto = Adjunto::find($adjunto->id);
		    $file_adjunto->delete();
		}		
    }

 /*   public function prueba()
    {
    	$nsoIP = '10.255.24.188';
    	$nsoPort = '8080';
    	$url = 'http://'.$nsoIP.':'.$nsoPort.'/api/running/devices?format=json';
    	$USER = 'admin';
    	$PASS = 'admin';

    	$client = new \GuzzleHttp\Client();
		$response = $client->request('GET', $url, [
                    'auth'    => [
                        	$USER
                    	,	$PASS
                    ],
                    'headers' => [
                        	'Contet-Type: application/vnd.yang.data+xml'
                    ]
                ]
            );
		$devicesResponse = json_decode($response->getBody(),true);
		$devicesListTMP  = $devicesResponse['tailf-ncs:devices']['device'];
		//print_r($devicesResponseTMP['tailf-ncs:devices']['device']);
		$devicesList = array();
		foreach ($devicesListTMP as $k=>$v){
    		$devicesList[$k]=$v['name'];
		}
    	//$url='http://'.$nsoIP.':'.$nsoPort.'/api/running/services/pcrf_plan:pcrf_plan?format=json';
		//echo $response->getStatusCode();
		//echo $response->getHeaderLine('content-type');
		//echo $response->getBody();

		return view('pruebaNSO.hef', [ 'devicesList' => $devicesList  ]);
    }

    public function pruebaPOST(Request $request)
    {
    	$dominios = array();
    	foreach(explode("\n", $request->urls) as $url)
    	{
    		array_push($dominios,$url);
    	}

    	$nsoIP = '10.255.24.188';
    	$nsoPort = '8080';
    	$url = 'http://'.$nsoIP.':'.$nsoPort.'/api/running/services';
    	$USER = 'admin';
    	$PASS = 'admin';

    	$payload = json_encode('{
    		"URL_WAM":"URL_WAM": {
    			"name": "URL_WAM"
    		,	"cliente": "test"
    		,	"device": "GGSN-GV"
    		,	"flag": "1"
    		,	"lista_dominios": '.print_r($dominios).'
    		}
    	}');
    	var_dump($payload);
/*
    	$client = new \GuzzleHttp\Client();
	   	$response = $client->post($url ,[
					'auth'  => [
                        	$USER
                    	,	$PASS
                    ],
                    'json'	=> [
                    	$payload
                    ],
                    'headers' => [
                        	'Contet-Type: application/vnd.yang.data+xml'
                    ]
				]	
			);
    	//services WAN::: client  
    	//flag
		echo $response->getStatusCode();
		echo $response->getHeaderLine('content-type');
		echo $response->getBody();

    }*/	
}
