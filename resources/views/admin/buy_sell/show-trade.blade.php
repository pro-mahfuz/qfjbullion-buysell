<div class="modal-header {{ $runningBuySell->type == 'buy' ? ' bg-info' : 'bg-danger' }} ">
    <h5 class="modal-title" id="exampleModalLabel">CLOSE TRADE </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>



<form action="{{ route('admin.buysell.deposit.save') }}" method="POST">
    @csrf
    <div class="modal-body">
        <div class="table-responsive mt-3">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Date</th>
                        <th scope="col">Type</th>
                        <th scope="col">Start Rate</th>
                        <th scope="col">TT Quantity</th>
                        <th scope="col">Total (AED)</th>

<!--
                        <th scope="col">Cut Position</th>
                        <th scope="col">Balance</th>

                  -->
                    </tr>
                </thead>

                <tbody id="table1" class="collapse show">
                    <?php $sl = 1; ?>
                    <tr>
                        <th scope="row">{{ $sl++ }}</th>
                        <td>{{ $runningBuySell->created_at }}</td>
                        <td>{{ $runningBuySell->type }}</td>
                        <td>{{ number_format($runningBuySell->current_rate, 2) }}</td>
                        <td>{{ $runningBuySell->tt_quantity - $runningBuySell->close_quanntity }}</td>
                        <td>{{ number_format($runningBuySell->total_amount_aed, 2) }}</td>
                        <!-- <td>{{ number_format($runningBuySell->current_rate, 2) }}</td>
                        <td>{{ number_format($runningBuySell->current_rate, 2) }}</td> -->

                    </tr>

                </tbody>

            </table>
        </div>

    </div>
    <div class="col-md-12">
        <div class="row">

            <div class="col-md-4">
                <div class="form-group">
                    <label for="reference_no">Reference No </label>
                    <input type="text" name="reference_no" id="reference_no" class="form-control" required=""
                        value="">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">TTB Quantity </label>
                    <input type="number" name="quantity" id="quantity" class="form-control" required=""
                        value="{{ $runningBuySell->tt_quantity - $runningBuySell->close_quantity }}" min="1"
                        max="{{ $runningBuySell->tt_quantity - $runningBuySell->close_quantity }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="current_rate">Closing Rate</label>
                    <input type="text" name="current_rate" id="current_rate" class="form-control" required=""
                        value="{{ $running_rate }}">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="current_rate">Running Rate</label>
                   <h4>{{ $running_rate }}</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="current_rate">Loss/Profit </label>
                   <h4>{{ number_format($transaction_amount,2) }}</h4>
                </div>
            </div>

        </div>
    </div>

    <input type="hidden" name="business_id" value="{{ $runningBuySell->business_id }}" />
    <input type="hidden" name="starting_rate" value="{{ $runningBuySell->current_rate }}" />
    <!-- <input type="hidden" name="transaction_amount" value="0"/> -->
    <input type="hidden" name="customer_id" value="{{ $runningBuySell->customer_id }}" />
    <input type="hidden" name="transaction_type" value="{{ $runningBuySell->type }}" />
    <input type="hidden" name="reference_table" value="buysells" />
    <input type="hidden" name="reference_row" value="{{ $runningBuySell->id }}" />
    <input type="hidden" name="tnx_id" value="{{ time() }}" />
    <div class="modal-footer">

        <button type="submit" class="btn {{ $runningBuySell->type == 'buy' ? ' btn-info' : 'btn-danger' }} ">Close
            Trade</button>
    </div>

</form>
