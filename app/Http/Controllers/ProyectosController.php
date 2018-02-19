<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lib\Proyecto;
use App\Lib\EasyRedmineConn;
use Exception;
use App\Proyecto as modelProyecto;
use App\Adjunto;

class ProyectosController extends Controller
{
    /*
     * Funciones Listar Proyectos
     */
    private $xml_proyectos;    

    public function lst_proyectos()
    {
    	//EasyRedmineConn Clase donde se implementan las llamadas a la API de Redmine
    	$redmineConnectionAPI = new EasyRedmineConn();
    	$respuesta=$redmineConnectionAPI->listarProyectos(0,100);
    	if($respuesta == null)
    	{
    		return "<h1>Servicio temporalmente indisponible</h1><br><h3>Cominicarse con Ingeniería SVA (ingenieriasva@claro.com.gt) para mayor información</h3>";
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

		//Se establece el poryecto raíz, éstos valor se buscarón de antemano
		$raiz = new Proyecto('221','OTs Internas','OTs de Clientes internos','0','0', Null);
		self::get_proyectos_hijos($raiz->proyectos,$raiz->id,$proyectos);

		//Se convierte el árbol de proyectos en un array, tomando únicamente las hojas.
		$listaProyectos = array();
		self::get_proyectos_array($listaProyectos,$raiz);

		return view('aplicacionOTS.proyectos',[ 'listaProyectos' => $listaProyectos ]);
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
				//archivo de formulario genérico
				$archivoFormularioGenerico = Null;
				$Proyecto = modelProyecto::where('numeroProyecto',$project->id)->first();
				if(isset($Proyecto))
				{
					$adjunto = Adjunto::find($Proyecto->id);
					$archivoFormularioGenerico = $adjunto->id;
				}
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

    public function lst_informacion()
    {
		    	//EasyRedmineConn Clase donde se implementan las llamadas a la API de Redmine
    	$redmineConnectionAPI = new EasyRedmineConn();
    	$respuesta=$redmineConnectionAPI->listarProyectos(0,100);
    	if($respuesta == null)
    	{
    		return "<h1>Servicio temporalmente indisponible</h1><br><h3>Cominicarse con Ingeniería SVA (ingenieriasva@claro.com.gt) para mayor información</h3>";
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

		//Se establece el poryecto raíz, éstos valor se buscarón de antemano
		$raiz = new Proyecto('221','OTs Internas','OTs de Clientes internos','0','0', Null);
		self::get_proyectos_hijos($raiz->proyectos,$raiz->id,$proyectos);

		//Se convierte el árbol de proyectos en un array, tomando únicamente las hojas.
		$listaProyectos = array();
		self::get_proyectos_array($listaProyectos,$raiz);

		return view('aplicacionOTS.informacion', [ 'listaProyectos' => $listaProyectos ]);
    }

    public function download($fileId)
	{
		$file = Adjunto::find($fileId);
		if (file_exists($file->absolute_path))
		{
			$headers = array(
				'Cache-Control' => 'no-store,no-cache, must-revalidate, post-check=0, pre-check=0',
			);

			return response()->download($file->absolute_path, $file->name, $headers);
		}
		else
		{
			exit('Requested file does not exist on our server! ');
		}
	}

}
