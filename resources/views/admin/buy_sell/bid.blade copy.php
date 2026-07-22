<div class="row mt-5">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">Buy Sell</div>
            <div class="card-body">
                <form action="{{ route('admin.transaction.save') }}" method="POST" id="tradeForm">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="bid">TT</label>
                            <input type="number" class="form-control" name="bid" id="bid"
                                placeholder="Enter TT" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="gold_value">Gold Value</label>
                            <input type="number" class="form-control" name="gold_value" id="gold_value" required>
                        </div>
                    </div>
                    <input type="hidden" name="id" id="customer_id" value="{{ $customer->id }}">
                    <hr>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <button type="button" id="buyBtn" class="btn btn-info btn-block"
                                onclick="handleTransactionClick('buy')">Buy</button>
                        </div>
                        <div class="form-group col-md-6 text-right">
                            <button type="button" id="sellBtn" class="btn btn-danger btn-block"
                                onclick="handleTransactionClick('sell')">Sell</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
        <div class="btn-group" role="group" aria-label="Test">

<button href="#" class="btn btn-primary btn-sm load_modal" data-toggle="modal"
    style="background-color:  #b85caa;"
    data-action="{{ route('admin.transaction.show.statement', ['id' => $customer->id, 'type' => 'statement']) }}">
    Statement Preview
</button>

<button class="btn btn-success" style="margin-left: 5px" type="button" data-bs-toggle="collapse"
    data-bs-target="#table2" aria-expanded="false" onclick="sendInvoice('withdraw')">Withdraw
    List</button>
<button class="btn btn-primary" style="margin-left: 5px" type="button" data-bs-toggle="collapse"
    data-bs-target="#table3" aria-expanded="false" onclick="sendInvoice('deposit')">Deposit
    List</button>
</div>
        </div>
    </div>
</div>

<div class="row mt-5">
<div class="col-md-12">
        <div class="card">

            <div class="card-header">Running State</div>
            <div class="card-body" id="runningStateDiv">
                <!-- Inputs will be dynamically added here -->
            </div>
        </div>
    </div>
</div>

