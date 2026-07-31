@extends('client.layouts.app')

@section('title', 'Statement')

@section('content_header')
    <h1>Statement</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">

            <form method="GET" action="{{ route('client.show.statement') }}" class="mb-4">
                <div class="form-row">
                    <div class="col-md-4">
                        <label for="start_date">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control"
                            value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control"
                            value="{{ request('end_date') }}">
                    </div>
                    <input type="hidden" name="goldValue" value="{{ request('goldValue') }}">
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-block">Filter</button>
                    </div>
                </div>
            </form>

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>Customer Statement</h3>
                        <div class="d-flex">
                            <button class="btn btn-danger btn-sm mr-2" onclick="downloadStatement('statement')">Download
                                Statement</button>

                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <p><strong>Full Name:</strong> {{ $name ?? 'N/A' }}</p>
                        <p><strong>Date:</strong> {{ now() }}</p>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <p><strong>Market Price:</strong> <span
                                id="marketPrice">{{ number_format($market_price, 3) }}</span></p>
                        <p><strong>Cut Position:</strong> <span style="font-size: 20px;">{{ $cut_position ?? 'N/A' }}
                                {{ $value }} @if ($value < 0)
                                    (Sell Position)
                                @else
                                    ( Buy Position)
                                @endif
                            </span>
                            </span>
                        </p>
                    </div>

                    <!-- Balance Information Table -->
                    <h4>Balance Information</h4>
                    <div class="table-responsive">


                        <table class="table table-bordered table-hover table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Balance </th>
                                    <th>Deposit</th>
                                    <th>Withdraw</th>
                                    <th>Profit-Loss</th>
                                    <th>Gold (Onz)</th>
                                    <th>Gold (TTB)</th>

                                    <th>Total($)</th>
                                    <th>Current($)</th>
                                    <th>Equity P&L ($)</th>
                                    <th>Equity P&L (AED)</th>
                                    <th>Net Balance</th>
                                    <th>Withdrawable</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @php
                                        $profiLoss = $deposit - $withdraw + ($profit - $loss);
                                    @endphp
                                    <td>{{ number_format($profiLoss, 3) }}</td>
                                    <td>{{ $deposit }}</td>
                                    <td>{{ $withdraw }}</td>
                                    <td>{{ number_format($profit - $loss, 3) }}</td>
                                    <td>{{ $value * 3.746 }}</td>
                                    <td>{{ $value }}</td>
                                    <td> {{ number_format(($market_price * 13.7639) / 3.674, 3) }} </td>
                                    <td> {{ number_format((($market_price * 13.7639) / 3.674) * $value, 3) }} </td>

                                    <td>{{ is_numeric($equity) ? number_format($equity / 3.6715, 3) : '0' }}</td>
                                    <td>{{ number_format($equity, 3) }}</td>
                                    <td>{{ number_format($profiLoss ?? 0 + $totalProfitLoss, 3) }}</td>
                                    <td>{{ number_format(($profiLoss ?? 0 + $totalProfitLoss) - 36.32 * 13.7639 * $value, 3) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>

                    <!-- Outstanding Positions Table -->


                    <div class="row">
                        <div class="col-md-3">
                            <h4 class="mt-5">Active Positions</h4>
                        </div>
                        <div class="col-md-9">

                            <p class="mt-5"><span>Buy Qty : {{ $sumBuy }}</span> | <span>Sell Qty:
                                    {{ $sumSell }}</span> | <span>Net Qty : {{ $value }} @if ($value < 0)
                                        (Sell Position)
                                    @else
                                        ( Buy Position)
                                    @endif
                                </span> | <span>Total P/L : {{ $totalProfitLoss }}</span></p>

                        </div>
                    </div>
                    <table class="table table-sm table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center">SL</th>
                                <th style="text-align: left">Date</th>
                                <th style="text-align: center">Item </th>
                                <th style="text-align: center">PCS</th>
                                <th style="text-align: center">B/S</th>
                                <th style="text-align: center">QTY Onz</th>
                                <th style="text-align: center">Open Rate</th>
                                <th style="text-align: center">Total Onz</th>
                                <th style="text-align: right">P/L AED</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                                $buyTTB = 0;
                                $sellTTB = 0;
                            @endphp
                            @foreach ($outstanding_positions as $positionArray)
                                @php
                                    if ($positionArray['type'] == 'sell') {
                                        $sellTTB += $positionArray['tt_quantity'];
                                    } else {
                                        $buyTTB += $positionArray['tt_quantity'];
                                    }
                                @endphp
                                <tr>
                                    <td style="text-align: center">{{ $i++ }}</td>
                                    <td style="text-align: left">
                                        {{ date('d-M-Y', strtotime($positionArray['created_at'])) }}
                                    </td>
                                    <td style="text-align: center">TTB</td>
                                    <td style="text-align: center">{{ $positionArray['tt_quantity'] }}</td>
                                    <td style="text-align: center">{{ $positionArray['type'] }}</td>

                                    <td style="text-align: center">
                                        {{ (($positionArray['tt_quantity'] ?? 0) * 3.746) / 10 }}
                                    </td>
                                    <td style="text-align: center">{{ $positionArray['current_rate'] }}</td>

                                    <td style="text-align: center">{{ ($positionArray['tt_quantity'] ?? 0) * 3.746 }}</td>
                                    <td style="text-align: right">{{ number_format($positionArray['profit_loss'], 3) }}
                                    </td>
                                </tr>
                            @endforeach

                            <tr class="table-active">
                                <td colspan="7"><strong>Sub Total Profit/Loss</strong></td>
                                <td colspan="1" style="text-align: center">
                                    <strong>{{ number_format(($buyTTB - $sellTTB) * 3.746, 3) }}</strong>
                                </td>
                                <td colspan="1" style="text-align: right">
                                    <strong>{{ $totalProfitLoss }}</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Profit and Loss Summary Table -->
                    <h4 class="mt-4">Profit and Loss Summary</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">

                            <thead class="thead-dark">
                                <tr>
                                    <th>SL</th>
                                    <th>Item Code</th>
                                    <th>Date</th>
                                    <th>Gold Qty</th>
                                    <th>B/S</th>
                                    <th>Rate</th>
                                    <th>Date</th>
                                    <th>B/S</th>
                                    <th>Rate</th>
                                    <th>P/L AED</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($net_matched)
                                    @php
                                        $i = 1;
                                        $total_profit_or_loss = 0;
                                    @endphp
                                    @foreach ($net_matched as $detailArray)
                                        @php
                                            $total_profit_or_loss += (float) $detailArray['transaction_amount'];
                                        @endphp
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>TTB</td>
                                            <td>{{ date('d-M-Y', strtotime($detailArray['created_at'])) }}</td>
                                            <td>{{ $detailArray['quantity'] }}</td>
                                            <td>{{ $detailArray['transaction_type'] == 'sell' ? 'Sell' : 'Buy' }}</td>
                                            <td>{{ number_format($detailArray['starting_rate'], 3) }}</td>
                                            <td>{{ date('d-M-Y', strtotime($detailArray['transaction_date'])) }}</td>
                                            <td>{{ $detailArray['transaction_type'] == 'buy' ? 'Sell' : 'Buy' }}</td>
                                            <td>{{ number_format($detailArray['current_rate'], 3) }}</td>
                                            <td>{{ number_format($detailArray['transaction_amount'], 3) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-active">
                                        <td colspan="9"><strong>Total Profit/Loss</strong></td>
                                        <td><strong>AED {{ number_format($total_profit_or_loss, 3) }}</strong></td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="10" class="text-center">No transactions found.</td>
                                    </tr>
                                @endif

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
    <script>
        let previousPrice = null;
        let isFetching = false;
        let bid = 0;
        async function getGoldPrice(loader = true) {
            if (loader) {
                $('#loader').show();
                $('#transactionContainer').hide();
            }
            console.log('Fetching gold price...');

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
                previousPrice = data.ask;
                bid = data.bid;

                console.log('Gold price fetched:', previousPrice);
            } catch (error) {
                console.error('Error fetching the gold price:', error);
                // fire('Error fetching the gold price');
            } finally {
                isFetching = false;
            }

            // $('#marketPrice').text(previousPrice.toFixed(3));

            if (loader) {
                $('#loader').hide();
                $('#transactionContainer').show();
            }
            // Fetch the gold price again after 5 seconds
            setTimeout(() => getGoldPrice(false), 2500);
        }

        getGoldPrice();

        function downloadStatement(type) {
            var id = "{{ $id }}";
            var url = "{{ route('admin.transaction.send.invoice') }}";
            var previousPrice = "{{ $market_price }}";
            var startDate = "{{ request('start_date') }}";
            var endDate = "{{ request('end_date') }}";

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    id: id,
                    type: type,
                    previousPrice: previousPrice,
                    startDate: startDate,
                    endDate: endDate,
                    _token: "{{ csrf_token() }}"
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(response) {
                    var blob = new Blob([response]);
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    var currentDate = new Date().toISOString().slice(0, 10);
                    link.download = `statement-${currentDate}.pdf`;
                    link.click();
                },
                error: function(xhr, status, error) {
                    console.error("Error occurred: ", error);
                    alert("An error occurred while generating the statement.");
                }
            });
        }

        function fire(text) {
            Swal.fire({
                icon: 'error',
                text: text,
                position: 'top-end',
                toast: true,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        }
    </script>
@endpush
