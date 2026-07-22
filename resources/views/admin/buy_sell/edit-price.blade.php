<div class="modal fade" id="dynamicModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> <!-- Added .modal-lg here -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Rate</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="editForm" onsubmit="return validateAndSubmitEdit(event)"
                action="{{ route('admin.buysell.store.price') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div id="errorMessage" class="alert alert-danger d-none"></div>

                    <div class="row">
                        <div class="col-md-12">
                            <h6>{{ Str::ucfirst($transaction->type) }} Trade</h6>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Start Rate</th>
                                        <th>TT</th>
                                    </tr>
                                </thead>
                                <tbody id="buyTableBody">
                                    <tr>
                                        <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $transaction->type }}</td>
                                        <td>{{ $transaction->current_rate }}</td>
                                        <td>{{ $transaction->tt_quantity }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <br>
                    <div class="row"
                        <?= auth()->user()->can('update_running_trade') == false ? 'style="display:none"' : '' ?>>


                        <div class="col-md-3">

                            <label for="created_at">Date Time</label>
                            <input type="datetime-local" name="created_at"
                                value="{{ $transaction->created_at->format('Y-m-d\TH:i') }}" class="form-control"
                                required>

                        </div>

                        <div class="col-md-3">
                            <label for="type">Type</label>
                            <select name="type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="buy" {{ $transaction->type == 'buy' ? 'selected' : '' }}>Buy</option>
                                <option value="sell" {{ $transaction->type == 'sell' ? 'selected' : '' }}>Sell
                                </option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="rate">Edit Rate</label>
                            <input type="text" name="rate" value="{{ $transaction->current_rate }}"
                                class="form-control" required>

                        </div>

                        <div class="col-md-3">
                            <label for="tt">TT Quantity</label>
                            <input type="number" name="tt" class="form-control"
                                value="{{ $transaction->tt_quantity }}" required>
                        </div>

                    </div>
                    <br>
                    <div class="row">

                        <div class="col-md-3">
                            <label for="rate">Take Profit</label>
                            <input type="text" name="take_profit" value="{{ $transaction->take_profit }}"
                                class="form-control" >

                        </div>

                        <div class="col-md-3">
                            <label for="tt">Stop Loss</label>
                            <input type="text" name="stop_loss" class="form-control"
                                value="{{ $transaction->stop_loss }}" >
                        </div>

                    </div>


                </div>

                <input type="hidden" id="transactionId" name="transactionId" value="{{ $transaction->id }}">

                <div class="modal-footer">
                    <button id="submitButton" type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let errorTimeout = null;

    function validateAndSubmitEdit(event) {
        event.preventDefault(); // Prevent the form from submitting by default

        const rate = parseFloat(document.querySelector('input[name="rate"]').value);
        const previousRate = parseFloat(document.querySelector('#buyTableBody tr td:nth-child(4)').textContent);

        if (rate < 0) {
            showError('Rate cannot be Zero or Negative');
            return false;
        }

        const submitButton = document.getElementById('submitButton');
        submitButton.disabled = true;
        submitButton.textContent = 'Processing...';

        document.getElementById("editForm").submit();
    }

    function showError(message) {
        const errorMessage = document.getElementById('errorMessage');
        errorMessage.textContent = message;
        errorMessage.classList.remove('d-none');

        if (errorTimeout) clearTimeout(errorTimeout);

        errorTimeout = setTimeout(() => {
            errorMessage.classList.add('d-none');
        }, 2000);
    }
</script>
