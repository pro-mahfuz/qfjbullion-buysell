<div class="row mt-5">
    <div class="col-md-6">
        
        <h5>Balance Summary</h5>
        <table class="table table-striped table-bordered">
            
            <thead>
                <tr>
                    <th style="text-align:center;">BALANCE</th>
                    <th style="text-align:center;">DEPOSIT</th>
                    <th style="text-align:center;">WITHDRAW</th>
                    <th style="text-align:center;">EQUITY</th>
                    <th style="text-align:center;">MARGIN LIMIT</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td style="text-align:center; font-size:23px"> {{ $current_amount }} </td>
                    <td style="text-align:center; font-size:23px"> {{ $deposit }} </td>
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
    <!--<div class="col-md-6">-->
    <!--    <div class="row">-->
    <!--        @can('pending_buy_sell')-->
    <!--            <div class="form-group col-md-12">-->
    <!--                {{-- <button type="button" id="pendingBtn" class="btn btn-primary btn-block load_modal"-->
    <!--                    data-toggle="modal">Pending </button> --}}-->

    <!--                <button class="btn btn-primary load_modal col-md-12" data-bs-toggle="modal"-->
    <!--                    data-action="{{ route('admin.buysell.get.pending', ['id' => $customer->id, 'type' => 'deposit']) }}">-->
    <!--                    Pending-->
    <!--                </button>-->
    <!--            </div>-->
    <!--        @endcan-->

    <!--    </div>-->

    <!--    @can('buy_sell_box')-->
    <!--    <div class="card">-->
    <!--        <div class="card-header bg-success text-white" style="text-align:center; font-size:30px;"> BUY SELL BOX-->
    <!--        </div>-->
    <!--        <div class="card-body">-->
    <!--            <form action="{{ route('admin.transaction.save') }}" method="POST" id="tradeForm">-->
    <!--                @csrf-->
    <!--                <div class="row">-->
    <!--                    <div class="form-group col-md-12">-->
    <!--                        <label for="reference_no">Reference/Ticket No. </label>-->
    <!--                        <input type="text" class="form-control" name="reference_no" id="reference_no"-->
    <!--                            placeholder="Reference No.">-->
    <!--                    </div>-->
    <!--                    <div class="form-group col-md-6">-->
    <!--                        <label for="bid">TTB QTY</label>-->
    <!--                        <input type="number" class="form-control" name="bid" id="bid"-->
    <!--                            placeholder="Enter TT" required>-->
    <!--                    </div>-->
    <!--                    <div class="form-group col-md-6">-->
    <!--                        <label for="gold_value">Running Rate</label>-->
    <!--                        <input type="number" class="form-control" name="gold_value" id="gold_value" required>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--                <input type="hidden" name="id" id="customer_id" value="{{ $customer->id }}">-->
    <!--                <hr>-->
    <!--                <div class="row">-->

    <!--                    <div class="form-group col-md-6">-->
    <!--                        <button type="button" id="buyBtn" class="btn btn-info btn-block"-->
    <!--                            onclick="handleTransactionClick('buy')"-->
    <!--                            @if ($customer->status == 'deactived') disabled @endif>Buy</button>-->
    <!--                    </div>-->

    <!--                    <div class="form-group col-md-6 text-right">-->
    <!--                        <button type="button" id="sellBtn" class="btn btn-danger btn-block"-->
    <!--                            onclick="handleTransactionClick('sell')"-->
    <!--                            @if ($customer->status == 'deactived') disabled @endif>Sell</button>-->
    <!--                    </div>-->

    <!--                </div>-->
    <!--            </form>-->
    <!--        </div>-->

    <!--    </div>-->
    <!--    @endcan-->
    <!--</div>-->

</div>

