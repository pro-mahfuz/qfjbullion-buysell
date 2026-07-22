<div class="row">
    <div class="col-md-6">
        <table class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Address </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>{{ $supplier->full_name }} </th>
                    <th>{{ $supplier->mobile }} </th>
                    <th>{{ $supplier->address }} </th>
                </tr>
                </thead>

        </table>

        <h3>Transaction History</h3>

        @if (count($deposit_list) > 0)
            <table class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th style="text-align: right;">Date</th>
                        <th style="text-align: right;">Deposit</th>
                        <th style="text-align: right;">Withdraw</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $sl = 0;
                    $gram = 0; ?>
                    @foreach ($deposit_list as $row)
                        <?php
                        ?>
                        <tr>
                            <td>{{ date('d/M/Y', strtotime($row->created_at)) }}</td>
                            <td style="text-align: right;">
                                {{ $row->deposit_amount > 0 ? 'AED ' . number_format($row->deposit_amount, 2) : '--' }} </td>

                            <td style="text-align: right;">
                                {{ $row->withdraw_amount > 0 ? 'AED ' . number_format($row->withdraw_amount, 2) : '--' }}
                            </td>

                        </tr>
                    @endforeach


                </tbody>
            </table>
        @else
            <h3 style="color: #900000;text-align:center;">No Transaction Found.</h3>
        @endif

    </div>
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="created_at">DateTime</label>
                    <input type="datetime-local" name="created_at" class="form-control" required=""
                        value="{{ date('Y-m-d\TH:i', strtotime('now +4 hours')) }}">
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label for="">Type </label>
                    <select name="type" class="form-control" required="">
                        <option value="withdraw"> Widthdraw </option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="">Reference No </label>
                    <input type="text" name="ref_no" class="form-control">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="deposit_amount">Amount (AED)</label>
                    <input type="number" step="0.01" name="deposit_amount" id="diposit_amount" class="form-control"
                        required="">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="note"> Note</label>
                    <textarea name="note" class="form-control"></textarea>
                </div>
            </div>



            <div class="col-md-12 text-right">

                <input type="hidden" name="payment_account_id" value="2" />
                <input type="hidden" name="staff_note" value="N/A" />

                <button type="submit" name="submit" class="btn btn-primary">Save</button>
                <button type="submit" name="submit_and_continue" class="btn btn-secondary">Save and Continue</button>
            </div>
        </div>
    </div>

</div>
