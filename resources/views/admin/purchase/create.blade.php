@extends('layouts.app')


@section('content_header')
    <h1> Unfixed Purchase</h1>
@stop


@section('content')

    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-end">
            <a href="{{ route('admin.purchase.list') }}" class="btn btn-success">
                <i class="fa fa-plus"></i> Unfixed Purchase List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <div class="card-body">
                    <form action="{{ route('admin.purchase.store') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="type">Supplier Type <span>*</span></label>
                                <select name="type" id="supplier_type" class="form-control" required>
                                    <option value="">None</option>
                                    <option value="1" {{ old('type') == '1' ? 'selected' : '' }}>Client
                                    </option>
                                    <option value="0" {{ old('type') == '0' ? 'selected' : '' }}>Supplier
                                    </option>
                                </select>

                            </div>

                            <!-- Supplier Dropdown -->
                            <div class="col-md-3">
                                <label for="supplier">Search By Name <span>*</span></label>
                                <select name="supplier_id" id="supplier" class="form-control" required disabled>
                                    <option value="">None</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" data-type="{{ $supplier->type }}"
                                            {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->full_name }} ({{ $supplier->mobile_number }})
                                        </option>
                                    @endforeach
                                </select>

                            </div>


                            <div class="col-md-3">
                                <label for="created_at">DateTime</label>
                                <input type="datetime-local" name="created_at" id="created_at" class="form-control" required
                                    value="{{ date('Y-m-d\TH:i', strtotime('now +4 hours')) }}">
                            </div>

                            <div class="col-md-3">
                                <label for="ref_no">Reference No</label>
                                <input type="text" name="ref_no" id="ref_no" class="form-control">
                            </div>

                            <div class="col-md-3">
                                <label for="products">Products <span>*</span></label>
                                <select name="products" id="products" class="form-control">
                                    <option value="">Select Product</option>
                                    @foreach ($products as $row)
                                        <option value="{{ $row->id }}">{{ $row->title }} ({{ $row->price_aed }}
                                            AED)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th style="width: 150px;">Product</th>
                                        <th>Quantity (Gram)</th>
                                        <th>Pure Gold</th>
                                        <th style="width: 180px;" id="goldPrice">Rate per Oz (AED)</th>
                                        <th>Premium (USD)</th>
                                        <th>Premium (AED)</th>
                                        <th>Rate (Per Gram)</th>
                                        <th>Subtotal (AED)</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody">
                                    @foreach ($PurchaseItems as $index => $row)
                                        <tr>
                                            <td>
                                                <button class="btn btn-danger btn-sm removeItem"
                                                    data-id="{{ $row->id }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                            <td>{{ $row->product->title }}
                                                <input type="hidden" name="items[{{ $index }}][product_id]"
                                                    value="{{ $row->product_id }}">
                                                <input type="hidden" name="items[{{ $index }}][product_name]"
                                                    value="{{ $row->product->title }}">
                                            </td>
                                            <td><input type="text" name="items[{{ $index }}][quantity]"
                                                    class="form-control quantity_calculation" data-id="{{ $row->id }}"
                                                    data-purity="{{ $row->product->purity }}"></td>
                                            <td><input type="text" name="items[{{ $index }}][pure_quantity]"
                                                    class="form-control" readonly></td>
                                            <td><input type="text" name="items[{{ $index }}][unfix_rate_oz]"
                                                    class="form-control unfix_calculation"></td>
                                            <td><input type="text" name="items[{{ $index }}][discount_usd]"
                                                    class="form-control discount_usd"></td>
                                            <td><input type="text" name="items[{{ $index }}][discount_aed]"
                                                    class="form-control discount_aed" readonly></td>
                                            <td><input type="text" name="items[{{ $index }}][unfix_rate_gram]"
                                                    class="form-control" readonly></td>
                                            <td><input type="text" name="items[{{ $index }}][unfix_subtotal]"
                                                    class="form-control unfix_subtotal" readonly></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="7" class="text-end">Unfix Total (AED)</th>
                                        <th colspan="2"><input type="text" id="unfix_total" name="unfix_total"
                                                class="form-control" readonly></th>
                                    </tr>
                                    <tr>
                                        <th colspan="7" class="text-end">Premium (AED)</th>
                                        <th colspan="2"><input type="text" name="discount" id="discount"
                                                class="form-control" readonly></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="staff_note">Staff Note</label>
                                <textarea name="staff_note" id="staff_note" class="form-control"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="note">Narration</label>
                                <textarea name="note" id="note" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" name="submit" class="btn btn-primary">Save</button>
                            <button type="submit" name="submit_and_continue" class="btn btn-secondary">Save and
                                Continue</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop



