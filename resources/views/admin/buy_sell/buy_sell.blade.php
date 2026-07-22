@extends('layouts.master')

@section('title', 'Buy Sell')

@section('content_header')
    <!--<h1>Customer Search</h1>-->
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

<style>
  .table td, .table th {
    padding: .25rem !important;
    font-size: .85rem !important;
	height: 20px !important;
  }
</style>

@section('content')
<ul class="breadcrumb breadcrumb-style">
		<li class="breadcrumb-item">
		  	<a href="{{route('admin.dashboard.buysell')}}">
				<i class="fas fa-home"></i>
			</a>
		</li>
		<li class="breadcrumb-item active">Buy & Sell</li>
	</ul>

    <div class="row">
        <div class="col-md-12 col-sm-12 ">
            <div>
                <form action="{{ route('admin.buysell.customer.search') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4 d-flex align-items-center justify-content-end">
                            <h6>Customer Search</h6>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" style="margin-top: 0px;">
                                <input type="text" class="form-control" required="" name="customer"
                                    style="line-height: 2rem;"
                                    @if ($customer) value="{{ $customer->name }}" @endif
                                    placeholder="name,phone,email,code">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group" style="margin-top: 0px;">

                                <button type="submit" class="btn btn-info" title="Search"><i class="fa fa-search"></i>
                                    Search</button>
                            </div>
                        </div>
                        
                    </div>
                </form>
            </div>
            @if ($customer)
            
                <div class="row mt-5">
                    <div class="col-md-4" style="text-align: left;">
                        <div class="info-box-content">
                            <h5>Customer: {{ $customer->name }} (  {{ $customer->customer_code }} )</h5>
                        </div>
                    </div>
                    
                    @if ($customer)
                        <div class="col-md-8 d-flex justify-content-end" style="text-align: right;">
                            @can('buysell')
                            <form action="{{ route('admin.buysell.customer.search') }}" method="GET">
                                <input type="hidden" class="form-control" required="" name="customer"
                                    @if ($customer) value="{{ $customer->name }}" @endif>
                                <button type="submit" class="btn btn-info mr-2">Trade Summary</button>
                            </form>
                            @endcan
                            @can('satement_send')
                            <button type="button" onclick="showStatement('statement')" class="btn btn-primary mr-2" style="height: 34px;">
                                Statement
                            </button>
                            @endcan
                            @can('withdraw_list')
                            <a href="{{ route('admin.buysell.show.preview', ['id' => $customer->id, 'type' => 'withdraw']) }}" class="btn btn-danger mr-2" style="height: 34px;">
                                Withdraw
                            </a>
                            @endcan
                            @can('deposit_list')
                            <a href="{{ route('admin.buysell.show.preview', ['id' => $customer->id, 'type' => 'deposit']) }}" class="btn btn-success mr-2" style="height: 34px;">
                                Deposit
                            </a>
                            @endcan
                        </div>
                    @endif
                </div>


                <div class="row d-flex justify-content-center">
                    <h5> SELL: <span id="sellrate">0.00</span> &nbsp; BUY: <span id="buyrate">0.00</span></h5>
                </div>
                <div class="row mt-5">
                    <div class="col-md-6">
                        
                        <h5 class="">Balance Summary</h5>
                        <table class="table table-striped table-bordered">
                            
                            <thead>
                                <tr>
                                    <th style="text-align:center;">BALANCE</th>
                                    
                                    @if(auth()->user()->can('deposit_add') == true)
                                        <th style="text-align:center;">
                                            @if ($customer)
                                                <small>
                                                    <a href="{{ route('admin.buysell.deposit_withdraw', ['customer_id' => $customer->id, 'type' => 'deposit']) }}" class="btn btn-success btn-sm py-0" >
                                                        + DEPOSIT
                                                    </a>
                                                </small>
                                            @endif
                                        </th>
                                    @else
                                        <th style="text-align:center;">DEPOSIT</th>
                                    @endif
                                    
                                    @if(auth()->user()->can('withdraw_add') == true)
                                    <th style="text-align:center;">
                                        @if ($customer)
                                            <small>
                                                <a href="{{ route('admin.buysell.deposit_withdraw', ['customer_id' => $customer->id, 'type' => 'withdraw']) }}" class="btn btn-warning btn-sm py-0" >
                                                    - WITHDRAW
                                                </a>
                                            </small>
                                        @endif
                                    </th>
                                    @else
                                        <th style="text-align:center;">WITHDRAW</th>
                                    @endif
                                    <th style="text-align:center;">EQUITY</th>
                                    <th style="text-align:center;">CUT POSITION</th>
                                </tr>
                            </thead>
                
                            <tbody>
                                <tr>
                                    <td style="text-align:center; font-size:23px"> {{ $current_amount }} </td>
                                    <td style="text-align:center; font-size:23px"> {{ $deposit }} </td>
                                    <td style="text-align:center; font-size:23px"> {{ $withdraw }} </td>
                                    <td style="text-align:center; font-size:23px"> <span id="equity">0.00</span> </td>
                                    <td style="text-align:center; font-size:23px"> <span id="cutposition">{{isset($runningBuySell) ? number_format($customer->cutposition, 2):0 }}</span> </td>
                                </tr>
                
                            </tbody>
                
                        </table>
                        
                    </div>
                    <div class="col-md-6">
                        <h5>TTB Summary</h5>
                        <table class="table table-striped table-bordered">
                            
                            <thead>
                                <tr>
                                    <th style="text-align:center;">TTB LIMIT</th>
                                    <th style="text-align:center;">BUY TTB</th>
                                    <th style="text-align:center;">SELL TTB</th>
                                    <th style="text-align:center;">ACTIVE TTB</th>
                                    <th style="text-align:center;">POSITION</th>
                                </tr>
                            </thead>
                
                            <tbody>
                                <tr>
                                    <td style="text-align:center; font-size:23px"> <span id="availableTTB">0.00</span> </td>
                                    <td style="text-align:center; font-size:23px"> <span id="runningBuyTTB">{{$runningBuy}}</span> </td>
                                    <td style="text-align:center; font-size:23px"> <span id="runningSellTTB">{{$runningSell}}</span> </td>
                                    <td style="text-align:center; font-size:23px"> <span id="">{{abs($runningBuy - $runningSell)}}</span> </td>
                                    <td style="text-align:center; font-size:23px"> <span id=""></span> {{$runningBuy - $runningSell < 0 ? 'Sell' : 'Buy'}}</td>
                                </tr>
                
                            </tbody>
                
                        </table>
                    </div>
                    
                    
                
                </div>
                
                <div class="row">
                    
                    <div class="col-md-3">
                         @can('buysell')
                        <div class="card">
                            <div class="card-header d-flex justify-content-center bg-success">
                			  <h4 class="text-center">BUY</h4>
                			</div>
                            <div class="card-body">
                                <form action="{{ route('admin.transaction.save') }}" method="POST" id="tradeForm">
                                    @csrf
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="reference_no">Reference/Ticket No. </label>
                                            <input type="text" class="form-control" name="reference_no" id="reference_no"
                                                placeholder="Reference No.">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="bid">TTB QTY</label>
                                            <input type="number" class="form-control" name="bid_buy" id="bid_buy"
                                                placeholder="Enter TT" >
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="gold_value_sell">Running Rate</label>
                                            <input type="number" class="form-control" name="gold_value_buy" id="gold_value_buy" >
                                        </div>
                                    </div>
                                    <input type="hidden" name="id" id="customer_id" value="{{ $customer->id }}">
                                    <hr>
                                    <div class="row">
                
                                        <div class="form-group col-md-12">
                                            <button type="button" id="buyBtn" class="btn btn-success btn-block"
                                                onclick="handleTransactionClick('buy')"
                                                @if ($customer->status == 'deactived') disabled @endif>Buy</button>
                                        </div>
                
                                    </div>
                                </form>
                            </div>
                
                        </div>
                        @endcan
                    </div>
                    
                    <div class="col-md-3">
                        @can('buysell')
                        <div class="card">
                            <div class="card-header d-flex justify-content-center bg-danger">
                			  <h4 class="text-center">SELL</h4>
                			</div>
                            <div class="card-body">
                                <form action="{{ route('admin.transaction.save') }}" method="POST" id="tradeForm">
                                    @csrf
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="reference_no">Reference/Ticket No. </label>
                                            <input type="text" class="form-control" name="reference_no" id="reference_no"
                                                placeholder="Reference No.">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="bid">TTB QTY</label>
                                            <input type="number" class="form-control" name="bid_sell" id="bid_sell"
                                                placeholder="Enter TT" >
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="gold_value_sell">Running Rate</label>
                                            <input type="number" class="form-control" name="gold_value_sell" id="gold_value_sell" >
                                        </div>
                                    </div>
                                    <input type="hidden" name="id" id="customer_id" value="{{ $customer->id }}">
                                    <hr>
                                    <div class="row">
                
                                        <div class="form-group col-md-12 text-right">
                                            <button type="button" id="sellBtn" class="btn bg-danger btn-block"
                                                onclick="handleTransactionClick('sell')"
                                                @if ($customer->status == 'deactived') disabled @endif>Sell</button>
                                        </div>
                
                                    </div>
                                </form>
                            </div>
                
                        </div>
                        @endcan
                    </div>
                    
                    <div class="col-md-6">
                        @can('buysell')
                        <!--<div class="form-group col-md-12">-->
                        <!--    {{-- <button type="button" id="pendingBtn" class="btn btn-primary btn-block load_modal"-->
                        <!--        data-toggle="modal">Pending </button> --}}-->
        
                        <!--    <button class="btn btn-primary load_modal col-md-12" data-bs-toggle="modal"-->
                        <!--        data-action="{{ route('admin.buysell.get.pending', ['id' => $customer->id, 'type' => 'deposit']) }}">-->
                        <!--        Pending-->
                        <!--    </button>-->
                        <!--</div>-->
                        
                        <div class="card">
                            <div class="card-header d-flex justify-content-center bg-warning">
                			  <h4 class="text-center">Pending Buy/Sell</h4>
                			</div>
                            <div class="card-body">
                                <form action="{{ route('admin.buysell.store.pending') }}" method="POST">
                                    @csrf
                                    <div class="row">
                
                                        <div class="form-group col-md-4">
                                            <label for="ticket_no" id="ticket_no">Reference/Ticket No.</label>
                                            <input type="text" name="ticket_no" id="ticket_no" class="form-control"
                                                placeholder="Ticket no">
                                            <div class="invalid-feedback" style="display: none;">Please enter a valid ticket no.</div>
                                        </div>
                                    
                                        <div class="form-group col-md-4">
                                            <label for="type">Type:</label>
                                            <select name="type" id="type" class="form-control" required>
                                                <option value="">Select Type</option>
                                                <option value="buy">Buy</option>
                                                <option value="sell">Sell</option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group col-md-4">
                                            <label for="tt">TTB Quantity:</label>
                                            <input type="number" name="tt" id="tt" class="form-control"
                                                placeholder="Enter TTB Quantity" required>
                                        </div>
                
                                        <div class="form-group col-md-4">
                                            <label for="limit_" id="limit-label">Limit:</label>
                                            <input type="text" name="limit_" id="limit_" class="form-control" 
                                                placeholder="Amount">
                                            <div class="invalid-feedback" style="display: none;">Please enter a valid number (up to 4
                                                decimal places).</div>
                                        </div>
                
                                        <div class="form-group col-md-4" style="display: none">
                                            <label for="limit">Take Profit:</label>
                                            <input type="text" name="limit[tp]" id="limit" class="form-control"
                                                 placeholder="Amount">
                                            <div class="invalid-feedback" style="display: none;">Please enter a valid number (up to 4
                                                decimal places).</div>
                                        </div>
                
                                        <div class="form-group col-md-4" style="display: none">
                                            <label for="limit">Stop Limit:</label>
                                            <input type="text" name="limit[sl]" id="limit" class="form-control"
                                                 placeholder="Amount">
                                            <div class="invalid-feedback" style="display: none;">Please enter a valid number (up to 4
                                                decimal places).</div>
                                        </div>
                                        
                
                                        <div class="form-group col-md-4">
                                            <label for="stop_" id="stop-label">Stop:</label>
                                            <input type="text" name="stop_" id="stop_" class="form-control" 
                                                placeholder="Amount">
                                            <div class="invalid-feedback" style="display: none;">Please enter a valid number (up to 4
                                                decimal places).</div>
                                        </div>
                
                                        <div class="form-group col-md-4" style="display: none">
                                            <label for="limit">Take Profit:</label>
                                            <input type="text" name="stop[tp]" id="limit" class="form-control"
                                                 placeholder="Amount">
                                            <div class="invalid-feedback" style="display: none;">Please enter a valid number (up to 4
                                                decimal places).</div>
                                        </div>
                
                                        <div class="form-group col-md-4" style="display: none">
                                            <label for="limit">Stop Limit:</label>
                                            <input type="text" name="stop[sl]" id="limit" class="form-control"
                                                 placeholder="Amount">
                                            <div class="invalid-feedback" style="display: none;">Please enter a valid number (up to 4
                                                decimal places).</div>
                                        </div>
                
                                    </div>
                
                                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                               
                                    <hr>
                                    <div class="row">
                                        <div class="form-group col-md-12 text-center">
                                            <button type="submit" class="btn bg-warning btn-block">Submit Trade</button>
                                        </div>
                
                                    </div>
                                </form>
                            </div>
                
                        </div>
                        @endcan
                    </div>
        
                </div>
                
                
            @else
                <div class="alert alert-danger">No customer found</div>
            @endif

            

        </div>
    </div>
    

    <script>
        const currentAmount = @json($current_amount);
        var maxtt_per_K = {{$customer->maxtt_per_K}};
        var runningBuyTTB = {{ $runningBuy }};
        var runningSellTTBValue = {{ $runningSell ?? 0 }};
        let buyPriceGlobal = null;
        
        document.addEventListener("DOMContentLoaded", function () {
        // Setup CSRF for AJAX POST
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        // Async function to fetch gold price and update DOM
        async function getGoldPrice() {
            try {
                const response = await fetch('https://furqanjewelry.com/api/get-gold-price', {
                    method: 'GET'
                });

                if (!response.ok) {
                    console.error('Error fetching the gold price');
                    return;
                }

                let data = await response.json();
                console.log(data);

                let sellPrice = data.gold_sell_price - 0.53;
                document.getElementById('sellrate').textContent = `$${sellPrice.toFixed(2)}`;
                buyPriceGlobal = parseFloat(sellPrice);

                let buyPrice = sellPrice + 1;
                document.getElementById('buyrate').textContent = `$${buyPrice.toFixed(2)}`;
                
                
                


                // Use a safe way to insert customer ID
                var customerId = {{ Js::from($customer->id) }}; // Laravel 9+
                var formData = {
                    customer_id: customerId,
                    sellPrice: sellPrice
                };

                // Send to backend via POST
                $.ajax({
                    type: 'POST',
                    url: "{{ route('admin.buysell.customer.get.buysell') }}",
                    data: formData,
                    dataType: 'json',
                    success: function (res) {
                        console.log(res.data);
                        
                        console.log(maxtt_per_K);
                        
                        
                        let ttb_limit = Math.round(res.data.equity.toFixed(2) / (1000 / maxtt_per_K));
                        document.getElementById('availableTTB').textContent = `${ttb_limit.toFixed(2)}`;
                        
                        let total_service_charge = res.data.sum_of_running_service_charge * 13.7628;
                        document.getElementById('equity').textContent = `${(res.data.equity - total_service_charge).toFixed(2)}`;
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX error:', error);
                    }
                });

            } catch (error) {
                console.error('Error fetching the gold price:', error);
            }
        }

        // Run every second (1000 ms)
        setInterval(getGoldPrice, 1000);
        });
        
        
        function handleTransactionClick(type) {
            console.log("type_" +type)
            var formData = {
                tt_quantity: document.getElementById('bid_'+type).value,
                current_rate: document.getElementById('gold_value_'+type).value,
                total_amount_aed: (document.getElementById('gold_value_'+type).value * 3.745 * 3.67 * document.getElementById('bid_'+type).value).toFixed(2),
                close_quanntity: 0,
                type: type,
                cut_position: 0,
                reference_no: document.getElementById('reference_no').value,
                customer_id: {{ $customer->id }},
                _token: '{{ csrf_token() }}'
            };
            var tt_quantity = document.getElementById('bid_'+type).value;
            var current_rate = document.getElementById('gold_value_'+type).value;
    console.log(tt_quantity);
            if (tt_quantity == '' || current_rate == '') {
                fire('Please fill all the fields');
                return false;
            }
    
            if (maxtt_per_K === null) {
                fire('Please enter Max TT Per Thousand');
                return false;
            }
    
    
            let amount = parseFloat(currentAmount.replace(/,/g, ''));
    
            let equity = parseFloat(document.getElementById('equity').textContent.replace(/,/g, ''));
            let getAvailableTT = parseFloat(document.getElementById('availableTTB').textContent.replace(/,/g, ''));
            let exicution_ttb = 0;
    
    
            if (equity < 0) {
                fire('You are not eligible to buy/sell , Please check customer equity!!');
                return false;
            }
    
            if (type == 'buy') {
                exicution_ttb = (runningBuyTTB > runningSellTTBValue) ? (getAvailableTT - ((runningBuyTTB - runningSellTTBValue > 0) ? runningBuyTTB - runningSellTTBValue : 0)) : (getAvailableTT + ( runningSellTTBValue - runningBuyTTB));
            } else {
                exicution_ttb = (runningSellTTBValue > runningBuyTTB) ? (getAvailableTT - (runningSellTTBValue - runningBuyTTB)) : (getAvailableTT + (runningBuyTTB - runningSellTTBValue));
    
            }
    
            if (exicution_ttb < tt_quantity) {
                fire('TTB Quantity limit exceeded, Please check TTB Quantity.');
                return false;
            }
    
    
            document.getElementById('buyBtn').disabled = true;
            document.getElementById('sellBtn').disabled = true;
            document.getElementById('bid_'+type).disabled = true;
            document.getElementById('gold_value_'+type).disabled = true;
    
            $.ajax({
                type: 'POST',
                url: "{{ route('admin.buysell.save.bid') }}",
                data: formData,
                dataType: 'json',
                encode: true,
                success: function(response) {
                    if (response.success) {
                        $("#runningStateDiv").html(response.html);
                        Swal.fire({
                            icon: 'success',
                            text: "Successfuly trade start.",
                            position: 'top-end',
                            toast: true,
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'swal-custom-toast'
                            }
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        fire('Error: ' + response.message);
    
                    }
    
                    document.getElementById('buyBtn').disabled = false;
                    document.getElementById('sellBtn').disabled = false;
                    document.getElementById('bid_'+type).disabled = false;
                    document.getElementById('gold_value_'+type).disabled = false;
    
                    document.getElementById('bid_'+type).value = "";
                    document.getElementById('gold_value_'+type).value = "";
                    document.getElementById('reference_no').value = "";
                },
                error: function(xhr, status, error) {
                    fire('Error: ' + error);
                    document.getElementById('buyBtn').disabled = false;
                    document.getElementById('sellBtn').disabled = false;
                }
            });
        }
    
        function fire(text) {
            Swal.fire({
                icon: 'error',
                text: text,
                position: 'top-end',
                toast: true,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: {
                    popup: 'swal-custom-toast'
                }
            });
        }

        
        
   
    </script>
    
    <script>
        
    
    
        function showStatement(type) {
    
            const id = $('#customer_id').val();
            console.log(id);
            if (!id) {
                alert('Customer ID is required!');
                return;
            }
    
            if (buyPriceGlobal === null) {
                alert('Gold price is not available!');
                return;
            }
    
            const url = "{{ route('admin.transaction.show.statement') }}" +
                `?id=${encodeURIComponent(id)}&type=${encodeURIComponent(type)}&goldValue=${encodeURIComponent(buyPriceGlobal)}`;
            window.open(url, '_self');
        }
        function sendInvoice(type) {
            var id = $('#customer_id').val();
            var url = "{{ route('admin.transaction.send.invoice') }}";
            var goldValue = "N/A";
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    id: id,
                    type: type,
                    goldValue: goldValue,
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    console.log(data);

                    if (data.status == 'success') {
                        alert("WhatsApp message sent successfully.");
                    } else {
                        alert("Failed to send WhatsApp message.");
                    }
                }
            });
        }
        
        document.getElementById("type").addEventListener("change", function() {
            let selectedType = this.value;
            let limitLabel = document.getElementById("limit-label");
            let stopLabel = document.getElementById("stop-label");
            if (selectedType === "buy") {
                limitLabel.textContent = "Buy Limit:(Below from Market)";
                stopLabel.textContent = "Buy Stop:(Up from Market)";
            } else if (selectedType === "sell") {
                limitLabel.textContent = "Sell Limit:(Up from Market)";
                stopLabel.textContent = "Sell Stop:(Below from Market)";
            } else {
                limitLabel.textContent = "Limit";
                stopLabel.textContent = "Stop Limit";
            }
        });
    
        function validateDecimalInput(input) {
            const regex = /^\d+(\.\d{1,4})?$/; // Matches numbers with up to 4 decimal places
            const value = input.value.trim();
            const feedback = input.nextElementSibling;
    
            if (regex.test(value)) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                feedback.style.display = 'none';
            } else {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
                feedback.style.display = 'block';
            }
        }
    
    
        document.querySelectorAll('input[type="text"]').forEach(input => {
            input.addEventListener('input', () => validateDecimalInput(input));
        });
    </script>
@stop

@section('scripts')
    

@stop
