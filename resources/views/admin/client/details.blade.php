@extends('layouts.app')

@section('content_header')
    <h1>SHADHIN JEWELRY TRADING LLC</h1>
@stop


@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="invoice p-3 mb-3">
                        <div id="print">
                            <div style="font-family: 'Cambria', serif !important;">
                                <div class="row" style="display: flex;align-items: center;">
                                    <div class="col-sm-4 col-4 invoice-col text-center"
                                        style="margin-top:10px; margin-bottom:10px;">
                                        <p style="font-size: 20px;text-align: left;">
                                            SHADHIN JEWELRY TRADING LLC
                                        </p>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-12" style="text-align:center; padding: 10px 10px;">
                                        <div class=""
                                            style="background-color: #dae6ed; padding: 20px; border-radius: 5rem;">
                                            <p style="font-size: 30px; display:contents;"><b>Client Statement </b></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-4 invoice-col text-right"
                                        style="margin-top:20px;margin-left:0;line-height:18px !important;">
                                        <!--<p style="font-size: 26px;"><b></b></p>-->
                                        {{--  --}}
                                        <p style="font-size: 18px;line-height:26px;">{{ $business->full_address }}<br>
                                            info@shadhinjewelry.com<br>
                                            +971 50 213 0553<br>
                                            www.shadhinportal.com

                                        </p>
                                    </div>
                                    <hr
                                        style="width:100%; height: 2px; text-align: left; margin-left: 0; background-color: #4f7893;">
                                </div>

                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-12">
                                        <table class="table table-striped table-bordered" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th
                                                        style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:12px;padding:2px 5px;">
                                                        Client Name</th>
                                                    <th
                                                        style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:12px;padding:2px 5px;">
                                                        Initial Amount</th>
                                                        <th
                                                        style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:12px;padding:2px 5px;">
                                                         Deposit </th>
                                                        <th
                                                        style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:12px;padding:2px 5px;">
                                                        Withdraw </th>
                                                    <th
                                                        style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:12px;padding:2px 5px;">
                                                        Total Fix</th>
                                                    <th
                                                        style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:12px;padding:2px 5px;">
                                                        Current Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td
                                                        style="border: 2px solid !important; text-align: center;font-size:19px;">
                                                        {{ $supplier->full_name }} </td>
                                                    <td
                                                        style="border: 2px solid !important; text-align: center;font-size:19px;">
                                                        {{ $supplier->init_balance ?? 0 }} </td>
                                                        <td
                                                        style="border: 2px solid !important; text-align: center;font-size:19px;">
                                                        {{ $supplier->deposit_amount ?? 0 }} </td>
                                                        <td
                                                        style="border: 2px solid !important; text-align: center;font-size:19px;">
                                                        {{ $supplier->withdraw_amount ?? 0 }} </td>
                                                    <td
                                                        style="border: 2px solid !important; text-align: center;font-size:19px;">
                                                        {{ $supplier->sell_amount ?? 0 }} </td>
                                                    <td
                                                        style="border: 2px solid !important; text-align: center;font-size:19px;">
                                                        {{ $supplier->init_balance + $supplier->deposit_amount - ($supplier->sell_amount+$supplier->withdraw_amount) }}
                                                    </td>
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
                                                    <th
                                                        style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:12px;padding:2px 5px;">
                                                        DateTime </th>
                                                    <th
                                                        style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:12px;padding:2px 5px;">
                                                        DocumentNo</th>
                                                    <th
                                                        style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:12px;padding:2px 5px;">
                                                        Perticuler</th>
                                                    <th
                                                        style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:12px;padding:2px 5px;">
                                                        Type </th>
                                                    <th
                                                        style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:12px;padding:2px 5px;">
                                                        Naration </th>
                                                    <th
                                                        style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:12px;padding:2px 5px;">
                                                        Debit </th>
                                                    <th
                                                        style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:12px;padding:2px 5px;">
                                                        Credit </th>
                                                    <th
                                                        style="border: 2px solid !important; text-align: center; background-color: #dae6ed !important; font-size:12px;padding:2px 5px;">
                                                        Balance </th>
                                                    <th
                                                        style="border: 2px solid !important; text-align: center; background-color: gold !important; font-size:12px;padding:2px 5px;">
                                                        Unfix </th>
                                                    <th
                                                        style="border: 2px solid !important; text-align: center; background-color: gold !important; font-size:12px;padding:2px 5px;">
                                                        Fixed </th>
                                                    <th
                                                        style="border: 2px solid !important; text-align: center; background-color: gold !important; font-size:12px;padding:2px 5px;">
                                                        GoldBalance </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $deposit = 0;
                                                    $costing = 0;
                                                    $gold_deposit = 0;
                                                    $gold_costing = 0;
                                                @endphp
                                                @if (count($accounts) > 0)

                                                    @foreach ($accounts as $row)
                                                        @if ($row->type != 'deposit')
                                                            @php
                                                                $costing +=
                                                                    $row->data->type != 'buy'
                                                                        ? $row->data->unfix_subtotal ?? 0
                                                                        : 0;
                                                                $gold_deposit +=
                                                                    $row->data->type == 'buy'
                                                                        ? $row->data->pure_quantity ?? 0
                                                                        : 0;
                                                                $gold_costing +=
                                                                    $row->data->type != 'buy'
                                                                        ? $row->data->pure_quantity ?? 0
                                                                        : 0;

                                                            @endphp
                                                            <tr>
                                                                <td
                                                                    style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                                    {{ date('d-M-Y', strtotime($row->data->purchase->created_at)) }}
                                                                </td>
                                                                <td
                                                                    style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                                    {{ $row->data->purchase->invoice_no }}</td>
                                                                <td
                                                                    style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                                    {{ $row->data->product_name }}</td>
                                                                <td
                                                                    style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                                    {{ $row->data->type == 'buy' ? 'Unfix' : 'Fixed' }}</td>
                                                                <td
                                                                    style="border: 2px solid !important; text-align: left; font-size:12px;padding:2px 5px;">
                                                                    {{ $row->data->purchase->note }}</td>
                                                                <td
                                                                    style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                                    {{ $row->data->type != 'buy' ? $row->data->unfix_subtotal : '--' }}
                                                               </td>
                                                                <td
                                                                    style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                               
                                                                    {{ $row->data->type == 'buy' ? '--' : '--' }}
                                                                    
                                                                <td
                                                                    style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                                    {{ number_format($deposit - $costing, 3) }}</td>
                                                                <td
                                                                    style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                                    {{ $row->data->type == 'buy' ? $row->data->pure_quantity : '--' }}
                                                                </td>
                                                                <td
                                                                    style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                                    {{ $row->data->type != 'buy' ? $row->data->pure_quantity : '--' }}
                                                                </td>

                                                                <td
                                                                    style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                                    {{ $gold_deposit - $gold_costing }} GMS</td>

                                                            </tr>
                                                        @else
                                                            @php
                                                                $deposit +=  $row->data->deposit_amount ?? 0;
                                                                $costing += $row->data->withdraw_amount ?? 0;
                                                            @endphp
                                                            <tr>
                                                                <td
                                                                    style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                                    {{ date('d-M-Y', strtotime($row->data->created_at)) }}
                                                                </td>
                                                                <td
                                                                    style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                                    {{ strtoupper(substr($row->data->type, 0, 3)) }}-{{ sprintf('%06d', $row->data->id) }}
                                                                </td>
                                                                <td
                                                                    style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                                    {{ $row->data->ref_no }}</td>
                                                                <td
                                                                    style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                                    {{ $row->data->type }}</td>
                                                                <td
                                                                    style="border: 2px solid !important; text-align: left; font-size:12px;padding:2px 5px;">
                                                                    {{ $row->data->note }}</td>
                                                                <td
                                                                    style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                                    {{ $row->data->withdraw_amount ?? '--' }}</td>
                                                                    <td
                                                                    style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                                    {{ $row->data->deposit_amount ?? '--' }} </td>
                                                             
                                                                <td
                                                                    style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                                    {{ number_format($deposit - $costing, 3) }}</td>
                                                                <td
                                                                    style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                                    --</td>
                                                                <td
                                                                    style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                                    --</td>
                                                                <td
                                                                    style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                                    {{ $gold_deposit - $gold_costing }} GMS</td>
                                                            </tr>
                                                        @endif
                                                        <!-- Display Balance -->
                                                    @endforeach
                                                @endif

                                                <tr>
                                                    <td style="border: 2px solid !important; text-align: right; font-size:12px;padding:2px 5px;"
                                                        colspan="5">TOTAL</td>
                                                    <td
                                                        style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                        {{ number_format($costing, 3) }}</td>
                                                        <td
                                                        style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                        {{ number_format($deposit, 3) }}</td>
                                                     <td
                                                        style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                        {{ number_format($deposit - $costing, 3) }}</td>
                                                    <td
                                                        style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                        {{ $gold_deposit }} </td>
                                                    <td
                                                        style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                        {{ $gold_costing }} </td>

                                                    <td
                                                        style="border: 2px solid !important; text-align: center; font-size:12px;padding:2px 5px;">
                                                        {{ $gold_deposit - $gold_costing }} GMS</td>

                                                </tr>
                                            </tbody>
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
