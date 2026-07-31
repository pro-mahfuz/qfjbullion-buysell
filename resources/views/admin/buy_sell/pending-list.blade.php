<div class="table-responsive mt-3">
    <table class="table table-striped table-bordered">

        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Date</th>
                <th scope="col">Type</th>
                <th scope="col">TTB Qty</th>
                <th scope="col">Limit</th>
                <th scope="col">Stop</th>
            </tr>
        </thead>

        <tbody id="table1" class="collapse show">
            @if (isset($pending))
            <?php $sl = 1; ?>
            @foreach ($pending as $transaction)
            <tr>
                <th scope="row">{{ $sl++ }}</th>
                <td>{{ $transaction->created_at }}</td>
                <td>{{ $transaction->type }}</td>
                <td>{{ $transaction->tt_quantity }}</td>
                <td>{{ number_format($transaction->threshold_rate, 3) }}</td>
                <td>{{ number_format($transaction->stop_limit, 3) }}</td>


                {{-- <td> <a href="#"
                                class="btn {{ $transaction->type == 'buy' ? ' btn-info' : 'btn-danger' }} btn-sm load_modal"
                data-toggle="modal"
                data-action="{{ route('buysell.showtrade', ['id' => $transaction->id]) }}">
                {{ $transaction->type == 'buy' ? 'Buy Close' : 'Sell Close' }}
                </a> </td>

                </td> --}}
            </tr>
            @endforeach
            @endif

        </tbody>

    </table>

</div>