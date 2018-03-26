@extends('layouts.app')

@section('content')
{!! Charts::styles() !!}
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Main Application -->
                    <div class="app">
                        <div class="row">
                            <div class="col-md-6">
                                {!! $chartOpenTask->html() !!}
                            </div>
                            <div class="col-md-6">
                                {!! $chartDateTask->html() !!}
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-10 col-sm-offset-1">
                                <table class="table table-striped">
                                    <tr>
                                        <th>No.</th>
                                        <th>Estado</th>
                                        <th>Asunto</th>
                                        <th>Proyecto</th>
                                        @if($esAdmin)
                                        <th>Asignada a</th>
                                        @endif
                                        <th>Start Date</th>
                                        <th>Due Date</th>
                                        <th>Recepcion Solicitud</th>
                                        <th>Envio de OT</th>
                                        <th>BO Ejecución</th>
                                        <th>IP Ejecución</th>
                                    </tr>
                                @foreach($listaTareas as $tarea)
                                <tr>
                                    <td>{{ $tarea->id }}</td>
                                    <td>{{ $tarea->status}}</td>
                                    <td>{{ $tarea->subject }}</td>
                                    <td>{{ $tarea->projectName }}</td>
                                    @if($esAdmin)
                                    <td>{{ $tarea->assignedToId }}</td>
                                    @endif
                                    <td>{{ $tarea->startDate }}</td>
                                    <td>{{ $tarea->DueDate }}</td>
                                    <td>{{ $tarea->RecepcionSolicitud }}</td>
                                    <td>{{ $tarea->EnvioOT }}</td>
                                    <td>{{ $tarea->BOEjecucion }}</td>
                                    <td>{{ $tarea->IPEjecucion }}</td>
                                </tr>
                                @endforeach
                                </table>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                {!! $chartSurveyGrade->html() !!}
                            </div>
                            <div class="col-md-6">
                                {!! $chartSurveyTime->html() !!}
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                {!! $chartSurveyExec->html() !!}
                            </div>
                            <div class="col-md-6">
                                <table class="table table-striped">
                                    <tr>
                                        <th>Últimas cinco obervaciones</th>
                                    </tr>
                                    @foreach($listaEncuesta as $encuesta)
                                    <tr>
                                        <td>{{ $encuesta->observaciones }}</td>
                                    </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Of Main Application -->
{!! Charts::scripts() !!}
{!! $chartOpenTask->script() !!}
{!! $chartDateTask->script() !!}
{!! $chartSurveyTime->script() !!}
{!! $chartSurveyExec->script() !!}
{!! $chartSurveyGrade->script() !!}
@endsection
