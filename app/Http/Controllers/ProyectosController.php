<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lib\Proyecto;
use App\Lib\EasyRedmineConn;
use Exception;
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
		$listaProyectos = array();
		$listaProyectos = $redmineConnectionAPI->lst_proyectos();
		if(is_null($listaProyectos))
		{
			return view('aplicacionOTS.paginaDeError');
		}
		return view('aplicacionOTS.proyectos',[ 'listaProyectos' => $listaProyectos ]);
    }  

    public function lst_informacion()
    {
		//EasyRedmineConn Clase donde se implementan las llamadas a la API de Redmine
    	$redmineConnectionAPI = new EasyRedmineConn();
		$listaProyectos = array();
		$listaProyectos = $redmineConnectionAPI->lst_proyectos();
		if(is_null($listaProyectos))
		{
			return view('aplicacionOTS.paginaDeError');
		}
		return view('aplicacionOTS.informacion', [ 'listaProyectos' => $listaProyectos ]);
    }

/*
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
*/

}
