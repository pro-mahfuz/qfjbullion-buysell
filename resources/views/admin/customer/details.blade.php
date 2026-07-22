@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="invoice p-3 mb-3">
                        <div id="print">
                            <div style="font-family: 'Cambria', serif !important;">
                                <div class="row align-items-center">
                                    <!-- Customer Image/Placeholder -->
                                    <div class="col-sm-3 text-center mb-3">
                                        <i class="fa fa-user fa-5x"></i>
                                    </div>

                                    <!-- Customer Name Section -->
                                    <div class="col-sm-6 text-center mb-3">
                                        <div class="bg-light p-3 rounded-pill">
                                            <p class="mb-0" style="font-size: 30px;">
                                                <strong>{{ $customer->name }}</strong>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Business Information -->
                                    <div class="col-sm-3 text-right mb-3">
                                        <?php $business = getCurrentSchoolDetails(); ?>
                                        <p style="font-size: 18px; line-height: 1.5;">
                                            {{ $business->full_address }}<br>
                                            {{ $business->phone }}<br>
                                            {{ $business->email }}<br>
                                            {{ $business->domain }}
                                        </p>
                                    </div>

                                    <hr class="my-2" style="background-color: #4f7893; height: 2px;">

                                    <!-- Action Buttons -->
                                    <div class="col-sm-12 text-center mb-3">
                                        <a href="#" class="btn btn-primary load_modal" data-toggle="modal"
                                            data-action="{{ route('buysell.deposit', ['id' => $customer->id, 'type' => 'deposit']) }}"
                                            style="min-width: 170px;">Deposit</a>
                                        <a href="#" class="btn btn-success load_modal" data-toggle="modal"
                                            data-action="{{ route('buysell.deposit', ['id' => $customer->id, 'type' => 'withdraw']) }}"
                                            style="min-width: 170px;">Withdraw</a>
                                        <a href="#" class="btn btn-info load_modal" data-toggle="modal"
                                            data-action="{{ route('buysell.deposit', ['id' => $customer->id, 'type' => 'buy']) }}"
                                            style="min-width: 170px;">Buy</a>
                                        <a href="#" class="btn btn-danger load_modal" data-toggle="modal"
                                            data-action="{{ route('buysell.deposit', ['id' => $customer->id, 'type' => 'sell']) }}"
                                            style="min-width: 170px;">Sell</a>
                                        <a href="#" class="btn btn-dark load_modal" data-toggle="modal"
                                            data-action="{{ route('customer.details', $customer->id) }}"
                                            style="min-width: 170px;">Statement</a>
                                        <a href="#" class="btn btn-warning load_modal" data-toggle="modal"
                                            data-action="{{ route('customer.update', $customer->id) }}"
                                            style="min-width: 170px;">Edit</a>
                                    </div>

                                    <hr class="my-2" style="background-color: #4f7893; height: 2px;">
                                </div>

                                <!-- Transaction Table -->
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-striped table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th class="text-center" style="font-size: 12px;">Customer Name</th>
                                                    <th class="text-center" style="font-size: 12px;">Initial Amount</th>
                                                    <th class="text-center" style="font-size: 12px;">Total Deposit</th>
                                                    <th class="text-center" style="font-size: 12px;">Total Fix</th>
                                                    <th class="text-center" style="font-size: 12px;">Current Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center" style="font-size: 19px;">
                                                        {{ $customer->name }}
                                                    </td>
                                                    <td class="text-center">{{ $customer->initial_amount }}</td>
                                                    <td class="text-center">{{ $customer->total_deposit }}</td>
                                                    <td class="text-center">{{ $customer->total_fix }}</td>
                                                    <td class="text-center">{{ $customer->current_balance }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <br><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
