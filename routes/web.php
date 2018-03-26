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

Route::get('/home', 'HomeController@index')->name('home');

//
Route::get('/', 'ProyectosController@lst_proyectos');
Route::get('informacion','ProyectosController@lst_informacion');

Route::match(['get', 'post'], 'tarea/fgshow', 'TareasController@showFormularioGenerico');
Route::post('tarea/new', 'TareasController@ingresoTarea');
Route::get('nvotconfirmacion/email/{email}/seq/{confirm_token}','TareasController@confirmarCorreo');




// ___Authentication Routes___
// ...Auth::routes();
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// ...Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

// ...Registration Routes...

Route::group( [ 'middleware' => ['role:admin'] ] , function() {

    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@register');

});


Route::group( [ 'middleware' => ['role:admin|owner'] ] , function() {

    Route::get('projects/show','FormsProyectosController@show');
    Route::post('projects/{proyecto}/show','FormsProyectosController@showSingle');
    Route::post('projects/{proyecto}/edit','FormsProyectosController@edit');
    Route::post('projects/{proyecto}/deleteAttachment','FormsProyectosController@deleteAttachment');

});

//Descarga de archivo en formato ZIP
Route::get('projects/{proyecto}/downloadAttachments','FormsProyectosController@downloadAttachments');

Route::group( [ 'middleware' => ['role:owner'] ] , function() {

    Route::get('tasks/show','FormsTareasController@show');
    Route::post('tasks/closereject','FormsTareasController@closereject');

});


Route::get('encuesta/tarea/{tarea}/seq/{seq}', 'EncuestaController@showEncuesta');
Route::post('encuesta/edit', 'EncuestaController@ingresoEncuesta');

Route::get('buscartarea/tarea/{tarea}/email/{email}/seq/{confirm_token}', 'BusquedasController@buscarTarea');
Route::post('buscartarea/form', 'BusquedasController@buscarTareap');

Route::post('/download','AdjuntosController@descargar_adjunto');




/*Route::get('download/{filename}', function($filename)
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
});*/

//Route::post('formsProyectos/create','FormsProyectosController@create');

//Route::get('prueba', 'TareasController@prueba');
//Route::post('prueba', 'TareasController@pruebaPOST');


