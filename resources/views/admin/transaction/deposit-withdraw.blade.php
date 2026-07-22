@extends('layouts.master')

@section('content')

<section class="section">
	<ul class="breadcrumb breadcrumb-style">
		<li class="breadcrumb-item">
		  	<a href="{{route('admin.dashboard.buysell')}}">
				<i class="fas fa-home"></i>
			</a>
		</li>
		<li class="breadcrumb-item active">Transaction</li>
	</ul>
	
	<div class="row">
	    <div class="offset-md-1 col-md-4">
	        @can('deposit_add')
			<div class="card">
				<div class="card-body m-0 pb-0">
					<div class="row">
						<div class="col-md-12" id="invoice_chart_div">
							<form id="" action="#" method="POST">
								<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
								<input type="hidden" name="customer_id" id="customer_id">
								<div class="row d-flex align-items-center justify-content-center">
									<h6>Deposit</h6>
								</div>
								<div class="row d-flex align-items-center justify-content-center">
									<div class="table-responsive">
										<table class="table table-striped table-hover custom-bordered m-0" id="example" style="width:100%;">
											<thead>
												
												<tr class="text-center">
													
													<th>Deposit Count</th>
													<th>Deposited Amount</th>
													
												</tr>
											</thead>
											<tbody>
											    <tr class="text-center" id="total_count">
												 <td>{{ $deposit['Total deposit Count'] }}</td>
												 <td>{{ $deposit['Total deposit Amount'] }}</td>
												</tr>
											</tbody>
											<tfoot id="table_footer" style="background: #F0F3FF">
												
											</tfoot>
										</table>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			@endcan
		</div>
		
	    <div class="offset-md-1 col-md-4">
	        @can('withdraw_add')
			<div class="card">
				<div class="card-body m-0 pb-0">
					<div class="row">
						<div class="col-md-12" id="invoice_chart_div">
							<form id="" action="#" method="POST">
								<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
								<input type="hidden" name="customer_id" id="customer_id">
								<div class="row d-flex align-items-center justify-content-center">
									<h6>Withdraw</h6>
								</div>
								<div class="row d-flex align-items-center justify-content-center">
									<div class="table-responsive">
										<table class="table table-striped table-hover custom-bordered m-0" id="example" style="width:100%;">
											<thead>
												
												<tr class="text-center">
													
													<th>Withdraw Count</th>
													<th>Withdrawn Amount</th>
													
												</tr>
											</thead>
											<tbody>
											    <tr class="text-center" id="total_count">
												 <td>{{ $withdraw['Total withdraw Count'] }}</td>
												 <td>{{ $withdraw['Total withdraw Amount'] }}</td>
												</tr>
											</tbody>
											<tfoot id="table_footer" style="background: #F0F3FF">
											</tfoot>
										</table>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			@endcan
		</div>
		
		
	</div>
	
	
    <div class="clearfix"></div>
   
    
    
    
    <div class="row">
        
        <div class="offset-md-1 col-md-4 mt-3">
            @can('deposit_add')
            <div class="card">
                <div class="card-header d-flex justify-content-center">
    			  <h4 class="text-center">Deposit Add</h4>
    			</div>
                <div class="card-body">
                    <form action="{{ route('admin.transaction.save') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                            
                            <div class="row">
                                @if ($customers != null)
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="note">Select Customer<span>*</span></label>
                                            <select name="customer_id" class="form-control select2-container common_select2" required>
                                                <option value="">Select Customer</option>
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}" {{ ($customer->id == $customer_id && $type == 'deposit') ? 'selected' : '' }}>{{ $customer->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @else
                                    <input type="hidden" name="customer_id" value="{{ $id }}" />
                                @endif
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="transaction_amount">Amount<span>*</span></label>
                                        <input type="text" name="transaction_amount" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="note">Note<span>*</span></label>
                                        <input type="text" name="note" class="form-control" required>
                                    </div>
                                </div>
        
                                <input type="hidden" name="starting_rate" value="0" />
                                <input type="hidden" name="business_id" value="{{ $businessId }}" />
                                <input type="hidden" name="transaction_type" value="deposit" />
                                <input type="hidden" name="reference_table" value="" />
                                <input type="hidden" name="reference_row" value="" />
                                <input type="hidden" name="tnx_id" value="{{ time() }}" />
                            </div>
                            
                            
                            <hr>
                            <div class="row d-flex justify-content-center">
        
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </div>
        
                            </div>
                                
                           
                    
                    </form>
                </div>

            </div>
            @endcan
        </div>
        
        <div class="offset-md-1 col-md-4 mt-3">
            @can('withdraw_add')
            <div class="card">
                <div class="card-header d-flex justify-content-center">
    			  <h4 class="text-center">Withdraw Add</h4>
    			</div>
                <div class="card-body">
                    <form action="{{ route('admin.transaction.save') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                            
                            <div class="row">
                                @if ($customers != null)
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="note">Select Customer<span>*</span></label>
                                            <select name="customer_id" class="form-control select2-container common_select2" required>
                                                <option value="">Select Customer</option>
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}" {{ ($customer->id == $customer_id && $type == 'withdraw') ? 'selected' : '' }}>{{ $customer->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @else
                                    <input type="hidden" name="customer_id" value="{{ $id }}" />
                                @endif
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="transaction_amount">Amount<span>*</span></label>
                                        <input type="text" name="transaction_amount" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="note">Note<span>*</span></label>
                                        <input type="text" name="note" class="form-control" required>
                                    </div>
                                </div>
        
                                <input type="hidden" name="starting_rate" value="0" />
                                <input type="hidden" name="business_id" value="{{ $businessId }}" />
                                <input type="hidden" name="transaction_type" value="withdraw" />
                                <input type="hidden" name="reference_table" value="" />
                                <input type="hidden" name="reference_row" value="" />
                                <input type="hidden" name="tnx_id" value="{{ time() }}" />
                            </div>
                            
                            
                            <hr>
                            <div class="row d-flex justify-content-center">
        
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </div>
        
                            </div>
                                
                           
                    
                    </form>
                </div>

            </div>
            @endcan
        </div>
    </div>
    
   
