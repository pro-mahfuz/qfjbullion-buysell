@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12 col-12">
        <div class="card">
            <div class="card-body">
                <div class="invoice p-3 mb-3">
                    <div id="print">

                        <div style="font-family: 'Cambria', serif !important;">
                            <div class="row" style="display: flex;align-items: center;">

                                <div class="col-sm-5 col-md-5 col-12" style="text-align:center; padding: 10px 10px;">
                                    <div class="" style="background-color: #dae6ed; padding: 20px; border-radius: 5rem;">
                                        <p style="font-size: 30px; display:contents;"><b>{{ $supplier->full_name }} </b></p>
                                    </div>
                                </div>
                                <div class="col-sm-7 col-7 invoice-col text-right">
                                    <p style="text-align: right;">{{ $supplier->mobile_number }}</p>
                                    <p style="text-align: right;">{{ $supplier->email }}</p>
                                    <p style="text-align: right;">{{ $supplier->trn_no }}</p>
                                </div>
                                <hr style="width:100%; height: 2px; text-align: left; margin-left: 0; background-color: #4f7893;">
                            </div>

                            <div class="row">

                                <div class="col-sm-5 col-md-5 col-5">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3>DEPOSIT <span class="pull-right"> {{ number_format(($supplier->init_balance +  $supplier->deposit_amount),2)   }} AED </span></h3>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">Deposit <span class="pull-right">2323 Gm</span></h5>
                                            <h5 class="card-title">Premium <span class="pull-right">2323 Gm</span></h5>
                                            <h5 class="card-title">Widthdraw <span class="pull-right">2323 Gm</span></h5>

                                            <a href="#" class="btn btn-primary">View Details</a>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-sm-5 col-md-5 col-5">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3>GOLD <span class="pull-right"> {{ number_format(($supplier->init_balance +  $supplier->deposit_amount),2)   }} AED </span></h3>
                                        </div>
                                        <div class="card-body">
                                        <h5 class="card-title">Unfix <span class="pull-right">2323 Gm</span></h5>
                                        <h5 class="card-title">Fixed <span class="pull-right">2323 Gm</span></h5>
                                        <h5 class="card-title">Balance <span class="pull-right">2323 Gm</span></h5>
                                        <a href="#" class="btn btn-primary">View Details</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2 col-md-2 col-2">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3>Action</h3>
                                        </div>
                                        <div class="card-body">

                                        <a href="#" class="btn btn-primary" style="width: 100%;">View Details</a>
                                        <a href="#" class="btn btn-primary" style="width: 100%;">View Details</a>
                                        <a href="#" class="btn btn-primary" style="width: 100%;">View Details</a>
                                        <a href="#" class="btn btn-primary" style="width: 100%;">View Details</a>
                                        </div>
                                    </div>
                                </div>
                                <table class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:19px;">Supplier Name</th>
                                            <th style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:19px;">Initial Amount</th>
                                            <th style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:19px;">Total Deposit </th>
                                            <th style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:19px;">Total Fix</th>
                                            <th style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:19px;">Current Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="border: 2px solid !important; text-align: center;font-size:19px;">{{ $supplier->full_name }} </td>
                                            <td style="border: 2px solid !important; text-align: center;font-size:19px;">{{ $supplier->init_balance??0 }} </td>
                                            <td style="border: 2px solid !important; text-align: center;font-size:19px;">{{ $supplier->deposit_amount??0 }} </td>
                                            <td style="border: 2px solid !important; text-align: center;font-size:19px;">{{ $supplier->sell_amount??0 }} </td>
                                            <td style="border: 2px solid !important; text-align: center;font-size:19px;">{{ ($supplier->init_balance +  $supplier->deposit_amount) -  $supplier->sell_amount   }} </td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-12">
                                <table class="table table-striped table-bordered" style="width:100%">

                                    <thead>
                                        <tr>
                                            <th style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:19px;">Date Time </th>
                                            <th style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:19px;">Doc No</th>
                                            <th style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:19px;">Perticuler</th>
                                            <th style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:19px;">Type </th>
                                            <th style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:19px;">Naration </th>
                                            <th style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:19px;">Debit </th>
                                            <th style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:19px;">Credit </th>
                                            <th style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:19px;">Balance </th>
                                            <th style="border: 2px solid !important; text-align: center; background-color: gold !important; font-size:19px;">Gold Unfix </th>
                                            <th style="border: 2px solid !important; text-align: center; background-color: gold !important; font-size:19px;">Gold Fixed </th>
                                            <th style="border: 2px solid !important; text-align: center; background-color: gold !important; font-size:19px;">Pure Quantity </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $deposit = 0;
                                        $costing = 0;
                                        $gold_deposit = 0;
                                        $gold_costing = 0;
                                        @endphp
                                        @if(count($accounts) > 0)

                                        @foreach($accounts as $row)
                                        @if($row->type != "deposit")
                                        @php
                                        $costing += $row->data->type != "buy" ? ($row->data->unfix_subtotal ?? 0) : 0;
                                        $gold_deposit += $row->data->type == "buy" ? ($row->data->pure_quantity ?? 0):0;
                                        $gold_costing += $row->data->type != "buy" ? ($row->data->pure_quantity ?? 0):0;

                                        @endphp
                                        <tr>
                                            <td style="border: 2px solid !important; text-align: center; font-size:15px;">{{ date("d/M/Y H:i",strtotime($row->data->purchase->created_at)) }} </td>
                                            <td style="border: 2px solid !important; text-align: center; font-size:15px;">{{ $row->data->purchase->invoice_no }}</td>
                                            <td style="border: 2px solid !important; text-align: center; font-size:15px;">{{ $row->data->product_name }}</td>
                                            <td style="border: 2px solid !important; text-align: center; font-size:15px;">{{ $row->data->type == "buy" ? "Unfix" : "Fixed" }}</td>
                                            <td style="border: 2px solid !important; text-align: center; font-size:15px;">{{ $row->data->purchase->note }}</td>
                                            <td style="border: 2px solid !important; text-align: center; font-size:15px;">{{ $row->data->type == "buy" ?"--":"--" }}</td>
                                            <td style="border: 2px solid !important; text-align: center; font-size:15px;">{{ $row->data->type != "buy" ? $row->data->unfix_subtotal : "--" }}</td>
                                            <td style="border: 2px solid !important; text-align: center; font-size:17px;">{{ number_format(($deposit - $costing), 2) }}</td>
                                            <td style="border: 2px solid !important; text-align: center; font-size:17px;">{{ $row->data->type == "buy" ? $row->data->pure_quantity : "--" }} </td>
                                            <td style="border: 2px solid !important; text-align: center; font-size:17px;">{{ $row->data->type != "buy" ? $row->data->pure_quantity : "--" }} </td>

                                            <td style="border: 2px solid !important; text-align: center; font-size:17px;">{{ $gold_deposit - $gold_costing }} GMS</td>

                                        </tr>
                                        @else
                                        @php
                                        $deposit += $row->data->deposit_amount ?? 0;
                                        $costing += $row->data->withdraw_amount ?? 0;
                                        @endphp
                                        <tr>
                                            <td style="border: 2px solid !important; text-align: center; font-size:15px;">{{ date("d/M/Y H:i",strtotime($row->data->created_at)) }} </td>
                                            <td style="border: 2px solid !important; text-align: center; font-size:15px;">{{ strtoupper(substr($row->data->type,0,3)) }}-{{ sprintf('%06d', $row->data->id ) }}</td>
                                            <td style="border: 2px solid !important; text-align: center; font-size:15px;">{{ $row->data->ref_no }}</td>
                                            <td style="border: 2px solid !important; text-align: center; font-size:15px;">{{ $row->data->type }}</td>
                                            <td style="border: 2px solid !important; text-align: center; font-size:15px;">{{ $row->data->note }}</td>
                                            <td style="border: 2px solid !important; text-align: center; font-size:15px;">{{ $row->data->deposit_amount??"--" }} </td>
                                            <td style="border: 2px solid !important; text-align: center; font-size:15px;">{{ $row->data->withdraw_amount ?? "--" }}</td>
                                            <td style="border: 2px solid !important; text-align: center; font-size:17px;">{{ number_format(($deposit - $costing), 2) }}</td>
                                            <td style="border: 2px solid !important; text-align: center; font-size:17px;">--</td>
                                            <td style="border: 2px solid !important; text-align: center; font-size:17px;">--</td>
                                            <td style="border: 2px solid !important; text-align: center; font-size:17px;">{{ $gold_deposit - $gold_costing }} Gram</td>
                                        </tr>


                                        @endif
                                        <!-- Display Balance -->
                                        @endforeach
                                        @endif

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td style="border: 2px solid !important; text-align: right; font-size:19px;" colspan="5">TOTAL</td>
                                            <td style="border: 2px solid !important; text-align: center; font-size:15px;">{{ number_format(($deposit), 2) }}</td>
                                            <td style="border: 2px solid !important; text-align: center; font-size:15px;">{{ number_format(($costing), 2) }}</td>
                                            <td style="border: 2px solid !important; text-align: center; font-size:17px;">{{ number_format(($deposit - $costing), 2) }}</td>
                                            <td style="border: 2px solid !important; text-align: center; font-size:17px;">{{ $gold_deposit }} </td>
                                            <td style="border: 2px solid !important; text-align: center; font-size:17px;">{{ $gold_costing }} </td>

                                            <td style="border: 2px solid !important; text-align: center; font-size:17px;">{{ $gold_deposit - $gold_costing }} GMS</td>

                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div><br><br>



                    </div>


                </div>

            </div>
        </div>
    </div>
</div>
</div>
@stop
