<div class="modal fade" id="dynamicModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="pendingBuySellModalLabel">Pending Buy/Sell</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.buysell.store.pending') }}" method="POST">

                <!-- Modal Body -->
                <div class="modal-body">
                    @csrf
                    <div class="row">


                        <div class="form-group col-md-6">
                            <label for="ticket_no" id="ticket_no">Ticket No</label>
                            <input type="text" name="ticket_no" id="ticket_no" class="form-control"
                                placeholder="Ticket no" required>
                            <div class="invalid-feedback" style="display: none;">Please enter a valid ticket no.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="type">Type:</label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="buy">Buy</option>
                                <option value="sell">Sell</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="limit_" id="limit-label">Limit:</label>
                            <input type="text" name="limit_" id="limit_" class="form-control" 
                                placeholder="Amount">
                            <div class="invalid-feedback" style="display: none;">Please enter a valid number (up to 4
                                decimal places).</div>
                        </div>

                        <div class="form-group col-md-6" style="display: none">
                            <label for="limit">Take Profit:</label>
                            <input type="text" name="limit[tp]" id="limit" class="form-control"
                                 placeholder="Amount">
                            <div class="invalid-feedback" style="display: none;">Please enter a valid number (up to 4
                                decimal places).</div>
                        </div>

                        <div class="form-group col-md-6" style="display: none">
                            <label for="limit">Stop Limit:</label>
                            <input type="text" name="limit[sl]" id="limit" class="form-control"
                                 placeholder="Amount">
                            <div class="invalid-feedback" style="display: none;">Please enter a valid number (up to 4
                                decimal places).</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tt">TTB Quantity:</label>
                            <input type="number" name="tt" id="tt" class="form-control"
                                placeholder="Enter TTB Quantity" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="stop_" id="stop-label">Stop:</label>
                            <input type="text" name="stop_" id="stop_" class="form-control" 
                                placeholder="Amount">
                            <div class="invalid-feedback" style="display: none;">Please enter a valid number (up to 4
                                decimal places).</div>
                        </div>

                        <div class="form-group col-md-6" style="display: none">
                            <label for="limit">Take Profit:</label>
                            <input type="text" name="stop[tp]" id="limit" class="form-control"
                                 placeholder="Amount">
                            <div class="invalid-feedback" style="display: none;">Please enter a valid number (up to 4
                                decimal places).</div>
                        </div>

                        <div class="form-group col-md-6" style="display: none">
                            <label for="limit">Stop Limit:</label>
                            <input type="text" name="stop[sl]" id="limit" class="form-control"
                                 placeholder="Amount">
                            <div class="invalid-feedback" style="display: none;">Please enter a valid number (up to 4
                                decimal places).</div>
                        </div>

                    </div>

                    <input type="hidden" name="customer_id" value="{{ $customerId }}">
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary text-left" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Trade</button>

                </div>

            </form>
        </div>
    </div>
</div>

<script>
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
