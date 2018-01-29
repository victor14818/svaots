<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Lib\Proyecto;
use App\Tarea;
use App\Encuesta;
use App\Adjunto;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Mail;
use App\Mail\verificacion;
use App\Mail\confirmacion;
use App\Mail\aviso;
use App\Mail\confirmacionCerrarTarea;

class porta_ots extends Controller
{

    /*
     * Funciones Listar Proyectos
     */
    private $xml_proyectos;
    private $key = 'bd20a77b8aae24076246a15b6ef5333fbc58fef8';
    private $ip = '10.98.72.11';
    public function lst_proyectos()
    {
        $respueta=self::curl_p(0,100);
	$this->xml_proyectos=simplexml_load_string($respueta);
	$proyectos=$this->xml_proyectos;
	$cmp_total=$proyectos['total_count'];

	$cmp=100;
	while($cmp < $cmp_total)
	{
		$respuesta=self::curl_p($cmp,100);
		$proyectos_tmp=simplexml_load_string($respuesta);
		self::append_simplexml($proyectos,$proyectos_tmp);
		$cmp+=100;
	}
	#$raiz = new Proyecto('7','OTs','Proyecto para revisión de OTs del Packet Core','0');
	$raiz = new Proyecto('221','OTs Internas','OTs de Clientes internos','0');
	self::get_proyectos_hijos($raiz->proyectos,$raiz->id);
	$var = self::ttl_proyectos_fs($raiz,0);
	return view('paginaInicial',['rpt' => $var]);
    }  

    private function append_simplexml(&$simplexml_to, &$simplexml_from)
    {
	foreach ($simplexml_from->children() as $simplexml_child)
	{
	    $simplexml_temp = $simplexml_to->addChild($simplexml_child->getName(), (string) $simplexml_child);
	    foreach ($simplexml_child->attributes() as $attr_key => $attr_value)
	    {
		$simplexml_temp->addAttribute($attr_key, $attr_value);
	    }

	    self::append_simplexml($simplexml_temp, $simplexml_child);
	}
    } 

    private function get_proyectos_hijos(&$arreglo_padre,$id_padre)
    {
	$proyectos=$this->xml_proyectos;
	foreach($proyectos->project as $project)
	{
	    if($project->parent['id'] == $id_padre)
	    {
		$proyecto_hijo=new Proyecto($project->id,$project->name,$project->description,$project->author["id"]);
		self::get_proyectos_hijos($proyecto_hijo->proyectos,"".$proyecto_hijo->id);
		array_push($arreglo_padre,$proyecto_hijo);
	    }
	}
    }

    private function ttl_proyectos_fs($current,$p)
    {
	$var = "";
	if(!empty($current->proyectos))
	{
	    if($p == '0'){ $var .= "<tr data-tt-id=\"".$current->id."\">";}
	    else{ $var .= "<tr data-tt-id=\"".$current->id."\" data-tt-parent-id=\"".$p."\">"; }
	    $var .= "<td>".$current->name."</td>";
	    $var .= "<td>".$current->description."</td>";
	    $var .= "</tr>";
	    foreach($current->proyectos as $project)
	    {
		$var .= self::ttl_proyectos_fs($project,$current->id);
	    }
	}else
	{
	    if($p == '0'){ $var .= "<tr data-tt-id=\"".$current->id."\">";}
	    else{ $var .= "<tr data-tt-id=\"".$current->id."\" data-tt-parent-id=\"".$p."\">"; }
	    $var .= "<td>".$current->name."</td>";
	    $var .= "<td>".$current->description."</td>";
	    $var .= "<td>";
	    $var .= "<form action=\"nueva_OT\" method=\"POST\">";
	    $var .= "<input type=\"hidden\" name=\"project_id\" value=\"".$current->id."\">";
	    $var .= "<input type=\"hidden\" name=\"project_name\" value=\"".$current->name."\">";
	    $var .= "<input type=\"hidden\" name=\"project_author\" value=\"".$current->author."\">";
	    $var .= "<input type=\"submit\" value=\"Nueva OT\" class=\"btn btn-danger\"></td>";
	    $var .= "</form>";
	    $var .= "</td>";
	    $var .= "</tr>";
	}
	return $var;
    }

