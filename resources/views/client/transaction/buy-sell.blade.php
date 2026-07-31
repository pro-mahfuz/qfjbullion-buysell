@extends('client.layouts.app')


@section('title', 'Buy Sell')

@section('content_header')
    <div class=" col-md-12 col-sm-12 d-flex justify-content-between align-items-center">
        <h1>Buy Sell</h1>
        <button type="button" onclick="showStatement('statement')" class="btn btn-sm btn-primary">
            Statement Preview
        </button>
    </div>
@stop



@section('content')

    <div class="col-md-12 col-sm-12">
        @if ($customer)
            @include('client.transaction.bs-bid', [
                'customer' => $customer,
                'current_amount' => $current_amount,
                'maxtt_per_K' => $customer['maxtt_per_K'],
                'conversion_rate' => $conversion_rate,
                'currency' => $currency,
                'service_charge' => $customer['service_charge'],
            ])
        @else
            <div class="alert alert-danger">No customer found</div>
        @endif

        <hr>
        @if ($lastTen)
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            Last 10 Closed Transactions
                        </div>
                        <div class="card-body">
                            <div class="table-responsive mt-3">
                                <table class="table">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Customer Name</th>
                                            <th scope="col">B/S Date</th>
                                            <th scope="col">Gold Qty</th>
                                            <th scope="col">B/S</th>
                                            <th scope="col">Rate</th>
                                            <th scope="col">Transcation Date</th>
                                            <th scope="col">B/S</th>
                                            <th scope="col">Rate</th>
                                            <th scope="col">P/L</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $sl = 1; ?>
                                        @foreach ($lastTen as $transaction)
                                            {{-- @dd($transaction->linked_buy->created_at) --}}
                                            <tr>
                                                <th scope="row">{{ $sl++ }}</th>
                                                <td>{{ $transaction['customer']['name'] }}</td>
                                                <td>{{ isset($transaction['linked_buy']['created_at']) ? \Carbon\Carbon::parse($transaction['linked_buy']['created_at'])->format('Y-m-d') : 'N/A' }}
                                                </td>
                                                <td>{{ $transaction['quantity'] }}</td>
                                                <td>{{ $transaction['transaction_type'] }}</td>
                                                <td>{{ $transaction['linked_buy']['current_rate'] ?? 'N/A' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($transaction['created_at'])->format('Y-m-d') }}
                                                </td>
                                                <td>{{ $transaction['transaction_type'] == 'buy' ? 'sell' : 'buy' }}
                                                </td>
                                                <td>{{ $transaction['current_rate'] }}</td>

                                                <td>{{ number_format($transaction['transaction_amount'], 3) }}</td>

                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
@stop

@section('scripts')
    <script>
        function sendInvoice(type) {
            var id = $('#customer_id').val();
            var url = "{{ route('admin.transaction.send.invoice') }}";
            var goldValue = "N/A";
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    id: id,
                    type: type,
                    goldValue: goldValue,
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    console.log(data);

                    if (data.status == 'success') {
                        alert("WhatsApp message sent successfully.");
                    } else {
                        alert("Failed to send WhatsApp message.");
                    }
                }
            });
        }
    </script>

@stop
