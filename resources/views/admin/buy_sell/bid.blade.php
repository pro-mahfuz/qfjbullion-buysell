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
                    <th style="text-align:center;">MARGIN LIMIT</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td style="text-align:center; font-size:23px"> {{ $current_amount }} </td>
                    <td style="text-align:center; font-size:23px"> {{ $deposit }} <br>
                     </td>
                    <td style="text-align:center; font-size:23px"> {{ $withdraw }} </td>
                    <td style="text-align:center; font-size:23px"> <span id="equity">0.00</span> </td>
                    <td style="text-align:center; font-size:23px"> <span id="cutposition">{{isset($runningBuySell) ? number_format($customer->cutposition, 3):0 }}</span> </td>
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
                    <td style="text-align:center; font-size:23px"> <span id="runningBuyTTB">0.00</span> </td>
                    <td style="text-align:center; font-size:23px"> <span id="runningSellTTB">0.00</span> </td>
                    <td style="text-align:center; font-size:23px"> <span id="">{{abs($runningBuy - $runningSell)}}</span> </td>
                    <td style="text-align:center; font-size:23px"> <span id=""></span> {{$runningBuy - $runningSell < 0 ? 'Sell' : 'Buy'}}</td>
                </tr>

            </tbody>

        </table>
        
    </div>

</div>

