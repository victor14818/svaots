@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">Form proyectos</div>
            <div class="panel-body">
                {{ Form::open(array('url' => 'formsProyectos/create', 'class' => 'form-horizontal', 'enctype'=>'multipart/form-data')) }}
                    {{ Form::token() }}
                    <div class="form-group {{ $errors->has('numeroProyecto') ? 'has-error' : '' }}">
                        <label for="numeroProyecto" class="col-md-4 control-label">Número proyecto</label>
                        <div class="col-md-6">
                            <input type="text" name="numeroProyecto" class="form-control" id="numeroProyecto">
                        </div>
                        <span class="text-danger">{{ $errors->first('numeroProyecto') }}</span>
                    </div>

                    <div class="form-group {{ $errors->has('nombreProyecto') ? 'has-error' : '' }}">
                        <label for="nombreProyecto" class="col-md-4 control-label">Nombre proyecto</label>
                        <div class="col-md-6">
                            <input type="text" name="nombreProyecto" class="form-control" id="nombreProyecto">
                        <span class="text-danger">{{ $errors->first('nombreProyecto') }}</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="archivoFormulario" class="col-md-4 control-label">Archivo form</label>
                        <div class="col-md-6">
                            <input type="file" name="archivoFormulario" class="form-control" id="archivoFormulario">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="formGenerico" class="col-md-4 control-label">Form genérico</label>
                        <div class="col-md-6">
                            <input type="checkbox" name="formGenerico" id="formGenerico">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                Save
                            </button>
                        </div>
                    </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@endsection

