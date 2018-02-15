<?php

namespace App\Lib;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

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
}

?>