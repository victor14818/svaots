<?php

namespace App\Lib;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;
use App\Lib\Proyecto;
use App\Lib\Tarea;

class EasyRedmineConn
{
    //key clave de seguridad usuario en Redmine
    private $key = 'bd20a77b8aae24076246a15b6ef5333fbc58fef8';
    //ip IP servidor Redmine
    private $ip = '10.98.72.11';

    function __construct()
    {
        
    }

    /*
    *listarProyectos Función para consultar el API de Redmine para listar proyectos
    *@offset Desplazamiento posición inicial número de item
    *@limit Cantidad de items a retornar (límite máxio 100)
    */
    public function listarProyectos($offset, $limit)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://".$this->ip."/projects.xml?offset=$offset&limit=$limit&key=".$this->key);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);    
        //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); 
        //curl_setopt($ch, CURLOPT_TIMEOUT, 1000);

        $response = curl_exec($ch);
        $flagError = false;
        if(curl_error($ch))
        {
            Log::info('Error: ' . curl_error($ch));
            $flagError = true;
        }
        curl_close($ch);
        if($flagError)
        {
            return null;
        }
        return $response;
    }

    /*
    *uploadAdjunto Función para cargar archvios a Redmine con curl
    *@absolute_path Ruta completa del archivo a cargar
    */
    private function uploadAdjunto($absolute_path)
    {
        exec('curl --data-binary "@'.$absolute_path.'" -H "Content-Type: application/octet-stream" -X POST "http://'.$this->ip.'/uploads.xml?key='.$this->key.'" --max-time "300"', $rs_redmine_api_upload);
        return $rs_redmine_api_upload[0];
    }

    /*
    *ingresar_uploads_redmine Función para cargar archvios a Redmine y retornar el listado de tokens en formato JSON
    *@adjuntos Lista de modelos Adjuntos
    *@str_uploads String donde se guara el listado de tokens en formato JSON
    */
    public function ingresar_uploads_redmine($adjuntos, &$str_uploads)
    {

        try
        {
            //Se verfica que exista conectividad a la IP de Redmine puerto 80
            $puerto = 80;
            $fp = fsockopen($this->ip, $puerto, $errno, $errstr, 30);
            if (!$fp) {
                throw new Exception('No hay conectividad con le IP ' . $this->ip . ' puerto '. $puerto);
            }

            //Se verifica que todos los archivos locales existan
            foreach($adjuntos as $adjunto)
            {
                if(!file_exists( $adjunto->absolute_path ))
                {
                    throw new Exception('El archivo no existe en el servidor local ' . $adjunto->absolute_path);
                }
            }

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
        }
        catch(\Exception $e)
        {
            Log::info($e);
            $str_uploads="";
        }
    }

    /*
    *createTarea Función para cargar una tarea a Redmine
    */
    public function createTarea($proyecto_id,$tracker_id,$status_id, $priority_id,$author_id,$assigned_to_id,$subject,$description,$due_date,$done_date,$done_ratio,$estimated_hours,$uploads)
    {
        try
        {
            $str_issue_req='{"issue":{"project_id":"'.$proyecto_id.'","tracker_id":"'
            .$tracker_id.'","status_id":"'.$status_id.'","priority_id":"'.$priority_id
            .'","author_id":"'.$author_id.'","assigned_to_id":"'.$assigned_to_id
            .'","subject":"'.$subject.'","description":"'.$description
            .'","start_date":"'.$due_date
            .'","due_date":"'.$done_date
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


            $response = curl_exec($ch);
            $flagError = false;
            if(curl_error($ch))
            {
                Log::info('Error: ' . curl_error($ch));
                $flagError = true;
            }
            curl_close($ch);
            if($flagError)
            {
                return null;
            }
            return $response;
        }catch(\Exception $e)
        {
            Log::info($e);
            return null;
        }
    }

    /*
    public function borrarTarea($id)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://".$this->ip."/issues/".$id.".xml?key=".$this->key);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        $response = curl_exec($ch);
        curl_close($ch);
    }
    */

    public function buscarTarea($id)
    {
        $str_request = "http://".$this->ip."/issues/".$id.".xml?key=".$this->key."&include=journals,attachments";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $str_request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        $response = curl_exec($ch);
        $flagError = false;
        if(curl_error($ch))
        {
            Log::info('Error: ' . curl_error($ch));
            $flagError = true;
        }
        curl_close($ch);
        if($flagError)
        {
            return null;
        }
        return $response;
    }

    public function getKey()
    {
        return $this->key;
    }

    /*
    * lst_proyectos Función que devuelve un array con Objetos tipo Proyecto.
    */
    public function lst_proyectos()
    {
        //EasyRedmineConn Clase donde se implementan las llamadas a la API de Redmine
        $redmineConnectionAPI = new EasyRedmineConn();
        $respuesta=$redmineConnectionAPI->listarProyectos(0,100);
        if($respuesta == null)
        {
            return null;
        }

        /*
        *Se parsea el XML obtenido para sacar el parámetro de total de proyectos en Redmine.
        *Además se obtiene los primeros 100 items (proyectos).
        */
        $proyectos=simplexml_load_string(preg_replace('/&(?!;{6})/', '&amp;', $respuesta));
        $cmp_total=$proyectos['total_count'];

        //Se obtiene los demás items (proyectos) en caso de ser necesario y se agregan como un solo archivo XML.
        $cmp=100;
        while($cmp < $cmp_total)
        {
            $respuesta=$redmineConnectionAPI->listarProyectos($cmp,100);
            $proyectos_tmp=simplexml_load_string(preg_replace('/&(?!;{6})/', '&amp;', $respuesta));
            self::append_simplexml($proyectos,$proyectos_tmp);
            $cmp+=100;
        }

        //Se establece el poryecto raíz, éstos valor se buscaron de antemano
        $raiz = new Proyecto('221','OTs Internas','OTs de Clientes internos','0','0', Null);
        self::get_proyectos_hijos($raiz->proyectos,$raiz->id,$proyectos);

        //Se convierte el árbol de proyectos en un array, tomando únicamente las hojas.
        $listaProyectos = array();
        self::get_proyectos_array($listaProyectos,$raiz);

        return $listaProyectos;
    }  

    /*
    *append_simplexml Función para unir dos archivos XML 
    *@simplexml_to Archivo que se mantiene como el original
    *@simplexml_from Archvo que va a ser agregado
    */
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

 
    /*
    *get_proyectos_hijos Función para formar un árbol con los items del archivo XML de proyectos Redmine
    *@arreglo_padre Arreglo donde se guardan los nodos hijos del nodo actual
    *@id_padre Id del nodo actual
    *@proyectos archivo XML de proyectos Redmine
    */ 
    private function get_proyectos_hijos(&$arreglo_padre,$id_padre,$proyectos)
    {
        foreach($proyectos->project as $project)
        {
            if($project->parent['id'] == $id_padre)
            {
                $tiempo_estimado=0;
                foreach($project->custom_fields->custom_field as $custom_field)
                {
                    if($custom_field["name"] == "Tiempo estimado")
                    {
                        $tiempo_estimado = $custom_field->value;
                    }
                }
                //Se buscan las propiedades locales
                //archivo de formulario genérico array que guarda los archivos locales asociados a este proyecto
                $archivoFormularioGenerico = Null;
                $proyecto_hijo=new Proyecto($project->id,$project->name,$project->description,$project->author["id"],$tiempo_estimado,$archivoFormularioGenerico);
                self::get_proyectos_hijos($proyecto_hijo->proyectos,"".$proyecto_hijo->id,$proyectos);
                array_push($arreglo_padre,$proyecto_hijo);
            }
        }
    }

    /*
    *get_proyectos_array Función para llenar un array con los nodos hojas de un árbol
    *@arreglo Archivo a llenar
    *@nodo Nodo actual
    */
    private function get_proyectos_array(&$arreglo,$nodo)
    {
        if(!empty($nodo->proyectos))
        {
            foreach($nodo->proyectos as $proyecto)
            {
                self::get_proyectos_array($arreglo,$proyecto);
            }
        }
        else
        {
            array_push($arreglo,$nodo);
        }
    }

    /*
    *listarTareas Función para realizar una conulsta al API de redmine 
    *@proyectoId Id del proyecto del que se desean obtener las tareas.
    */
    public function listarTareas($proyectoId,$offset)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://".$this->ip."/issues.xml?project_id=".$proyectoId."&offset=".$offset."&limit=100&key=".$this->key);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);    
        //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); 
        //curl_setopt($ch, CURLOPT_TIMEOUT, 1000);

        $response = curl_exec($ch);
        $flagError = false;
        if(curl_error($ch))
        {
            Log::info('Error: ' . curl_error($ch));
            $flagError = true;
        }
        curl_close($ch);
        if($flagError)
        {
            return null;
        }
        return $response;
    }

    /*
    *lst_tareas Función obtener un arreglo con las tareas de un determinado proyecto
    *@proyectoId Id del proyecto del que se desean obtener las tareas.
    */
    public function lst_tareas($proyectoId)
    {
        //EasyRedmineConn Clase donde se implementan las llamadas a la API de Redmine
        $redmineConnectionAPI = new EasyRedmineConn();
        $respuesta=$redmineConnectionAPI->listarTareas($proyectoId,0);
        if($respuesta == null)
        {
            return "<h1>Servicio temporalmente indisponible</h1><br><h3>Cominicarse con Ingeniería SVA (ingenieriasva@claro.com.gt) para mayor información</h3>";
        }

        $tareas=simplexml_load_string(preg_replace('/&(?!;{6})/', '&amp;', $respuesta));
        $cmp_total=$tareas['total_count'];

        //Se obtiene los demás items (tareas) en caso de ser necesario y se agregan como un solo archivo XML.
        $cmp=100;
        while($cmp < $cmp_total)
        {
            $respuesta=$redmineConnectionAPI->listarTareas($cmp,100);
            $tareas_tmp=simplexml_load_string(preg_replace('/&(?!;{6})/', '&amp;', $respuesta));
            self::append_simplexml($tareas,$tareas_tmp);
            $cmp+=100;
        }

        //Se recorre objeto simpleXML para formar un objeto de tipo Tarea.
        foreach($tareas->issue as $issue)
        {
            echo $issue->id;
        }
        
        //return $listaTareas;

    }


    /*
    *getTarea Función obtener una tarea desde Redmine por medio de su ID
    *@tareaId Id de la tarea en Redmine.
    */
    public function getTareaAPI($tareaId)
    {
        try{

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://".$this->ip."/issues/".$tareaId.".xml?key=".$this->key);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);

            $response = curl_exec($ch);
            $flagError = false;
            if(curl_error($ch))
            {
                Log::info('Error: ' . curl_error($ch));
                $flagError = true;
            }
            curl_close($ch);
            if($flagError)
            {
                return null;
            }
            return $response;
        }catch(\Exception $e)
        {
            return null;
        }
    }

    public function getTarea($tareaId)
    {
        try
        {

            //EasyRedmineConn Clase donde se implementan las llamadas a la API de Redmine
            $redmineConnectionAPI = new EasyRedmineConn();
            $respuesta=$redmineConnectionAPI->getTareaAPI($tareaId);
            if($respuesta == null)
            {
                return null;
            }

            /*
            *Se parsea el XML obtenido para sacar los valores de la tarea
            */
            $tareaXMLObject=simplexml_load_string(preg_replace('/&(?!;{6})/', '&amp;', $respuesta));
            $envioOT = null;
            $boEjecucion = null;
            $ipEjecucion = null;

            foreach($tareaXMLObject->custom_fields->custom_field as $custom_field)
            {
                switch ($custom_field["name"]) {
                    case "Envío de OT":
                        $envioOT = $custom_field->value;
                        break;                    
                    case "[BO] Ejecución":
                        $boEjecucion = $custom_field->value;
                        break;                    
                    case "[IP] Ejecución":
                        $ipEjecucion = $custom_field->value;
                        break;                    
                }
            }

            $tarea = new Tarea(
                    $tareaXMLObject->id
                ,   $tareaXMLObject->project["id"]
                ,   $tareaXMLObject->project["name"]
                ,   $tareaXMLObject->status["name"]
                ,   $tareaXMLObject->assigned_to["id"]
                ,   $tareaXMLObject->assigned_to["name"]
                ,   $tareaXMLObject->subject
                ,   $tareaXMLObject->description
                ,   $tareaXMLObject->start_date
                ,   $tareaXMLObject->due_date
                ,   null
                ,   $envioOT
                ,   $boEjecucion
                ,   $ipEjecucion
            );

            return $tarea;
        }catch(\Exception $e)
        {
            return null;
        }
    }


    /*
    *ModificarTarea Función que sirve para modificar campos de una tarea
    *@tareaId número de tarea a modificar
    *@campos array con los campos a modificar.
    */
    public function modificarTarea($tareaId,$campos)
    {
	$stringCampo = "<issue>";
        foreach($campos as $campo)
        {
            $stringCampo .= $campo;
        }
        $stringCampo .= "</issue>"; 

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://".$this->ip."/issues/".$tareaId.".xml?key=".$this->key);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $stringCampo);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/xml"
        ));

        $response = curl_exec($ch);
        $flagError = false;
        if(curl_error($ch))
        {
            Log::info('Error: ' . curl_error($ch));
            $flagError = true;
        }
        curl_close($ch);
        if($flagError)
        {
	    echo "error";
            return null;
        }
        return $response;
        
    }
}

?>
