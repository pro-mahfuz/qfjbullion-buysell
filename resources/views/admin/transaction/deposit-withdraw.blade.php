@extends('layouts.master')

@php
    $isDeposit = $type === 'deposit';
    $transactionLabel = $isDeposit ? 'Deposit' : 'Withdraw';
    $title = $transactionLabel . ' Add';
    $titleLower = strtolower($transactionLabel);
    $permission = $isDeposit ? 'deposit_add' : 'withdraw_add';
    $summary = $isDeposit ? $deposit : $withdraw;
    $count = $summary['Total ' . $titleLower . ' Count'] ?? 0;
    $amount = $summary['Total ' . $titleLower . ' Amount'] ?? 0;
@endphp

@section('title', $title)
@if ($isDeposit)
    @section('Deposit', 'active')
@else
    @section('Withdraw', 'active')
@endif

@section('content')
    <section class="section">
        <ul class="breadcrumb breadcrumb-style">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.buysell') }}"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item active">{{ $title }}</li>
        </ul>

        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card border-{{ $isDeposit ? 'success' : 'warning' }}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $title }}</h4>
                            <small class="text-muted">Create and review {{ $titleLower }} transactions.</small>
                        </div>
                        <div>
                            <button type="button" class="btn btn-danger btn-sm" onclick="window.history.back()">Back</button>
                            <a href="{{ $isDeposit ? route('admin.buysell.deposit.list') : route('admin.buysell.withdraw.list') }}" class="btn btn-outline-primary btn-sm">{{ $transactionLabel }} List</a>
                            <a href="{{ route('admin.buysell.customer.search') }}" class="btn btn-outline-secondary btn-sm">Trade Summary</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="p-3 rounded bg-light h-100">
                                    <small class="text-muted text-uppercase font-weight-bold">Total {{ $transactionLabel }} count</small>
                                    <div class="h3 mb-0 mt-1">{{ $count }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded bg-light h-100">
                                    <small class="text-muted text-uppercase font-weight-bold">Total {{ $transactionLabel }} amount</small>
                                    <div class="h3 mb-0 mt-1">AED {{ number_format((float) $amount, 3) }}</div>
                                </div>
                            </div>
                        </div>

                        @can($permission)
                            <form action="{{ route('admin.transaction.save') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="customer_id">Customer <span class="text-danger">*</span></label>
                                            <select name="customer_id" id="customer_id" class="form-control select2-container common_select2" required>
                                                <option value="">Select customer</option>
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}" @selected(old('customer_id', $customer_id) == $customer->id)>{{ $customer->name }} ({{ $customer->customer_code }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="transaction_amount">Amount (AED) <span class="text-danger">*</span></label>
                                            <input type="number" inputmode="decimal" step="0.01" min="0.01" name="transaction_amount" id="transaction_amount" class="form-control" value="{{ old('transaction_amount') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="note">Note <span class="text-danger">*</span></label>
                                            <input type="text" name="note" id="note" class="form-control" value="{{ old('note') }}" required>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="starting_rate" value="0">
                                <input type="hidden" name="business_id" value="{{ $businessId }}">
                                <input type="hidden" name="transaction_type" value="{{ $titleLower }}">
                                <input type="hidden" name="reference_table" value="">
                                <input type="hidden" name="reference_row" value="">
                                <input type="hidden" name="tnx_id" value="{{ time() }}">

                                <div class="d-flex justify-content-end border-top pt-3 mt-2">
                                    <button type="submit" class="btn btn-{{ $isDeposit ? 'success' : 'warning' }} px-4">Save {{ $title }}</button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-warning mb-0">You do not have permission to add a {{ $titleLower }} transaction.</div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
