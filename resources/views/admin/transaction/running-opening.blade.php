@extends('layouts.master')
<style>
  .table td, .table th {
    padding: .25rem !important;
    font-size: .85rem !important;
  }
</style>

@section('content')

    <ul class="breadcrumb breadcrumb-style" style="padding: .5rem .5rem; margin-bottom: 0rem;">
        <li class="breadcrumb-item">
            <a href="https://furqanjewelry.com/dashboard">
                <i class="fas fa-home"></i>
            </a>
        </li>
        <li class="breadcrumb-item">
            <span>Running Trade List</span>
        </li>
    </ul>
    
    
    
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 ">

            <div class="row transactionContainer">
                @foreach ($details as $key => $detail)
                    <div class="col-md-3 mt-2">
                        <div
                            class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <p class="font-weight-bold">{{ $key }}:</p>
                                    <p class="font-weight-bold">{{ $detail }}</p> 
                                </div>
                                <!--<div class="card-body">-->
                                <!--    <strong></strong>-->
                                <!--</div>-->
                        </div>
                    </div>
                @endforeach
                
                <div class="col-md-3 mt-2">
                    <div
                        class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <p class="font-weight-bold">Total TTB (Buy):</p>
                                <p class="font-weight-bold">{{ $buyDetails['total_tt'] }}</p> 
                            </div>
                            <!--<div class="card-body">-->
                            <!--    <strong>AVG Price:</strong> {{ $buyDetails['avg'] }}-->
                            <!--</div>-->
                    </div>
                </div>
                
                <div class="col-md-3 mt-2">
                    <div
                        class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <p class="font-weight-bold">Total TTB (Sell):</p>
                            <p class="font-weight-bold">{{ $sellDetails['total_tt'] }}</p>
                        </div>
                        <!--<div class="card-body">-->
                        <!--    <strong>AVG Price:</strong> {{ $sellDetails['avg'] }}-->
                        <!--</div>-->
                    </div>
                </div>

            </div>


            <div class="row">
                <div class="col-md-12">
                    <div class="card ">
                        <div class="card-header">
                            <h5>Running Trade List</h5>
                        </div>

                        <div class="card-body">
                           <div class="table-responsive">
                                <table class="table table-striped table-hover" id="example" style="width: 100%">
                                    <thead class="thead-dark text-center">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Customer Name</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">Type</th>
                                            <th scope="col">TT Quantity</th>
                                            <th scope="col">Starting Rate</th>
                                            <th scope="col">Total Value (AED)</th>
                                            <!--<th scope="col">Profit/Loss</th>-->
                                            <!--<th scope="col">Cut Position</th>-->
                                        </tr>
                                    </thead>
                                    
                                    <tbody class="text-center">
                                        @foreach ($transactions as $i => $transaction)
                                            <tr data-transaction-id="">
                                            <th scope="row">{{$i + 1}}</th>
                                            <td>{{$transaction->customer->name}}</td>
                                            <td>{{$transaction->created_at}}</td>
                                            <td>{{$transaction->type}}</td>
                                            <td>{{$transaction->tt_quantity - $transaction->close_quanntity}}</td>
                                            <td>{{$transaction->current_rate}}</td>
                                            <td>{{$transaction->current_rate * ($transaction->tt_quantity - $transaction->close_quanntity) * 13.7628}}</td>
                                            <!--<td class="profit-loss"></td>-->
                                            <!--<td class="cut-position"></td>-->
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
    </div>

@endsection


@section('page_js')

    <script>
        $(document).ready(function() {
            $('#example').DataTable({
                responsive: true,
                pageLength: 10,
                lengthChange: true
            });
        });
    </script>
@endsection
