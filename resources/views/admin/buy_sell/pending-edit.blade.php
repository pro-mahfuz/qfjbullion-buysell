<div class="modal fade" id="dynamicModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Update Pending Buy/Sell</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form action="{{ route('admin.buysell.pending.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="id" value="{{ $transaction->id }}">

                    <div class="row">
                        <!-- Type Selection -->
                        <div class="form-group col-md-4">
                            <label for="update_type">Type:</label>
                            <select name="type" id="update_type" class="form-control" required>
                                <option value="buy" {{ old('type', $transaction->type) == 'buy' ? 'selected' : '' }}>
                                    Buy</option>
                                <option value="sell"
                                    {{ old('type', $transaction->type) == 'sell' ? 'selected' : '' }}>Sell</option>
                            </select>
                        </div>

                        <!-- TT Quantity -->
                        {{-- @dd($transaction->limit) --}}
                        <div class="form-group col-md-4">
                            <label for="{{ $transaction->limit != 0 ? 'limit' : ' stop' }}"
                                id="{{ $transaction->limit != 0 ? 'limit' : ' stop' }}">
                                {{ $transaction->limit != 0 ? 'Limit' : ' Stop' }}:</label>
                            <input type="text" name="{{ $transaction->limit != 0 ? 'limit' : ' stop' }}"
                                id="{{ $transaction->limit != 0 ? 'limit' : ' stop' }}" class="form-control"
                                 placeholder="{{ $transaction->limit != 0 ? 'limit' : ' stop' }}"
                                value="{{ $transaction->limit != 0 ? $transaction->limit : $transaction->stop }}">
                            <div class="invalid-feedback" style="display: none;">Please enter a valid number (up to 4
                                decimal places).</div>
                        </div>


                        <div class="form-group col-md-4">
                            <label for="update_tt">TT Quantity:</label>
                            <input type="number" name="tt" id="update_tt" class="form-control"
                                placeholder="Enter TT Quantity" value="{{ old('tt', $transaction->tt) }}" required>
                        </div>


                        <div class="form-group col-md-2">
                            <label for="take_profit">Take Profit:</label>
                            <input type="text" name="take_profit" id="take_profit" class="form-control"
                                 placeholder="Amount"
                                value="{{ old('take_profit', $transaction->take_profit) }}">
                            <div class="invalid-feedback" style="display: none;">Please enter a valid number (up to 4
                                decimal places).</div>
                        </div>

                        <div class="form-group col-md-2">
                            <label for="stop_loss">Stop Loss:</label>
                            <input type="text" name="stop_loss" id="stop_loss" class="form-control"
                                placeholder="Amount"
                                value="{{ old('stop_loss', $transaction->stop_loss) }}">
                            <div class="invalid-feedback" style="display: none;">Please enter a valid number (up to 4
                                decimal places).</div>
                        </div>

                    </div>

                    <div class="text-right mt-3">
                        <button type="submit" class="btn btn-primary">Update Trade</button>
                    </div>
                </form>
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
    // Function to validate decimal inputs
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

    // Add event listeners for validation
    document.querySelectorAll('input[type="text"]').forEach(input => {
        input.addEventListener('input', () => validateDecimalInput(input));
    });
</script>
