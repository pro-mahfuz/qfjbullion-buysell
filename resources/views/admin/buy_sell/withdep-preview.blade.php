
@extends('layouts.master')

@section('title', 'Buy Sell')

@section('content_header')
    <!--<h1>Customer Search</h1>-->
@stop

<style>
	.recent-report__chart{
		margin-left: -30px;
	}
	.apexcharts-legend{
		margin-right: 30px;
		margin-top: 45px;
		margin-left: 10px;
	}
	#navbar_search_box{
		margin-top:10px;
	}

</style>
<style>
  .table td, .table th {
    padding: .25rem !important;
    font-size: .85rem !important;
	height: 20px !important;
  }

  .modal-title{
      color: #000000 !important;
  }
  .pagination {
      margin-top: -25px;
  }

  .page-link {
    padding: 0.2rem 0.5rem !important;
  }
</style>
<style>
  .custom-bordered th,
  .custom-bordered td,
  .custom-bordered {
    border: 1px solid black !important;
  }
</style>
@section('content')
<ul class="breadcrumb breadcrumb-style">
		<li class="breadcrumb-item">
		  	<a href="{{route('admin.dashboard.buysell')}}">
				<i class="fas fa-home"></i>
			</a>
		</li>
		<li class="breadcrumb-item active">Transaction Preview</li>
	</ul>
	
    <div class="row">
        <div class="col-md-12 col-sm-12 ">
            <div>
                <form action="{{ route('admin.buysell.customer.search') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4 d-flex align-items-center justify-content-end">
                            <h6>Customer Search</h6>
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
                            <h5>Customer: {{ $customer->name }} (  {{ $customer->customer_code }} )</h5>
                            <input type="hidden" class="form-control" id="customer_id" value="{{$customer->id}}" name="customer_id" >
                        </div>
                    </div>
                    
                    @if ($customer)
                        <div class="col-md-8 d-flex justify-content-end" style="text-align: right;">
                            @can('buysell')
                            <form action="{{ route('admin.buysell.customer.buysell') }}" method="GET">
                                <input type="hidden" class="form-control" required="" name="customer"
                                    @if ($customer) value="{{ $customer->name }}" @endif>
                                <button type="submit" class="btn btn-info mr-2">BUY & SELL</button>
                            </form>
                            @endcan
                            @can('satement_send')
                            <button type="button" onclick="showStatement('statement')" class="btn btn-primary mr-2" style="height: 34px;">
                                Statement
                            </button>
                            @endcan
                            @can('withdraw_list')
                            <a href="{{ route('admin.buysell.show.preview', ['id' => $customer->id, 'type' => 'withdraw']) }}" class="btn btn-danger mr-2" style="height: 34px;">
                                Withdraw
                            </a>
                            @endcan
                            @can('deposit_list')
                            <a href="{{ route('admin.buysell.show.preview', ['id' => $customer->id, 'type' => 'deposit']) }}" class="btn btn-success mr-2" style="height: 34px;">
                                Deposit
                            </a>
                            @endcan
                        </div>
                    @endif
                </div>

            @else
                <div class="alert alert-danger">No customer found</div>
            @endif

            <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                {{ ucfirst($type) }} Transactions
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm " id="transactionTable">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Date</th>
                                                <th>Previous</th>
                                                <th>Note</th>
                                                <th>Amount</th>
                                                @can('dashboard_transection_section')
                                                <th style="width: 60px;">Action</th>
                                                @endcan
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($transactionsByType) > 0)
                                                @foreach ($transactionsByType as $transaction)
                                                    <tr data-id="{{ $transaction->id }}">
                                                        <td class="transaction-date">
                                                            <span>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d-m-Y') }}</span>
                                                            <input type="date" class="form-control edit-field edit-date" style="display: none;"
                                                                value="{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('Y-m-d') }}" />
                                                        </td>
                                                        <td class="transaction-previous">
                                                            <span>{{ $transaction->actual_amount }}</span>
                                                            <input type="number" step="0.001" class="form-control edit-field edit-previous" style="display: none;"
                                                                value="{{ $transaction->actual_amount }}" />
                                                        </td>
                                                        <td class="transaction-note">
                                                            <span>{{ $transaction->note }}</span>
                                                            <input type="text" class="form-control edit-field edit-note" style="display: none;"
                                                                value="{{ $transaction->note }}" />
                                                        </td>
                                                        <td class="transaction-amount">
                                                            <span>{{ $transaction->transaction_amount }}</span>
                                                            <input type="number" step="0.001" class="form-control edit-field edit-amount" style="display: none;"
                                                                value="{{ $transaction->transaction_amount }}" />
                                                        </td>
                                                        @can('dashboard_transection_section')
                    
                                                        <td>
                                                            <button class="btn btn-sm btn-primary edit-transaction"
                                                                data-id="{{ $transaction->id }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <div class="edit-actions" style="display: none; white-space: nowrap;">
                                                                <button class="btn btn-sm btn-success submit-transaction"
                                                                        data-id="{{ $transaction->id }}">
                                                                    Save
                                                                </button>
                                                                <button class="btn btn-sm btn-secondary cancel-edit"
                                                                        data-id="{{ $transaction->id }}">
                                                                    Cancel
                                                                </button>
                                                                <button class="btn btn-sm btn-danger cancel-delete"
                                                                        data-id="{{ $transaction->id }}">
                                                                    Delete
                                                                </button>
                                                            </div>
                                                        </td>
        
                                                       @endcan
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5">No transactions found</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                        
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

        </div>
    </div>
