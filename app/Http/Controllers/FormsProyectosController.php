<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Adjunto;
use Redirect;
use App\Services\PayUService\Exception;
use Illuminate\Support\Facades\Storage;
use App\Lib\Proyecto;   
use App\Lib\EasyRedmineConn;
use App\Proyecto as modelProyecto;
use Route;
use Zip;

class FormsProyectosController extends Controller
{
    public function show()
    {
        $redmineConnectionAPI = new EasyRedmineConn();
        $listaProyectos = array();
        $listaProyectos = $redmineConnectionAPI->lst_proyectos();
    	return view('aplicacionOTS.formsProyectos',['listaProyectos' => $listaProyectos]);
    }

    public function showSingle(Request $request)
    {
        $proyecto = new Proyecto($request->proyectoId, $request->proyectoNombre,null,null,null,array());
        $listaArchivoFormularioGenerico = Adjunto::where('project',$request->proyectoId)->get();
        return view('aplicacionOTS.formsProyectosSingle',['proyecto' => $proyecto, 'listaArchivoFormularioGenerico' => $listaArchivoFormularioGenerico]);
    }

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

        $request = Request::create('projects/'.urlencode($request->proyectoNombre).'/show', 'POST', ['proyectoId' => $request->adjuntoId, 'proyectoNombre' => $request->proyectoNombre, '_token' => $request->session()->token()]);
        return Route::dispatch($request)->getContent();        
    }

    public function downloadAttachments($proyectoId)
    {
        try
        {
            //linux
            //$rutaProyectoArchivos = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix().'formulariosProyectos/'.$request->proyectoId.'/' . $file_name_storage;
            //windows
            $rutaProyectoArchivos = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix().'formulariosProyectos\\'.$proyectoId;
            $rutaProyectoArchivoComprimido = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix().'formulariosProyectos\\'.$proyectoId.'.zip';

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
