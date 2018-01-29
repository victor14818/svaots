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

class TareasController extends Controller
{

    /*
    * Funciones Ingresar Tarea
    */

    private $key = 'bd20a77b8aae24076246a15b6ef5333fbc58fef8';
    private $ip = '10.98.72.11';

    public function crearot(Request $request)
    {
	$project_id = $request->project_id;
	$project_name = $request->project_name;
	$project_author = $request->project_author;
	return view('formularioGenerico',['project_id' => $project_id, 'project_name' => $project_name, 'project_author' => $project_author]);
    }

    public function ingresoot(Request $request)
    {
	/*
	* Ingreso de Tarea en BD
	*/			
	try
	{
	    //Se asigna un token de confirmación
	    $confirmation_token = str_random(100);
	
	    //Se ingresar el registro de la tarea
	    //estado 2 sin validar
	    $issue_id = DB::table('tareas')->insertGetId(['proyecto' => $request->input("project_id"), 'proyecto_nombre' => $request->input("project_name"), 'proyecto_autor' => $request->input("project_author"), 'asunto' => $request->input("tarea_asunto"), 'descripcion' => preg_replace('/[[:space:]]+/',' ',$request->input("tarea_descripcion")), 'progreso' => 0, 'nombre_cntct' => $request->input("cliente_nombre"), 'email_cntct' => strtolower($request->input("cliente_email")), 'telefono_cntct' => $request->input('cliente_telefono'), 'area_cntct' => $request->input("cliente_area"), 'token_verificacion' => $confirmation_token,'estado' => 2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

	    //Ingreso de Adjuntos 
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
		    //Storage::delete('tmp_files/'.$file_name_storage);
		}		
	    }
		
	    // Se envía correo de validación
	    $data["name"] = $request->input("cliente_nombre");
	    $data["email"] = $request->input("cliente_email");
	    $data["confirmation_token"] = $confirmation_token;
	    $data["issue_id"] = $issue_id;
	    $data["issue_subject"] = $request->input("tarea_asunto");
	    Mail::to($request->input("cliente_email"))->send(new verificacion($data));

	    /*return view('resultadoCorreo',
		['msg' => 'Se ha enviado un correo a la siguiente dirección: '.$request->input("cliente_email").'. Por favor, siga las instrucciones en el mismo para validar la tarea'
		, 'issue_id' => $request->input("cliente_email"), 'flag' => 1 ]);*/
		$request->session()->flash('alert-success', 'Se ha enviado un correo a la siguiente dirección: '.$request->input("cliente_email").'. Por favor, siga las instrucciones en el mismo para validar la tarea');
		return Redirect::to('/');

	}catch(\Exception $e)
	{
	    if(!empty($issue_id))
	    {
	        Tarea::find($issue_id)->delete();
	        $files = Adjunto::where('issue', $issue_id)->get();
	        self::borrar_uploads_local($files);
	    }
	    Log::info($e);
	    /*return view('resultadoCorreo',
		['msg' => 'Link no válido, por favor comuníquese con el personal de Ingeniería SVA (ingenieriasva@claro.com.gt)'
		, 'issue_id' => '', 'flag' => 0]);*/

		$request->session()->flash('alert-warning', 'No se ha podido realizar la acción requerdida, por favor comuníquese con el personal de ingeniería SVA (ingenieriasva@claro.com.gt)');
		return Redirect::to('/');
	}
    }



