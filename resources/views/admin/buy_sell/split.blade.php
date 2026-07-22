<div class="modal fade" id="dynamicModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog .modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Split Trade</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="splitForm" onsubmit="return validateAndSubmit(event)" action="{{ route('admin.buysell.split.store') }}" method="POST">
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
                                        <th>Amount</th>
                                        <th>Start Rate</th>
                                        <th>TT</th>
                                    </tr>
                                </thead>
                                <tbody id="buyTableBody">
                                    <tr>
                                        <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $transaction->type }}</td>
                                        <td>{{ $transaction->total_amount_aed }}</td>
                                        <td>{{ $transaction->current_rate }}</td>
                                        <td>{{ $transaction->tt_quantity }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-4">
                            <label for="split_quantity">Split Amount</label>
                            <input type="number" name="split_quantity" class="form-control" required>
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

    function validateAndSubmit(event) {
        event.preventDefault();

        const ttSplit = parseFloat(document.querySelector('input[name="split_quantity"]').value);
        const ttQuantity = parseFloat(document.querySelector('#buyTableBody tr td:nth-child(5)').textContent);

        if (ttSplit > ttQuantity || ttSplit == ttQuantity) {
            showError('TT Split cannot be greater than or same as TT Quantity');
            return false;
        }
        const submitButton = document.getElementById('submitButton');
        submitButton.disabled = true;
        submitButton.textContent = 'Processing...';

        document.getElementById("splitForm").submit();
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