</section>   
    
@stop

@push('js')
    <script>
        $(document).ready(function() {
            let allTransactions = [];
            let currentPage = 1;
            let totalPages = 1;
            let businessId = '{{ $businessId }}';

            // Fetch transactions initially
            fetchAllTransactions(currentPage);

            // Filter button click event handler
            $('#filterBtn').on('click', function() {
                const customerId = $('#customerSelect').val();
                const fromDate = $('#fromDate').val();
                const toDate = $('#toDate').val();

                filterTransactions(customerId, fromDate, toDate);
            });

            // Clear button click event handler
            $('#clearFilterBtn').on('click', function() {
                $('#customerSelect').val('').trigger('change');
                $('#fromDate').val('');
                $('#toDate').val('');
                renderTransactions(allTransactions, currentPage, totalPages);
            });

            function fetchAllTransactions(page = 1) {
                $.ajax({
                    url: `{{ route('admin.transaction.show.withDepList') }}?type={{ $type }}&page=${page}`,
                    type: 'GET',
                    success: function(response) {
                        if (response.data.length > 0) {
                            allTransactions = response.data;
                            currentPage = response.meta.current_page;
                            totalPages = response.meta.total_pages;
                            renderTransactions(allTransactions, currentPage, totalPages);
                        } else {
                            displayError('No Transactions found');
                            $('#transactionContainer').hide();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error: ", xhr, status, error);
                        displayError('An error occurred while fetching transactions');
                    }
                });
            }

            function filterTransactions(customerId, fromDate, toDate) {
                let filteredTransactions = allTransactions;

                if (customerId) {
                    filteredTransactions = filteredTransactions.filter(transaction => transaction.customer_id ==
                        customerId);
                }

                if (fromDate) {
                    const fromTimestamp = new Date(fromDate).getTime();
                    filteredTransactions = filteredTransactions.filter(transaction => {
                        const transactionDate = new Date(transaction.created_at).getTime();
                        return transactionDate >= fromTimestamp;
                    });
                }

                if (toDate) {
                    const toTimestamp = new Date(toDate).getTime();
                    filteredTransactions = filteredTransactions.filter(transaction => {
                        const transactionDate = new Date(transaction.created_at).getTime();
                        return transactionDate <= toTimestamp;
                    });
                }

                renderTransactions(filteredTransactions, 1, 1); // Render filtered data without pagination
            }

            function renderTransactions(transactions, currentPage, totalPages) {
                let rows = '';
                let sl = 1;
                if (transactions.length > 0) {
                    transactions.forEach(transaction => {
                        rows += `
                    <tr>
                        <th scope="row">${sl++}</th>
                        <td>${transaction?.customer?.name || 'N/A'}</td>
                        <td>${transaction?.created_at ? new Date(transaction.created_at).toLocaleString('en-GB', { year: 'numeric', month: '2-digit', day: '2-digit' }) : 'N/A'}</td>
                        <td>${transaction?.transaction_type || 'N/A'}</td>
                        <td>${transaction?.transaction_amount != null ? transaction.transaction_amount.toFixed(2) : 'N/A'}</td>
                        <td>${transaction?.created_by || 'N/A'}</td>
                        <td>
                            ${transaction?.status === 0 ? `<button class="btn btn-primary btn-sm delete-transaction" data-id="${transaction.id}">
                                        Approve</button>` : ''}
                        </td>
                    </tr>
                    `;
                    });
                } else {
                    rows = '<tr><td colspan="6" class="text-center">No Transactions Found</td></tr>';
                }

                $('#transactionTable').html(rows);
                renderPagination(currentPage, totalPages);
                $('#transactionContainer').show();
            }

            function renderPagination(currentPage, totalPages) {
                let paginationHtml = '<nav><ul class="pagination justify-content-center">';
                for (let i = 1; i <= totalPages; i++) {
                    paginationHtml += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link pagination-link" href="#" data-page="${i}">${i}</a>
                </li>`;
                }
                paginationHtml += '</ul></nav>';

                $('#paginationContainer').html(paginationHtml);
            }

            $(document).on('click', '.pagination-link', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                fetchAllTransactions(page);
            });

            $(document).on('click', '.delete-transaction', function() {
                const transactionId = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to approve this transaction?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Approve',
                    cancelButtonText: 'Cancel',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        deleteTransaction(transactionId);
                    }
                });
            });

            function deleteTransaction(transactionId) {
                $.ajax({
                    url: '{{ route('admin.transaction.approve') }}',
                    type: 'POST',
                    data: {
                        id: transactionId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            text: 'Transaction approved successfully.'
                        });
                        fetchAllTransactions(currentPage);
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            text: 'An error occurred while approving the transaction.'
                        });
                    }
                });
            }

            function displayError(message) {
                Swal.fire({
                    icon: 'error',
                    text: message,
                    position: 'top-end',
                    toast: true,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            }
        });
    </script>
    
    <script>
    $(document).ready(function() {

            $('.select2-container common_select2').select2({
                dropdownParent: $(this)
            });
        });

        // Restrict 'note' input to numbers only
        $('input[name="note"]').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });
</script>
@endpush
