<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }

        h1,
        h2,
        h5 {
            color: #343a40;
        }

        .card {
            border: 1px solid #3b3c3c;
            border-radius: 8px;
            padding: 2px;
            background-color: #ffffff;
        }

        /* .header {
            background-color: #252525;
            color: white;
            padding: 10px;
            text-align: center;
        } */

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #c5c8c8 !important; /* Set the background color to #ededed */
            color: #3b3c3c;
            border: 1px solid #c4c4c4;

            padding: 3px;
            text-align: center;
            font-size: 0.85em;
            /* Smaller font size for table headers */
        }

        td {
            padding: 4px;
            /* Reduced padding for more compact appearance */
            border: 1px solid #c5c8c8;
            text-align: center;
            font-size: 0.85em;
            /* Smaller font size for table data */
        }

        th,
        td {
            font-size: 0.85em;
            /* Smaller font size for both headers and data */
        }

        .card-body p {
            margin-bottom: 5px;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f2f2f2;
        }

        .highlight {
            background-color: #f8f9fa;
        }

        .total-row {
            font-weight: bold;
            background-color: #f1f1f1;
        }

        .active-positions p,
        .info-row p {
            display: inline;
            margin: 0 5px;
            font-size: 0.8em;
            /* Smaller font size for specific text areas */
        }

        .table-responsive {
            margin-top: 0px;
            font-size: 0.85em;
            /* Smaller font for table content */
        }

        .footer {
            background-color: #252525;
            color: white;
            text-align: center;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        /* Reduce font size for active positions section */
        .col-md-12 h4 {
            font-size: 0.85em;
        }

        /* Styling for the "Active Positions" */
        .active-positions span {
            font-size: 0.8em;
            /* Smaller font size */
        }

        /* Styling for headings in the content */
        h2,
        h5 {
            font-size: 1em;
            /* Slightly smaller headings */
        }
        .table>:not(caption)>*>*
        {
            border: 1px solid #3b3c3c;
            background-color: #c5c8c8 !important;
        }
    </style>

</head>

<body>
    @if (isset($runningBuySell))
    <?php $runningTTB_profit = 0; ?>
    @foreach ($runningBuySell as $transaction)
        @php
            $ttb_qty = $transaction->tt_quantity - $transaction->close_quanntity;
            // Ensure numeric values
            $ttb_qty = is_numeric($ttb_qty) ? $ttb_qty : 0;
            $market_price = is_numeric($market_price) ? $market_price : 0;
            $transaction->current_rate = is_numeric($transaction->current_rate) ? $transaction->current_rate : 0;
            $transaction->service_charge = is_numeric($transaction->service_charge) ? $transaction->service_charge : 0;
        
            $current_value = $market_price * 13.7628 * $ttb_qty;
            $total_value = $transaction->current_rate * 13.7628 * $ttb_qty;
            $service_cost = $transaction->service_charge * 13.7628 * $ttb_qty;
        
            if ($transaction->type == 'buy') {
                $raw_profit = ($current_value - $total_value) - $service_cost;
            } else {
                $raw_profit = ($total_value - $current_value) - $service_cost;
            }
        
            $runningTTB_profit += $raw_profit;
            $profit_loss = number_format($raw_profit, 2);
        @endphp
    @endforeach
@endif
    
    
    <div class="row">
        <div class="col-md-12">
            
            <div class="header">
                <!--<h3 style="text-align: center;width: 270px;margin: 0 auto;margin-bottom: -50px;padding: 5px;border:1px solid #3b3c3c; border-radius: 25px"> Customer Statement</h1>-->
                <!--    <p style="margin:0px; padding: 0px;"><strong>Full Name:</strong> {{ $customer->name ?? 'N/A' }} ({{ $customer->customer_code }})-->
                <!--        <span style="text-align: right;float:right;"><strong>Market Price:</strong>-->
                <!--            {{ number_format($market_price, 2) }}-->
                <!--        </span>-->
                <!--    </p>-->
        
                <!--    <p><strong>Date: </strong>{{ request('startDate') ? date("d-M-Y",strtotime(request('startDate'))): date("d-M-Y",strtotime("NOW")) }}-->
                <!--            {{ request('endDate') ? ' to ' . date("d-M-Y",strtotime(request('endDate'))) : '' }}-->
                <!--        <span style="text-align: right;float:right;">-->
                <!--            <strong>Cut Position:</strong>-->
                <!--            @if ($value < 0)-->
                <!--            {{ $customer->cutposition }}-->
                <!--                (Sell)-->
                <!--            @elseif ($value > 0)-->
                <!--            {{ $customer->cutposition }}</strong>-->
                <!--                (Buy)-->
                <!--            @else-->
                <!--                0-->
                <!--            @endif-->
                <!--        </span>-->
                <!--    </p>-->
                    
                    <h3 style="text-align: center;width: 270px;margin: 0 auto;margin-bottom: -50px;padding: 5px;border:1px solid #3b3c3c; border-radius: 25px"> Customer Statement</h1>
                    <p style="margin:0px; padding: 0px;"><strong>Name:</strong> {{ $customer->name ?? 'N/A' }} ({{ $customer->customer_code }})
                        <span style="text-align: right;float:right;"><strong>Date: </strong>{{ request('startDate') ? date("d-M-Y",strtotime(request('startDate'))): date("d-M-Y",strtotime("NOW")) }}
                            {{ request('endDate') ? ' to ' . date("d-M-Y",strtotime(request('endDate'))) : '' }}
                        </span>
                    </p>
        
                    <p>
                        
                    </p>
                    
                    <div class="col-md-12" style="text-align: center;">
                            <p class="mt-5" style="font-weight: bold; font-size: 14px;"><span><span>Market Price: {{ number_format($market_price, 2) }}</span> |Buy Qty : {{ $sumBuy }}</span> | <span>Sell Qty:
                                    {{ $sumSell }}</span> | <span>Active Qty : {{ abs($value) }} @if ($value < 0)
                                        (Sell Position)
                                    @else
                                        ( Buy Position)
                                    @endif
                                </span> | <span>Total P/L : {{ number_format($runningTTB_profit, 2) }}</span> | 
                                
                                <span>
                                    Cut Position:
                                        @if ($value < 0)
                                            {{ $customer->cutposition }}
                                            (Sell)
                                        @elseif ($value > 0)
                                            {{ $customer->cutposition }}
                                            (Buy)
                                        @else
                                            0
                                        @endif
                                </span>
                            </span></p>
                        </div>
            </div>

            <div class="card">
                <div class="card-body" style="background: #fff;color:#000;">
                    <!--<div class="col-md-12" style="text-align: center;">-->
                    <!--        <p class="mt-5" style="font-weight: bold; font-size: 14px;"><span><span>Market Price: {{ number_format($market_price, 2) }}</span> |Buy Qty : {{ $sumBuy }}</span> | <span>Sell Qty:-->
                    <!--                {{ $sumSell }}</span> | <span>Active Qty : {{ abs($value) }} @if ($value < 0)-->
                    <!--                    (Sell Position)-->
                    <!--                @else-->
                    <!--                    ( Buy Position)-->
                    <!--                @endif-->
                    <!--            </span> | <span>Total P/L : {{ number_format($runningTTB_profit, 2) }}</span> | -->
                                
                    <!--            <span>-->
                    <!--                Cut Position:-->
                    <!--                    @if ($value < 0)-->
                    <!--                        {{ $customer->cutposition }}-->
                    <!--                        (Sell)-->
                    <!--                    @elseif ($value > 0)-->
                    <!--                        {{ $customer->cutposition }}-->
                    <!--                        (Buy)-->
                    <!--                    @else-->
                    <!--                        0-->
                    <!--                    @endif-->
                    <!--            </span>-->
                    <!--        </span></p>-->
                    <!--    </div>-->
                    
                    <h4 style="margin:0px; padding:0px;">Balance Information</h4>
                    <div class="table-responsive pt-0 mt-0">
                        <table class="table table-bordered table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    
                                    <th>Deposit</th>
                                    <th>Withdraw</th>
                                    <th>Profit-Loss</th>
                                    <th>Balance </th>
                                    <!--<th>Gold (Onz)</th>-->
                                    <th>Gold (TTB)</th>

                                    <th>Total Value ($)</th>
                                    <th>Total Value (AED)</th>
                                    <!--<th>Equity P&L ($)</th>-->
                                    <th>Equity (AED)</th>
                                    <!-- <th>Net Balance</th> -->
                                    <th>Withdrawable</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @php
                                        $profiLoss = $deposit + $withdraw + ($profit - $loss);
                                    @endphp
                                    
                                    <td>{{ $data['sum_of_deposit'], 2 }}</td>
                                    <td>{{ $data['sum_of_withdraw'], 2 }}</td>
                                    <td>{{ number_format((float) str_replace(',', '', $data['current_profit_loss']), 2) }}</td>
                                    <td>{{ number_format($data['current_balance'], 2) }}</td>
                                    <!--<td>{{ abs($value * 3.746) }}</td>-->
                                    <td>{{ abs($data['sum_of_running_buy_ttb'] - $data['sum_of_running_sell_ttb']) }} {{$data['sum_of_running_buy_ttb'] - $data['sum_of_running_sell_ttb'] < 0 ? 'Sell' : 'Buy'}}</td>
                                    <td> {{ $value != 0 ? number_format((($market_price) * abs($value)), 2):0 }}</td>
                                    <td> {{ $value != 0 ? number_format((($market_price) * abs($value) * 13.7628), 2):0 }} </td>

                                    @php
                                    $current_balance = $data['current_balance'];
                                    $sum_of_running_running_profit_loss = $data['sum_of_running_running_profit_loss'];
                                    $sum_of_running_service_charge = 1*($data['sum_of_running_buy_ttb'] + $data['sum_of_running_sell_ttb']) * 13.7628;
                                    $last_equity = $current_balance + $runningTTB_profit ;
                                    
                                    @endphp
                                    <!--<td>{{ is_numeric($equity) ? number_format($equity / 3.6715, 2) : '0' }}</td>-->
                                    <td>{{ number_format($last_equity, 2) }}</td>
                                    
                                    @php 
                                        $active_ttb = abs($data['sum_of_running_buy_ttb'] - $data['sum_of_running_sell_ttb']);
                                        $equity = $data['equity'] - ($data['sum_of_running_service_charge'] * 13.7628);
                                        
                                        $margin_gap = $active_ttb != 0 
                                            ? $equity / $active_ttb 
                                            : 0;
                                    
                                        $withdraw = $value != 0 
                                            ? number_format($equity - $margin_gap, 2) 
                                            : 0;
                                    @endphp
                                    <td>{{ $withdraw }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <h4 class="mt-5" style="margin-bottom:0px; padding:0px;">Active Positions</h4>
                        </div>
                    </div>
                    <table class="table table-striped table-bordered">

                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Reference No</th>
                                <th scope="col">Date</th>
                                <th scope="col">Type</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Open Rate</th>
                                <th scope="col">Total Value (AED)</th>
                                <th scope="col">Service Charge (AED)</th>
                                <th scope="col">Current Value (AED)</th>
                                <th scope="col">Profit/Loss (AED)</th>
                              
                            </tr>
                        </thead>
                       

                        <tbody id="table1" class="collapse show">
                            @if (isset($runningBuySell))
                                <?php $sl = 1; ?>
                                <?php $runningTTB = 0; ?>
                                <?php $runningSellTTB = 0; ?>
                                <?php $runningBuyTTB = 0; ?>
                                <?php $runningTTB_profit_loss = 0; ?>
                                @foreach ($runningBuySell as $transaction)
                                    <tr>
                                        <th scope="row">{{ $sl++ }}</th>
                                        <td>{{ $transaction->reference_no }}</td>
                                        <td>{{ $transaction->created_at }}</td>
                                        <td>{{ $transaction->type }}</td>
                                        <td>{{ $transaction->tt_quantity - $transaction->close_quanntity }}</td>
                                        <td id="current_rate-{{ $sl }}">{{ number_format($transaction->current_rate, 2) }}</td>
                                        <td id="oldbalance-{{ $sl }}" style="text-align: center;">
                                            {{ number_format($transaction->current_rate * 13.7628 * ($transaction->tt_quantity - $transaction->close_quanntity), 2) }}
                                        </td>
                                        
                                        <td id="service_charge-{{ $sl }}" style="text-align: center;">
                                            {{ number_format(($transaction->tt_quantity - $transaction->close_quanntity) * ($transaction->service_charge * 13.7628), 2) }}</td>

                                        
                                        <td style="text-align: center;">{{ number_format($market_price * 13.7628 * ($transaction->tt_quantity - $transaction->close_quanntity), 2) }}
                                        </td>
                                        
                                        @php
                                            $ttb_qty = $transaction->tt_quantity - $transaction->close_quanntity;
                                        
                                            // Ensure numeric values
                                            $ttb_qty = is_numeric($ttb_qty) ? $ttb_qty : 0;
                                            $market_price = is_numeric($market_price) ? $market_price : 0;
                                            $transaction->current_rate = is_numeric($transaction->current_rate) ? $transaction->current_rate : 0;
                                            $transaction->service_charge = is_numeric($transaction->service_charge) ? $transaction->service_charge : 0;
                                        
                                            $current_value = $market_price * 13.7628 * $ttb_qty;
                                            $total_value = $transaction->current_rate * 13.7628 * $ttb_qty;
                                            $service_cost = $transaction->service_charge * 13.7628 * $ttb_qty;
                                        
                                            if ($transaction->type == 'buy') {
                                                $raw_profit = ($current_value - $total_value) - $service_cost;
                                            } else {
                                                $raw_profit = ($total_value - $current_value) - $service_cost;
                                            }
                                        
                                            $runningTTB_profit_loss += $raw_profit;
                                            $profit_loss = number_format($raw_profit, 2);
                                        @endphp

                                        <td id="balance-{{ $sl }}" style="text-align: center;">
                                            {{ $profit_loss }}
                                        </td>
                                    </tr>
                                @endforeach
                                
                                <tr>
                                        <td colspan='9' class="text-right">Total P/L: </td>
                                        <td style="text-align: center;">
                                           {{number_format($runningTTB_profit_loss,2)}}
                                        </td>
                                    </tr>
                            @endif

                        </tbody>

                    </table>

                    <!-- Profit and Loss Summary Table -->
                    <h4 class="mt-4" style="margin-bottom:0px; padding:0px;">Profit and Loss Summary</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">

                            <thead class="thead-dark">
                                <tr>
                                    <th>SL</th>
                                    <th>Item Code</th>
                                    <th>Trade Type</th>
                                    <th>Date</th>
                                    <th>Gold Qty</th>
                                    <th>B/S</th>
                                    <th>Rate</th>
                                    <th>Date</th>
                                    <th>B/S</th>
                                    <th>Rate</th>
                                    <th>P/L AED</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($net_matched->isNotEmpty())
                                    @php
                                        $i = 1;
                                        $total_profit_or_loss = 0;
                                    @endphp
                                    @foreach ($net_matched as $detail)
                                        @php $total_profit_or_loss += (float) $detail->transaction_amount; @endphp
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>TTB</td>
                                            <td>{{ ucwords(str_replace('_', ' ', $detail->trade_type)) }}</td>
                                            <td>{{ date('d-M-Y', strtotime($detail->created_at)) }}</td>
                                            <td>{{ $detail->quantity }}</td>
                                            <td>{{ $detail->transaction_type == 'sell' ? 'Sell' : 'Buy' }}</td>
                                            <td>{{ number_format($detail->starting_rate, 2) }}</td>
                                            <td>{{ date('d-M-Y', strtotime($detail->transaction_date)) }}</td>
                                            <td>{{ $detail->transaction_type == 'buy' ? 'Sell' : 'Buy' }}</td>
                                            <td>{{ number_format($detail->current_rate, 2) }}</td>
                                            <td style="text-align: right;padding-right: 25px;">
                                                {{ number_format($detail->transaction_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-active">
                                        <td colspan="10"><strong>Total Profit/Loss</strong></td>
                                        <td style="text-align: right;padding-right: 25px;"><strong>AED
                                                {{ number_format($total_profit_or_loss, 2) }}</strong></td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="10" class="text-center">No transactions found.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    @if (isset($pending) && count($pending) > 0)


                        <h4 class="mt-4" style="margin-bottom:0px; padding:0px;">Pending</h4>

                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-sm">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">TTB Qty</th>
                                        <th scope="col">Limit</th>
                                        <th scope="col">Stop</th>

                                    </tr>
                                </thead>

                                <tbody id="table1" class="collapse show">
                                    <?php $sl = 1; ?>
                                    @foreach ($pending as $pen)
                                        <tr>
                                            <th scope="row">{{ $sl++ }}</th>
                                            <td>{{ $pen->created_at }}</td>
                                            <td>{{ $pen->type }}</td>
                                            <td>{{ $pen->tt }}</td>
                                            <td>{{ number_format($pen->limit, 2) }}</td>
                                            <td>{{ number_format($pen->stop_loss, 2) }}</td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif



                </div>
            </div>
        </div>
    </div>

    
</body>

</html>