    private function curl_p($offset, $limit)
    {
	$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://10.98.72.11/projects.xml?offset=$offset&limit=$limit&key=".$this->key);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
	$response = curl_exec($ch);
	curl_close($ch);
	return $response;
    }

    /*
     * Funciones Ingresar Tarea
     */

    public function nueva_ot(Request $request)
    {
	$project_id = $request->project_id;
	$project_name = $request->project_name;
	$project_author = $request->project_author;
	return view('formularioGenerico',['project_id' => $project_id, 'project_name' => $project_name, 'project_author' => $project_author]);
    }

    public function ingreso_ot(Request $request)
    {
	/*
	* Ingreso de Tarea en BD
	*/			
	try
	{
	    $confirmation_token = str_random(100);
	    $issue_id = DB::table('tareas')->insertGetId(['proyecto' => $request->input("project_id"), 'proyecto_nombre' => $request->input("project_name"), 'proyecto_autor' => $request->input("project_author"), 'asunto' => $request->input("tarea_asunto"), 'descripcion' => preg_replace('/[[:space:]]+/',' ',$request->input("tarea_descripcion")), 'progreso' => 0, 'nombre_cntct' => $request->input("cliente_nombre"), 'email_cntct' => strtolower($request->input("cliente_email")), 'telefono_cntct' => $request->input('cliente_telefono'), 'area_cntct' => $request->input("cliente_area"), 'token_verificacion' => $confirmation_token,'estado' => 2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
	    //estado 2 sin validar
	    //Ingreso de Adjuntos 
	    if($request->hasFile('adjuntos')) 
	    { 
		foreach($request->file('adjuntos') as $adjunto)
		{
		    $file_name_storage = str_replace("?","",$adjunto->getClientOriginalName());
	   	    $relative_path = $adjunto->storeAs('tmp_files',$file_name_storage,'local');
		    //Obtener la rutha completa del archivo
		    $absolute_path = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . $relative_path;			
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
		
	    /*
	    * Correo de Validación
	    */
	    $data["name"] = $request->input("cliente_nombre");
	    $data["email"] = $request->input("cliente_email");
	    $data["confirmation_token"] = $confirmation_token;
	    $data["issue_id"] = $issue_id;
	    $data["issue_subject"] = $request->input("tarea_asunto");
	    Mail::to($request->input("cliente_email"))->send(new verificacion($data));

	    return view('resultadoCorreo',['msg' => 'Se le ha enviado un correo. Siga las instrucciones para validar la tarea => ', 'issue_id' => $request->input("cliente_email") ]);
	}catch(\Exception $e)
	{
	    return view('resultadoCorreo',['msg' => 'error ' . $e, 'issue_id' => '' ]);
	}
    }

	public function confirmarCorreo($email, $confirm_token)
	{
		// Podría también utilizarse el id para buscar la tarea
/*		$issues = DB::table('tareas')->select('id', 'created_at')
					->where('email_cntct', '=', $email)
					->where('token_verificacion', '=', $confirm_token)
					->get();
*/
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
			/*
			* Confirmar confirmarción dentro del mismo día
			*/
			$fecha_actual = Carbon::now();
			$fecha_tarea = date_create($issue->created_at);
			//$fecha_tarea = date_create('2017-09-18');
			if(date_format($fecha_actual,'Y-m-d') == date_format($fecha_tarea,'Y-m-d'))
			{		
				/*
				* LLamar WebService Redmine para ingreso de tarea
				*/
				
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
						Mail::to("victor.vela@claro.com.gt")->send(new aviso($dat));
						
						/*
						* Borrar registo de tarea local
						*/
						foreach($files as $f)
	    					{
						    $file_adjunto = Adjunto::find($f->id);
						    $file_adjunto->issue = $tarea_id_rst;
						    $file_adjunto->save();
						}
						$tarea = Tarea::find($issue->id);
						$tarea->delete();
						//self::borrar_uploads_local($files);
						return view('resultadoCorreo',['msg' => 'La tarea ha sido validada exitosamente desde el correo '.$email.'. Se ha ingresado la OT con número de tarea ', 'issue_id' => $tarea_id_rst]);
						}catch(\Exception $e)
						{
						    return view('resultadoCorreo',['msg' => '', 'issue_id' => '']);
						}
					}else{
						return view('resultadoCorreo',['msg' => '', 'issue_id' => '']);								
					}
				}else{
					return view('resultadoCorreo',['msg' => '', 'issue_id' => '']);								
				}
			}else{
				$tarea = Tarea::find($issue->id);//update adjuntos con nueva tarea
				$tarea->delete();
				return view('resultadoCorreo',['msg' => 'El período de validación ha expirado por favor generar una nueva tarea ', 'issue_id' => '<a href="'.url('/').'">Inicio</a>' ]);				
			}
		}
		return view('resultadoCorreo',['msg' => '', 'issue_id' => '']);
	}

