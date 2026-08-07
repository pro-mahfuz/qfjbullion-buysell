<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        @page { margin: 20px 22px 30px; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #1f2937;
            font-size: 9px;
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
        .report-header { padding-bottom: 10px; margin-bottom: 6px; }
        .report-title { color: #102a43; font-size: 20px; font-weight: bold; margin: 0; }
        .report-subtitle { color: #667085; font-size: 9px; margin: 4px 0 0; }
        .meta-table { width: 100%; margin-top: 10px; border-collapse: collapse; }
        .meta-table td { border: 0; padding: 3px 0; text-align: left; font-size: 9px; }
        .meta-table .label { color: #667085; font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .meta-table .right { text-align: right; }
        .snapshot-table { width: 100%; border-collapse: separate; border-spacing: 5px; margin: 8px -5px 10px; }
        .snapshot-table td { width: 25%; background: #f3f7fa; border: 1px solid #d9e4ea; padding: 7px; text-align: left; }
        .snapshot-table .label { display: block; color: #667085; font-size: 7px; font-weight: bold; text-transform: uppercase; }
        .snapshot-table .value { display: block; color: #102a43; font-size: 11px; font-weight: bold; margin-top: 3px; }
        .section-title { color: #102a43; font-size: 11px; font-weight: bold; margin: 13px 0 4px; padding-bottom: 3px; border-bottom: 1px solid #cbd5e1; }
        .summary-table th { background: #1f5f74 !important; color: #fff; border-color: #1f5f74; font-size: 7px; }
        .summary-table td { font-size: 8px; }
        .data-table th { background: #eaf0f4 !important; color: #344054; border-color: #cbd5e1; font-size: 7px; }
        .data-table.closed-trades-table th { background: #1f6fb2 !important; color: #ffffff !important; border-color: #1f6fb2 !important; }
        .data-table td { font-size: 8px; }
        .total-row td { background: #eef7f3; }
        .report-footer { position: fixed; bottom: -18px; left: 0; right: 0; color: #667085; font-size: 7px; text-align: center; }
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
            $swap_charge = is_numeric($transaction->swap_charge ?? null) ? $transaction->swap_charge : 0;
        
            $current_value = $market_price * 13.7639 * $ttb_qty;
            $service_cost = $transaction->service_charge * 13.7639 * $ttb_qty;
            $chargeDirection = $transaction->type === 'sell' ? -1 : 1;
            $total_value = ($transaction->current_rate * 13.7639 * $ttb_qty) + ($chargeDirection * ($service_cost + $swap_charge));
        
            if ($transaction->type == 'buy') {
                $raw_profit = $current_value - $total_value;
            } else {
                $raw_profit = $total_value - $current_value;
            }
        
            $runningTTB_profit += $raw_profit;
            $profit_loss = number_format($raw_profit, 3);
        @endphp
    @endforeach
@endif
    
    
    <div class="row">
        <div class="col-md-12">
            
            <div class="report-header">
                <!--<h3 style="text-align: center;width: 270px;margin: 0 auto;margin-bottom: -50px;padding: 5px;border:1px solid #3b3c3c; border-radius: 25px"> Customer Statement</h1>-->
                <!--    <p style="margin:0px; padding: 0px;"><strong>Full Name:</strong> {{ $customer->name ?? 'N/A' }} ({{ $customer->customer_code }})-->
                <!--        <span style="text-align: right;float:right;"><strong>Market Price:</strong>-->
                <!--            {{ number_format($market_price, 3) }}-->
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
                    
                    @php
                        $statementStart = request('start_date', request('startDate'));
                        $statementEnd = request('end_date', request('endDate'));
                        $positionLabel = $value < 0 ? 'Sell' : ($value > 0 ? 'Buy' : 'Flat');
                    @endphp
                    <h1 class="report-title">Estimate</h1>
                    <p class="report-subtitle">Account activity and open-position valuation</p>
                    <table class="meta-table">
                        <tr>
                            <td><span class="label">Customer</span><br><strong>{{ $customer->name ?? 'N/A' }} ({{ $customer->customer_code }})</strong></td>
                            <td class="right"><span class="label">Statement period</span><br><strong>{{ $statementStart ? date('d M Y', strtotime($statementStart)) : 'All activity' }}{{ $statementEnd ? ' — ' . date('d M Y', strtotime($statementEnd)) : '' }}</strong></td>
                        </tr>
                    </table>
                    <table class="snapshot-table">
                        <tr>
                            <td><span class="label">Market price</span><span class="value">$ {{ number_format($market_price, 3) }}</span></td>
                            <td><span class="label">Net position</span><span class="value">{{ abs($value) }} TTB {{ $positionLabel }}</span></td>
                            <td><span class="label">Open P/L</span><span class="value">AED {{ number_format($runningTTB_profit, 3) }}</span></td>
                            <td><span class="label">Margin Limit</span><span class="value">{{ $marginLimit == 0 ? '0' : number_format($marginLimit, 3) . ' (' . $marginPosition . ')' }}</span></td>
                        </tr>
                    </table>
            </div>

            <div class="card">
                <div class="card-body" style="background: #fff;color:#000;">
                    <!--<div class="col-md-12" style="text-align: center;">-->
                    <!--        <p class="mt-5" style="font-weight: bold; font-size: 14px;"><span><span>Market Price: {{ number_format($market_price, 3) }}</span> |Buy Qty : {{ $sumBuy }}</span> | <span>Sell Qty:-->
                    <!--                {{ $sumSell }}</span> | <span>Active Qty : {{ abs($value) }} @if ($value < 0)-->
                    <!--                    (Sell Position)-->
                    <!--                @else-->
                    <!--                    ( Buy Position)-->
                    <!--                @endif-->
                    <!--            </span> | <span>Total P/L : {{ number_format($runningTTB_profit, 3) }}</span> | -->
                                
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
                    
                    <h4 class="section-title">Account overview</h4>
                    <div class="table-responsive pt-0 mt-0">
                        <table class="table table-bordered table-sm summary-table">
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
                                    <td>{{ number_format($corrected_realised_profit_loss ?? (float) str_replace(',', '', $data['current_profit_loss']), 3) }}</td>
                                    <td>{{ number_format($corrected_balance ?? $data['current_balance'], 3) }}</td>
                                    <!--<td>{{ abs($value * 3.746) }}</td>-->
                                    <td>{{ abs($data['sum_of_running_buy_ttb'] - $data['sum_of_running_sell_ttb']) }} {{$data['sum_of_running_buy_ttb'] - $data['sum_of_running_sell_ttb'] < 0 ? 'Sell' : 'Buy'}}</td>
                                    <td> {{ $value != 0 ? number_format((($market_price) * abs($value)), 3):0 }}</td>
                                    <td> {{ $value != 0 ? number_format((($market_price) * abs($value) * 13.7639), 3):0 }} </td>

                                    @php
                                    $current_balance = $data['current_balance'];
                                    $sum_of_running_running_profit_loss = $data['sum_of_running_running_profit_loss'];
                                    $sum_of_running_service_charge = 1*($data['sum_of_running_buy_ttb'] + $data['sum_of_running_sell_ttb']) * 13.7639;
                                    $last_equity = $corrected_equity ?? ($current_balance + $runningTTB_profit);
                                    
                                    @endphp
                                    <!--<td>{{ is_numeric($equity) ? number_format($equity / 3.6715, 3) : '0' }}</td>-->
                                    <td>{{ number_format($last_equity, 3) }}</td>
                                    
                                    @php 
                                        $active_ttb = abs($data['sum_of_running_buy_ttb'] - $data['sum_of_running_sell_ttb']);
                                        $equity = $last_equity;
                                        
                                        $margin_gap = $active_ttb != 0 
                                            ? $equity / $active_ttb 
                                            : 0;
                                    
                                        $withdraw = $value != 0 
                                            ? number_format($equity - $margin_gap, 3)
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
                            <h4 class="section-title">Active positions</h4>
                        </div>
                    </div>
                    <table class="table table-striped table-bordered data-table">

                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Reference No</th>
                                <th scope="col">Date</th>
                                <th scope="col">Type</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Open Rate</th>
                                @if ($show_service_charge ?? true)
                                    <th scope="col">Service Charge (AED)</th>
                                @endif
                                <th scope="col">Swap Charge (AED)</th>
                                <th scope="col">Total Value (AED)</th>
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
                                        <td id="current_rate-{{ $sl }}">{{ number_format($transaction->current_rate, 3) }}</td>
                                        @if ($show_service_charge ?? true)
                                            <td style="text-align: center;">{{ number_format(($transaction->tt_quantity - $transaction->close_quanntity) * ($transaction->service_charge * 13.7639), 3) }}</td>
                                        @endif

                                        <td style="text-align: center;">{{ number_format($transaction->swap_charge ?? 0, 3) }}</td>

                                        <td id="oldbalance-{{ $sl }}" style="text-align: center;">
                                            {{ number_format(
                                                ($transaction->current_rate * 13.7639 * ($transaction->tt_quantity - $transaction->close_quanntity))
                                                + (($transaction->type === 'sell' ? -1 : 1) * ((($transaction->tt_quantity - $transaction->close_quanntity) * ($transaction->service_charge * 13.7639)) + ($transaction->swap_charge ?? 0))),
                                                3
                                            ) }}
                                        </td>

                                        
                                        <td style="text-align: center;">{{ number_format($market_price * 13.7639 * ($transaction->tt_quantity - $transaction->close_quanntity), 3) }}
                                        </td>
                                        
                                        @php
                                            $ttb_qty = $transaction->tt_quantity - $transaction->close_quanntity;
                                        
                                            // Ensure numeric values
                                            $ttb_qty = is_numeric($ttb_qty) ? $ttb_qty : 0;
                                            $market_price = is_numeric($market_price) ? $market_price : 0;
                                            $transaction->current_rate = is_numeric($transaction->current_rate) ? $transaction->current_rate : 0;
                                            $transaction->service_charge = is_numeric($transaction->service_charge) ? $transaction->service_charge : 0;
                                            $swap_charge = is_numeric($transaction->swap_charge ?? null) ? $transaction->swap_charge : 0;

                                            $current_value = $market_price * 13.7639 * $ttb_qty;
                                            $service_cost = $transaction->service_charge * 13.7639 * $ttb_qty;
                                            $chargeDirection = $transaction->type === 'sell' ? -1 : 1;
                                            $total_value = ($transaction->current_rate * 13.7639 * $ttb_qty) + ($chargeDirection * ($service_cost + $swap_charge));

                                            if ($transaction->type == 'buy') {
                                                $raw_profit = $current_value - $total_value;
                                            } else {
                                                $raw_profit = $total_value - $current_value;
                                            }
                                        
                                            $runningTTB_profit_loss += $raw_profit;
                                            $profit_loss = number_format($raw_profit, 3);
                                        @endphp

                                        <td id="balance-{{ $sl }}" style="text-align: center;">
                                            {{ $profit_loss }}
                                        </td>
                                    </tr>
                                @endforeach
                                
                                <tr>
                                        <td colspan="{{ ($show_service_charge ?? true) ? 10 : 9 }}" class="text-right">Total P/L: </td>
                                        <td style="text-align: center;">
                                           {{number_format($runningTTB_profit_loss,3)}}
                                        </td>
                                    </tr>
                            @endif

                        </tbody>

                    </table>

                    <!-- Profit and Loss Summary Table -->
                    <h4 class="section-title">Closed trades</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm data-table closed-trades-table">

                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Item Code</th>
                                    <th>Open Date</th>
                                    <th>Gold Qty</th>
                                    <th>B/S</th>
                                    <th>Rate</th>
                                    <th>Closed Date</th>
                                    <th>B/S</th>
                                    <th>Rate</th>
                                    @if ($show_closed_service_charge ?? ($show_service_charge ?? true))
                                        <th>Service Charge (AED)</th>
                                    @endif
                                    <th>Swap Charge (AED)</th>
                                    <th>Total Value (AED)</th>
                                    <th>Current Value (AED)</th>
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
                                        @php
                                            $closedQuantity = (float) ($detail->display_quantity ?? $detail->quantity);
                                            $openingTrade = $detail->linked_buy;
                                            $openingRate = is_numeric($detail->starting_rate) ? (float) $detail->starting_rate : (float) ($openingTrade->current_rate ?? 0);
                                            $closingRate = is_numeric($detail->current_rate) ? (float) $detail->current_rate : 0;
                                            $serviceCharge = $closedQuantity * ((float) ($openingTrade->service_charge ?? 0) * 13.7639);
                                            $openingQuantity = (float) ($openingTrade->tt_quantity ?? $closedQuantity);
                                            $swapCharge = $openingQuantity > 0
                                                ? ((float) ($openingTrade->swap_charge ?? 0) * ($closedQuantity / $openingQuantity))
                                                : (float) ($openingTrade->swap_charge ?? 0);
                                            $chargeDirection = $detail->transaction_type === 'sell' ? -1 : 1;
                                            $totalValue = ($openingRate * 13.7639 * $closedQuantity) + ($chargeDirection * ($serviceCharge + $swapCharge));
                                            $currentValue = $closingRate * 13.7639 * $closedQuantity;
                                            $closedProfitLoss = $detail->transaction_type === 'buy'
                                                ? $currentValue - $totalValue
                                                : $totalValue - $currentValue;
                                            $total_profit_or_loss += $closedProfitLoss;
                                        @endphp
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>TTB</td>
                                            <td>{{ $openingTrade?->created_at ? date('d-M-Y', strtotime($openingTrade->created_at)) : '—' }}</td>
                                            <td>{{ number_format($detail->display_quantity ?? $detail->quantity, 3) }}</td>
                                            <td>{{ $detail->transaction_type == 'sell' ? 'Sell' : 'Buy' }}</td>
                                            <td>{{ number_format($detail->starting_rate, 3) }}</td>
                                            <td>{{ date('d-M-Y', strtotime($detail->transaction_date ?? $detail->created_at)) }}</td>
                                            <td>{{ $detail->transaction_type == 'buy' ? 'Sell' : 'Buy' }}</td>
                                            <td>{{ number_format($detail->current_rate, 3) }}</td>
                                            @if ($show_closed_service_charge ?? ($show_service_charge ?? true))
                                                <td>{{ number_format($serviceCharge, 3) }}</td>
                                            @endif
                                            <td>{{ number_format($swapCharge, 3) }}</td>
                                            <td>{{ number_format($totalValue, 3) }}</td>
                                            <td>{{ number_format($currentValue, 3) }}</td>
                                            <td style="text-align: right;padding-right: 25px;">
                                                {{ number_format($closedProfitLoss, 3) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-active">
                                        <td colspan="{{ ($show_closed_service_charge ?? ($show_service_charge ?? true)) ? 13 : 12 }}"><strong>Total Profit/Loss</strong></td>
                                        <td style="text-align: right;padding-right: 25px;"><strong>AED
                                                {{ number_format($total_profit_or_loss, 3) }}</strong></td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="{{ ($show_closed_service_charge ?? ($show_service_charge ?? true)) ? 14 : 13 }}" class="text-center">No transactions found.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    @if (isset($pending) && count($pending) > 0)


                        <h4 class="section-title">Pending orders</h4>

                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-sm data-table">
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
                                            <td>{{ number_format($pen->limit, 3) }}</td>
                                            <td>{{ number_format($pen->stop_loss, 3) }}</td>

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

    <div class="report-footer">
        Generated {{ now()->format('d M Y, H:i') }} · {{ $customer->customer_code }} · All amounts are shown in AED unless stated otherwise.
    </div>
</body>

</html>