<div class="row mt-2">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-center align-items-center">

                    <h5> SELL: <span id="sellrate">0.00</span> &nbsp; BUY: <span id="buyrate">0.00</span></h5>
                </div>
            </div>
            <div class="card-body" id="runningStateDiv">
                <div class="table-responsive mt-3">
                    <table class="table table-striped table-bordered">

                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Reference No</th>
                                <th scope="col">Date</th>
                                <th scope="col">Type</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Open Rate</th>
                                {{-- <th scope="col">Total (AED)</th>
                                <th scope="col">Current Rate (AED)</th> --}}
                                <th scope="col">Profit/Loss</th>
                                <th scope="col">TP</th>
                                <th scope="col">SL</th>
                                <th scope="col">Created By</th>
                                <th scope="col" style="witdh: 280px">Action</th>
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
                                        <th scope="row">{{ $sl++ }}</th>
                                        <td>{{ $transaction->reference_no }}</td>
                                        <td>{{ $transaction->created_at }}</td>
                                        <td>{{ $transaction->type }}</td>
                                        <td>{{ $transaction->tt_quantity - $transaction->close_quanntity }}</td>
                                        <td id="current_rate-{{ $sl }}">
                                            {{ number_format($transaction->current_rate, 3) }}</td>

                                        <td id="oldbalance-{{ $sl }}"
                                            style="text-align: center; display: none">
                                            {{ number_format($transaction->current_rate * 13.7639 * ($transaction->tt_quantity - $transaction->close_quanntity), 3) }}
                                        </td>
                                        <td style="text-align: center; display: none"><span
                                                data-id="{{ $sl }}" data-type="{{ $transaction->type }}"
                                                data-qty="{{ $transaction->tt_quantity - $transaction->close_quanntity }}"
                                                data-startrate="{{ number_format($transaction->current_rate * 13.7639 * ($transaction->tt_quantity - $transaction->close_quanntity), 3) }}"
                                                class="ratelist">{{ number_format($transaction->current_rate, 3) }}<span>
                                        </td>

                                        <td id="balance-{{ $sl }}" style="text-align: right;">
                                            {{ number_format($transaction->current_rate, 3) }}
                                        </td>
                                        <td> {{ $transaction->take_profit }} </td>
                                        <td> {{ $transaction->stop_loss }} </td>
                                        <td> {{ $transaction->created_by }} </td>
                                        <td>
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

                                                <a href="#" class="btn btn-success btn-sm load_modal ml-1"
                                                    data-toggle="modal"
                                                    style="background-color: #6C63FF; border-color: #6C63FF;"
                                                    data-action="{{ route('admin.buysell.match.trade', ['id' => $customer['id'], 'transaction_id' => $transaction]) }}">
                                                    Match Trade
                                                </a>

                                                <a href="#" class="btn btn-sm text-white load_modal ml-1"
                                                    data-toggle="modal"
                                                    style="background-color: #FF9F1C; border-color: #FF9F1C;"
                                                    data-action="{{ route('admin.buysell.split.trade', ['id' => $customer['id'], 'transaction_id' => $transaction]) }}">
                                                    Split Trade
                                                </a>


                                                    <a href="#" class="btn btn-sm text-white load_modal ml-1"
                                                        style="background-color: #2A9D8F; border-color: #2A9D8F;"
                                                        data-toggle="modal"
                                                        data-action="{{ route('admin.buysell.edit.price', ['id' => $customer['id'], 'transaction_id' => $transaction]) }}">
                                                        Modify
                                                    </a>

                                                <button class="btn btn-sm text-white ml-1"
                                                    style="background-color: #D72638; border-color: #D72638;"
                                                    onclick="voidTransaction({{ json_encode($transaction) }})">
                                                    Void
                                                </button>
                                            </div>
                                        </td>
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