    public function confirmarCorreo($email, $confirm_token)
    {
	/*
	* Recuperar tarea a confirmarCorreo
	*/
	$issues = DB::table('tareas')
				->where('email_cntct', '=', $email)
				->where('token_verificacion', '=', $confirm_token)
				->get();

	if(count($issues) == 1)
	{
	    $issue = $issues[0];
	    $flag_tarea = 0;
		
	    //Verificar si la confirmación está dentro del mismo día
	    $fecha_actual = Carbon::now();
            $fecha_tarea = date_create($issue->created_at);
	    if(date_format($fecha_actual,'Y-m-d') == date_format($fecha_tarea,'Y-m-d'))
	    {		
		//LLamar WebService Redmine para ingreso de tarea
			
		/*
		* Uploads
		*/
			
		/*
		* Recuperar los adjuntos
		*/
		$files = DB::table('adjuntos')
		  ->where('issue', '=', $issue->id)
		  ->get();
		$str_uploads = "";
		self::ingresar_uploads_redmine($files,$str_uploads); 
		/*
		* Ingresar Tarea redmine
		*/ 
		//Nuevo token
		$token_tarea = str_random(10);
		$proyecto_id = $issue->proyecto;
		$Date = date("Y-m-d");
		$estimated_hours = "10";
		$tracker_id = "5"; //Tracker 7 => OT --- Tracker 5 => Task
		$status_id = "1";  //Estatus (New)
		$priority_id = "2";//Prioridad (Normal)
		$author_id = "80"; //autor usuario 80 => otsingsva
		$assigned_to_id = $issue->proyecto_autor; 
		$subject = $issue->asunto; 
		$description = "|Cliente:".$issue->nombre_cntct.";Correo:".$issue->email_cntct.";Telefono:".$issue->telefono_cntct.";Area:".$issue->area_cntct.";Token:".$token_tarea.";Descripcion:".$issue->descripcion.";Cerrado:0|";  
		$done_ratio = "0";
		$path_uploads = "";
		$str_issue = "";
		$flag_tarea=self::createTarea($proyecto_id,$tracker_id,$status_id,$priority_id,$author_id,$assigned_to_id,$subject,$description,$Date,$done_ratio,$estimated_hours,$str_uploads,$str_issue);
			
		if( $flag_tarea == 0 ){
		    $tarea_str_rst=json_decode($str_issue,true);
	   	    $tarea_id_rst = $tarea_str_rst['issue']['id'];
		    if($tarea_id_rst != ''){
			//Enviar Correo con la confirmación y link para buscar con el nuevo token y número de tarea
			try
			{			
			    //Borrar registro de tarea
			    $tarea = Tarea::find($issue->id);
			    if(!is_null($tarea))
			    {
				$tarea->delete();
			    }
			    self::borrar_uploads_local($files);

  		    	    /*
			    * Correo de Confirmación
			    */
			    $data["name"] = $issue->nombre_cntct;
			    $data["token"] = $token_tarea;
			    $data["issue_id"] = $tarea_id_rst;
			    $data["issue_subject"] = $issue->asunto;
			    $data["correo"] = $issue->email_cntct;
			    Mail::to($issue->email_cntct)->send(new confirmacion($data));

			    /*
			    * Correo de Aviso
			    */

			    $dat["tel"] = $issue->telefono_cntct;
			    $dat["asunto"] = $issue->asunto;
			    $dat["name"] = $issue->nombre_cntct;
			    $dat["mail"] = $issue->email_cntct;
			    $dat["issue_id"] = $tarea_id_rst;
			    $dat["ot_proyecto"] = $issue->proyecto_nombre;
			    Mail::to("ingenieriasva@claro.com.gt")->send(new aviso($dat));

					
			    return view('resultadoCorreo',['msg' => 'La tarea ha sido validada exitosamente. Se ha ingresado la OT con número de tarea '.$tarea_id_rst, 'issue_id' => $tarea_id_rst, 'flag' => 1]);
			}catch(\Exception $e)
			{
			    self::borrarTarea($tarea_id_rst);
			    Log::info($e);
			    return view('resultadoCorreo',['msg' => 'No se logró realizar la acción requerida, por favor comuníquese con el personal de Ingeniería SVA (ingenieriasva@claro.com.gt)', 'issue_id' => '', 'flag' => 0]);
			}
		    }
		}
		Log::info('Problemas con el ingreso o recupero de la tarea a Redmine');
	        return view('resultadoCorreo',['msg' => 'No se logró realizar la acción requerida, por favor comuníquese con el personal de Ingeniería SVA (ingenieriasva@claro.com.gt)', 'issue_id'=>'', 'flag' => 0]);								
	    }else{
		$tarea = Tarea::find($issue->id);//update adjuntos con nueva tarea
		$tarea->delete();
		Log::info('Periodo de validación expirado, fecha ingreso tarea => '.date_format($fecha_tarea,'Y-m-d').', fecha validación => '.date_format($fecha_actual,'Y-m-d'));
		return view('resultadoCorreo',['msg' => 'El periodo de validación ha expirado por favor generar una nueva tarea ', 'issue_id' => '', 'flag' => 0 ]);				
	    }
	}
	Log::info('Problema recuperando la tarea a validar');
        return view('resultadoCorreo',['msg' => 'No se logró realizar la acción requerida, por favor comuníquese con el personal de Ingeniería SVA (ingenieriasva@claro.com.gt)', 'issue_id' => '', 'flag' => 0]);
    }
	