    private function uploadAdjunto($absolute_path)
    {
		exec('curl --data-binary "@'.$absolute_path.'" -H "Content-Type: application/octet-stream" -X POST "http://'.$this->ip.'/uploads.xml?key='.$this->key.'"', $rs_redmine_api_upload);
		return $rs_redmine_api_upload[0];
    }

	 private function ingresar_uploads_redmine($adjuntos, &$str_uploads)
	{
		/*
		* Ingresar uploads redmine
		*/
		$str_uploads = "";
	    foreach($adjuntos as $adjunto)
	    {
			//Subir el archivo a redmine, parsear el resultado XML
			$upload = simplexml_load_string(self::uploadAdjunto($adjunto->absolute_path));
			#$str_uploads .= "<upload><token>".$upload->token."</token><filename>".$adjunto->getClientOriginalName()."</filename><description></description><content_type>".Storage::mimeType("tmp_files/" . $adjunto->getClientOriginalName())."</content_type></upload>";
			if($str_uploads == ''){
				$str_uploads .= '{"token":"'.$upload->token.'","filename":"'.$adjunto->name.'","content_type":"'.Storage::mimeType("tmp_files/" . $adjunto->name).'"}';
			}else{
				$str_uploads .= ',{"token":"'.$upload->token.'","filename":"'.$adjunto->name.'","content_type":"'.Storage::mimeType("tmp_files/" . $adjunto->name).'"}';
			}
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
	
    private function createTarea($proyecto_id,$tracker_id,$status_id, $priority_id,$author_id,$assigned_to_id,$subject,$description,$Date,$done_ratio,$estimated_hours,$uploads,&$str_issue)
    {
	try
	{
		#$str_issue_req="<issue><project_id>".$proyecto_id."</project_id><tracker_id>$tracker_id</tracker_id><status_id>$status_id</status_id><priority_id>$priority_id</priority_id><author_id>$author_id</author_id><assigned_to_id>$assigned_to_id</assigned_to_id><subject>$subject</subject><description>$description</description><start_date>$Date</start_date><due_date>".date("Y-m-d", strtotime($Date." + 15 days"))."</due_date><done_ratio>$done_ratio</done_ratio><estimated_hours>$estimated_hours</estimated_hours><uploads type=\"Array\">".$uploads."</uploads></issue>";

		$str_issue_req='{"issue":{"project_id":"'.$proyecto_id.'","tracker_id":"'.$tracker_id.'","status_id":"'.$status_id.'","priority_id":"'.$priority_id.'","author_id":"'.$author_id.'","assigned_to_id":"'.$assigned_to_id.'","subject":"'.$subject.'","description":"'.$description.'","start_date":"'.$Date.'","due_date":"'.date("Y-m-d", strtotime($Date." + 15 days")).'","done_ratio":"'.$done_ratio.'","estimated_hours":"'.$estimated_hours.'","uploads":['.$uploads.']}}';

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
		return 1;
	}
    }

    /*
     * Funciones Buscar Tarea
     */

    public function buscar_ot($tarea_default, $email, $confirm_token)
    {
        return view('buscarTarea', ['id_defautl' => $tarea_default, 'id_email' => $email,'token' => $confirm_token]);
    }

    public function buscar_tarea(Request $request)
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
		    $token_clnt = explode(":",$description_valores[4])[1];
		    $description = explode(":",$description_valores[5])[1];
		    if($correo_clnt != strtolower($request->cor) || $token_clnt != $request->cod)
		    { 
			throw new \Exception("Código o Token incorrecto"); 
		    }
	    	}else{ throw new \Exception("mal formato de la descripción en redmine");}
	    }else{ throw new \Exception("mal formato de la descripción en redmine");}

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
            $str_journals = "<table class=\"table table-striped table-bordered\"><thead><tr><th>Autor Nota</th><th>Nota</th><th>Fecha</th></tr></thead><tbody>";
	    foreach($journals->journal as $journal)
	    {
		$str_details = "";
		foreach($journal->details->detail as $detail)
		{
		    if($detail['property'] == 'attr')
		    {
			$str_details .= "La propiedad " .self::cambio_str((string)$detail['name']). " se ha actualizado<br>";
		    }
		}
		$str_journals .= "<tr><td>".$journal->user['name']."</td><td>".$journal->notes."<br>".$str_details."</td><td>".$journal->created_on."</td></tr>";
	    }
	    $str_journals .= "</tbody></table>";

