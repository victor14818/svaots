<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lib\Proyecto;

class ProyectosController extends Controller
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
	#$raiz = new Proyecto('7','OTs','Proyecto para revisión de OTs del Packet Core','0','0');
	$raiz = new Proyecto('221','OTs Internas','OTs de Clientes internos','0','0');
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
		$tiempo_estimado=0;
		foreach($project->custom_fields->custom_field as $custom_field)
		{
		    if($custom_field["name"] == "Tiempo estimado")
		    {
 			$tiempo_estimado = $custom_field->value;
		    }
		}
		$proyecto_hijo=new Proyecto($project->id,$project->name,$project->description,$project->author["id"],$tiempo_estimado);
		self::get_proyectos_hijos($proyecto_hijo->proyectos,"".$proyecto_hijo->id);
		array_push($arreglo_padre,$proyecto_hijo);
	    }
	}
    }

    private function ttl_proyectos_fs($current,$p)
    {
	$var = "";
	/*
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
	    $var .= "<form action=\"nvot\" method=\"POST\">";
	    $var .= "<input type=\"hidden\" name=\"project_id\" value=\"".$current->id."\">";
	    $var .= "<input type=\"hidden\" name=\"project_name\" value=\"".$current->name."\">";
	    $var .= "<input type=\"hidden\" name=\"project_author\" value=\"".$current->author."\">";
	    $var .= "<input type=\"submit\" value=\"Nueva OT\" class=\"btn btn-danger\"></td>";
	    $var .= "</form>";
	    $var .= "</td>";
	    $var .= "</tr>";
	}
	*/
	if(!empty($current->proyectos))
	{
	    $var .= "<tr>"; 
	    $var .= "<td>".$current->name."</td>";
	    $var .= "<td colspan='2'>".$current->description."</td>";
	    $var .= "</tr>";
	    foreach($current->proyectos as $project)
	    {
			$var .= self::ttl_proyectos_fs($project,$current->id);
	    }
	}else
	{
	    $var .= "<tr>"; 
	    $var .= "<td style=\"font-family:'Courier New', Courier, monospace; font-style: italic; font-size:80%\">".$current->name."</td>";
	    $var .= "<td style=\"font-family:'Courier New', Courier, monospace; font-style: italic; font-size:80%\">".$current->description."</td>";
	    $var .= "<td>";
	    $var .= "<form action=\"nvot\" method=\"POST\">";
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
	curl_setopt($ch, CURLOPT_URL, "http://".$this->ip."/projects.xml?offset=$offset&limit=$limit&key=".$this->key);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);	
	$response = curl_exec($ch);
	curl_close($ch);

	return $response;
    }

    public function lst_informacion()
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
	#$raiz = new Proyecto('7','OTs','Proyecto para revisión de OTs del Packet Core','0','0');
	$raiz = new Proyecto('221','OTs Internas','OTs de Clientes internos','0','0');
	self::get_proyectos_hijos($raiz->proyectos,$raiz->id);
	$var = "<table class=\"table table-striped\"><tr><th>Proyecto</th><th>Tiempo estimado (días)</th></tr>";
	$var .= self::tti_proyectos_fs($raiz,0);
	$var .= "</table>";
	return view('informacion',['var'=>$var]);
    }

    private function tti_proyectos_fs($current,$p)
    {
	$var = "";
	if(!empty($current->proyectos))
	{
	    foreach($current->proyectos as $project)
	    {
		$var .= self::tti_proyectos_fs($project,$current->id);
	    }
	}else
	{
	    $var = "<tr><td>".$current->name."</td><td>".$current->tiempoEstimado."</td></tr>";
	}
	return $var;
    }

}