<script>
    let previousPrice = null;
    let isFetching = false;
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
            const response = await fetch('https://furqanjewelry.com/api/get-gold-price', {
                method: 'GET',
                // headers: {
                //     'x-access-token': 'goldapi-7q9uy0tkwrfdtlo-io',
                //     'Content-Type': 'application/json',
                // },
            });

            if (!response.ok) {
                fire('Error fetching the gold price');
            }

            let data = await response.json();
            
            let sellPrice = data.gold_sell_price - 0.53;
            let sellDiv = document.getElementById('sellrate');
            sellDiv.textContent = `$${sellPrice.toFixed(3)}`;
            
            let buyPrice = parseFloat(sellPrice) + 1;
            buyPriceGlobal = parseFloat(sellPrice) + 1;
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
                try {
                    letPrfoitLoss = 0;
                    if (dataType == 'sell') {
                        letPrfoitLoss = ((current_rate - price) - serviceCharge) * perQtyPrice;
                    } else {
                        letPrfoitLoss = ((price - current_rate) - serviceCharge) * perQtyPrice;
                    }

                    let profit_lossDiv = document.getElementById("profit_loss-" + dataId);
                    if (profit_lossDiv != null) {
                        profit_lossDiv.textContent = `After Charge: ${letPrfoitLoss.toFixed(3)}`;
                    }

                } catch (e) {
                    console.log(e);
                }
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


    function handleTransactionClick(type) {
        var formData = {
            tt_quantity: document.getElementById('bid').value,
            current_rate: document.getElementById('gold_value').value,
            total_amount_aed: (document.getElementById('gold_value').value * 3.745 * 3.67 * document.getElementById(
                'bid').value).toFixed(3),
            close_quanntity: 0,
            type: type,
            cut_position: 0,
            reference_no: document.getElementById('reference_no').value,
            customer_id: {{ $customer->id }},
            _token: '{{ csrf_token() }}'
        };
        var tt_quantity = document.getElementById('bid').value;
        var current_rate = document.getElementById('gold_value').value;

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
        console.log("equity" + equity);
        console.log("tt_quantity " + tt_quantity);
        console.log("Sell TTB " + runningSellTTBValue);
        console.log("Buy TTB " + runningBuyTTB);
        console.log("Available TTB " + getAvailableTT);


        if (equity < 0) {
            fire('You are not eligible to buy/sell , Please check customer equity!!');
            return false;
        }

        if (type == 'buy') {
            exicution_ttb = (runningBuyTTB > runningSellTTBValue) ? (getAvailableTT - ((runningBuyTTB -
                runningSellTTBValue > 0) ? runningBuyTTB - runningSellTTBValue : 0)) : (getAvailableTT + (
                runningSellTTBValue - runningBuyTTB));
        } else {
            exicution_ttb = (runningSellTTBValue > runningBuyTTB) ? (getAvailableTT - (runningSellTTBValue -
                runningBuyTTB)) : (getAvailableTT + (runningBuyTTB - runningSellTTBValue));

        }

        if (exicution_ttb < tt_quantity) {
            fire('TTB Quantity limit exceeded, Please check TTB Quantity.');
            return false;
        }


        document.getElementById('buyBtn').disabled = true;
        document.getElementById('sellBtn').disabled = true;
        document.getElementById('bid').disabled = true;
        document.getElementById('gold_value').disabled = true;

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
                document.getElementById('bid').disabled = false;
                document.getElementById('gold_value').disabled = false;

                document.getElementById('bid').value = "";
                document.getElementById('gold_value').value = "";
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
                        <td colspan="10">
                            <form id="save-${rowId}"  action="{{ route('admin.buysell.deposit.save') }}" method="POST" >
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="reference_no">Reference No</label>
                                            <input type="text" name="reference_no" class="form-control reference_no">
                                            <span class="text-danger" id="error-message-${rowId}" style="display:none;"></span>
                                        </div>
                                    </div> 
                                    <input type="hidden" name="quantity" class="quantity-input" required value="${qty}" min="1" max="${qty}">
                             
                                    <div class="col-md-6">
                                            <label for="close_rate-${rowId}" class="mr-2">Closing Rate</label>
                                                <input type="text" inputmode="decimal" name="current_rate" id="close_rate-${rowId}" class="form-control" >
                                    </div>
                                    </div>
                                    <div class="col-md-6">
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
                                <div class="form-group text-right mt-3">
                                    <button type="submit" class="btn btn-primary submit-btn" id="submitButton" onclick="validateAndSubmit(this, 'save-${rowId}')" >Submit</button>
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

                    // quantityInput.addEventListener('input', function() {
                    //     const quantityValue = quantityInput.value;
                    //     if (quantityValue && !quantityInput.disabled) {
                    //         submitButton.disabled = false;
                    //     } else {
                    //         submitButton.disabled = true;
                    //     }
                    // });
                    // currentRateInput.addEventListener('input', function() {
                    //     let valid = validateDecimalInput(currentRateInput);
                    //     if (!valid) {
                    //         submitButton.disabled = true;
                    //     }
                    // });


                    // manualRateCheckbox.addEventListener('change', function() {
                    //     if (this.checked) {
                    //         currentRateInput.removeAttribute(
                    //             'id');
                    //     } else {
                    //         currentRateInput.id =
                    //             `close_rate-${rowId}`;
                    //     }
                    // });
                }
            }
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


    // document.querySelectorAll('input[name="current_rate"]')
    //     .forEach(input => {
    //         input.addEventListener('input', () => validateDecimalInput(input));
    //     });

    function validateAndSubmit(event, formId) {

        const submitButton = document.getElementById('submitButton');
        submitButton.disabled = true;
        submitButton.textContent = 'Processing...';
        document.getElementById(formId).submit();

    }

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
