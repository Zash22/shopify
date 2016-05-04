@extends('layouts.app')

@section('content')
    <div class="container spark-screen">
        <div class="row">
            <div class="col-md-8 col-md-offset-1">

                {!! Form::open(['method' => 'POST', 'action' => 'PreferenceController@store', 'class'=>'form-horizontal']) !!}

                {{ Form::hidden('shop', $_GET['shop'], array('id' => 'shop')) }}


                <div class="panel panel-default">
                    <div class="panel-heading">MDS Login Details</div>
                    <div class="panel-body">
                        <div class="form-group">
                            {!! Form::label('mds_user', 'MDS Login User name', ['class'=>'col-md-4 control-label']) !!}
                            <div class="col-md-6">
                                {!! Form::text('mds_user', null, ['class'=>'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('mds_pass', 'MDS Password', ['class'=>'col-md-4 control-label']) !!}
                            <div class="col-md-6">
                                {!! Form::text('mds_pass', null, ['class'=>'form-control']) !!}
                            </div>
                        </div>
                    </div>
                </div>


                <div class="form-group ">
                    {!! Form::label('risk', 'risk cover enabled?', ['class'=>'col-md-3 control-label']) !!}
                    <div class="col-md-6">
                        {!! Form::radio('risk', null, true,['class'=>'form-control']) !!}

                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Overnight Before 10:00</div>
                    <div class="panel-body">
                        <div class="form-group">
                            {!! Form::label('display_1', 'Display Overnight Before 10:00 as', ['class'=>'col-md-4 control-label']) !!}
                            <div class="col-md-6">
                                {!! Form::text('display_1', null, ['class'=>'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('markup_1', 'Markup %', ['class'=>'col-md-4 control-label']) !!}
                            <div class="col-md-6">
                                {!! Form::text('markup_1', null, ['class'=>'form-control']) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Overnight Before 16:00</div>
                    <div class="panel-body">
                        <div class="form-group">
                            {!! Form::label('display_2', 'Display Overnight Before 16:00 as', ['class'=>'col-md-4 control-label']) !!}
                            <div class="col-md-6">
                                {!! Form::text('display_2',  null, ['class'=>'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('markup_2', 'Markup %', ['class'=>'col-md-4 control-label']) !!}
                            <div class="col-md-6">
                                {!! Form::text('markup_2', null, ['class'=>'form-control']) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Road Freight</div>
                    <div class="panel-body">
                        <div class="form-group">
                            {!! Form::label('display_3', 'Display Road Freight Express', ['class'=>'col-md-4 control-label']) !!}
                            <div class="col-md-6">
                                {!! Form::text('display_3',  null, ['class'=>'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('markup_3', 'Markup %', ['class'=>'col-md-4 control-label']) !!}
                            <div class="col-md-6">
                                {!! Form::text('markup_3', null, ['class'=>'form-control']) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Road Freight Express</div>
                    <div class="panel-body">
                        <div class="form-group">
                            {!! Form::label('display_5', 'Display Road Freight', ['class'=>'col-md-4 control-label']) !!}
                            <div class="col-md-6">
                                {!! Form::text('display_5', null, ['class'=>'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('markup_5', 'Markup %', ['class'=>'col-md-4 control-label']) !!}
                            <div class="col-md-6">
                                {!! Form::text('markup_5', null, ['class'=>'form-control']) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary col-md-offset-4">
                    Save
                </button>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
@endsection
