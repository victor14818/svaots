<?php
/*
Controlador para las configuraciones de los Formularios
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Adjunto;
use Redirect;
use App\Services\PayUService\Exception;
use Illuminate\Support\Facades\Storage;
use App\Lib\Proyecto;   
use App\Lib\EasyRedmineConn;
use Route;
use Zip;
use Auth;

class FormsProyectosController extends Controller
{

    /*
    *show Función que busca los proyectos de Redmine y los filtra según el ID que tiene asignado el ususario localmente (redmineId).
    */
    public function show()
    {
        $redmineConnectionAPI = new EasyRedmineConn();
        $listaProyectosTmp = array();
        $listaProyectosTmp = $redmineConnectionAPI->lst_proyectos();
        $listaProyectos = array();

        if(Auth::user()->hasRole('admin'))
        {
            $listaProyectos = $listaProyectosTmp;
        }
        else
        {
            //Filtro de proyectos según el autor y el usuario logueado.
            foreach($listaProyectosTmp as $proyecto)
            {
                if($proyecto->author == Auth::user()->redmineId)
                {
                    array_push($listaProyectos,$proyecto);
                }
            }            
        }
    	return view('aplicacionGestion.formsProyectos',['listaProyectos' => $listaProyectos]);
    }

    /*
    *showSingle Función que busca los archivo asociaciados a un proyecto.
    */
    public function showSingle(Request $request)
    {
        $proyecto = new Proyecto($request->proyectoId, $request->proyectoNombre,null,null,null,array());
        $listaArchivoFormularioGenerico = Adjunto::where('project',$request->proyectoId)->get();
        return view('aplicacionGestion.formsProyectosSingle',['proyecto' => $proyecto, 'listaArchivoFormularioGenerico' => $listaArchivoFormularioGenerico]);
    }

    /*
    *edit Función que guarda los archivos que vienen en el Request y los asocia a un proyecto con su Id.
    */
    public function edit(Request $request)
    {
        try{
        	if($request->hasFile('attachments')) 
            { 
                foreach($request->file('attachments') as $adjunto)
                {
                    $file_name_storage = str_replace("?","",$adjunto->getClientOriginalName());
                    $relative_path = $adjunto->storeAs('formulariosProyectos/'.$request->proyectoId.'/',$file_name_storage,'local');
                    //Obtener la rutha completa del archivo
                    $absolute_path = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix().$relative_path;         

                    $file = new Adjunto;
                    $file->name = $file_name_storage;
                    $file->type = Storage::mimeType('formulariosProyectos/'.$request->proyectoId.'/' . $file_name_storage);
                    $file->absolute_path = $absolute_path;
                    $file->relative_path = $relative_path;
                    $file->project = $request->proyectoId;
                    $file->save();                  
                }
            }

            $request->session()->flash('alert-success', 'saved successfully!');
        
        }catch(\Exception $e){

            $request->session()->flash('alert-danger', $e->getMessage());
            
        }

        return Redirect::to('projects/show');

    }

    /*
    *deleteAttachment Función que elimina un archivo asociado a un poryecto.
    */
    public function deleteAttachment(Request $request)
    {
        try
        {
            $file = Adjunto::find($request->adjuntoId);

            if(isset($file))
            {
                $file->delete();                
                Storage::delete($file->relative_path);
            }
            
            $request->session()->flash('alert-success', 'deleted successfully!');

        }catch(\Exception $e){

            $request->session()->flash('alert-danger', $e->getMessage());            
        
        }

        //$request = Request::create('projects/'.preg_replace('/[^A-Za-z0-9]/', '', $request->proyectoNombre).'/show', 'POST', ['proyectoId' => $request->proyectoId, 'proyectoNombre' => $request->proyectoNombre, '_token' => $request->session()->token()]);
        //return Route::dispatch($request)->getContent();    
        return Redirect::to('projects/show');    
    }

    /*
    *downloadAttachments Función que descarga todos los archcivos asociados a un proyecto comprimidos en formato ZIP. 
    */
    public function downloadAttachments($proyectoId)
    {
        try
        {
            //linux
	    $rutaProyectoArchivos = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix().'formulariosProyectos/'.$proyectoId;
            $rutaProyectoArchivoComprimido = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix().'formulariosProyectos/'.$proyectoId.'.zip';
            //windows
            //$rutaProyectoArchivos = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix().'formulariosProyectos\\'.$proyectoId;
            //$rutaProyectoArchivoComprimido = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix().'formulariosProyectos\\'.$proyectoId.'.zip';

            $existe = Storage::disk('local')->has('formulariosProyectos/'.$proyectoId.'.zip');
            if(!empty($existe))
            {
                $zip = Zip::open($rutaProyectoArchivoComprimido);  
            }else
            {
                $zip = Zip::create($rutaProyectoArchivoComprimido);                
            }
            $zip->add($rutaProyectoArchivos,true);
            $zip->close();
            $headers = [ 'Content-Type' => 'application/octet-stream' ];
            return response()->download($rutaProyectoArchivoComprimido,'formularios.zip',$headers);
        }catch(\Exception $e){
            echo $e->getMessage();            
        }
    }

}
