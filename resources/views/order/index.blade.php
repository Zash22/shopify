@extends('layouts.app')


@section('content')
    <style>
        th {
            text-align: center !important;
        }
    </style>
    <div class="container-flow">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-primary">
                    <div class="panel-heading">Orders</div>




                        @if(count($yolos) > 0)

                            <div class="panel panel-success">
                                <div class="panel-heading">
                                    All Orders
                                </div>
                                <div class="panel-body" style="padding:0 !important">
                                    <table class="table table-condensed table-striped" style="text-align:center !important">
                                        <thead>
                                        <tr>
                                            <th>Order Date</th>
                                            <th>Client</th>
                                            <th>Financial status</th>
                                            <th>Fufillment status</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($yolos as $yolo)
                                            <tr >
                                                <td>{{ $yolo->order->created_at }}</td>
                                                <td>{{ $yolo->order->customer->email}}</td>
                                                <td>{{ $yolo->order->financial_status }}</td>
                                                <td>{{ $yolo->order->fulfillment_status }}</td>
                                                <td>
                                                    {!! link_to_route('order.show', 'View', $yolo->order->id ,['class' => 'btn-sm btn-primary']) !!}
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>

                                @else
                                    <p>
                                    <div>No results available.</div>
                                    </p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
