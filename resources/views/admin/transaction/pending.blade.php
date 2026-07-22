@extends('layouts.app')

@section('content')
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
                <div class="x_title">
                    <h2>
                        @if ($type == '1')
                            Running
                        @else
                            Pending
                        @endif List
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

                    <!-- Transaction Table -->
                    <div class="student-list search-form mt-3" id="transactionContainer" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer">Search Customer<span>*</span></label>
                                    <select name="customer" class="form-control select2-container
 common_select2" id="customerSelect"
                                        required>
                                        <option value="">Select Customer</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="transactionData" class="table-responsive table-sm mt-3">
                            <table class="table table-sm">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Customer Name</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">TT Quantity</th>
                                        <th scope="col">Threshold Rate</th>
                                        <th scope="col">Stop Limit</th>
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

            // Initialize select2
            $('.select2-container
 common_select2').select2({
                dropdownParent: $("#dynamicModal")
            });

            // Fetch all transactions initially
            fetchAllTransactions();

            // Fetch filtered transactions based on customer selection
            $('#customerSelect').on('change', function() {
                const customerId = $(this).val();
                filterTransactions(customerId);
            });

            // Fetch all transactions function
            function fetchAllTransactions(page = 1) {
                $.ajax({
                    url: '{{ route('admin.transaction.show.runningPendingList') }}?page=' + page,
                    type: 'GET',
                    success: function(response) {
                        if (response.data.length > 0) {
                            allTransactions = response.data;
                            renderTransactions(allTransactions);
                            $('#paginationLinks').html(response.links);

                            // Pagination click handling
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

            // Filter transactions based on customer selection
            function filterTransactions(customerId) {
                let filteredTransactions = allTransactions;

                if (customerId) {
                    filteredTransactions = allTransactions.filter(transaction => transaction.customer_id ==
                        customerId);
                }

                renderTransactions(filteredTransactions);
            }

            // Render the transaction table
            function renderTransactions(transactions) {
                let rows = '';
                let sl = 1;

                if (transactions.length > 0) {
                    transactions.forEach(transaction => {
                        rows += `
                        <tr>
                            <th scope="row">${sl++}</th>
                            <td>${transaction.customer.name}</td>
                            <td>${new Date(transaction.created_at).toLocaleString('en-GB', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' })}</td>
                            <td>${transaction.type}</td>
                            <td>${transaction.tt_quantity}</td>
                            <td>${transaction.threshold_rate}</td>
                            <td>${transaction.stop_limit}</td>
                        </tr>
                    `;
                    });
                    $('#transactionTable').html(rows);
                    $('#transactionContainer').show();
                } else {
                    $('#transactionTable').html(
                        '<tr><td colspan="7" class="text-center">No Transactions Found</td></tr>');
                    $('#transactionContainer').show();
                }
            }

            // Display error messages
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
@endpush
