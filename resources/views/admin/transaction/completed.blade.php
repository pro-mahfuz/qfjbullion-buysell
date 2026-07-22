@extends('layouts.app')

@section('content')
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
                <div class="x_title">
                    <h2>
                        Completed Transactions
                    </h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <!-- Cards Section -->
                    <div class="row transactionContainer">
                        @foreach ($details as $key => $detail)
                            <div class="col-md-4 mt-2">
                                <div
                                    class="card {{ ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger', 'bg-secondary'][$loop->index % 6] }}">
                                    <div class="card-header text-center text-white">
                                        {{ $key }}
                                    </div>
                                    <div class="card-body text-center text-white">
                                        <strong>{{ $detail }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer">Search Customer<span>*</span></label>
                                <select name="customer" class="form-control select2-container
 common_select2" id="customerSelect" required>
                                    <option value="">Select Customer</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="fromDate">From Date</label>
                                <input type="date" class="form-control" id="fromDate">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="toDate">To Date</label>
                                <input type="date" class="form-control" id="toDate">
                            </div>
                        </div>

                        <div class="col-md-12 text-right">
                            <button class="btn btn-primary" id="dateFilterBtn">Filter</button>
                            <button class="btn btn-secondary" id="clearFilterBtn">Clear</button>
                        </div>
                    </div>


                    <!-- Transaction Table -->
                    <div class="student-list search-form mt-3" id="transactionContainer" style="display: none;">
                        <div id="transactionData" class="table-responsive table-sm mt-3">
                            <table class="table table-sm">
                                <thead class="thead-dark">
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
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="transactionTable">
                                </tbody>
                            </table>
                            <div id="paginationLinks" class="d-flex justify-content-end mt-3 pagination pagination-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
@push('js')
    <script>
        $(document).ready(function() {
            let allTransactions = [];


            // Fetch transactions initially
            fetchAllTransactions();

            // Event handler for customer change
            $('#customerSelect').on('change', function() {
                const customerId = $(this).val();
                filterTransactions(customerId, $('#fromDate').val(), $('#toDate').val());
            });

            // Date filter event handler
            $('#dateFilterBtn').on('click', function() {
                const fromDate = $('#fromDate').val();
                const toDate = $('#toDate').val();
                filterTransactions($('#customerSelect').val(), fromDate, toDate);
            });

            // Clear filter button event handler
            $('#clearFilterBtn').on('click', function() {
                // Reset all filters
                $('#customerSelect').val('').trigger('change'); // Reset customer select
                $('#fromDate').val(''); // Clear from date
                $('#toDate').val(''); // Clear to date

                // Render all transactions again
                renderTransactions(allTransactions);
            });

            // Fetch all transactions
            function fetchAllTransactions(page = 1) {
                $.ajax({
                    url: '{{ route('admin.transaction.completed.list') }}?page=' + page,
                    type: 'GET',
                    success: function(response) {
                        if (response.data.length > 0) {
                            allTransactions = response.data;
                            renderTransactions(allTransactions);
                            $('#paginationLinks').html(response.links);

                            // Handle pagination click
                            $('#paginationLinks a').on('click', function(event) {
                                event.preventDefault();
                                const page = $(this).attr('href').split('page=')[1];
                                fetchAllTransactions(page);
                            });
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

            // Filter transactions by customer and date
            function filterTransactions(customerId = null, fromDate = null, toDate = null) {
                let filteredTransactions = allTransactions;

                // Filter by customer if selected
                if (customerId) {
                    filteredTransactions = filteredTransactions.filter(transaction => transaction.customer_id ==
                        customerId);
                }

                // Filter by date range if selected
                if (fromDate || toDate) {
                    filteredTransactions = filteredTransactions.filter(transaction => {
                        const transactionDate = new Date(transaction.created_at);
                        const from = fromDate ? new Date(fromDate) : null;
                        const to = toDate ? new Date(toDate) : null;

                        // Compare dates
                        return (!from || transactionDate >= from) && (!to || transactionDate <= to);
                    });
                }

                renderTransactions(filteredTransactions);
            }

            // Render the transaction table
            function renderTransactions(transactions) {
                let rows = '';
                let sl = 1;

                if (transactions.length > 0) {
                    transactions.forEach(transaction => {
                        const type = transaction.transaction_type == 'buy' ? 'sell' : 'buy';
                        rows += `<tr id="transaction-row-${transaction.id}">
                    <th scope="row">${sl++}</th>
                    <td>${transaction.reference_no ?? "N/A"}</td>
                    <td>${transaction.customer ? transaction.customer.name : 'N/A'}</td>
                    <td>${new Date(transaction.linked_buy.created_at).toLocaleString('en-GB', { year: 'numeric', month: '2-digit', day: '2-digit'})}</td>
                    <td>${transaction.quantity}</td>
                    <td>${transaction.transaction_type}</td>
                    <td>${transaction.linked_buy.current_rate}</td>
                    <td>${new Date(transaction.created_at).toLocaleString('en-GB', { year: 'numeric', month: '2-digit', day: '2-digit'})}</td>
                    <td>${type}</td>
                    <td>${transaction.current_rate}</td>
                    <td>${transaction.transaction_amount.toFixed(2)}</td>
                    <td>
                        <button class="btn btn-danger btn-sm delete-transaction" data-id="${transaction.id}">
                            Void
                        </button>
                    </td>
                </tr>`;
                    });
                    $('#transactionTable').html(rows);
                    $('#transactionContainer').show();
                } else {
                    $('#transactionTable').html(
                        '<tr><td colspan="12" class="text-center">No Transactions Found</td></tr>');
                    $('#transactionContainer').show();
                }
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


        $(document).on('click', '.delete-transaction', function() {
            const transactionId = $(this).data('id');

            Swal.fire({
                title: 'Enter Password',
                input: 'password',
                inputAttributes: {
                    autocapitalize: 'off',
                    placeholder: 'Enter your password'
                },
                showCancelButton: true,
                confirmButtonText: 'Delete',
                showLoaderOnConfirm: true,
                preConfirm: (password) => {
                    // Using the regular AJAX without a Promise
                    $.ajax({
                        url: '{{ route('admin.password.check') }}',
                        type: 'POST',
                        data: {
                            password: password,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                deleteTransaction(transactionId);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    text: 'Password is incorrect.'
                                });
                            }
                        },
                        error: function(xhr, status, error) {

                            Swal.fire({
                                icon: 'error',
                                text: 'An error occurred while verifying the password.'
                            });

                        }
                    });
                }
            });
        });

        // Function to delete the transaction after successful password verification
        function deleteTransaction(transactionId) {
            $.ajax({
                url: '{{ route('admin.transaction.delete') }}',
                type: 'POST',
                data: {
                    id: transactionId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        text: 'Transaction deleted successfully.'
                    });
                    window.location.reload();

                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        text: 'An error occurred while deleting the transaction.'
                    });
                }
            });
        }
    </script>
@endpush
