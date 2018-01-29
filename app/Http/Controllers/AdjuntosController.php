<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class AdjuntosController extends Controller
{

    public function descargar_adjunto(Request $request)
    {
	try
	{
	    $url = $request->fileUrl;
	    $name = $request->fileName;
	    $contents = file_get_contents($url);
	    header('Content-Type: '.$request->fileContentType);
	    Storage::disk('local')->put($name, $contents);
	    $absolute_path = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . $name;
	    return response()->download($absolute_path)->deleteFileAfterSend(true);
	}catch(\Exception $e)
	{
	    Log::info($e);
	    App::abort();
	}
    }

}