	    //links de descarga
	    $str_rst_links = "";
	    $files = DB::table('adjuntos')
		->where('issue', '=', $request->tarea_id)
		->get();
	    foreach($files as $file)
	    {
		$str_rst_links .= "<a href=\"".url('/')."/download/".$file->name."\">".$file->name."</a><br>";
	    }

	    return response()->json(array('issue_subject' => $subject, 'issue_description' => $description, 'issue_status' => $status, 'issue_done_ratio' => $done_ratio, 'issue_start_date' => $start_date, 'issue_due_date' => $due_date, 'issue_assigned' => $assigned_to_name, 'issue_project_name' => $project_name, 'tiempo_activo' => $tiempo_activo, 'journals' => $str_journals, 'nombre_clnt' => $nombre_clnt, 'down_links' => $str_rst_links), 200);
	
	}catch(\Exception $e){
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
	    
	    return response()->json(array('correo' => $correo_clnt, 'cerrado' => $cerrado), 200);
	}catch(\Exception $e){
	    App::abort(403,$e);
	}
    }

	
    /*
     * Encuesta
     */

    public function ingreso_encuesta(Request $request)
    {
		/*
		 * Parámetros
		*/
		$resultado = $request->resultado;
		$tiempo = $request-> tiempo;
		$observaciones = $request->observaciones;
		$tarea = $request->tarea;
		$proyecto = $request->proyecto;
		/*
		 * Ingresar encuesta BD 
		 */
	
		$encuesta = new Encuesta;
		$encuesta->rst_sat_requerimiento = $resultado;
		$encuesta->rst_sat_tiempo = $tiempo;
		$encuesta->observaciones = $observaciones;
		$encuesta->tarea = $tarea;
		$encuesta->proyecto = $proyecto;
		$encuesta->save();

		Tarea::where('tarea',$tarea)->update(['estado' => 0, 'fecha_finalizacion' => Carbon::now()]);
		
		return response()->json(array('rsp'=> ''), 200);
    }
	
	public function cambio_str($str)
	{
		switch ($str)
		{
			case "status_id":
				$str = "estado";
				break;
			case "done_ratio":
				$str = "progreso";
		}
		return $str;
	}

    public function nueva_encuesta()
    {
	return view('encuesta',['project_name' => 'test']);
    }

    public function showencuesta($tarea, $seq)
    {
	$encuesta = Encuesta::where('tarea',$tarea)->where('token',$seq)->first();
	return view('encuesta',['encuesta' => $encuesta]);
    }

}

