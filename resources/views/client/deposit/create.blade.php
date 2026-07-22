<div class="modal fade" id="dynamicModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <form action="{{ route('client.deposit.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-dialog .modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">NEW {{ strtoupper($type) }} </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="transaction_amount">Amount<span>*</span></label>
                                <input type="number" name="transaction_amount" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="note">Note<span>*</span></label>
                                <input type="text" name="note" class="form-control" required>
                            </div>
                        </div>
                        <input type="hidden" name="customer_id" value="{{ $customer_id }}" />

                        <input type="hidden" name="starting_rate" value="0" />
                        <input type="hidden" name="business_id" value="{{ $business_id }}" />
                        <input type="hidden" name="transaction_type" value="{{ $type }}" />
                        <input type="hidden" name="reference_table" value="" />
                        <input type="hidden" name="reference_row" value="" />
                        <input type="hidden" name="tnx_id" value="{{ time() }}" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        // Initialize Select2 on modal open
        $('#dynamicModal').on('shown.bs.modal', function() {
                $(this).find('.select2-container
                    common_select2 ').select2({
                    dropdownParent: $(this)
                });
        });

    // Restrict 'note' input to numbers only
    $('input[name="note"]').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    });
</script>
