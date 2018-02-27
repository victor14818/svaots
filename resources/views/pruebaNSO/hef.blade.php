@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    {{ Form::open([ 'url' => 'prueba', 'method' => 'POST']) }}
                    <div class="row">
                        <div class="col-md-3">
                            {{ Form::label('Service Device') }}
                        </div>
                        <div class="col-md-6">
                            {{ Form::text('serviceDevice', '', array('class' => 'form-control')) }}
                        </div>
                    </div><br>
                    <div class="row">
                        <div class="col-md-3">
                            {{ Form::label('Device') }}
                        </div>
                        <div class="col-md-6">
                            {{ Form::select('device', $devicesList) }}
                        </div>
                    </div><br>
                    <div class="row">
                        <div class="col-md-3">
                            {{ Form::label('Client') }}
                        </div>
                        <div class="col-md-6">
                            {{ Form::text('client', '', array('class' => 'form-control')) }}
                        </div>
                    </div><br>
                    <div class="row">
                        <div class="col-md-3">
                            {{ Form::label('Service Type') }}
                        </div>
                        <div class="col-md-6">
                            {{ Form::select('serviceType', ['DOMAIN']) }}
                        </div>
                    </div><br>
                    <div class="row">
                        <div class="col-md-3">
                            {{ Form::label('URLS') }}
                        </div>
                        <div class="col-md-6">
                            {{ Form::textArea('urls',  '', array('class' => 'form-control')) }}
                        </div>
                    </div><br>
                    <div class="row">
                        <div class="col-md-1">
                            {{ Form::submit('Submit') }}
                        </div>
                    </div><br>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection