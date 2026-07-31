<div class="row mt-2">
    <div class="col-md-6 d-flex">
        <div class="card flex-grow-1">
            <div class="card-header bg-success text-white" style="text-align:center; font-size:30px;">BUY SELL BOX
            </div>
            <div class="card-body">
                <form action="{{ route('client.transaction.save') }}" method="POST" id="tradeForm">
                    @csrf
                    <div class="row">
                        <!-- Select Product Button -->
                        <div class="form-group col-md-12">
                            <button type="button" class="btn btn-primary btn-block load_modal" data-toggle="modal"
                                data-target="#dynamicModal12" data-action="{{ route('client.shop') }}">
                                Select Product
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Selected Product Name -->
                        <div class="form-group col-md-12">
                            <label for="selected_product">Selected Product</label>
                            <input type="text" class="form-control" name="selected_product" id="selected_product"
                                readonly>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Qty Input -->
                        <div class="form-group col-md-6">
                            <label for="qty">Qty</label>
                            <input type="number" class="form-control" name="qty" id="qty" required disabled>
                        </div>

                        <!-- Running Rate -->
                        <div class="form-group col-md-6">
                            <label for="gold_value">Running Rate</label>
                            <input type="text" class="form-control" name="gold_value" id="gold_value" readonly>
                        </div>
                    </div>

                    <!-- Hidden Customer ID -->
                    <input type="hidden" name="id" id="customer_id" value="{{ $customer['id'] }}">
                    <input type="hidden" name="selected_product_id" id="selected_product_id">

                    <hr>

                    <!-- Buy and Sell Buttons -->
                    <div class="row">
                        <div class="form-group col-md-6">
                            <button type="button" id="buyBtn" class="btn btn-info btn-block"
                                onclick="handleTransactionClick('buy')"
                                @if ($customer['status'] == 'deactived') disabled @endif>Buy</button>
                        </div>

                        <div class="form-group col-md-6 text-right">
                            <button type="button" id="sellBtn" class="btn btn-danger btn-block"
                                onclick="handleTransactionClick('sell')"
                                @if ($customer['status'] == 'deactived') disabled @endif>Sell</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6 d-flex">
        <div class="card flex-grow-1">
            <div class="card-body">
                <div class="row">
                    <!-- BALANCE -->
                    <div class="col-md-6 col-lg-4 mt-1">
                        <div class="card text-white bg-primary mb-3 d-flex" style="height: 100%;">
                            <div class="card-body d-flex flex-column align-items-center flex-grow-1">
                                <span style="font-weight:bold; font-size:16px;">BALANCE</span>
                                <span style="font-size:18px;">{{ $current_amount }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- EQUITY -->
                    <div class="col-md-6 col-lg-4 mt-1">
                        <div class="card text-white bg-success mb-3 d-flex" style="height: 100%;">
                            <div class="card-body d-flex flex-column align-items-center flex-grow-1">
                                <span style="font-weight:bold; font-size:16px;">EQUITY</span>
                                <span id="equity" style="font-size:18px;">0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- CUT POSITION -->
                    <div class="col-md-6 col-lg-4 mt-1">
                        <div class="card text-white bg-danger mb-3 d-flex" style="height: 100%;">
                            <div class="card-body d-flex flex-column align-items-center flex-grow-1">
                                <span style="font-weight:bold; font-size:12px;">CUT POSITION</span>
                                <span id="cutposition" style="font-size:18px;">0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- AVAILABLE TTB -->
                    <div class="col-md-6 col-lg-4 mt-1">
                        <div class="card text-white bg-warning mb-3 d-flex" style="height: 100%;">
                            <div class="card-body d-flex flex-column align-items-center flex-grow-1">
                                <span style="font-weight:bold; font-size:12px;">AVAILABLE TTB</span>
                                <span id="availableTTB" style="font-size:18px;">0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- BUY TTB -->
                    <div class="col-md-6 col-lg-4 mt-1">
                        <div class="card text-white bg-info mb-3 d-flex" style="height: 100%;">
                            <div class="card-body d-flex flex-column align-items-center flex-grow-1">
                                <span style="font-weight:bold; font-size:16px;">BUY TTB</span>
                                <span id="runningBuyTTB" style="font-size:18px;">0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- SELL TTB -->
                    <div class="col-md-6 col-lg-4 mt-1">
                        <div class="card text-white bg-secondary mb-3 d-flex" style="height: 100%;">
                            <div class="card-body d-flex flex-column align-items-center flex-grow-1">
                                <span style="font-weight:bold; font-size:12px;">SELL TTB</span>
                                <span id="runningSellTTB" style="font-size:18px;">0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row mt-2">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center pull-right">
                    <h5>BUY: <span id="buyrate">0.00</span> &nbsp; SELL: <span id="sellrate">0.00</span></h5>
                </div>
            </div>
            <div class="card-body" id="runningStateDiv">
                <div class="table-responsive mt-3">
                    <table class="table table-striped table-bordered">

                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Date</th>
                                <th scope="col">Type</th>
                                <th scope="col">TT Quantity</th>
                                <th scope="col">Starting Rate</th>
                                <th scope="col">Total ({{ $currency }})</th>
                                <th scope="col">Current Rate ({{ $currency }})</th>
                                <th scope="col">Profit/Loss</th>
                                <th scope="col" style="witdh: 280px">Action</th>
                            </tr>
                        </thead>

                        <tbody id="table1" class="collapse show">
                            @if (isset($runningBuySell))
                                <?php $sl = 1; ?>
                                <?php $runningTTB = 0; ?>
                                <?php $runningSellTTB = 0; ?>
                                <?php $runningBuyTTB = 0; ?>
                                <?php $converted = 3.74632 * 3.674 * $conversion_rate * 100; ?>
                                @foreach ($runningBuySell as $transaction)
                                    <tr>
                                        <th scope="row">{{ $sl++ }}</th>
                                        <td>{{ $transaction['created_at'] }}</td>
                                        <td>{{ $transaction['type'] }}</td>
                                        <td>{{ $transaction['tt_quantity'] - $transaction['close_quanntity'] }}</td>
                                        <td id="current_rate-{{ $sl }}">
                                            {{ number_format($transaction['current_rate'], 3) }}</td>

                                        <td id="oldbalance-{{ $sl }}" style="text-align: center;">
                                            {{ number_format($transaction['current_rate'] * $converted * ($transaction['tt_quantity'] - $transaction['close_quanntity']), 3) }}
                                        </td>
                                        <td style="text-align: center;"><span data-id="{{ $sl }}"
                                                data-type="{{ $transaction['type'] }}"
                                                data-qty="{{ $transaction['tt_quantity'] - $transaction['close_quanntity'] }}"
                                                data-startrate="{{ number_format($transaction['current_rate'] * $converted * ($transaction['tt_quantity'] - $transaction['close_quanntity']), 3) }}"
                                                class="ratelist">{{ number_format($transaction['current_rate'], 3) }}<span>
                                        </td>
                                        <td id="balance-{{ $sl }}" style="text-align: right;">
                                            {{ number_format($transaction['current_rate'], 3) }}
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="javascript:void(0)"
                                                    class="btn {{ $transaction['type'] == 'buy' ? 'btn-info' : 'btn-danger' }} btn-sm load_form ml-1"
                                                    data-id="{{ $sl }}"
                                                    data-transaction="{{ json_encode($transaction) }}"
                                                    data-current-rate="{{ $transaction['current_rate'] }}"
                                                    data-qty="{{ $transaction['tt_quantity'] - $transaction['close_quanntity'] }}">
                                                    {{ $transaction['type'] == 'buy' ? 'Buy Close' : 'Sell Close' }}
                                                </a>

                                                <a href="#" class="btn btn-success btn-sm load_modal ml-1"
                                                    data-toggle="modal" style="background-color: #1a1287;"
                                                    data-action="{{ route('admin.buysell.match.trade', ['id' => $customer['id'], 'transaction_id' => $transaction]) }}">
                                                    Match Trade
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                    <?php
                                    $runningTTB += $transaction['type'] == 'buy' ? $transaction['tt_quantity'] - $transaction['close_quanntity'] : -($transaction['tt_quantity'] - $transaction['close_quanntity']);
                                    $runningSellTTB += $transaction['type'] == 'buy' ? 0 : $transaction['tt_quantity'] - $transaction['close_quanntity'];
                                    $runningBuyTTB += $transaction['type'] == 'buy' ? $transaction['tt_quantity'] - $transaction['close_quanntity'] : 0;
                                    ?>
                                @endforeach
                            @endif

                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    let previousPrice = null;
    let isFetching = false;

    var maxtt_per_K = @json($maxtt_per_K);
    var runningTTB = {{ $runningTTB }};
    var runningSellTTBValue = {{ $runningSellTTB ?? 0 }};
    var runningBuyTTB = {{ $runningBuyTTB }};
    var serviceCharge = {{ $service_charge ?? 0 }};
    var conversion_rate = {{ $conversion_rate }};
    var convertedRate = 3.74632 * 3.674 * conversion_rate * 100;
    var currency = '{{ $currency }}';

    document.getElementById('runningSellTTB').textContent = runningSellTTBValue;
    document.getElementById('runningBuyTTB').textContent = runningBuyTTB;

    const currentAmount = @json($current_amount);
    async function getGoldPrice() {
        if (isFetching) return;

        isFetching = true;

        try {
            const response = await fetch('https://gold.shadhinportal.com/api/gold?currency=aed', {
                method: 'GET',
                // headers: {
                //     'x-access-token': 'goldapi-7q9uy0tkwrfdtlo-io',
                //     'Content-Type': 'application/json',
                // },
            });

            if (!response.ok) {
                fire('Error fetching the gold price');
            }

            const data = await response.json();
            const buyPrice = data.ask;
            previousPrice = buyPrice;
            const priceDiv = document.getElementById('buyrate');
            priceDiv.textContent = `$${buyPrice}`;

            const sellPrice = data.bid;
            const sellDiv = document.getElementById('sellrate');
            sellDiv.textContent = `$${sellPrice}`;

            const gold_value = document.getElementById('gold_value');
            gold_value.value = buyPrice;

            let sum = 0;
            // alert(currency);
            $('.ratelist').each(function(index) {
                let qty = $(this).attr("data-qty");
                let dataId = $(this).attr("data-id");
                let dataType = $(this).attr("data-type");
                let perQtyPrice = qty * convertedRate;

                let runningValue = dataType == 'sell' ? perQtyPrice * sellPrice : perQtyPrice * buyPrice;

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
                        letPrfoitLoss = ((current_rate - sellPrice) - serviceCharge) * perQtyPrice;
                    } else {
                        letPrfoitLoss = ((buyPrice - current_rate) - serviceCharge) * perQtyPrice;
                    }
                    console.log(serviceCharge);
                    console.log('cvo', convertedRate);

                    // alert(letPrfoitLoss);
                    const profit_lossDiv = document.getElementById("profit_loss-" + dataId);
                    profit_lossDiv.textContent = `After Charge: ${letPrfoitLoss.toFixed(3)}`;

                    const current_rateDiv = document.getElementById("close_rate-" + dataId);
                    current_rateDiv.value = `${buyPrice.toFixed(3)}`;

                } catch (e) {

                }
            });

            let amount = parseFloat(currentAmount.replace(/,/g, ''));

            let new_amount = amount + sum;
            const equityDiv = document.getElementById('equity');
            equityDiv.textContent = `${new_amount.toFixed(3)}`;
            const cutpositionDiv = document.getElementById('cutposition');
            if (runningTTB == 0) {
                cutpositionDiv.textContent = 0;
            } else {
                cutpositionDiv.textContent =
                    `${(buyPrice - ((new_amount.toFixed(3) / convertedRate / runningTTB ).toFixed(3))).toFixed(3)}`;
            }

            if (maxtt_per_K === null) {
                fire('Please enter Max TT Per Thousand');
                return false;
            }

            const max = Math.round(new_amount / (1000 / maxtt_per_K));
            if (max < 0) {
                max = 0;
            }
            const availableTTB = document.getElementById('availableTTB');
            availableTTB.textContent = `${(max).toFixed(3)}`;

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
</script>
<script>
    var runningBuySell = @json($runningBuySell);

    function handleTransactionClick(type) {
        var formData = {
            tt_quantity: document.getElementById('qty').value,
            current_rate: document.getElementById('gold_value').value,
            total_amount_aed: (document.getElementById('gold_value').value * convertedRate * document
                .getElementById(
                    'qty').value).toFixed(3),
            close_quanntity: 0,
            type: type,
            cut_position: 0,
            customer_id: {{ $customer['id'] }},
            product_id: document.getElementById('selected_product_id').value,
            _token: '{{ csrf_token() }}'
        };
        var tt_quantity = document.getElementById('qty').value;
        var current_rate = document.getElementById('gold_value').value;
        var selected_product_id = document.getElementById('selected_product_id').value;

        if (selected_product_id == '') {
            fire('Please select a product');
            return false;
        }

        if (tt_quantity == '' || current_rate == '') {
            fire('Please fill all the fields');
            return false;
        }

        if (maxtt_per_K === null) {
            fire('Please enter Max TT Per Thousand');
            return false;
        }

        let amount = parseFloat(currentAmount.replace(/,/g, ''));

        const equity = parseFloat(document.getElementById('equity').textContent.replace(/,/g, ''));

        if (equity < 0) {
            fire('You are not eligible to buy/sell , Please check customer equity!!');
            return false;
        }

        const calculateAvailableTT = Math.round(equity / (1000 / maxtt_per_K));

        if (calculateAvailableTT < tt_quantity) {
            // alert(tt_quantity)
            fire('You are not eligible to buy/sell , Please check customer equity!!');
            return false;
        }


        document.getElementById('buyBtn').disabled = true;
        document.getElementById('sellBtn').disabled = true;
        document.getElementById('qty').disabled = true;
        document.getElementById('gold_value').disabled = true;

        $.ajax({
            type: 'POST',
            url: "{{ route('client.save.bid') }}",
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
                    }).then(() => {
                        location.reload();
                    });

                } else {
                    fire('Error: ' + response.message);

                }

                document.getElementById('buyBtn').disabled = false;
                document.getElementById('sellBtn').disabled = false;
                document.getElementById('qty').disabled = false;
                document.getElementById('gold_value').disabled = false;

                document.getElementById('qty').value = "";
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
                        <td colspan="9">
                            <form id="save-${rowId}"  action="{{ route('client.buysell.store') }}" method="POST" >
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="reference_no">Reference No</label>
                                            <input type="text" name="reference_no" class="form-control reference_no" required>
                                            <span class="text-danger" id="error-message-${rowId}" style="display:none;"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">TTB Quantity</label>
                                            <input type="number" name="quantity" class="form-control quantity-input" required value="${qty}" min="1" max="${qty}" disabled>
                                        </div>
                                    </div>
                                        <div class="col-md-4">
                                                <label for="close_rate-${rowId}" class="mr-2">Closing Rate :</label>
                                                <input type="text" name="current_rate" id="close_rate-${rowId}" class="form-control" value="" readonly>
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
                                    <button type="submit" class="btn btn-primary submit-btn" id="submitButton" onclick="validateAndSubmit(this, 'save-${rowId}')" disabled>Submit</button>
                                </div>
                            </form>
                        </td>
                    `;
                    this.closest('tr').after(newRow);

                    const referenceNoInput = newRow.querySelector('.reference_no');
                    const quantityInput = newRow.querySelector('.quantity-input');
                    const errorMessage = newRow.querySelector(`#error-message-${rowId}`);
                    const submitButton = newRow.querySelector('.submit-btn');
                    const currentRateInput = newRow.querySelector(`#close_rate-${rowId}`);

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
                                    } else {
                                        errorMessage.textContent = data.message;

                                        errorMessage.style.display = 'block';
                                        quantityInput.disabled = true;
                                        submitButton.disabled = true;
                                        referenceNoInput.focus();
                                    }
                                },
                                error: function(error) {
                                    console.error('Error:', error);
                                    errorMessage.textContent = error.responseJSON.message;
                                    errorMessage.style.display = 'block';
                                    quantityInput.disabled = true;
                                    submitButton.disabled = true;
                                }
                            });
                        } else {
                            quantityInput.disabled = true;
                            submitButton.disabled = true;
                        }
                    });

                    quantityInput.addEventListener('input', function() {
                        const quantityValue = quantityInput.value;
                        if (quantityValue && !quantityInput.disabled) {
                            submitButton.disabled = false;
                        } else {
                            submitButton.disabled = true;
                        }
                    });
                }
            }
        });
    });

    function validateAndSubmit(event, formId) {

        const submitButton = document.getElementById('submitButton');
        submitButton.disabled = true;
        submitButton.textContent = 'Processing...';
        document.getElementById(formId).submit();

    }


    function showStatement(type) {

        if (previousPrice === null) {
            alert('Gold price is not available!');
            return;
        }

        // Construct the URL with query parameters
        const url = "{{ route('client.show.statement') }}" +
            `?goldValue=${encodeURIComponent(previousPrice)}`;
        // Open the constructed URL in a new tab
        window.open(url, '_blank');
    }
</script>

{{--
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
@stop --}}
