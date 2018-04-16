@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">Form proyectos</div>
            <div class="panel-body">
                <div class="form-group ">
                    <div class="row col-md-8 col-md-offset-2">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($listaProyectos as $proyecto)
                                <tr>
                                    <td>{{ $proyecto->id }}</td>
                                    <td>{{ $proyecto->name }}</td>
                                    <td>
                                        {{ Form::open(array('url' => 'projects/'.preg_replace('/[^A-Za-z0-9]/', '', $proyecto->name).'/show', 'class' => 'form-horizontal', 'method' => 'POST')) }}
                                        {{ Form::hidden('proyectoId',$proyecto->id) }}
                                        {{ Form::hidden('proyectoNombre',$proyecto->name) }}
                                        {{ Form::submit('Editar') }}
                                        {{ Form::close() }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

