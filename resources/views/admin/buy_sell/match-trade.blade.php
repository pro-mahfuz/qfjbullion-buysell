<div class="modal fade" id="dynamicModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Match Trade</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="tradeForm" onsubmit="return validateAndSubmitMatchTrade(event)"
                action="{{ route('admin.buysell.match.trade.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div id="errorMessage" class="alert alert-danger d-none"></div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6>{{ Str::ucfirst($transaction->type) }} Trade </h6>
                            <table class="table table-bordered">

                                <tbody id="buyTableBody">
                                    <tr>
                                        <td>Date</td>
                                        <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Type</td>
                                        <td>{{ $transaction->type }}</td>
                                    </tr>
                                    @php
                                        $oldAmount =
                                            $transaction->current_rate *
                                            3.74632 *
                                            3.674 *
                                            ($transaction->tt_quantity - $transaction->close_quanntity);
                                    @endphp
                                    <tr>
                                        <td>Amount</td>
                                        <td>{{ number_format($oldAmount, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ $transaction->type }} Rate</td>
                                        <td>{{ $transaction->current_rate }}</td>
                                    </tr>
                                    <tr>
                                        <td>Quantity </td>
                                        <td>{{ $transaction->tt_quantity }} TTB</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6>{{ $transaction->type == 'buy' ? 'Sell' : 'Buy' }} Trades</h6>
                            <div style="max-height: 250px; overflow-y: auto;">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>TTB</th>
                                            <th>Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sellTableBody">
                                        @if (count($trades) > 0)
                                            @foreach ($trades as $trade)
                                                @php
                                                    $oldTradeAmount =
                                                        $trade->current_rate *
                                                        3.74632 *
                                                        3.674 *
                                                        ($trade->tt_quantity - $trade->close_quanntity);
                                                @endphp
                                                <tr>
                                                    <td><input type="radio" name="sellRadio"
                                                            onclick="selectTrade('sell', {{ $trade->tt_quantity }}, '{{ $trade->id }}', '{{ $trade->current_rate }}', '{{ $oldTradeAmount }}', '{{ $oldAmount }}')
                                                            "data-tt="{{ $trade->tt_quantity }}">
                                                        &nbsp;&nbsp;&nbsp; {{ $trade->tt_quantity }} TTB

                                                    </td>

                                                    <td>{{ $trade->current_rate }}</td>

                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6">No trades found</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                    <!-- Hidden inputs to store the selected trade data -->
                    <input type="hidden" id="transactionId" name="transactionId" value="{{ $transaction->id }}">
                    <input type="hidden" id="customerId" name="customerId" value="{{ $transaction->customer_id }}">
                    <input type="hidden" id="starting_rate" name="starting_rate"
                        value="{{ $transaction->current_rate }}">
                    <input type="hidden" id="type" name="type" value="{{ $transaction->type }}">
                    <input type="hidden" id="quantity" name="quantity" value="{{ $transaction->tt_quantity }}">

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
        </div>
    </div>
</div>

<script>
    let ttToCompare = ({{ $transaction->tt_quantity }});
    let selectedTT = null;
    let selectedTradeId = null;
    let selectedTradeRate = null;
    let errorTimeout = null;

    function selectTrade(type, tt, tradeId, tradeRate, tradeAmount, transactionAmount) {
        selectedTT = tt;
        selectedTradeId = tradeId;
        selectedTradeRate = tradeRate;
        // Update hidden inputs with the selected trade data
        document.getElementById('selectedTradeId').value = selectedTradeId;
        document.getElementById('selectedTradeRate').value = selectedTradeRate;
        const profitLoss = document.getElementById('profitLoss');
        if (type === 'sell') {
            profitLoss.textContent = (tradeAmount - transactionAmount).toFixed(2);
        } else {
            profitLoss.textContent = (transactionAmount - tradeAmount).toFixed(2);
        }

    }

    function validateAndSubmitMatchTrade(event) {
        const errorMessage = document.getElementById('errorMessage');
        errorMessage.classList.add('d-none');

        if (ttToCompare && selectedTT) {
            if (ttToCompare != selectedTT) {
                showErrorMatch("Please select matching buy and sell trades!");
                return false;
            }
        } else {
            showErrorMatch("Please select a trade!");
            return false;
        }
        const submitButton = document.getElementById('submitButton');
        submitButton.disabled = true;
        submitButton.textContent = 'Processing...';
        return true;
    }

    function showErrorMatch(message) {
        const errorMessage = document.getElementById('errorMessage');
        errorMessage.textContent = message;
        errorMessage.classList.remove('d-none');

        if (errorTimeout) clearTimeout(errorTimeout);

        errorTimeout = setTimeout(() => {
            errorMessage.classList.add('d-none');
        }, 2000);
    }
</script>
