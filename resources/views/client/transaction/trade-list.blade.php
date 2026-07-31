<div class="table-responsive mt-3">
    <table class="table table-striped table-bordered">

        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Date</th>
                <th scope="col">Type</th>
                <th scope="col">TT Quantity</th>
                <th scope="col">Starting Rate</th>
                <th scope="col">Total (AED)</th>
                <th scope="col">Current Rate (AED)</th>
                <th scope="col">Profit/Loss</th>
                <th scope="col">Action</th>
            </tr>
        </thead>

        <tbody id="table1" class="collapse show">
            @if (isset($runningBuySell))
                <?php $sl = 1; ?>
                @foreach ($runningBuySell as $transaction)
                    <tr>
                        <th scope="row">{{ $sl++ }}</th>
                        <td>{{ $transaction['created_at'] }}</td>
                        <td>{{ $transaction['type'] }}</td>
                        <td>{{ $transaction['tt_quantity'] - $transaction['close_quanntity'] }}</td>
                        <td>{{ number_format($transaction['current_rate'], 3) }}</td>
                        <td id="oldbalance-{{ $sl }}">
                            {{ number_format($transaction['current_rate'] * 3.74632 * 3.674 * ($transaction['tt_quantity'] - $transaction['close_quanntity']), 3) }}
                        </td>
                        <td><span data-id="{{ $sl }}" data-type="{{ $transaction['type'] }}"
                                data-qty="{{ $transaction['tt_quantity'] - $transaction['close_quanntity'] }}"
                                data-startrate="{{ number_format($transaction['current_rate'] * 3.745 * 3.67 * ($transaction['tt_quantity'] - $transaction['close_quanntity']), 3) }}"
                                class="ratelist">{{ number_format($transaction['current_rate'], 3) }}<span></td>
                        <td id="balance-{{ $sl }}">{{ number_format($transaction['current_rate'], 3) }}</td>
                        <td> <a href="#"
                                class="btn {{ $transaction['type'] == 'buy' ? ' btn-info' : 'btn-danger' }} btn-sm load_modal"
                                data-toggle="modal"
                                data-action="{{ route('admin.buysell.showtrade', ['id' => $transaction['id']]) }}">
                                {{ $transaction['type'] == 'buy' ? 'Buy Close' : 'Sell Close' }}
                            </a> </td>
                    </tr>
                @endforeach
            @endif
        </tbody>

    </table>
</div>


<script>
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
            const buyPrice = data.ask;
            const priceDiv = document.getElementById('buyrate');
            priceDiv.textContent = `$${buyPrice}`;

            const sellPrice = data.bid;
            const sellDiv = document.getElementById('sellrate');
            sellDiv.textContent = `$${sellPrice}`;

            // $(".fix_amount").val(currentPrice);
            // const totalPriceAED = (($("#fix_amount").val() / ouncesToGrams) * usdToAedRate) * $("#pure_quantity").val();
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

            previousPrice = buyPrice;

            $('.ratelist').each(function(index) {
                let qty = $(this).attr("data-qty");
                let dataId = $(this).attr("data-id");
                let dataType = $(this).attr("data-type");

                let runningValue = qty * buyPrice * 3.74632 * 3.674;

                $(this).html(runningValue.toFixed(3));

                let oldBalance = parseFloat($("#oldbalance-" + dataId).text().replace(/,/g, ''));

                let newBalance = runningValue - oldBalance;
                if (dataType == 'sell') {
                    newBalance = oldBalance - runningValue;

                }

                $("#balance-" + dataId).html(newBalance.toFixed(3));
            });
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