<script>
    const runningBuySell = @json($runningBuySell);

    function handleTransactionClick(type) {
        document.getElementById('buyBtn').disabled = true;
        document.getElementById('sellBtn').disabled = true;

        document.getElementById('bid').disabled = true;
        document.getElementById('gold_value').disabled = true;

        console.log('runningBuySell:', runningBuySell);
        const runningStateDiv = document.getElementById('runningStateDiv');
        runningStateDiv.innerHTML = '';


        if (runningBuySell.length != 0) {
            const table = document.createElement('table');
            table.classList.add('table', 'table-bordered');

            const thead = document.createElement('thead');
            thead.innerHTML = `
            <tr>
                <th>Qty</th>
                <th>Starting Price</th>
                <th>Status</th>
                <th>Closing Qty</th>
                <th>Action</th>
                <th>Select for Closing</th>
            </tr>
        `;
            table.appendChild(thead);

            const tbody = document.createElement('tbody');
            console.log(runningBuySell.length);
            const bid = document.getElementById('bid').value;
            const goldValue = document.getElementById('gold_value').value;

            Object.values(runningBuySell).forEach((transaction) => {
                if (transaction.transaction_type == type) {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                    <td>${transaction.qty}</td>
                    <td>${transaction.current_rate}</td>
                    <td>${transaction.running_status}</td>

                    <td>
                        <input type="number" class="form-control" id="closing_bid_${transaction.id}" placeholder="Closing Bid" disabled>
                    </td>

                    <td>
                        <input type="number" class="form-control" id="closing_price_${transaction.id}" placeholder="Enter Closing Price" disabled>
                    </td>
\
                    <td>
                        <input type="radio" id="selected_transaction_${transaction.id}" name="selected_transaction" value="${transaction.id}" hidden>

                        <button class="btn btn-sm btn-primary" type="button" id ="selected_transaction"
                        name="selected_transaction" value="${transaction.id}"
                         onclick="enableClosingInputs(${transaction.id})"> Close
                        </button>
                    </td>
                    `;
                    tbody.appendChild(row);
                }
            });
            if (bid && goldValue) {
                const row = document.createElement('tr');

                row.innerHTML = `
                    <td>${bid}</td>
                    <td>${goldValue}</td>
                    <td>Running</td>
                    <td>N/A</td>
                    <td>
                        <input type="number" class="form-control" id="closing_price_running" placeholder="Enter Closing Price">
                    </td>
                    `;
                tbody.appendChild(row);
            }
            table.appendChild(tbody);

            runningStateDiv.appendChild(table);
        }


        const submitButtonDiv = document.createElement('div');
        submitButtonDiv.classList.add('form-group');
        submitButtonDiv.innerHTML = `
            <button type="button" class="btn btn-primary btn-block" onclick="submitTransaction('${type}')">Submit Transaction</button>
            `;
        runningStateDiv.appendChild(submitButtonDiv);

        const cancelButtonDiv = document.createElement('div');
        cancelButtonDiv.classList.add('form-group');
        cancelButtonDiv.innerHTML = `
        <button type="button" class="btn btn-secondary btn-block" onclick="resetTransactionForm()">Cancel</button>
        `;
        runningStateDiv.appendChild(cancelButtonDiv);
    }

    function enableClosingInputs(transactionId) {
        Object.values(runningBuySell).forEach(transaction => {
            const input = document.getElementById(`closing_price_${transaction.id}`);
            if (input) input.disabled = true;
            const inputBid = document.getElementById(`closing_bid_${transaction.id}`);
            if (inputBid) inputBid.disabled = true;

            const radio = document.getElementById(`selected_transaction_${transaction.id}`);
            if (radio) radio.checked = false;
        });

        const selectedInput = document.getElementById(`closing_price_${transactionId}`);
        selectedInput.disabled = false;
        const selectedBidInput = document.getElementById(`closing_bid_${transactionId}`);
        selectedBidInput.disabled = false;

        const selectedRadio = document.getElementById(`selected_transaction_${transactionId}`);
        selectedRadio.checked = true;

    }

    function resetTransactionForm() {
        document.getElementById('buyBtn').disabled = false;
        document.getElementById('sellBtn').disabled = false;

        document.getElementById('bid').disabled = false;
        document.getElementById('gold_value').disabled = false;

        document.getElementById('runningStateDiv').innerHTML = '';
    }

    function submitTransaction(type) {
        const selectedTransactionId = document.querySelector('input[name="selected_transaction"]:checked');

        const closingPrice = 0;

        const closingPriceInput = document.getElementById('closing_price_running');
        if (closingPriceInput != null) {
            closingPrice = closingPriceInput.value;
        }


        const form = document.getElementById('tradeForm');

        let transactionId = 0;
        if (selectedTransactionId != null) {
            transactionId = selectedTransactionId.value;
        }

        if (transactionId != 0) {
            const transactionIdInput = document.createElement('input');
            transactionIdInput.type = 'hidden';
            transactionIdInput.name = 'transaction_id';
            transactionIdInput.value = transactionId;
            form.appendChild(transactionIdInput);

            const transactionValue = document.getElementById('closing_price_' + transactionId).value;

            const transactionValueInput = document.createElement('input');
            transactionValueInput.type = 'hidden';
            transactionValueInput.name = 'closing_price_transaction';
            transactionValueInput.value = transactionValue;
            form.appendChild(transactionValueInput);


            const transactionBidValue = document.getElementById('closing_bid_' + transactionId).value;

            const transactionBidValueInput = document.createElement('input');
            transactionBidValueInput.type = 'hidden';
            transactionBidValueInput.name = 'closing_bid_transaction';
            transactionBidValueInput.value = transactionBidValue;
            form.appendChild(transactionBidValueInput);

        }

        const typeInput = document.createElement('input');
        typeInput.type = 'hidden';
        typeInput.name = 'type';
        typeInput.value = type;
        form.appendChild(typeInput);

        if (closingPrice) {
            const closingPriceInputHidden = document.createElement('input');
            closingPriceInputHidden.type = 'hidden';
            closingPriceInputHidden.name = 'closing_price';
            closingPriceInputHidden.value = closingPrice;
            form.appendChild(closingPriceInputHidden);
        }


        const bidInputHidden = document.createElement('input');
        bidInputHidden.type = 'hidden';
        bidInputHidden.name = 'bid';
        bidInputHidden.value = document.getElementById('bid').value;
        form.appendChild(bidInputHidden);

        const goldValueInputHidden = document.createElement('input');
        goldValueInputHidden.type = 'hidden';
        goldValueInputHidden.name = 'gold_value';
        goldValueInputHidden.value = document.getElementById('gold_value').value;
        form.appendChild(goldValueInputHidden);

        form.submit();
    }
</script>
