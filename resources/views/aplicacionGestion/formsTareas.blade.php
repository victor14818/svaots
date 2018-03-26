@extends('layouts.app')

@section('content')
@if($errors->any())
<div class="alert alert-info">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Info!</strong> {{$errors->first()}}.
</div>
@endif
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">Tareas</div>
            <div class="panel-body">
                <div class="form-group ">
                    <div class="row col-md-10">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Id tarea</th>
                                    <th>Proyecto</th>
                                    <th>Estado</th>
                                    <th>Asunto</th>
                                    <th>Descripción</th>
                                    <th>Fecha de ingreso</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($listaTareas as $tarea)
                                <tr>
                                    <td>{{ $tarea->id }}</td>
                                    <td>{{ $tarea->projectName }}</td>
                                    <td>{{ $tarea->status }}</td>
                                    <td>{{ $tarea->subject }}</td>
                                    <td>{{ $tarea->description }}</td>
                                    <td>{{ $tarea->startDate }}</td>
                                    <td>
                                        {{ Form::button('Rechazar',['class' => 'btn btn-danger', 'onclick' => 'rechazar('.$tarea->id.',"'.$tarea->projectName.'")']) }}
                                    </td>
                                    <td>
                                        {{ Form::button('Cerrar',['class' => 'btn btn-info', 'onclick' => 'cerrar('.$tarea->id.',"'.$tarea->projectName.'")'] ) }}
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

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Mensaje</h4>
            </div>
            <form method="POST" action="{{ url('/') }}/tasks/closereject">
                <div class="modal-body">
                {{ csrf_field() }}
                    <input type="hidden" id="tipoAccionTarea" name="tipoAccionTarea" value="Close">
                    <input type="hidden" id="tareaId" name="tareaId">
                    <input type="hidden" id="projectName" name="projectName">
                    <div class="form-group">
                        <label for="message-text" class="control-label">Message:</label>
                        <textarea class="form-control" id="messageText" name="messageText"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary" value="Send message">
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function cerrar(tareaId,proyectoNombre) {
    document.getElementById('tipoAccionTarea').value = 'Close';
    document.getElementById('tareaId').value = tareaId;
    document.getElementById('projectName').value = proyectoNombre;
    $("#exampleModal").modal()
}
function rechazar(tareaId,proyectoNombre) {
    document.getElementById('tipoAccionTarea').value = 'Reject';
    document.getElementById('tareaId').value = tareaId;
    document.getElementById('projectName').value = proyectoNombre;
    $("#exampleModal").modal()
}
</script>

@endsection

