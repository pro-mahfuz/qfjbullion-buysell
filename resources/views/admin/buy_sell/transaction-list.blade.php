<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                Last 10 Transactions
            </div>
            <div class="card-body">
                <div class="btn-group" role="group" aria-label="Table toggles">
                    <button class="btn btn-primary" style="margin-left: 5px" type="button" data-bs-toggle="collapse"
                        data-bs-target="#table1" aria-expanded="true" onclick="toggleTable('table1')">Deposit</button>
                    <button class="btn btn-success" style="margin-left: 5px" type="button" data-bs-toggle="collapse"
                        data-bs-target="#table2" aria-expanded="false" onclick="toggleTable('table2')">Withdraw</button>
                </div>

                <div class="table-responsive mt-3">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Date</th>
                                <th scope="col">Amount</th>
                            </tr>
                        </thead>

                        <tbody id="table1" class="collapse show">
                            @if (isset($transactionsByType['deposit']))
                                <?php $sl = 1; ?>
                                @foreach ($transactionsByType['deposit'] as $transaction)
                                    <tr>
                                        <th scope="row">{{ $sl++ }}</th>
                                        <td>{{ $transaction->transaction_date }}</td>
                                        <td>{{ number_format($transaction->transaction_amount, 3) }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endif

                        </tbody>

                        <tbody id="table2" class="collapse">
                            @if (isset($transactionsByType['withdraw']))
                                <?php $sl = 1; ?>
                                @foreach ($transactionsByType['withdraw'] as $transaction)
                                    <tr>
                                        <th scope="row">{{ $sl++ }}</th>
                                        <td>{{ $transaction->transaction_date }}</td>
                                        <td>{{ number_format($transaction->transaction_amount, 3) }}
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

