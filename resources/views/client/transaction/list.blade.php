@extends('client.layouts.app')

@section('title', 'Deposit List')

@section('content_header')
    <h1>
        Completed Transactions
    </h1>
@stop


@section('content')

    {{-- <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-end">

            @if ($type == 'deposit')
                <a href="#" class="btn btn-sm btn-primary load_modal" data-toggle="modal"
                    data-action="{{ route('client.deposit.create', ['type' => 'deposit']) }}">
                    New Deposit
                </a>
            @else
                <a href="#" class="btn btn-sm btn-primary load_modal" data-toggle="modal"
                    data-action="{{ route('client.deposit.create', ['type' => 'withdraw']) }}">
                    New Withdraw
                </a>
            @endif
        </div>
    </div> --}}

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-responsive">
                    <table id="transaction-table" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Ticket No</th>
                                <th scope="col">Customer Name</th>
                                <th scope="col">B/S Date</th>
                                <th scope="col">Gold Qty</th>
                                <th scope="col">B/S</th>
                                <th scope="col">Rate</th>
                                <th scope="col">Transaction Date</th>
                                <th scope="col">B/S</th>
                                <th scope="col">Rate</th>
                                <th scope="col">P/L</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($transactions)
                                <?php $sl = 0; ?>
                                @foreach ($transactions as $row)
                                    <?php $sl++; ?>
                                    <tr>
                                        <td>{{ $sl }}</td>
                                        <td>{{ $row['reference_no'] }}</td>
                                        <td>{{ $row['name'] ?? 'N/A' }}</td>
                                        <td>{{ $row['buy_sell_date'] }}</td>
                                        <td>{{ $row['quantity'] }}</td>
                                        <td>{{ $row['transaction_type'] }}</td>
                                        <td>{{ $row['buy_sell_rate'] }}</td>
                                        <td>{{ $row['created_at'] }}</td>
                                        <td>{{ $row['transaction_type'] == 'buy' ? 'sell' : 'buy' }}</td>
                                        <td>{{ $row['current_rate'] }}</td>
                                        <td>{{ number_format($row['transaction_amount'], 2) }}</td>

                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center">No data found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop


@section('js')
    <script>
        $(document).ready(function() {
            $('#transaction-table').DataTable();
        });
    </script>
@stop
