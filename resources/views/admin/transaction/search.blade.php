@extends('layouts.master')

@section('content')
<ul class="breadcrumb breadcrumb-style">
		<li class="breadcrumb-item">
		  	<a href="{{route('admin.dashboard.buysell')}}">
				<i class="fas fa-home"></i>
			</a>
		</li>
		<li class="breadcrumb-item active">Un-matched Trade Search</li>
	</ul>

    <div class="row">
        <div class="offset-md-4 col-md-4 col-sm-12">
            <div class="card">
                <!--<div class="x_title">-->
                <!--    <h2>SEARCH TRADE</h2>-->
                <!--    <div class="clearfix"></div>-->
                <!--</div>-->
                <div class="card-body">
                    <!-- Search Form -->
                    <div class="search-form">
                        <form action="{{ route('admin.transaction.show.search') }}" method="GET">
                            <div class="row d-flex justify-content-center align-items-center">
                                <div class="col-md-12 text-center">
                                    <label class="font-weight-bold">SEARCH TRADE REFERENCE</label>
                                </div>
                                
                            </div>
                            <div class="row d-flex justify-content-center align-items-center">
                                
                                <div class="col-md-12">
                                        <input type="text" class="form-control" required name="ticketNo"
                                            placeholder="TICKET NUMBER">
                                </div>
                                
                            </div>
                            <div class="row d-flex justify-content-center align-items-center">
                                
                                <div class="col-md-12 text-center mt-2">
                                        <button type="submit" class="btn btn-info" title="Search"><i
                                                class="fa fa-search"></i> Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    

                    <div class="container">
                        @if ($transaction != null)
                            {{-- @dd($transaction) --}}

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><strong>Customer Name:</strong> {{ $transaction->customer->name }}
                                    </h5>
                                    <p class="mb-0"><strong>Ticket Number:</strong> {{ $transaction->reference_no }}</p>
                                    <p class="mb-0"><strong>Gold Quantity:</strong> {{ $transaction->quantity }}</p>
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        <!-- Buy Transaction -->
                                        <div class="col-md-6">
                                            <h6><strong>Type:</strong> Buy</h6>
                                            <p class="mb-2"><strong>Buy Date:</strong>
                                                {{ $transaction->linked_buy?->created_at ? $transaction->linked_buy->created_at->format('d/m/Y H:i') : 'N/A' }}
                                            </p>
                                            <p class="mb-2"><strong>Rate:</strong>
                                                {{ $transaction->linked_buy?->current_rate ? number_format($transaction->linked_buy->current_rate, 2) . ' AED' : 'N/A' }}
                                            </p>

                                        </div>

                                        <!-- Sell Transaction -->
                                        <div class="col-md-6">
                                            <h6><strong>Type:</strong> Sell</h6>
                                            <p class="mb-2"><strong>Transaction Date:</strong>
                                                {{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                                            <p class="mb-2"><strong>Rate:</strong>
                                                {{ number_format($transaction->current_rate, 2) }} AED</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer text-center">
                                    <h4><strong>Profit/Loss:</strong>
                                        {{ number_format($transaction->transaction_amount, 2) }} AED</h4>
                                </div>
                            </div>

                            <!-- Void Transaction Form -->
                            <form action="{{ route('admin.transaction.delete') }}" id="voidTransactionForm_" method="POST">
                                @csrf
                                <input type="hidden" id="transactionId" name="id" value="{{ $transaction->id }}">
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-danger" title="Unmatch Trade">
                                        <i class="fa fa-trash"></i> Unmatch Trade
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
@push('js')
    <script>
        $(document).ready(function() {
            $('#voidTransactionForm').on('submit', function(event) {
                event.preventDefault();

                var transactionId = $('#transactionId').val();
                console.log(transactionId);

                $.ajax({
                    url: "{{ route('admin.transaction.delete') }}",
                    method: "POST",
                    data: {
                        id: transactionId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log(response);
                        Swal.fire({
                            title: 'Success!',
                            text: 'Transaction has been voided successfully.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            
                        });
                    },
                    error: function(xhr, status, error) {
                        // Handle error with SweetAlert
                        Swal.fire({
                            title: 'Error!',
                            text: 'There was an error voiding the transaction.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
    </script>
@endpush
