@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $proyecto->id }} - {{ $proyecto->name }}</div>
            <div class="panel-body">
                <div class="form-group ">
                    <div class="row col-md-8 col-md-offset-2">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($listaArchivoFormularioGenerico as $adjunto)
                                <tr>
                                    <td colspan="2">{{ $adjunto->name }}</td>
                                    <td>
                                        {{ Form::open(array('url' => 'projects/'.preg_replace('/[^A-Za-z0-9]/', '', $proyecto->name).'/deleteAttachment', 'class' => 'form-horizontal', 'method' => 'POST')) }}
                                        {{ Form::hidden('proyectoId',$proyecto->id) }}
                                        {{ Form::hidden('proyectoNombre',$proyecto->name) }}
                                        {{ Form::hidden('adjuntoId',$adjunto->id) }}
                                        {{ Form::submit('Eliminar', ['class' => 'btn btn-danger']) }}
                                        {{ Form::close() }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {{ Form::open(array('url' => 'projects/'.preg_replace('/[^A-Za-z0-9]/', '', $proyecto->name).'/edit', 'class' => 'form-horizontal', 'enctype'=>'multipart/form-data')) }}
                {{ Form::token() }}
                    <div class="form-group ">
                        <div class="row col-md-8 col-md-offset-2">
                            <label for="archivoFormulario" class="control-label">Archivo:</label>
                            <div id="divAttachments">
                                <input type="file" name="attachments[]" class="form-control" />
                            </div>
                            <a href="#" id="addNewAttachment" onclick="addField();">
                                Agregue otro archivo
                            </a>
                        </div>
                    </div>
                {{ Form::hidden('proyectoId',$proyecto->id) }}
                {{ Form::submit('Guardar') }}
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
<script>

function addField(){
  $('form input:file').last().after($('<input type="file" name="attachments[]" class="form-control"/>'));
}

</script>
@endsection