@can('running_trade_list')
<div class="row mt-2">
    <div class="col-md-12">
        <h5>Trading Summary</h5>
        <div class="card">
            <div class="card-body" id="runningStateDiv">
                <div class="table-responsive">
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
                                <th scope="col">Swap Charge (AED)</th>
                                <th scope="col">Current Value (AED)</th>
                                <th scope="col">Profit/Loss (AED)</th>
                                <th scope="col">TP</th>
                                <th scope="col">SL</th>
                                <th scope="col">Days</th>
                                <th scope="col">Created By</th>
                                @can('update_running_trade')
                                <th scope="col" style="witdh: 280px">Action</th>
                                @endcan
                            </tr>
                        </thead>

                        <tbody id="table1" class="collapse show">
                            @if (isset($runningBuySell))
                                <?php $sl = 1; ?>
                                <?php $runningTTB = 0; ?>
                                <?php $runningSellTTB = 0; ?>
                                <?php $runningBuyTTB = 0; ?>
                                @foreach ($runningBuySell as $transaction)
                                    <tr>
                                        <th style="text-align: center;" scope="row">{{ $sl++ }}</th>
                                        <td style="text-align: center;">{{ $transaction->reference_no }}</td>
                                        <td style="text-align: center;">{{ $transaction->created_at }}</td>
                                        <td style="text-align: center;">{{ $transaction->type }}</td>
                                        <td style="text-align: center;">{{ $transaction->tt_quantity - $transaction->close_quanntity }}</td>
                                        <td style="text-align: center;" id="current_rate-{{ $sl }}">{{ number_format($transaction->current_rate, 3) }}</td>
                                        <td style="text-align: center;" id="oldbalance-{{ $sl }}" style="text-align: center;">
                                            {{ number_format(
                                                ($transaction->current_rate * 13.7639 * ($transaction->tt_quantity - $transaction->close_quanntity))
                                                + (($transaction->tt_quantity - $transaction->close_quanntity) * ($transaction->service_charge * 13.7639))
                                                + ($transaction->swap_charge ?? 0),
                                                3
                                            ) }}
                                        </td>
                                        
                                        <td id="service_charge-{{ $sl }}" style="text-align: center;">
                                            {{ number_format(($transaction->tt_quantity - $transaction->close_quanntity) * ($transaction->service_charge * 13.7639), 3) }}</td>

                                        <td style="text-align: center;">
                                            {{ number_format($transaction->swap_charge ?? 0, 3) }}
                                        </td>

                                        
                                        <td style="text-align: center;">
                                            <span data-id="{{ $sl }}" data-type="{{ $transaction->type }}" data-qty="{{ $transaction->tt_quantity - $transaction->close_quanntity }}" data-startrate="{{ number_format(($transaction->current_rate * 13.7639 * ($transaction->tt_quantity - $transaction->close_quanntity)) + (($transaction->tt_quantity - $transaction->close_quanntity) * ($transaction->service_charge * 13.7639)) + ($transaction->swap_charge ?? 0), 3) }}" class="ratelist">
                                            <span>
                                        </td>

                                        <td id="balance-{{ $sl }}" style="text-align: center;"></td>
                                        <td style="text-align: center;"> {{ $transaction->take_profit }} </td>
                                        <td style="text-align: center;"> {{ $transaction->stop_loss }} </td>
                                        <td style="text-align: center;">
                                            {{ $transaction->created_at->copy()->startOfDay()->diffInDays(now()->startOfDay()) }}
                                        </td>
                                        <td style="text-align: center;"> {{ $transaction->created_by }} </td>
                                        @can('update_running_trade')
                                        <td style="text-align: center;">
                                            <div class="d-flex">
                                                <a href="javascript:void(0)"
                                                    class="btn btn-sm text-white load_form ml-1"
                                                    style="background-color: {{ $transaction['type'] == 'buy' ? '#17A2B8' : '#E63946' }}; border-color: {{ $transaction['type'] == 'buy' ? '#17A2B8' : '#E63946' }};"
                                                    data-id="{{ $sl }}"
                                                    data-transaction="{{ json_encode($transaction) }}"
                                                    data-current-rate="{{ $transaction['current_rate'] }}"
                                                    data-qty="{{ $transaction['tt_quantity'] - $transaction['close_quanntity'] }}">
                                                    {{ $transaction['type'] == 'buy' ? 'Buy Close' : 'Sell Close' }}
                                                </a>

                                                <a href="javascript:void(0)" class="btn btn-success btn-sm ml-1 matchTradeForm"
                                                    style="background-color: #6C63FF; border-color: #6C63FF;"
                                                    data-id="{{ $sl }}"
                                                    data-transaction="{{ json_encode($transaction) }}"
                                                    data-current-rate="{{ $transaction['current_rate'] }}"
                                                    data-qty="{{ $transaction['tt_quantity'] - $transaction['close_quanntity'] }}">
                                                    Match Trade
                                                </a>

                                                <a href="#" class="btn btn-sm text-white splitForm ml-1"
                                                    style="background-color: #FF9F1C; border-color: #FF9F1C;"
                                                    data-id="{{ $sl }}"
                                                    data-transaction="{{ json_encode($transaction) }}"
                                                    data-current-rate="{{ $transaction['current_rate'] }}"
                                                    data-qty="{{ $transaction['tt_quantity'] - $transaction['close_quanntity'] }}">
                                                    Split Trade
                                                </a>


                                                <a href="#" class="btn btn-sm text-white load_modify_form ml-1"
                                                    style="background-color: #2A9D8F; border-color: #2A9D8F;"
                                                    data-id="{{ $sl }}"
                                                    data-transaction="{{ json_encode($transaction) }}"
                                                    data-current-rate="{{ $transaction['current_rate'] }}"
                                                    data-qty="{{ $transaction['tt_quantity'] - $transaction['close_quanntity'] }}">
                                                    Modify
                                                </a>
                                                @can('void_trade')
                                                <button class="btn btn-sm text-white ml-1"
                                                    style="background-color: #D72638; border-color: #D72638;"
                                                    onclick="voidTransaction({{ json_encode($transaction) }})">
                                                    Void
                                                </button>
                                                @endcan
                                            </div>
                                        </td>
                                        @endcan
                                    </tr>

                                    <?php $runningTTB += $transaction->type == 'buy' ? $transaction->tt_quantity - $transaction->close_quanntity : -($transaction->tt_quantity - $transaction->close_quanntity); ?>
                                    <?php $runningSellTTB += $transaction->type == 'buy' ? 0 : $transaction->tt_quantity - $transaction->close_quanntity; ?>
                                    <?php $runningBuyTTB += $transaction->type == 'buy' ? $transaction->tt_quantity - $transaction->close_quanntity : 0; ?>
                                @endforeach
                                
                            @endif

                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endcan


