@extends('layouts.master')

@section('content')
<style>
	.recent-report__chart{
		margin-left: -30px;
	}
	.apexcharts-legend{
		margin-right: 30px;
		margin-top: 45px;
		margin-left: 10px;
	}
	#navbar_search_box{
		margin-top:10px;
	}

</style>
<style>
  .table td, .table th {
    padding: .25rem !important;
    font-size: .85rem !important;
	height: 20px !important;
  }

  .modal-title{
      color: #000000 !important;
  }
  .pagination {
      margin-top: -25px;
  }

  .page-link {
    padding: 0.2rem 0.5rem !important;
  }
</style>
<style>
  .custom-bordered th,
  .custom-bordered td,
  .custom-bordered {
    border: 1px solid black !important;
  }
</style>
<style>
    .statement-shell { border: 0; border-radius: 14px; box-shadow: 0 8px 28px rgba(16, 24, 40, .08); overflow: hidden; }
    .statement-header { background: linear-gradient(120deg, #102a43, #1f5f74); color: #fff; border: 0; padding: 1.25rem 1.5rem; }
    .statement-header h5 { color: #fff; font-weight: 700; letter-spacing: .01em; }
    .statement-header .btn { border-radius: 6px; font-weight: 600; }
    .statement-body { padding: 1.5rem; background: #f7f9fc; }
    .statement-panel { background: #fff; border: 1px solid #e7edf4; border-radius: 10px; padding: 1.1rem 1.25rem; margin-bottom: 1.25rem; }
    .statement-meta { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
    .statement-meta__item { color: #667085; font-size: .78rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
    .statement-meta__item strong { display: block; margin-top: .3rem; color: #101828; font-size: .98rem; font-weight: 600; letter-spacing: 0; text-transform: none; }
    .statement-filter { display: flex; align-items: end; gap: .75rem; flex-wrap: wrap; }
    .statement-filter .form-group { margin: 0; min-width: 175px; }
    .statement-filter label { display: block; margin-bottom: .25rem; color: #475467; font-size: .78rem; font-weight: 700; }
    .statement-filter .btn { line-height: 30px !important; padding-top: 0; padding-bottom: 0; }
    .statement-filter__action { min-width: auto !important; }
    .metric-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .85rem; }
    .metric { border: 1px solid #e7edf4; border-radius: 8px; padding: .85rem; background: #fff; }
    .metric__label { display: block; color: #667085; font-size: .72rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
    .metric__value { display: block; margin-top: .35rem; color: #101828; font-size: 1.08rem; font-weight: 700; }
    .statement-section { margin: 1.75rem 0 .65rem; color: #102a43; font-size: 1.05rem; font-weight: 700; }
    .statement-table { margin-bottom: 0; background: #fff; }
    .statement-table thead th { background: #eef3f8; border-color: #dce5ef; color: #344054; font-size: .72rem !important; font-weight: 700; letter-spacing: .03em; text-transform: uppercase; white-space: nowrap; }
    .statement-table.closed-trades-table thead th { background: #1f6fb2 !important; border-color: #1f6fb2 !important; color: #ffffff !important; }
    .statement-table td { border-color: #e7edf4; vertical-align: middle; }
    .statement-table .table-active td { background: #f2f8f6; }
    .position-summary { display: flex; flex-wrap: wrap; gap: .65rem 1.25rem; color: #475467; font-size: .86rem; }
    .position-summary strong { color: #101828; }
    @media (max-width: 991px) { .statement-meta, .metric-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 575px) { .statement-meta, .metric-grid { grid-template-columns: 1fr; } .statement-body { padding: 1rem; } }
</style>
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
        
            $current_value = $market_price * 13.7639 * $ttb_qty;
            $total_value = $transaction->current_rate * 13.7639 * $ttb_qty;
            $service_cost = $transaction->service_charge * 13.7639 * $ttb_qty;
        
            if ($transaction->type == 'buy') {
                $raw_profit = ($current_value - $total_value) - $service_cost;
            } else {
                $raw_profit = ($total_value - $current_value) - $service_cost;
            }
        
            $runningTTB_profit += $raw_profit;
            $profit_loss = number_format($raw_profit, 3);
        @endphp
    @endforeach
@endif

<ul class="breadcrumb breadcrumb-style">
		<li class="breadcrumb-item">
		  	<a href="{{route('admin.dashboard.buysell')}}">
				<i class="fas fa-home"></i>
			</a>
		</li>
		<li class="breadcrumb-item active">Statement</li>
	</ul>
    <div class="row">
        <div class="col-md-12">

            <!--<form method="GET" action="{{ route('admin.transaction.show.statement') }}" class="mb-4">-->
            <!--    <div class="form-row">-->
            <!--        <div class="col-md-4">-->
            <!--            <label for="start_date">Start Date</label>-->
            <!--            <input type="date" name="start_date" id="start_date" class="form-control"-->
            <!--                value="{{ request('start_date') }}">-->
            <!--        </div>-->
            <!--        <div class="col-md-4">-->
            <!--            <label for="end_date">End Date</label>-->
            <!--            <input type="date" name="end_date" id="end_date" class="form-control"-->
            <!--                value="{{ request('end_date') }}">-->
            <!--        </div>-->
            <!--        <input type="hidden" name="id" value="{{ request('id') }}">-->
            <!--        <input type="hidden" name="type" value="{{ request('type') }}">-->
            <!--        <input type="hidden" name="goldValue" value="{{ request('goldValue') }}">-->
            <!--        <div class="col-md-4 d-flex align-items-end">-->
            <!--            <button type="submit" class="btn btn-primary btn-block">Filter</button>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</form>-->

            <div class="card statement-shell">
                <div class="card-header statement-header">
                    <div class="col-md-4 p-0">
                        <h5 class="mb-0">Customer Statement</h5>
                        
                    </div>
                    <div class="col-md-8 text-right p-0">
                        {{-- @can('statement_send') --}}
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.customer.list') }}" class="btn btn-primary btn-sm mr-2">Customer
                                List</a>
                            <a href="{{ route('admin.buysell.customer.search', ['customer' => $customer->customer_code]) }}"
                                class="btn btn-primary btn-sm mr-2">
                                Trade Summary
                            </a>
                            
                            <form action="{{ route('admin.transaction.send.invoice') }}" method="POST">
                                @csrf
                                <input type="hidden" class="form-control" required="" name="id" value="{{ $customer->id }}">
                                <input type="hidden" class="form-control" required="" name="type" value="statement">
                                <input type="hidden" class="form-control" required="" name="goldValue" value="{{ $market_price }}">
                                <input type="hidden" class="form-control" required="" name="start_date" value="{{ request('start_date') }}">
                                <input type="hidden" class="form-control" required="" name="end_date" value="{{ request('end_date') }}">
                                <button class="btn btn-danger btn-sm mr-2" type="submit">Download Statement</button>
                            </form>
                            
                            <!--<button class="btn btn-danger btn-sm mr-2" onclick="downloadStatement('statement')">Download-->
                            <!--    Statement</button>-->
                            <!--<button class="btn btn-success btn-sm" onclick="sendInvoice('statement')">Send-->
                            <!--    Statement</button>-->
                                
                                
                        </div>
                        {{-- @endcan --}}
                    </div>
                </div>

                <div class="card-body statement-body">
                    <div class="statement-panel">
                        <form method="GET" action="{{ route('admin.transaction.show.statement') }}" class="statement-filter">
                            <input type="hidden" name="id" value="{{ $customer->id }}">
                            <input type="hidden" name="type" value="statement">
                            <input type="hidden" name="goldValue" value="{{ $market_price }}">
                            <div class="form-group"><label for="start_date">From</label><input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}"></div>
                            <div class="form-group"><label for="end_date">To</label><input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}"></div>
                            <div class="form-group statement-filter__action">
                                <button type="submit" class="btn btn-primary btn-sm">Apply period</button>
                                <a href="{{ route('admin.transaction.show.statement', ['id' => $customer->id, 'type' => 'statement', 'goldValue' => $market_price]) }}" class="btn btn-outline-secondary btn-sm">Clear dates</a>
                            </div>
                        </form>
                    </div>

                    <div class="statement-panel statement-meta">
                        <div class="statement-meta__item">Customer<strong>{{ $customer->name ?? 'N/A' }} ({{ $customer->customer_code }})</strong></div>
                        <div class="statement-meta__item">Statement period<strong>{{ request('start_date') ? date('d M Y', strtotime(request('start_date'))) : 'All activity' }}{{ request('end_date') ? ' — ' . date('d M Y', strtotime(request('end_date'))) : '' }}</strong></div>
                        <div class="statement-meta__item">Market price<strong id="marketPrice">$ {{ number_format($market_price, 3) }}</strong></div>
                    </div>

                    <h4 class="statement-section">Account overview</h4>
                    @php
                        $netTtb = $data['sum_of_running_buy_ttb'] - $data['sum_of_running_sell_ttb'];
                        $activeTtb = abs($netTtb);
                        $netEquity = $data['equity'] - ($data['sum_of_running_service_charge'] * 13.7639);
                        $withdrawable = $value != 0 && $activeTtb != 0 ? $netEquity - ($netEquity / $activeTtb) : 0;
                    @endphp
                    <div class="metric-grid">
                        <div class="metric"><span class="metric__label">Deposits</span><span class="metric__value">AED {{ $data['sum_of_deposit'] }}</span></div>
                        <div class="metric"><span class="metric__label">Withdrawals</span><span class="metric__value">AED {{ $data['sum_of_withdraw'] }}</span></div>
                        <div class="metric"><span class="metric__label">Realised P/L</span><span class="metric__value">AED {{ number_format((float) str_replace(',', '', $data['current_profit_loss']), 3) }}</span></div>
                        <div class="metric"><span class="metric__label">Cash balance</span><span class="metric__value">AED {{ number_format($data['current_balance'], 3) }}</span></div>
                        <div class="metric"><span class="metric__label">Net gold position</span><span class="metric__value">{{ $activeTtb }} TTB {{ $netTtb < 0 ? 'Sell' : ($netTtb > 0 ? 'Buy' : '') }}</span></div>
                        <div class="metric"><span class="metric__label">Position value</span><span class="metric__value">AED {{ $value != 0 ? number_format($market_price * abs($value) * 13.7639, 3) : '0.00' }}</span></div>
                        <div class="metric"><span class="metric__label">Equity</span><span class="metric__value">AED {{ number_format($netEquity, 3) }}</span></div>
                        <div class="metric"><span class="metric__label">Withdrawable</span><span class="metric__value">AED {{ number_format($withdrawable, 3) }}</span></div>
                    </div>
                    
                    <h4 class="statement-section">Active positions</h4>
                    <div class="statement-panel position-summary">
                        <span>Buy quantity: <strong>{{ $sumBuy }}</strong></span>
                        <span>Sell quantity: <strong>{{ $sumSell }}</strong></span>
                        <span>Net position: <strong>{{ abs($value) }} {{ $value < 0 ? 'Sell' : ($value > 0 ? 'Buy' : '') }}</strong></span>
                        <span>Unrealised P/L: <strong>AED {{ number_format($runningTTB_profit, 3) }}</strong></span>
                        <span>Margin Limit: <strong>{{ $value == 0 ? '0' : $customer->cutposition . ' (' . ($value < 0 ? 'Sell' : 'Buy') . ')' }}</strong></span>
                    </div>
                    <div class="table-responsive">
                    <table class="table table-striped table-bordered statement-table">

                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Reference No</th>
                                <th scope="col">Date</th>
                                <th scope="col">Type</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Open Rate</th>
                                <th scope="col">Service Charge (AED)</th>
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
                                        <td style="text-align: center;">{{ number_format(($transaction->tt_quantity - $transaction->close_quanntity) * ($transaction->service_charge * 13.7639), 3) }}</td>

                                        <td style="text-align: center;">{{ number_format($transaction->swap_charge ?? 0, 3) }}</td>

                                        <td id="oldbalance-{{ $sl }}" style="text-align: center;">
                                            {{ number_format(
                                                ($transaction->current_rate * 13.7639 * ($transaction->tt_quantity - $transaction->close_quanntity))
                                                + (($transaction->tt_quantity - $transaction->close_quanntity) * ($transaction->service_charge * 13.7639))
                                                + ($transaction->swap_charge ?? 0),
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
                                            $total_value = ($transaction->current_rate * 13.7639 * $ttb_qty) + $service_cost + $swap_charge;

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
                                        <td colspan='10' class="text-right">Total P/L: </td>
                                        <td style="text-align: center;">
                                           {{number_format($runningTTB_profit_loss,3)}}
                                        </td>
                                    </tr>
                            @endif

                        </tbody>

                    </table>
                    </div>

                    <!-- Profit and Loss Summary Table -->
                    <h4 class="statement-section">Closed trades</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm statement-table closed-trades-table">

                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Item Code</th>
                                    <th>Date</th>
                                    <th>Gold Qty</th>
                                    <th>B/S</th>
                                    <th>Rate</th>
                                    <th>Date</th>
                                    <th>B/S</th>
                                    <th>Rate</th>
                                    <th>Service Charge (AED)</th>
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
                                            $closedQuantity = is_numeric($detail->quantity) ? (float) $detail->quantity : 0;
                                            $openingTrade = $detail->linked_buy;
                                            $openingRate = is_numeric($detail->starting_rate) ? (float) $detail->starting_rate : (float) ($openingTrade->current_rate ?? 0);
                                            $closingRate = is_numeric($detail->current_rate) ? (float) $detail->current_rate : 0;
                                            $serviceCharge = $closedQuantity * ((float) ($openingTrade->service_charge ?? 0) * 13.7639);
                                            $openingQuantity = (float) ($openingTrade->tt_quantity ?? $closedQuantity);
                                            $swapCharge = $openingQuantity > 0
                                                ? ((float) ($openingTrade->swap_charge ?? 0) * ($closedQuantity / $openingQuantity))
                                                : (float) ($openingTrade->swap_charge ?? 0);
                                            $totalValue = ($openingRate * 13.7639 * $closedQuantity) + $serviceCharge + $swapCharge;
                                            $currentValue = $closingRate * 13.7639 * $closedQuantity;
                                            $closedProfitLoss = $detail->transaction_type === 'buy'
                                                ? $currentValue - $totalValue
                                                : $totalValue - $currentValue;
                                            $total_profit_or_loss += $closedProfitLoss;
                                        @endphp
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>TTB</td>
                                            <td>{{ date('d-M-Y', strtotime($detail->created_at)) }}</td>
                                            <td>{{ $detail->quantity }}</td>
                                            <td>{{ $detail->transaction_type == 'sell' ? 'Sell' : 'Buy' }}</td>
                                            <td>{{ number_format($detail->starting_rate, 3) }}</td>
                                            <td>{{ date('d-M-Y', strtotime($detail->transaction_date)) }}</td>
                                            <td>{{ $detail->transaction_type == 'buy' ? 'Sell' : 'Buy' }}</td>
                                            <td>{{ number_format($detail->current_rate, 3) }}</td>
                                            <td>{{ number_format($serviceCharge, 3) }}</td>
                                            <td>{{ number_format($swapCharge, 3) }}</td>
                                            <td>{{ number_format($totalValue, 3) }}</td>
                                            <td>{{ number_format($currentValue, 3) }}</td>
                                            <td style="text-align: right;padding-right: 25px;">
                                                {{ number_format($closedProfitLoss, 3) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-active">
                                        <td colspan="13"><strong>Total Profit/Loss</strong></td>
                                        <td style="text-align: right;padding-right: 25px;"><strong>AED
                                                {{ number_format($total_profit_or_loss, 3) }}</strong></td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="14" class="text-center">No transactions found.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    @if (isset($pending) && count($pending) > 0)


                        <h4 class="statement-section">Pending orders</h4>

                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-sm statement-table">
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
    
    <script>
        let previousPrice = null;
        let isFetching = false;
        let bid = 0;
        async function getGoldPrice(loader = true) {
            if (loader) {
                $('#loader').show();
                $('#transactionContainer').hide();
            }
            console.log('Fetching gold price...');

            if (isFetching) return;

            isFetching = true;

            try {
                const response = await fetch('https://www.goldapi.io/api/XAU/USD', {
                    method: 'GET',
                    headers: {
                        'x-access-token': 'goldapi-7q9uy0tkwrfdtlo-io',
                        'Content-Type': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error(`Error: ${response.status}`);
                }

                const data = await response.json();
                previousPrice = data.ask;
                bid = data.bid;

                console.log('Gold price fetched:', previousPrice);
            } catch (error) {
                console.error('Error fetching the gold price:', error);
                // fire('Error fetching the gold price');
            } finally {
                isFetching = false;
            }

            // $('#marketPrice').text(previousPrice.toFixed(3));

            if (loader) {
                $('#loader').hide();
                $('#transactionContainer').show();
            }
            // Fetch the gold price again after 5 seconds
            setTimeout(() => getGoldPrice(false), 2500);
        }

        getGoldPrice();

        // function downloadStatement(type) {
            
        //     var id = "{{ $customer->id }}";
        //     var url = "{{ route('admin.transaction.send.invoice') }}";
        //     var previousPrice = "{{ $market_price }}";
        //     var startDate = "{{ request('start_date') }}";
        //     var endDate = "{{ request('end_date') }}";
        //     console.log(id);

        //     $.ajax({
        //         url: url,
        //         type: 'POST',
        //         data: {
        //             id: id,
        //             type: type,
        //             previousPrice: previousPrice,
        //             startDate: startDate,
        //             endDate: endDate,
        //             _token: "{{ csrf_token() }}"
        //         },
        //         xhrFields: {
        //             responseType: 'blob'
        //         },
        //         success: function(response) {
        //             var blob = new Blob([response]);
        //             var link = document.createElement('a');
        //             link.href = window.URL.createObjectURL(blob);
        //             var currentDate = new Date().toISOString().slice(0, 10);
        //             link.download = `statement-${currentDate}.pdf`;
        //             link.click();
        //         },
        //         error: function(xhr, status, error) {
        //             console.error("Error occurred: ", error);
        //             alert("An error occurred while generating the statement.");
        //         }
        //     });
        // }

        function fire(text) {
            Swal.fire({
                icon: 'error',
                text: text,
                position: 'top-end',
                toast: true,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        }
    </script>
@stop

@push('js')
    
@endpush

{{--
<script>
    function sendInvoice(type) {
        var id = "{{ $customer->id }}";
var url = "{{ route('transaction.send.invoice') }}";
var marketPrice = "{{ $market_price }}";
var goldValue = "N/A";
$.ajax({
url: url,
type: 'POST',
data: {
id: id,
type: type,
goldValue: goldValue,
marketPrice: marketPrice,
_token: "{{ csrf_token() }}"
},
success: function(data) {
if (data.status == 'success') {
alert("WhatsApp message sent successfully.");
} else {
alert("Failed to send WhatsApp message.");
}
}
});
}
</script> --}}