    private function createTarea($proyecto_id,$tracker_id,$status_id, $priority_id,$author_id,$assigned_to_id,$subject,$description,$Date,$done_ratio,$estimated_hours,$uploads,&$str_issue)
    {
	try
	{
	    $str_issue_req='{"issue":{"project_id":"'.$proyecto_id.'","tracker_id":"'
	    .$tracker_id.'","status_id":"'.$status_id.'","priority_id":"'.$priority_id
	    .'","author_id":"'.$author_id.'","assigned_to_id":"'.$assigned_to_id
	    .'","subject":"'.$subject.'","description":"'.$description
	    .'","start_date":"'.$Date
	    .'","due_date":"'.date("Y-m-d", strtotime($Date." + 15 days"))
	    .'","done_ratio":"'.$done_ratio.'","estimated_hours":"'
	    .$estimated_hours.'","uploads":['.$uploads.']}}';

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, "http://".$this->ip."/issues.json?key=".$this->key);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch, CURLOPT_HEADER, FALSE);
	    curl_setopt($ch, CURLOPT_POST, TRUE);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $str_issue_req);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		  "Content-Type: application/json"
	    ));
	    $str_issue = curl_exec($ch);
	    curl_close($ch);
	    return 0;
	}catch(\Exception $e)
	{
	    Log::info($e);
	    return 1;
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

    private function uploadAdjunto($absolute_path)
    {
	exec('curl --data-binary "@'.$absolute_path.'" -H "Content-Type: application/octet-stream" -X POST "http://'.$this->ip.'/uploads.xml?key='.$this->key.'"', $rs_redmine_api_upload);
	return $rs_redmine_api_upload[0];
    }

    private function ingresar_uploads_redmine($adjuntos, &$str_uploads)
    {
	try
	{
	    /*
	    * Ingresar uploads redmine
	    */
	    $str_uploads = "";
	    foreach($adjuntos as $adjunto)
	    {
	        //Subir el archivo a redmine, parsear el resultado XML
	        $upload = simplexml_load_string(self::uploadAdjunto($adjunto->absolute_path));
	        if($str_uploads == ''){
		    $str_uploads .= '{"token":"'.$upload->token.'","filename":"'.$adjunto->name.'","content_type":"'.Storage::mimeType("tmp_files/" . $adjunto->name).'"}';
	        }else{
		    $str_uploads .= ',{"token":"'.$upload->token.'","filename":"'.$adjunto->name.'","content_type":"'.Storage::mimeType("tmp_files/" . $adjunto->name).'"}';
	        }
	    }		
        }catch(\Exception $e)
	{
	    Log::info($e);
	    $str_uploads="";
	}
    }

    private function borrarTarea($id)
    {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://".$this->ip."/issues/".$id.".xml?key=".$this->key);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	$response = curl_exec($ch);
	curl_close($ch);
    }

}
