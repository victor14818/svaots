<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Proyecto;	
use App\Adjunto;
use Redirect;
use App\Services\PayUService\Exception;
use Illuminate\Support\Facades\Storage;

class FormsProyectosController extends Controller
{
    public function show()
    {
    	return view('aplicacionOTS.formsProyectos');
    }

    public function create(Request $request)
    {
    	$this->validate($request, [
                    'numeroProyecto' => 'required|integer'
                ,   'nombreProyecto' => 'required|max:250'
                ]);

    	$Proyecto = new Proyecto;
        $Proyecto->numeroProyecto = $request->numeroProyecto;
        $Proyecto->nombreProyecto = $request->nombreProyecto;
        if($request->hasFile('archivoFormulario')) 
	    { 		
	    	$adjunto = $request->archivoFormulario;
		    $file_name_storage = str_replace("?","",$adjunto->getClientOriginalName());
	   	    $relative_path = $adjunto->storeAs('tmp_files',$file_name_storage,'local');
		    $absolute_path = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix().$relative_path;			

		    $file = new Adjunto;
		    $file->name = $file_name_storage;
		    $file->type = Storage::mimeType("tmp_files/" . $file_name_storage);
		    $file->absolute_path = $absolute_path;
		    $file->relative_path = $relative_path;
		    $file->save();					

		     $Proyecto->formularioId = $file->id;
		}

		if($request->has('formGenerico')) 
	    {
	    	 $Proyecto->formularioGenerico = 1;
	    }
        
        try{
        
            $Proyecto->save();
            $request->session()->flash('alert-success', 'saved successfully!');
        
        }catch(\Exception $e){

            $request->session()->flash('alert-danger', $e->getMessage());
            
        }

        return Redirect::to('home');

    }
}