@push('js')
    <script>
        function calculateSum() {
            let sum = 0;
            let sum_d = 0;

            // Select all elements with the class 'unfix_subtotal'
            const elements = document.querySelectorAll('.unfix_subtotal');

            // Iterate over the selected elements

            elements.forEach(function(element) {
                // Parse the text content or value of the element as a float and add it to the sum
                let value = parseFloat(element.textContent.trim() || element.value.trim());
                if (!isNaN(value)) { // Ensure that the value is a valid number
                    sum += value;
                }
            });

            // Update the element with id 'total' with the calculated sum
            $('#unfix_total').val(sum.toFixed(3));


            const discount_aed = document.querySelectorAll('.discount_aed');

            // Iterate over the selected elements

            discount_aed.forEach(function(element) {
                // Parse the text content or value of the element as a float and add it to the sum
                let value = parseFloat(element.textContent.trim() || element.value.trim());
                if (!isNaN(value)) { // Ensure that the value is a valid number
                    sum_d += value;
                }
            });
            $('#discount').val(sum_d.toFixed(3));
        }

        function calculateUnfix(id) {
            var unfixValue = parseFloat($("#unfix_calculation_" + id).val()) || 0;
            var pureQuantity = parseFloat($("#pure_quantity_" + id).val()) || 0;
            var discount_usd = parseFloat($("#discount_usd_" + id).val()) || 0;

            var ozValue = (((unfixValue + discount_usd) * 3.674) / 31.1035).toFixed(3);
            var subtotalValue = (ozValue * pureQuantity).toFixed(3);

            $("#discount_aed_" + id).val(((pureQuantity / 31.1035) * discount_usd * 3.674).toFixed(3));
            $("#unfix_oz_" + id).val(ozValue);
            $("#unfix_subtotal_" + id).val(subtotalValue);

            calculateSum();
        }


        $(document).ready(function() {
            $('#supplier_type').change(function() {
                const selectedType = $(this).val();

                if (selectedType) {
                    // Enable the Supplier Dropdown
                    $('#supplier').prop('disabled', false);

                    // Filter suppliers based on type
                    $('#supplier option').each(function() {
                        const supplierType = $(this).data('type');

                        if (supplierType == selectedType || $(this).val() === '') {
                            $(this).show(); // Show matching suppliers
                        } else {
                            $(this).hide(); // Hide non-matching suppliers
                        }
                    });

                    // Reset the Supplier Dropdown
                    $('#supplier').val('');
                } else {
                    // Disable and reset the Supplier Dropdown if no type is selected
                    $('#supplier').prop('disabled', true).val('');
                }
            });

            $('.removeItem').click(function(e) {
                e.preventDefault();


                var id = $(this).attr("data-id");

                //  alert(id);

                $.ajax({
                    type: 'POST',
                    url: '{{ route('admin.purchase.removeitem') }}',
                    data: {
                        id: id,
                        _token: '{{ csrf_token() }}' // Including the CSRF token
                    },
                    dataType: 'text',
                    success: function(data) {
                        try {
                            // Append the received data to the tbody
                            $('#tbody').html(data);
                            $("#products").val('');

                            // Re-enable the dropdown after successful submission
                            $('#products').prop('disabled', false);
                        } catch (e) {
                            console.error('Error parsing JSON data: ', e);

                            // Re-enable the dropdown if there was an error
                            $('#products').prop('disabled', false);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error: ', status, error);

                        // Re-enable the dropdown if there was an error
                        $('#products').prop('disabled', false);
                    }
                });
            });




            $('#diposit_quantity').on('input', function() {
                $("#unfix_quantity").val($('#diposit_quantity').val());

            });

            $('.quantity_calculation').on('input', function() {
                $("#pure_quantity_" + $(this).attr("data-id")).val($(this).val() * $(this).attr(
                    "data-purity"));
            });


            $('.unfix_calculation').on('input', function() {
                var id = $(this).attr("data-id");
                calculateUnfix(id);
            });

            $('.discount_usd').on('input', function() {
                var id = $(this).attr("data-id");
                calculateUnfix(id);
            });


            $('#products').change(function(e) {
                e.preventDefault();

                // Disable the dropdown to prevent multiple submissions
                $('#products').prop('disabled', true);

                $.ajax({
                    type: 'POST',
                    url: '{{ route('admin.purchase.additem') }}',
                    data: {
                        product_id: $('#products option:selected').val(),
                        _token: '{{ csrf_token() }}' // Including the CSRF token
                    },
                    dataType: 'text',
                    success: function(data) {
                        try {
                            // Append the received data to the tbody
                            $('#tbody').html(data);
                            $("#products").val('');

                            // Re-enable the dropdown after successful submission
                            $('#products').prop('disabled', false);
                        } catch (e) {
                            console.error('Error parsing JSON data: ', e);

                            // Re-enable the dropdown if there was an error
                            $('#products').prop('disabled', false);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error: ', status, error);

                        // Re-enable the dropdown if there was an error
                        $('#products').prop('disabled', false);
                    }
                });
            });



        });


        let previousPrice = null;
        let isFetching = false;

        async function getGoldPrice() {
            if (isFetching) return; // Prevent overlapping requests

            isFetching = true; // Set the flag to indicate that a request is in progress

            try {
                const response = await fetch('https://www.goldapi.io/api/XAU/USD', {
                    method: 'GET',
                    headers: {
                        'x-access-token': 'goldapi-7q9uy0tkwrfdtlo-io',
                        'Content-Type': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error(`Error: ${response.status}`);
                }

                const data = await response.json();
                const currentPrice = data.price;

                const priceDiv = document.getElementById('goldPrice');
                priceDiv.textContent = `Gold Price: $${currentPrice}`;
                // $(".fix_amount").val(currentPrice);
                // const totalPriceAED = ((currentPrice / ouncesToGrams) * usdToAedRate) * $("#pure_quantity").val();
                // $("#total_amount").val(totalPriceAED.toFixed(3));
                if (previousPrice !== null) {
                    if (currentPrice > previousPrice) {
                        priceDiv.style.backgroundColor = 'red';
                        priceDiv.style.color = 'white';
                    } else if (currentPrice < previousPrice) {
                        priceDiv.style.backgroundColor = 'green';

                        priceDiv.style.color = 'white';
                    } else {
                        priceDiv.style.backgroundColor = 'white';

                        priceDiv.style.color = 'black';
                    }
                }

                previousPrice = currentPrice;

            } catch (error) {
                console.error('Error fetching the gold price:', error);
            } finally {
                isFetching = false; // Reset the flag to allow the next request
            }

            // Determine the current day of the week
            const currentDay = new Date().getDay();

            // Only schedule the next fetch if it's not Saturday (6) or Sunday (0)
            if (currentDay !== 0 && currentDay !== 6) {
                setTimeout(getGoldPrice, 1000);
            }
        }

        // Start the initial fetch
        getGoldPrice();
    </script>
@endpush
