<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'porta_ots@lst_proyectos');

Route::post('nueva_OT', 'porta_ots@nueva_ot');

Route::post('nuevo_ingreso', 'porta_ots@ingreso_ot');

Route::get('validarTarea/email/{email}/seq/{confirm_token}','porta_ots@confirmarCorreo');

Route::get('buscar_OT/tarea/{tarea}/email/{email}/seq/{confirm_token}', 'porta_ots@buscar_ot');

Route::post('buscar_tarea', 'porta_ots@buscar_tarea');

Route::post('ingresar_encuesta', 'porta_ots@ingreso_encuesta');

Route::get('download/{filename}', function($filename)
{
	$relative_path = 'tmp_files/'. $filename;
    $absolute_path = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . $relative_path;
	
    if (file_exists($absolute_path))
    {
        return response()->download($absolute_path);
    }
    else
    {

        exit('Requested file does not exist on our server! ' . $absolute_path);
    }
});
//->where('filename', '[A-Za-z0-9\-\_\.]+');

Route::post('buscar_code', 'porta_ots@retornar_links');

Route::get('encuesta', 'porta_ots@nueva_encuesta');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::post('cerrar_tarea', 'porta_ots@cerrar_tarea');

Route::get('encuesta/tarea/{tarea}/seq/{seq}', 'porta_ots@showencuesta');

