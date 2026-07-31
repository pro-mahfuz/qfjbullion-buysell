@extends('layouts.app')
@section('content_header')
    <h1>Fixed Purchase Edit</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Sale</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.sale.update', $purchase->id) }}" method="POST">
                        @csrf
                        {{-- @dd($purchase) --}}
                        <input type="hidden" name="supplier_id" value={{ $purchase->supplier_id }}>
                        <div class="row">

                            <div class="col-md-12">
                                <div class="row">
                                    <!-- DateTime input with default value set -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="diposit_quantity">DateTime</label>
                                            <input type="datetime-local" name="created_at" class="form-control"
                                                required=""
                                                value="{{ old('created_at', date('Y-m-d\TH:i', strtotime('now +4 hours'))) }}">
                                        </div>

                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="supplier_id">Products <span>*</span></label>
                                            <select name="product_id" id="product_id" class="form-control" required="">
                                                <option value="">Select product</option>
                                                @if (count($purchases) > 0)
                                                    @foreach ($purchases as $row)
                                                        <option value="{{ $row->product_id }}"
                                                            data-purity="{{ $purities[$row->product_id] }}"
                                                            {{ old('product_id', $purchaseItem->product_id) == $row->product_id ? 'selected' : '' }}>
                                                            {{ $row->product_name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>



                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="quantity">Fix Quantity (Gram)</label>
                                            <input type="text" name="quantity" id="quantity"
                                                value="{{ old('quantity', $purchaseItem->quantity ?? 1000) }}"
                                                class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="pure_quantity">Pure Quantity</label>
                                            <input type="hidden" name="pure_rate" id="pure_rate" value="0.995">
                                            <input type="text" name="pure_quantity" id="pure_quantity"
                                                value="{{ old('pure_quantity', $purchaseItem->pure_quantity ?? 995) }}"
                                                class="form-control" readonly>
                                        </div>
                                    </div>

                                    <!-- Fix Amount label -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Fix Amount <span id="goldPrice"
                                                    style="padding: 10px;">Loading...</span></label>
                                        </div>
                                    </div>

                                    <!-- Checkbox for editable state -->
                                    <div class="input-group col-md-6">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <input type="checkbox" aria-label="Checkbox for following text input"
                                                    id="editableCheckbox"
                                                    {{ old('editableCheckbox', $purchase->editableCheckbox ? 'checked' : '') }}>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control fix_amount" name="unfix_rate_oz"
                                            aria-label="Text input with checkbox" id="fix_amount" disabled>
                                    </div>

                                    <!-- Reduce Total Amount input -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Reduce Total Amount</label>
                                            <input type="text" name="deposit_amount" id="total_amount"
                                                value="{{ old('deposit_amount', $purchase->deposit_amount) }}"
                                                class="form-control">
                                        </div>
                                    </div>

                                    <!-- Narration (Text area) with old value -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="note">Narration</label>
                                            <textarea name="note" id="note" class="form-control">{{ old('note', $purchase->note) }}</textarea>
                                        </div>
                                    </div>

                                    <!-- Submit and Close buttons -->
                                    <div class="col-md-12 text-right">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success">Submit</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop



@push('js')
    <script>
        $(document).ready(function() {
            document.getElementById('editableCheckbox').addEventListener('change', function() {
                var input = document.getElementById('fix_amount');
                if (this.checked) {
                    input.disabled = false;
                    input.classList.remove('fix_amount');
                } else {
                    input.disabled = true;
                    input.classList.add('fix_amount');
                }
            });

            $('#quantity').on('input', function() {
                $("#pure_quantity").val(($('#quantity').val() * $('#pure_rate').val()).toFixed(3));
                $("#total_amount").val(($('#pure_quantity').val() * $('#fix_amount').val()).toFixed(3));
            });

            $('#fix_amount').on('change', function() {
                $("#total_amount").val(($('#quantity').val() * $(this).val()).toFixed(3));
            });

            $('#product_id').on('change', function() {
                var selectedValue = $(this).val();
                if (selectedValue) {
                    $('#pure_rate').val();
                } else {
                    $('#supplier_details').hide();
                }
            });

            document.getElementById('product_id').addEventListener('change', function() {
                const selectElement = document.getElementById('product_id');
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                const purity = selectedOption.getAttribute('data-purity');
                $('#pure_rate').val(purity);
                $("#pure_quantity").val(($('#quantity').val() * $('#pure_rate').val()).toFixed(3));
            });

        });

        let previousPrice = null;
        let isFetching = false;
        const usdToAedRate = 3.674;
        const ouncesToGrams = 31.1035;

        async function getGoldPrice() {
            if (isFetching) return;
            isFetching = true;

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
                $(".fix_amount").val(currentPrice);
                const totalPriceAED = (($("#fix_amount").val() / ouncesToGrams) * usdToAedRate) * $("#pure_quantity")
                    .val();
                $("#total_amount").val(totalPriceAED.toFixed(3));

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
                isFetching = false;
            }

            const currentDay = new Date().getDay();
            if (currentDay !== 0 && currentDay !== 6) {
                setTimeout(getGoldPrice, 1000);
            }
        }

        getGoldPrice();
    </script>
@endpush
