<div class="row">
    <div class="col-md-6">
        <table class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Initial</th>
                    <th>Deposit </th>
                    <th>Sale</th>
                    <th style="text-align: right;">Balance</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>{{ $supplier->full_name }} </th>
                    <th>{{ $supplier->init_balance }} </th>
                    <th>{{ $supplier->deposit_amount }} </th>
                    <th>{{ $supplier->sell_amount }} </th>
                    <th style="text-align: right;">
                        {{ $supplier->init_balance + $supplier->deposit_amount - $supplier->sell_amount }} </th>
                </tr>
                </thead>

        </table>


        <table class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Product </th>
                    <th style="text-align: right;">Unfix</th>
                    <th style="text-align: right;">Fix</th>
                    <th style="text-align: right;">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @if (count($purchases) > 0)
                    <?php $sl = 0;
                    $gram = 0; ?>
                    @foreach ($purchases as $row)
                        <?php
                        ?>
                        <tr>
                            <td>{{ $row->product_name }}</td>
                            <td style="text-align: right;">{{ $row->total_buy_quantity }} Gram</td>
                            <td style="text-align: right;">{{ $row->total_sale_quantity }} Gram</td>
                            <td style="text-align: right;">{{ $row->total_buy_quantity - $row->total_sale_quantity }}
                                Gram</td>

                        </tr>
                    @endforeach

                @endif
            </tbody>
        </table>



    </div>
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="diposit_quantity">DateTime</label>
                    <input type="datetime-local" name="created_at" class="form-control" required=""
                        value="{{ date('Y-m-d\TH:i', strtotime('now +4 hours')) }}">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="product_id">Products <span>*</span></label>
                    <select name="product_id" id="product_id" class="form-control" required="">
                        <option value="">Select product</option>
                    @if (count($purities) > 0)
                        @foreach ($purities as $row)
                            <option value="{{ $row->id }}" data-purity="{{ $row->purity }}"
                                {{ old('product_id') == $row->id ? 'selected' : '' }}>
                                {{ $row->title }}
                            </option>
                        @endforeach
                    @endif
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label for="quantity">Fix Quantity (Gram)</label>
                    <input type="text" name="quantity" id="quantity" value="1000" class="form-control">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="pure_quantity">Pure Quantity</label>
                    <input type="hidden" name="pure_rate" id="pure_rate" value="0.995">
                    <input type="text" name="pure_quantity" id="pure_quantity" value="995" class="form-control"
                        readonly>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="">Fix Amount <span id="goldPrice"
                            style="padding: 10px;">Loading...</span></label>
                </div>
            </div>
            <div class="input-group col-md-12">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input type="checkbox" aria-label="Checkbox for following text input" id="editableCheckbox">
                    </div>
                </div>
                <input type="text" class="form-control fix_amount" name="unfix_rate_oz"
                    aria-label="Text input with checkbox" id="fix_amount" disabled>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="">Reduce Total Amount</label>
                    <input type="text" name="deposit_amount" id="total_amount" class="form-control">
                </div>
            </div>


            <div class="col-md-12">
                <div class="form-group">
                    <label for="note"> Narration</label>
                    <textarea name="note" id="note" class="form-control"></textarea>
                </div>
            </div>
            <div class="col-md-12 text-right">
                <button type="submit" name="submit" class="btn btn-primary">Save</button>
                <button type="submit" name="submit_and_continue" class="btn btn-secondary">Save and Continue</button>
            </div>
        </div>
    </div>

</div>

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
            $("#pure_quantity").val(($('#quantity').val() * $('#pure_rate').val()).toFixed(2));
            $("#total_amount").val(($('#pure_quantity').val() * $('#fix_amount').val()).toFixed(2));
        });

        $('#fix_amount').on('change', function() {
            $("#total_amount").val(($('#quantity').val() * $(this).val()).toFixed(2));
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
            $("#pure_quantity").val(($('#quantity').val() * $('#pure_rate').val()).toFixed(2));
        });

    });

    let previousPrice = null;
    let isFetching = false;
    const usdToAedRate = 3.674;

    const ouncesToGrams = 31.1035;

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
            $(".fix_amount").val(currentPrice);
            const totalPriceAED = (($("#fix_amount").val() / ouncesToGrams) * usdToAedRate) * $("#pure_quantity")
                .val();
            $("#total_amount").val(totalPriceAED.toFixed(2));
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
