@extends('layouts.app')

@section('content_header')
    <h1> Edit Unfixed Purchase</h1>
@stop

@section('content')

    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-end">
            <a href="{{ route('admin.purchase.list') }}" class="btn btn-success">
                <i class="fa fa-list"></i> Unfixed Purchase List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.purchase.update', $purchase->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $purchase->id }}">

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="supplier">Supplier <span>*</span></label>
                                <select name="supplier_id" id="supplier" class="form-control" required>
                                    <option value="">Select Supplier</option>
                                    @foreach ($suppliers as $row)
                                        <option value="{{ $row->id }}"
                                            {{ $purchase->supplier_id == $row->id ? 'selected' : '' }}>
                                            {{ $row->full_name }} ({{ $row->mobile_number }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="created_at">DateTime</label>
                                <input type="datetime-local" name="created_at" id="created_at" class="form-control" required
                                    value="{{ date('Y-m-d\TH:i', strtotime($purchase->created_at)) }}">
                            </div>

                            <div class="col-md-3">
                                <label for="ref_no">Reference No</label>
                                <input type="text" name="ref_no" id="ref_no" class="form-control"
                                    value="{{ $purchase->ref_no }}">
                            </div>

                            <div class="col-md-3">
                                <label for="products">Products <span>*</span></label>
                                <select name="products" id="products" class="form-control">
                                    <option value="">Select Product</option>
                                    @foreach ($products as $row)
                                        <option value="{{ $row->id }}"
                                            {{ in_array($row->id, $purchase->items->pluck('product_id')->toArray()) ? 'selected' : '' }}>
                                            {{ $row->title }} ({{ $row->price_aed }} AED)
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
                                    @foreach ($purchase->items as $index => $item)
                                        <tr>
                                            <td>
                                                <button class="btn btn-danger btn-sm removeItem"
                                                    data-id="{{ $item->id }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                            <td>{{ $item->product->title }}
                                                <input type="hidden" name="items[{{ $index }}][product_id]"
                                                    value="{{ $item->product_id }}">
                                            </td>
                                            <td><input type="text" name="items[{{ $index }}][quantity]"
                                                    class="form-control quantity_calculation" value="{{ $item->quantity }}"
                                                    data-id="{{ $index }}"
                                                    data-purity="{{ $item->product->purity }}"></td>
                                            <td><input type="text" name="items[{{ $index }}][pure_quantity]"
                                                    class="form-control" value="{{ $item->pure_quantity }}" readonly></td>
                                            <td><input type="text" name="items[{{ $index }}][unfix_rate_oz]"
                                                    class="form-control unfix_calculation"
                                                    value="{{ $item->unfix_rate_oz }}"
                                                    id="unfix_calculation_{{ $index }}"></td>
                                            <td><input type="text" name="items[{{ $index }}][discount_usd]"
                                                    class="form-control discount_usd" value="{{ $item->discount_usd }}"
                                                    data-id="{{ $index }}"></td>
                                            <td><input type="text" name="items[{{ $index }}][discount_aed]"
                                                    class="form-control discount_aed" value="{{ $item->discount_aed }}"
                                                    readonly id="discount_aed_{{ $index }}"></td>
                                            <td><input type="text" name="items[{{ $index }}][unfix_rate_gram]"
                                                    class="form-control" value="{{ $item->unfix_rate_gram }}" readonly>
                                            </td>
                                            <td><input type="text" name="items[{{ $index }}][unfix_subtotal]"
                                                    class="form-control unfix_subtotal" value="{{ $item->unfix_subtotal }}"
                                                    readonly id="unfix_subtotal_{{ $index }}"></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="7" class="text-end">Unfix Total (AED)</th>
                                        <th colspan="2"><input type="text" id="unfix_total" name="unfix_total"
                                                class="form-control" value="{{ $purchase->unfix_total }}" readonly></th>
                                    </tr>
                                    <tr>
                                        <th colspan="7" class="text-end">Premium (AED)</th>
                                        <th colspan="2"><input type="text" name="discount" id="discount"
                                                class="form-control" value="{{ $purchase->discount }}" readonly></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="staff_note">Staff Note</label>
                                <textarea name="staff_note" id="staff_note" class="form-control">{{ $purchase->staff_note }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="note">Narration</label>
                                <textarea name="note" id="note" class="form-control">{{ $purchase->note }}</textarea>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success w-25">Update</button>
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
                let value = parseFloat(element.value.trim());
                if (!isNaN(value)) {
                    sum += value;
                }
            });

            // Update the element with id 'total' with the calculated sum
            $('#unfix_total').val(sum.toFixed(3));

            const discount_aed = document.querySelectorAll('.discount_aed');
            discount_aed.forEach(function(element) {
                let value = parseFloat(element.value.trim());
                if (!isNaN(value)) {
                    sum_d += value;
                }
            });

            $('#discount').val(sum_d.toFixed(3));
        }

        function calculateUnfix(id) {
            var unfixValue = parseFloat($("#unfix_calculation_" + id).val()) || 0;
            var pureQuantity = parseFloat($("input[name='items[" + id + "][quantity]']").val()) || 0;
            var discount_usd = parseFloat($("input[name='items[" + id + "][discount_usd]']").val()) || 0;

            var ozValue = (((unfixValue + discount_usd) * 3.674) / 31.1035).toFixed(3);
            var subtotalValue = (ozValue * pureQuantity).toFixed(3);

            $("input[name='items[" + id + "][discount_aed]']").val(((pureQuantity / 31.1035) * discount_usd * 3.674)
                .toFixed(3));
            $("input[name='items[" + id + "][unfix_subtotal]']").val(subtotalValue);
            $("input[name='items[" + id + "][unfix_rate_gram]']").val(ozValue);

            calculateSum();
        }

        $(document).ready(function() {
            $('.removeItem').click(function(e) {
                e.preventDefault();

                var id = $(this).attr("data-id");

                $.ajax({
                    type: 'POST',
                    url: '{{ route('admin.purchase.removeitem') }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id
                    },
                    success: function(response) {
                        $("#unfix_total").val(response.total);
                        $("#tbody").html(response.data);
                    }
                });
            });

            $('.quantity_calculation').change(function() {
                var id = $(this).attr("data-id");
                calculateUnfix(id);
            });

            $('.discount_usd').change(function() {
                var id = $(this).attr("data-id");
                calculateUnfix(id);
            });
        });

        let previousPrice = null;
        let isFetching = false;
        // const usdToAedRate = 3.674; // Example exchange rate, update with the latest rate
        // const ouncesToGrams = 31.1035; // 1 troy ounce = 31.1035 grams

        // Function to fetch the gold price
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