@stop

@section('scripts')
    

@stop





<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        console.log('clicked!');
        // Cache the table element and necessary selectors for improved performance
        const $table = $('#transactionTable');
        const $tbody = $table.find('tbody');

        // Handle Edit button click
        $tbody.on("click", ".edit-transaction", function() {
            
            let $button = $(this);
            let $row = $button.closest('tr');
            let $editButton = $row.find('.edit-transaction');

            $row.find('.edit-field').each(function() {
                $(this).data('original-value', $(this).val());
            });
            $row.find('td > span').hide();
            $row.find('.edit-field, .edit-actions').show();
            $editButton.hide();
        });

        // Handle Submit button click (for each row)
        $tbody.on("click", ".submit-transaction", function() {
            let $button = $(this);
            let $row = $button.closest('tr');
            let transactionId = $button.data("id");
            let updatedAmount = $row.find('.edit-amount').val().trim();
            let updatedDate = $row.find('.edit-date').val();
            let updatedPrevious = $row.find('.edit-previous').val().trim();
            let updatedNote = $row.find('.edit-note').val().trim();
            let transactionType = "{{ $type }}"; // Pass the type variable
            // Validate input
            if (!updatedAmount || isNaN(updatedAmount)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Amount',
                    text: 'Please enter a valid numeric amount.'
                });
                return;
            }
            if (!updatedDate) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Date',
                    text: 'Please select a transaction date.'
                });
                return;
            }

            // Disable buttons to prevent double-click
            $row.find('.submit-transaction, .cancel-edit').prop('disabled', true);

            // Send the updated amount for the specific row to the server
            $.ajax({
                url: "{{ route('admin.buysell.deposit.update') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: transactionId,
                    transaction_amount: updatedAmount,
                    transaction_date: updatedDate,
                    actual_amount: updatedPrevious,
                    note: updatedNote,
                    type: transactionType
                },
                success: function(response) {
                    if (response.success) {
                        // Show success Swal
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: 'The transaction has been updated successfully.',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        window.location.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to save changes. Please try again.'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    let errorMessage =
                    "An error occurred. Please try again.";
                    try {
                        const response = JSON.parse(xhr.responseText);

                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        console.error("Error parsing response JSON:", e);
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage
                    });
                },
                complete: function() {
                    // Enable the buttons again after AJAX completes
                    $row.find('.submit-transaction, .cancel-edit').prop('disabled', false);
                }
            });
        });

        // Handle Cancel button click (for each row)
        $tbody.on("click", ".cancel-edit", function() {
            let $button = $(this);
            let $row = $button.closest('tr');

            $row.find('.edit-field').each(function() {
                $(this).val($(this).data('original-value'));
            });
            $row.find('.edit-field, .edit-actions').hide();
            $row.find('td > span').show();
            $row.find('.edit-transaction').show();
        });

        $tbody.on("click", ".cancel-delete", function() {
            let $button = $(this);
            let $row = $button.closest('tr');
            let transactionId = $row.data("id");

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send AJAX request to delete the transaction
                    $.ajax({
                        url: "{{ route('admin.buysell.deposit.delete') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: transactionId
                        },
                        success: function(response) {
                            if (response.success) {
                                // Remove the row from the table
                                $row.remove();

                                // Show success Swal
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error:", xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseText
                            });
                        }
                    });
                }
            });
        });

    });
</script>