@can('pending_trade_list')
@if (isset($pending) && count($pending) > 0)
    <div class="row mt-2">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Pending Buy/Sell</h5>
                </div>

                <div class="card-body" id="pendingDiv">
                    <div class="table-responsive mt-3">
                        <table class="table table-striped table-bordered">

                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">TTB Qty</th>
                                    {{-- <th scope="col">Threashold</th> --}}
                                    <th scope="col">Limit</th>
                                    <th scope="col">Stop</th>
                                    <th scope="col">Created By</th>
                                    <th scope="col">Action </th>
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
                                        <td>{{ number_format($pen->stop, 3) }}</td>
                                        <td>{{ $pen->created_by }}</td>
                                        <td>
                                            <div class="d-flex ">
                                                <!-- Update Button -->
                                                <button class="btn btn-primary btn-sm load_modal me-2"
                                                    data-bs-toggle="modal"
                                                    data-action="{{ route('admin.buysell.pending.edit', ['id' => $pen->id]) }}">
                                                    Update
                                                </button>

                                                <!-- Delete Button -->
                                                <form
                                                    action="{{ route('admin.buysell.pending.delete', ['id' => $pen->id]) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm ml-1">
                                                        <i class="fa fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endcan

<script>
    let previousPrice = null;
    let isFetching = false;
    let rateErrorShown = false;
    var usdToAedRate = 3.674;

    var maxtt_per_K = @json($maxtt_per_K);
    var runningTTB = {{ $runningTTB }};
    var lastRunningrate = {{ $runningBuySell[0]->current_rate ?? 0 }};
    var runningSellTTBValue = {{ $runningSellTTB ?? 0 }};
    var runningBuyTTB = {{ $runningBuyTTB }};
    var serviceCharge = {{ $customer->service_charge ?? 0 }};
    console.log("rinning TTB " + runningTTB);
    console.log("rate " + lastRunningrate);
    if(runningTTB==0){
        document.getElementById('cutposition').textContent = 0;

    }

    document.getElementById('runningSellTTB').textContent = runningSellTTBValue;
    document.getElementById('runningBuyTTB').textContent = runningBuyTTB;
    const ouncesToGrams = 31.1035;
    const currentAmount = @json($current_amount);
    let buyPriceGlobal = null;


    function showStatement(type) {

        const id = $('#customer_id').val();
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




    async function getGoldPrice() {
        if (isFetching) return;

        isFetching = true;
        try {
            const response = await fetch("{{ route('rate') }}", {
                method: 'GET',
            });

            if (!response.ok) {
                throw new Error('Gold rate service returned an error');
            }

            let data = await response.json();
            if (!Number.isFinite(Number(data.value))) {
                throw new Error('Gold rate service returned an invalid rate');
            }
            rateErrorShown = false;
            
            let sellPrice = data.value - 0.53;
            let sellDiv = document.getElementById('sellrate');
            sellDiv.textContent = `$${sellPrice.toFixed(3)}`;
            
            let buyPrice = parseFloat(sellPrice) + 1;
            buyPriceGlobal = sellPrice;
            let price = sellPrice;

            let priceDiv = document.getElementById('buyrate');
            priceDiv.textContent = `$${buyPrice.toFixed(3)}`;

            

            let sum = 0;
            $('.ratelist').each(function(index) {
                let qty = $(this).attr("data-qty");
                let dataId = $(this).attr("data-id");
                let dataType = $(this).attr("data-type");
                let perQtyPrice = qty * 13.7639;

                // let runningValue = dataType == 'sell' ?   perQtyPrice * buyPrice : perQtyPrice * sellPrice;
                let runningValue = perQtyPrice * price;

                $(this).html(runningValue.toFixed(3));

                let oldBalance = parseFloat($("#oldbalance-" + dataId).text().replace(/,/g, ''));
                

                let newBalance = 0;
                if (dataType == 'sell') {
                    newBalance = oldBalance - runningValue;
                } else {
                    newBalance = runningValue - oldBalance;
                }

                let current_rate = parseFloat($("#current_rate-" + dataId).text().replace(/,/g, ''));

                sum = sum + newBalance;
                
                $("#balance-" + dataId).html(newBalance.toFixed(3));
                
                // try {
                //     letPrfoitLoss = 0;
                //     if (dataType == 'sell') {
                //         letPrfoitLoss = ((current_rate - price) - (serviceCharge * 13.7639)) * perQtyPrice;
                //     } else {
                //         letPrfoitLoss = ((price - current_rate) - (serviceCharge * 13.7639)) * perQtyPrice;
                //     }

                //     let profit_lossDiv = document.getElementById("profit_loss-" + dataId);
                //     if (profit_lossDiv != null) {
                //         profit_lossDiv.textContent = `After Charge: ${letPrfoitLoss.toFixed(3)}`;
                //     }

                // } catch (e) {
                //     console.log(e);
                // }
            });

            let amount = parseFloat(currentAmount.replace(/,/g, ''));

            let new_amount = amount + sum;
            let equityDiv = document.getElementById('equity');
            equityDiv.textContent = `${new_amount.toFixed(3)}`;
            // console.log("maxtt_per_K " + maxtt_per_K);

            if (maxtt_per_K === null) {
                fire('Please enter Max TT Per Thousand');
                return false;
            }

            let max = Math.round(new_amount / (1000 / maxtt_per_K));
            if (max < 0) {
                max = 0;
            }


            let availableTTB = document.getElementById('availableTTB');
            availableTTB.textContent = `${(max).toFixed(3)}`;
            // const equityDiv = document.getElementById('equity');
            // equityDiv.textContent = `$${sellPrice}`;

        } catch (error) {
            console.error('Error fetching the gold price:', error);
            if (!rateErrorShown) {
                fire('Unable to fetch the current gold price. Retrying…');
                rateErrorShown = true;
            }
        } finally {
            isFetching = false;
        }

        const currentDay = new Date().getDay();
        if (currentDay !== 0 && currentDay !== 6) {
            setTimeout(getGoldPrice, 1000);
        }
    }
    getGoldPrice();

    // $(document).ready(function() {
    //     $('#pendingBtn').on('click', function(event) {
    //         event.preventDefault();
    //         const customerId = "{{ $customer->id }}";

    //         const url =
    //             `{{ route('admin.buysell.get.pending') }}?id=${encodeURIComponent(customerId)}`;

    //         $(this).attr('data-action', url);

    //         loadModalContent(url);
    //     });
    // });

    function loadModalContent(url) {

        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                $('#modalBody').html(response);
                $('#modal').modal('show');
            },
            error: function(xhr, status, error) {
                fire('Error: ' + error);
            }
        });
    }
