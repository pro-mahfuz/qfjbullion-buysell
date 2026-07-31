@extends('layouts.app')

@section('title', 'Buy Sell')

@section('content_header')
    <!--<h1>Customer Search</h1>-->
@stop


@section('content')

    <div class="row">
        <div class="col-md-12 col-sm-12 ">
            <div>
                <form action="{{ route('admin.buysell.customer.search') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4 text-right">
                            <h5>Customer Search</h5>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" style="margin-top: 0px;">
                                <input type="text" class="form-control" required="" name="customer"
                                    style="line-height: 2rem;"
                                    @if ($customer) value="{{ $customer->name }}" @endif
                                    placeholder="name,phone,email,code">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group" style="margin-top: 0px;">

                                <button type="submit" class="btn btn-info" title="Search"><i class="fa fa-search"></i>
                                    Search</button>
                            </div>
                        </div>
                        
                    </div>
                </form>
            </div>
            @if ($customer)
            
                <div class="row mt-5">
                    <div class="col-md-4" style="text-align: left;">
                        <div class="info-box-content">
                            <h3>Customer: {{ $customer->name }} (  {{ $customer->customer_code }} )</h3>
                        </div>
                    </div>
                    
                    @if ($customer)
                        <div class="col-md-8 d-flex justify-content-end" style="text-align: right;">
                            <form action="{{ route('admin.buysell.customer.search') }}" method="GET">
                                <input type="hidden" class="form-control" required="" name="customer"
                                    @if ($customer) value="{{ $customer->name }}" @endif>
                                <button type="submit" class="btn btn-info mr-2">Buy & Sell</button>
                            </form>

                            <form action="{{ route('admin.buysell.customer.trading.list') }}" method="GET">
                                <input type="hidden" class="form-control" required="" name="customer"
                                    @if ($customer) value="{{ $customer->name }}" @endif>
                                <button type="submit" class="btn btn-info mr-2">Trading List</button>
                            </form>
                            <button type="button" onclick="showStatement('statement')" class="btn btn-primary mr-2">
                                Statement Preview
                            </button>

                            <button class="btn btn-danger load_modal mr-2" data-bs-toggle="modal"
                                data-action="{{ route('admin.buysell.show.preview', ['id' => $customer->id, 'type' => 'withdraw']) }}">
                                Withdraw Preview
                            </button>
                            @can('dashboard_transection_section')
                                <a href="#" class="btn btn-danger load_modal mr-2" data-toggle="modal"
                                    data-action="{{ route('admin.buysell.deposit', ['id' => $customer->id, 'type' => 'withdraw']) }}">
                                    <i class="fa fa-minus"></i>
                                </a>
                            @endcan
                            <button class="btn btn-info load_modal  mr-2" data-bs-toggle="modal"
                                data-action="{{ route('admin.buysell.show.preview', ['id' => $customer->id, 'type' => 'deposit']) }}">
                                Deposit Preview
                            </button>
                            @can('dashboard_transection_section')
                            <a href="#" class="btn btn-info load_modal mr-2" data-toggle="modal"
                                data-action="{{ route('admin.buysell.deposit', ['id' => $customer->id, 'type' => 'deposit']) }}">
                                <i class="fa fa-plus"></i>
                            </a>
                        @endcan
                        </div>
                    @endif
                </div>


                @include('admin.buy_sell.bid-tradingList', [
                    'customer' => $customer,
                    'current_amount' => $current_amount,
                    'maxtt_per_K' => $customer->maxtt_per_K,
                    'deposit' => $deposit,
                    'withdraw' => $withdraw,
                ])
            @else
                <div class="alert alert-danger">No customer found</div>
            @endif

            <hr>
            @can('dashboard_transection_section')

                <div class="row">
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between bg-info">
                                <b class="text-dark text-right">Deposit</b>
                                {{-- @can('deposit_add') --}}
                                @if ($customer)
                                    <small>
                                        <a href="#" class="btn btn-primary btn-sm load_modal" data-toggle="modal"
                                            data-action="{{ route('admin.buysell.deposit', ['id' => $customer->id, 'type' => 'deposit']) }}">
                                            + Deposit
                                        </a>
                                    </small>
                                @endif
                                {{-- @endcan --}}
                            </div>
                            <div class="card-body">
                                <h3 class="card-title text-center">
                                    {{ $deposit }} AED
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between " style="background-color: #00bc8c;">
                                <b class="text-dark">Withdraw</b>
                                {{-- @can('withdraw_add') --}}
                                @if ($customer)
                                    <small>
                                        <a href="#" class="btn btn-sm btn-primary load_modal" data-toggle="modal"
                                            data-action="{{ route('admin.buysell.deposit', ['id' => $customer->id, 'type' => 'withdraw']) }}">
                                            - Withdraw
                                        </a>
                                    </small>
                                @endif
                                {{-- @endcan --}}
                            </div>
                            <div class="card-body">
                                <h3 class="card-title text-center">{{ $withdraw }} AED</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-header text-center" style="background-color: #3498db;">
                                <b class="text-dark">Buy Amount </b>
                            </div>
                            <div class="card-body text-center">
                                <h3 class="card-title">{{ $buy }} AED</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-header text-center" style="background-color: #e74c3c;">
                                <b class="text-dark">Sell Amount</b>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title text-center">{{ $sell }} AED</h3>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
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
                                                <th scope="col">Reference No </th>
                                                <th scope="col">B/S Date</th>
                                                <th scope="col">Gold Qty</th>
                                                <th scope="col">B/S</th>
                                                <th scope="col">Rate</th>
                                                <th scope="col">Transcation Date</th>
                                                <th scope="col">B/S</th>
                                                <th scope="col">Rate</th>
                                                <th scope="col" style="text-align: right;padding-right: 20px;">P/L</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $sl = 1; ?>
                                            @foreach ($lastTen as $transaction)
                                                {{-- @dd($transaction->linked_buy->created_at) --}}
                                                <tr>
                                                    <th scope="row">{{ $sl++ }}</th>
                                                    <td>{{ $transaction->customer->name }}</td>
                                                    <td>{{ $transaction->reference_no }}</td>
                                                    <td>{{ $transaction->linked_buy && $transaction->linked_buy->created_at ? $transaction->linked_buy->created_at->format('Y-m-d') : 'N/A' }}
                                                    </td>
                                                    <td>{{ $transaction->quantity }}</td>
                                                    <td>{{ $transaction->transaction_type }}</td>
                                                    <td>{{ $transaction->linked_buy->current_rate ?? 'N/A' }}</td>
                                                    <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                                                    <td>{{ $transaction->transaction_type == 'buy' ? 'sell' : 'buy' }}</td>
                                                    <td>{{ $transaction->current_rate }}</td>
                                                    <td style="text-align: right;padding-right: 20px;">
                                                        {{ number_format($transaction->transaction_amount, 3) }}</td>


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
