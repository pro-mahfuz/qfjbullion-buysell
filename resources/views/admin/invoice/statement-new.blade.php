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

            <div class="card">
                <div class="card-header">
                    <div class="col-md-4 p-0">
                        <h5>Customer Statement</h5>
                        
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

                <div class="card-body" style="background: #fff;color:#000;">
                    <!-- Customer Information -->
                    <div class="d-flex justify-content-between">
                        <p><strong>Full Name:</strong> {{ $customer->name ?? 'N/A' }} ({{ $customer->customer_code }})</p>
                        <p><strong>Date:</strong> {{ request('start_date') ? date("d-M-Y",strtotime(request('start_date'))) : now() }}
                            {{ request('end_date') ? ' to ' . date("d-M-Y",strtotime(request('end_date'))) : '' }}</p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p><strong>Market Price:</strong> <span
                                id="marketPrice">{{ number_format($market_price, 2) }}</span></p>

                        <p>

                            <strong>Cut Position:</strong> <span style="font-size: 20px;">
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
                            </span>
                        </p>
                    </div>

                    <h4>Balance Information</h4>
                    <div class="table-responsive">
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
                            <h4 class="mt-5">Active Positions</h4>
                        </div>
                        <div class="col-md-9">
                            <p class="mt-5"><span>Buy Qty : {{ $sumBuy }}</span> | <span>Sell Qty:
                                    {{ $sumSell }}</span> | <span>Net Qty : {{ abs($value) }} @if ($value < 0)
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
                    <h4 class="mt-4">Profit and Loss Summary</h4>
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


                        <h4 class="mt-4">Pending</h4>

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

            // $('#marketPrice').text(previousPrice.toFixed(2));

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
