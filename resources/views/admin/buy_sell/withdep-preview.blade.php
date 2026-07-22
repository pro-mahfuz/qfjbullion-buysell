
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
                    
                                                <!-- Hidden by default -->
                                                <th style="width: 60px;">Action</th>
                                                <th class="new-amount-cell1" style="display: none;">New Amount</th>
                                                {{-- <th class="new-amount-cell2" style="display: none;">New Action</th> --}}
                                                @endcan
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($transactionsByType) > 0)
                                                @foreach ($transactionsByType as $transaction)
                                                    <tr data-id="{{ $transaction->id }}">
                                                        <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d-m-Y') }}
                                                        </td>
                                                        <td class="transaction-note">
                                                            <span>{{ $transaction->actual_amount }}</span>
                                                        </td>
                                                        <td class="transaction-note">
                                                            <span>{{ $transaction->note }}</span>
                                                        </td>
                                                        <td class="transaction-amount">
                                                            <span>{{ $transaction->transaction_amount }}</span>
                                                        </td>
                                                        @can('dashboard_transection_section')
                    
                                                        <td>
                                                            <button class="btn btn-sm btn-primary edit-transaction"
                                                                data-id="{{ $transaction->id }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        </td>
                                                        <td class="new-amount-cell" style="display: none; width: auto;">
                                                            <div class="d-flex ">
                                                                <input type="text" class="form-control new-amount col-md-6"
                                                                    value="{{ $transaction->transaction_amount }}"
                                                                    data-id="{{ $transaction->id }}" />
                                                                <div class="action-cell ml-2">
                                                                    <button class="btn btn-sm btn-success submit-transaction"
                                                                        data-id="{{ $transaction->id }}">
                                                                        Submit
                                                                    </button>
                                                                    <button class="btn btn-sm btn-danger cancel-edit mt-1"
                                                                        data-id="{{ $transaction->id }}">
                                                                        Cancel
                                                                    </button>
                                                                    <button class="btn btn-sm btn-danger cancel-delete mt-1"
                                                                        data-id="{{ $transaction->id }}">
                                                                        Delete
                                                                    </button>
        
        
                                                                </div>
                                                            </div>
                                                        </td>
        
                                                       @endcan
                                                        {{-- <td class="action-cell" style="display: none;">
                                                            <button class="btn btn-sm btn-success submit-transaction"
                                                                data-id="{{ $transaction->id }}">
                                                                Submit
                                                            </button>
                                                            <button class="btn btn-sm btn-danger cancel-edit"
                                                                data-id="{{ $transaction->id }}">
                                                                Cancel
                                                            </button>
                                                        </td> --}}
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
            let $newAmountCell = $row.find('.new-amount-cell');
            let $newAmountCell1 = $row.find('.new-amount-cell1');
            // let $newAmountCell2 = $row.find('.new-amount-cell2');
            let $actionCell = $row.find('.action-cell');
            let $editButton = $row.find('.edit-transaction');

            // Show the new amount input field, submit button, and cancel button
            $newAmountCell.show();
            $newAmountCell1.show();
            $actionCell.show();

            // Show the new amount column header
            $table.find("th.new-amount-cell1").show();
            // $table.find("th.new-amount-cell2").show();
            // Hide the Edit button
            $editButton.hide();
        });

        // Handle Submit button click (for each row)
        $tbody.on("click", ".submit-transaction", function() {
            let $button = $(this);
            let $row = $button.closest('tr');
            let transactionId = $button.data("id");
            let updatedAmount = $row.find('.new-amount').val().trim();
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
                    type: transactionType
                },
                success: function(response) {
                    if (response.success) {
                        // Update the table with the new value in the original column
                        $row.find('.transaction-amount span').text(updatedAmount);

                        // Hide the new amount input field and action buttons
                        $row.find('.new-amount-cell').hide();
                        $row.find('.action-cell').hide();

                        // Hide the new amount column header
                        $table.find("th.new-amount-cell1").hide();
                        // $table.find("th.new-amount-cell2").hide();

                        // Show the Edit button again
                        $row.find('.edit-transaction').show();

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

            // Hide the new amount input field and action buttons
            $row.find('.new-amount-cell').hide();
            $row.find('.action-cell').hide();
            $row.find('.new-amount-cell1').hide();

            // Hide the new amount column header
            $table.find("th.new-amount-cell1").hide();
            // $table.find("th.new-amount-cell2").hide();

            // Show the Edit button again
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
