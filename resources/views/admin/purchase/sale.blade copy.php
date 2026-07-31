@extends('layouts.app')

@section('content')
<div class="clearfix"></div>

<form action="{{ route('admin.sale.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="supplier_id">Supplier <span>*</span></label>
                <select name="supplier_id" id="supplier_id" class="form-control" required="">
                    <option value="">Select Supplier</option>
                    @if(count($suppliers) > 0)
                    @foreach($suppliers as $row)
                    <option value="{{ $row->id }}">{{ $row->full_name }} ({{ $row->mobile_number }})</option>
                    @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-12">

                <div class="form-group" id="supplier_details" style="display:none">

                </div>
            </div>
        </div>

    </div>

</form>

<script>
    $(document).ready(function () {

        document.getElementById('editableCheckbox').addEventListener('change', function () {
            var input = document.getElementById('fix_amount');
            if (this.checked) {
                input.disabled = false;
                input.classList.remove('fix_amount');
            } else {
                input.disabled = true;
                input.classList.add('fix_amount');
            }
        });

        $('#quantity').on('input', function () {
            $("#pure_quantity").val(($('#quantity').val() * $('#pure_rate').val()).toFixed(3));
            $("#total_amount").val(($('#pure_quantity').val() * $('#fix_amount').val()).toFixed(3));
        });

        $('#fix_amount').on('change', function () {
            $("#total_amount").val(($('#quantity').val() * $(this).val()).toFixed(3));
        });

        $('#supplier_id').on('change', function () {
            var selectedValue = $(this).val();
            if (selectedValue) {
                $.ajax({
                    url: '{{ route("purchase.listbysupplier") }}',
                    method: 'POST',
                    data: {
                        'supplier_id': selectedValue,
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        $('#supplier_details').html(response).show();
                    },
                    error: function (xhr, status, error) {
                        alert('An error occurred: ' + xhr.responseText);
                        console.error('Error details:', status, error, xhr.responseText);
                    }
                });
            } else {
                $('#supplier_details').hide();
            }
        });
    });

    let previousPrice = null;
    let isFetching = false;
    const usdToAedRate = 3.674; // Example exchange rate, update with the latest rate
    const ouncesToGrams = 31.1035; // 1 troy ounce = 31.1035 grams

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
            const totalPriceAED = ((currentPrice / ouncesToGrams) * usdToAedRate) * $("#pure_quantity").val();
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


@stop
