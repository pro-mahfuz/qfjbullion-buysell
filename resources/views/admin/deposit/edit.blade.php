@extends('layouts.app')
@section('content_header')
    <h1>Deposit Edit</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.supplier.deposit.update', $deposit->id) }}" method="POST">
                        @csrf


                        <input type="hidden" name="supplier_id" value="{{$deposit->supplier_id}}" />
                        <div class="row">
                            {{-- <div class="col-md-6">
                                <!-- Supplier Info Table -->
                                <table class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Mobile</th>
                                            <th>Address</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $supplier->full_name }}</td>
                                            <td>{{ $supplier->mobile }}</td>
                                            <td>{{ $supplier->address }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <!-- Transaction History -->
                                <h3>Transaction History</h3>

                                @if (count($deposit_list) > 0)
                                    <table class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th style="text-align: right;">Date</th>
                                                <th style="text-align: right;">Deposit</th>
                                                <th style="text-align: right;">Withdraw</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($deposit_list as $row)
                                                <tr>
                                                    <td>{{ date('d/M/Y', strtotime($row->created_at)) }}</td>
                                                    <td style="text-align: right;">
                                                        {{ $row->deposit_amount > 0 ? 'AED ' . number_format($row->deposit_amount, 2) : '--' }}
                                                    </td>
                                                    <td style="text-align: right;">
                                                        {{ $row->withdraw_amount > 0 ? 'AED ' . number_format($row->withdraw_amount, 2) : '--' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <h3 style="color: #900000;text-align:center;">No Transaction Found.</h3>
                                @endif
                            </div> --}}

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="created_at">DateTime</label>
                                    <input type="datetime-local" name="created_at" class="form-control" required=""
                                        value="{{ old('created_at', date('Y-m-d\TH:i', strtotime($deposit->created_at))) }}">
                                </div>

                                <div class="form-group">
                                    <label for="type">Type</label>
                                    <select name="type" class="form-control" required="">
                                        <option value="deposit" {{ $deposit->type == 'deposit' ? 'selected' : '' }}>Deposit</option>
                                        <option value="withdraw" {{ $deposit->type == 'withdraw' ? 'selected' : '' }}>Withdraw</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="ref_no">Reference No</label>
                                    <input type="text" name="ref_no" class="form-control" value="{{ old('ref_no', $deposit->ref_no) }}">
                                </div>

                                <div class="form-group">
                                    <label for="deposit_amount">Amount (AED)</label>
                                    <input type="number" step="0.01" name="deposit_amount" class="form-control" required=""
                                        value="{{ old('deposit_amount', $deposit->deposit_amount) }}">
                                </div>

                                <div class="form-group">
                                    <label for="note">Note</label>
                                    <textarea name="note" class="form-control">{{ old('note', $deposit->note) }}</textarea>
                                </div>

                                <div class="text-right">
                                    <input type="hidden" name="payment_account_id" value="{{ old('payment_account_id', $deposit->payment_account_id) }}" />
                                    <input type="hidden" name="staff_note" value="{{ old('staff_note', $deposit->staff_note) }}" />

                                    <button type="submit" class="btn btn-success">Update</button>
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