</script>
<script>
    var runningBuySell = @json($runningBuySell);


    

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

    document.querySelectorAll('.load_form').forEach(button => {
        button.addEventListener('click', function() {
            const rowId = this.getAttribute('data-id');
            const transactionData = JSON.parse(this.getAttribute('data-transaction'));
            const qty = parseFloat(this.getAttribute('data-qty'));
            const currentRate = parseFloat(this.getAttribute('data-current-rate'));

            if (transactionData) {
                const row = document.getElementById(`form-row-${rowId}`);

                if (row) {
                    row.remove();
                } else {
                    const newRow = document.createElement('tr');
                    newRow.id = `form-row-${rowId}`;
                    newRow.innerHTML = `
                        <td colspan="{{ auth()->user()->can('update_running_trade') ? 16 : 15 }}">
                            <form id="save-${rowId}"  action="{{ route('admin.buysell.deposit.save') }}" method="POST" >
                                @csrf
                                <div class="row d-flex justify-content-center mx-5">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="reference_no" class="font-weight-bold">Reference No</label>
                                            <input type="text" name="reference_no" class="form-control reference_no">
                                            <span class="text-danger" id="error-message-${rowId}" style="display:none;"></span>
                                        </div>
                                    </div> 
                                    
                                </div> 
                                <div class="row d-flex justify-content-center mx-5">
                                    
                                    <input type="hidden" name="quantity" class="quantity-input" required value="${qty}" min="1" max="${qty}">
                             
                                    <div class="col-md-3">
                                            <label for="close_rate-${rowId}" class="mr-2 font-weight-bold">Closing Rate</label>
                                                <input type="text" inputmode="decimal" name="current_rate" id="close_rate-${rowId}" class="form-control" >
                                    
                                    </div>
                                    
                                </div>
                                <div class="row d-flex justify-content-center mx-5">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <h5 id="profit_loss-${rowId}"></h5>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="business_id" value="${transactionData.business_id}" />
                                <input type="hidden" name="starting_rate" value="${transactionData.current_rate}" />
                                <input type="hidden" name="customer_id" value="${transactionData.customer_id}" />
                                <input type="hidden" name="transaction_type" value="${transactionData.type}" />
                                <input type="hidden" name="reference_table" value="buysells" />
                                <input type="hidden" name="reference_row" value="${transactionData.id}" />
                                <input type="hidden" name="tnx_id" value="${Date.now()}" />
                                
                                <div class="row d-flex justify-content-center mx-5">
                                    <div class="col-md-3">
                                        <div class="form-group text-right mt-3">
                                            <button type="submit" class="btn btn-primary submit-btn" id="submitButton" onclick="validateAndSubmit(this, 'save-${rowId}')" >Submit</button>
                                        </div>
                                    </div>
                                </div>
                                
                            </form>
                        </td>
                    `;
                    this.closest('tr').after(newRow);

                    const referenceNoInput = newRow.querySelector('.reference_no');
                    const quantityInput = newRow.querySelector('.quantity-input');
                    const errorMessage = newRow.querySelector(`#error-message-${rowId}`);
                    const submitButton = newRow.querySelector('.submit-btn');
                    const manualRateCheckbox = newRow.querySelector(`.manual-rate`);
                    const currentRateInput = newRow.querySelector(`#close_rate-${rowId}`);
                    const takeProfitInput = newRow.querySelector(`#take_profit-${rowId}`);
                    const stopLossInput = newRow.querySelector(`#stop_loss-${rowId}`);

                    currentRateInput.removeAttribute('id');

                    referenceNoInput.addEventListener('blur', function() {
                        const referenceNo = referenceNoInput.value;

                        if (referenceNo) {
                            $.ajax({
                                url: `{{ route('validate.reference', '') }}/${referenceNo}`,
                                method: 'GET',
                                success: function(data) {
                                    if (data.success) {
                                        errorMessage.style.display = 'none';
                                        quantityInput.disabled = false;
                                        submitButton.disabled = false;
                                        currentRateInput.disabled = false;
                                    } else {
                                        errorMessage.textContent =
                                            'Invalid reference number.';
                                        errorMessage.style.display = 'block';
                                        quantityInput.disabled = false;
                                        submitButton.disabled = true;
                                        currentRateInput.disabled = false;
                                        referenceNoInput.focus();
                                    }
                                },
                                error: function(error) {
                                    console.error('Error:', error);
                                    errorMessage.textContent =
                                        'Error checking reference number.';
                                    errorMessage.style.display = 'block';
                                    quantityInput.disabled = false;
                                    submitButton.disabled = true;
                                    currentRateInput.disabled = false;
                                }
                            });
                        } else {
                            quantityInput.disabled = false;
                            submitButton.disabled = false;
                            currentRateInput.disabled = false;
                        }
                    });
                }
            }
        });
    });
    
    
    function formatDateTime(datetime) {
        const date = new Date(datetime);
    
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-based
        const day = String(date.getDate()).padStart(2, '0');
    
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const seconds = String(date.getSeconds()).padStart(2, '0');
    
        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    }
    
    document.querySelectorAll('.load_modify_form').forEach(button => {
        button.addEventListener('click', function() {
            const rowId = this.getAttribute('data-id');
            const transactionData = JSON.parse(this.getAttribute('data-transaction'));
            const qty = parseFloat(this.getAttribute('data-qty'));
            const currentRate = parseFloat(this.getAttribute('data-current-rate'));

            if (transactionData) {
                const row = document.getElementById(`form-row-${rowId}`);

                if (row) {
                    row.remove();
                } else {
                    const newRow = document.createElement('tr');
                    newRow.id = `form-row-${rowId}`;
                    newRow.innerHTML = `
                        <td colspan="{{ auth()->user()->can('update_running_trade') ? 16 : 15 }}">
                            <form id="modifyForm-${rowId}" class="modify-trade-form" action="{{ route('admin.buysell.store.price') }}" method="POST" >
                                @csrf
                                
                                <div class="row mx-5"
                                    <?= auth()->user()->can('update_running_trade') == false ? 'style="display:none"' : '' ?>>
            
            
                                    <div class="col-md-3">
            
                                        <label for="created_at" class="font-weight-bold">Date Time</label>
                                        <input type="datetime-local" name="created_at"
                                            value="${formatDateTime(transactionData.created_at)}" class="form-control"
                                            required>
            
                                    </div>
            
                                    <div class="col-md-3">
                                        <label for="type" class="font-weight-bold">Type</label>
                                        <select name="type" class="form-control" required>
                                            <option value="">Select Type</option>
                                            <option value="buy" ${transactionData.type == 'buy' ? 'selected' : ''}>Buy</option>
                                            <option value="sell" ${transactionData.type == 'sell' ? 'selected' : ''}>Sell
                                            </option>
                                        </select>
                                    </div>
            
                                    <div class="col-md-3">
                                        <label for="rate" class="font-weight-bold">Edit Rate</label>
                                        <input type="text" name="rate" value="${transactionData.current_rate}"
                                            class="form-control" required>
            
                                    </div>
            
                                    <div class="col-md-3">
                                        <label for="tt" class="font-weight-bold">TT Quantity</label>
                                        <input type="number" name="tt" class="form-control"
                                            value="${qty}" required>
                                    </div>
            
                                </div>
                                <br>
                                
                                <div class="row mx-5">
                                    <div class="col-md-3">
                                        <label for="rate" class="font-weight-bold">Take Profit</label>
                                        <input type="text" name="take_profit" value="${transactionData.take_profit == null ? 0 : transactionData.take_profit}"
                                            class="form-control" >
                                    </div>
            
                                    <div class="col-md-3">
                                        <label for="tt" class="font-weight-bold">Stop Loss</label>
                                        <input type="text" name="stop_loss" class="form-control"
                                            value="${transactionData.stop_loss == null ? 0 : transactionData.take_profit}" >
                                    </div>

                                    <div class="col-md-3">
                                        <label for="swap_charge" class="font-weight-bold">Swap Charge</label>
                                        <input type="number" name="swap_charge" class="form-control" step="any" inputmode="decimal"
                                            value="${transactionData.swap_charge == null ? 0 : transactionData.swap_charge}" required>
                                    </div>
                                    
                                    <div class="col-md-3 d-flex justify-content-start align-items-end">
                                        <input type="hidden" id="transactionId" name="transactionId" value="${transactionData.id}">
                                        <div class="form-group" style="margin-bottom:0px !important">
                                            <button type="submit" class="btn btn-primary submit-btn">Save changes</button>
                                        </div>
                                    </div>
                                </div>
                               
                            </form>
                        </td>
                    `;
                    this.closest('tr').after(newRow);

                    
                }
            }
        });
    });
    
    
    document.querySelectorAll('.splitForm').forEach(button => {
        button.addEventListener('click', function() {
            const rowId = this.getAttribute('data-id');
            const transactionData = JSON.parse(this.getAttribute('data-transaction'));
            const qty = parseFloat(this.getAttribute('data-qty'));
            const currentRate = parseFloat(this.getAttribute('data-current-rate'));

            if (transactionData) {
                const row = document.getElementById(`form-row-${rowId}`);

                if (row) {
                    row.remove();
                } else {
                    const newRow = document.createElement('tr');
                    newRow.id = `form-row-${rowId}`;
                    newRow.innerHTML = `
                        <td colspan="14">
                            <form id="splitForm-${rowId}" onsubmit="return validateAndSubmit(event)" action="{{ route('admin.buysell.split.store') }}" method="POST">
                                @csrf
                                <div class="row d-flex justify-content-center  mx-5">
                                    <div id="errorMessage" class="alert alert-danger d-none"></div>
                
                                    <div class="col-md-3">
                                        <label for="split_quantity" class="font-weight-bold">Split TTB Quantity</label>
                                        <input type="number" name="split_quantity" class="form-control" required>
                                    </div>
                                    
                                </div>
                
                                <input type="hidden" id="transactionId" name="transactionId" value="${transactionData.id}">
                
                                <div class="row d-flex justify-content-center  mx-5 mt-2">
                                    <div class="col-md-4 d-flex justify-content-end">
                                        <button id="submitButton" type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                    
                                </div>
                            </form>
                        </td>
                    `;
                    this.closest('tr').after(newRow);

                    
                }
            }
        });
    });
    
    function selectTrade(type, tt, tradeId, tradeRate, tradeAmount, transactionAmount) {
        selectedTT = tt;
        selectedTradeId = tradeId;
        selectedTradeRate = tradeRate;
        // Update hidden inputs with the selected trade data
        document.getElementById('selectedTradeId').value = selectedTradeId;
        document.getElementById('selectedTradeRate').value = selectedTradeRate;
        const profitLoss = document.getElementById('profitLoss');
        if (type === 'sell') {
            profitLoss.textContent = (tradeAmount - transactionAmount).toFixed(3);
        } else {
            profitLoss.textContent = (transactionAmount - tradeAmount).toFixed(3);
        }

    }
    
    document.querySelectorAll('.matchTradeForm').forEach(button => {
        button.addEventListener('click', function () {
            const customerid = $('#customer_id').val();
            const rowId = this.getAttribute('data-id');
            const transactionData = JSON.parse(this.getAttribute('data-transaction'));
            const qty = parseFloat(this.getAttribute('data-qty'));
            const currentRate = parseFloat(this.getAttribute('data-current-rate'));
    
            let url = `/admin/buysell/trades-customer/${customerid}/${transactionData.type}/${qty}`;
            $.ajax({
                url: url,
                type: 'GET',
                success: function (res) {
                    const trades = res.trades;
    
                    const existingRow = document.getElementById(`form-row-${rowId}`);
                    if (existingRow) {
                        existingRow.remove();
                        return;
                    }
    
                    const newRow = document.createElement('tr');
                    newRow.id = `form-row-${rowId}`;
                    newRow.innerHTML = `
                        <td colspan="14">
                            <form id="matchTradeForm-${rowId}" onsubmit="return validateAndSubmitMatchTrade(event)"
                                action="{{ route('admin.buysell.match.trade.store') }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div id="errorMessage" class="alert alert-danger d-none"></div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>${transactionData.type.toUpperCase()} Trade</h6>
                                            <table class="table table-bordered">
                                                <tbody>
                                                    <tr><td>Date</td><td>${formatDateTime(transactionData.created_at)}</td></tr>
                                                    <tr><td>Type</td><td>${transactionData.type}</td></tr>
                                                    <tr><td>Amount</td><td>${(transactionData.current_rate * 13.7639 * (transactionData.tt_quantity - transactionData.close_quanntity)).toFixed(3)}</td></tr>
                                                    <tr><td>${transactionData.type} Rate</td><td>${transactionData.current_rate}</td></tr>
                                                    <tr><td>Quantity</td><td>${transactionData.tt_quantity} TTB</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
    
                                        <div class="col-md-6">
                                            <h6>${transactionData.type === 'buy' ? 'SELL' : 'BUY'} Trades</h6>
                                            <div style="max-height: 250px; overflow-y: auto;">
                                                <table class="table table-bordered">
                                                    <thead><tr><th>TTB</th><th>Rate</th></tr></thead>
                                                    <tbody id="sellTableBody">
                                                        ${trades.length > 0 ? trades.map(trade => {
                                                            let oldTradeAmount = (trade.current_rate * 13.7639 * (trade.tt_quantity - trade.close_quanntity)).toFixed(3);
                                                            return `
                                                                <tr>
                                                                    <td>
                                                                        <input type="radio" name="sellRadio"
                                                                            onclick="selectTrade('sell', ${trade.tt_quantity}, '${trade.id}', '${trade.current_rate}', '${oldTradeAmount}', '${oldTradeAmount}')"
                                                                            data-tt="${trade.tt_quantity}">
                                                                        &nbsp;&nbsp;&nbsp; ${trade.tt_quantity} TTB
                                                                    </td>
                                                                    <td>${trade.current_rate}</td>
                                                                </tr>`;
                                                        }).join('') : `<tr><td colspan="2">No trades found</td></tr>`}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
    
                                    <input type="hidden" name="transactionId" value="${transactionData.id}">
                                    <input type="hidden" name="customerId" value="${transactionData.customer_id}">
                                    <input type="hidden" name="starting_rate" value="${transactionData.current_rate}">
                                    <input type="hidden" name="type" value="${transactionData.type}">
                                    <input type="hidden" name="quantity" value="${transactionData.tt_quantity}">
                                    <input type="hidden" id="selectedTradeId" name="selectedTradeId" value="">
                                    <input type="hidden" id="selectedTradeRate" name="selectedTradeRate" value="">
                                </div>
                                <hr>
                                <div class="col-md-12">
                                    <div class="form-group col-md-6">
                                        <h7>Profit Loss: <span id="profitLoss">0.00</span></h7>
                                    </div>
                                    <div class="modal-footer">
                                        <button id="submitButton" type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                            </form>
                        </td>`;
    
                    // Append row after the current button's row
                    button.closest('tr').after(newRow);
                }
            });
        });
    });


    function validateDecimalInput(input) {
        const regex = /^\d+(\.\d{1,4})?$/;
        const value = input.value.trim();

        if (regex.test(value)) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            return true;
        } else {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
            return false;
        }
    }

    function validateAndSubmit(event, formId) {

        const submitButton = document.getElementById('submitButton');
        submitButton.disabled = true;
        submitButton.textContent = 'Processing...';
        document.getElementById(formId).submit();

    }

    document.addEventListener('submit', function(event) {
        const form = event.target;
        if (!form.matches('.modify-trade-form')) {
            return;
        }

        event.preventDefault();
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const submitButton = form.querySelector('.submit-btn');
        const originalText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = 'Saving...';

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new FormData(form)
        })
        .then(async response => {
            const payload = await response.json();
            if (!response.ok) {
                throw new Error(payload.message || 'Unable to save the trade changes.');
            }
            return payload;
        })
        .then(payload => {
            const trade = payload.trade;
            const tradeRow = form.closest('tr').previousElementSibling;
            const cells = tradeRow.cells;
            const currentValue = tradeRow.querySelector('.ratelist');

            cells[2].textContent = trade.created_at;
            cells[3].textContent = trade.type;
            cells[4].textContent = trade.quantity;
            cells[5].textContent = trade.open_rate;
            cells[6].textContent = trade.total_value;
            cells[7].textContent = trade.service_charge;
            cells[8].textContent = trade.swap_charge;
            cells[11].textContent = trade.take_profit;
            cells[12].textContent = trade.stop_loss;

            currentValue.dataset.type = trade.type;
            currentValue.dataset.qty = trade.quantity;
            currentValue.dataset.startrate = trade.total_value;
            form.closest('tr').remove();

            if (typeof getGoldPrice === 'function') {
                getGoldPrice();
            }

            Swal.fire({ icon: 'success', title: 'Saved', text: 'Trade updated successfully.', timer: 1400, showConfirmButton: false });
        })
        .catch(error => {
            Swal.fire({ icon: 'error', title: 'Update failed', text: error.message });
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        });
    });

    function voidTransaction(transactionId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This action cannot be undone. Do you want to void this transaction?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Void it!',
        }).then((result) => {
            if (result.value == true) {
                $.ajax({
                    url: '{{ route('admin.buysell.delete') }}',
                    type: 'POST',
                    data: {
                        id: transactionId.id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            text: 'Transaction deleted successfully.'
                        });
                        window.location.reload();

                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            text: 'An error occurred while deleting the transaction.'
                        });
                    }
                });
            }
        });
    }
</script>

@section('styles')
    <style>
        td .d-flex.flex-wrap>a,
        td .d-flex.flex-wrap>button {
            flex: 1 0 auto;
            /* Ensures buttons adjust their width automatically */
            white-space: nowrap;
            /* Prevents text wrapping inside buttons */
        }
    </style>
@stop
