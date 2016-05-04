@extends('layouts.app')

@section('content')

    <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-primary">
            <div class="panel-heading">Order {!! $order->order->id !!}</div>
            <div class="panel-body">
                @include('flash::message')

                <table class="table table-condensed table-striped">

                    <tr >
                        <td class="col-md-6">ID</td>
                        <td>{!! $order->order->id !!}</td>
                    </tr>
                    <tr >
                        <td>Status</td>
                    </tr>

                    <tr >
                        <td>User</td>
                        <td>{!! $order->order->customer->email !!}</td>
                    </tr>

                    {!! Form::model($order, ['route'=>['order.update', $order->order->id], 'method' => 'PUT',
                             'class'=>'form-horizontal']) !!}
                    <div class="form-group">
                        {!! Form::label('order_id', 'Registration', ['class'=>'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::text('RegNo', null, ['class'=>'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('Notes', 'Notes', ['class'=>'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::text('Notes', null, ['class'=>'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary col-md-offset-4">
                            Update
                        </button>

                        <a href="/Order" class="btn btn-primary ">
                            Back
                        </a>
                    </div>
                    </table>

            </div>
<div>
        </div>
@endsection