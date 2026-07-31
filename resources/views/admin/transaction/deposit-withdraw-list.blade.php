@extends('layouts.master')

@php
    $isDeposit = $type === 'deposit';
    $title = $isDeposit ? 'Deposit List' : 'Withdraw List';
    $addRoute = $isDeposit
        ? route('admin.buysell.deposit_withdraw', ['customer_id' => 'null', 'type' => 'deposit'])
        : route('admin.buysell.deposit_withdraw', ['customer_id' => 'null', 'type' => 'withdraw']);
    $clearRoute = $isDeposit ? route('admin.buysell.deposit.list') : route('admin.buysell.withdraw.list');
@endphp

@section('title', $title)
@if ($isDeposit)
    @section('DepositList', 'active')
@else
    @section('WithdrawList', 'active')
@endif

@section('content')
    <style>
        .transaction-list-table { font-size: .88rem; }
        .transaction-list-table th { background: #f1f5f9; border-color: #dbe4ee; color: #334155; font-size: .73rem; letter-spacing: .04em; text-transform: uppercase; white-space: nowrap; }
        .transaction-list-table td { border-color: #e8eef5; vertical-align: middle; }
        .transaction-list-table .amount { color: #0f5132; font-size: .95rem; font-weight: 700; white-space: nowrap; }
        .transaction-list-table .reference { color: #475569; font-size: .8rem; }
        .transaction-list-table .note { color: #64748b; display: block; max-width: 360px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .transaction-stat { border: 1px solid #e2e8f0; border-radius: .5rem; padding: .8rem 1rem; background: #f8fafc; }
        .transaction-stat small { color: #64748b; display: block; font-size: .72rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
        .transaction-stat strong { color: #0f172a; display: block; font-size: 1.12rem; margin-top: .2rem; }
    </style>
    <section class="section">
        <ul class="breadcrumb breadcrumb-style">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.buysell') }}"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item active">{{ $title }}</li>
        </ul>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ $title }}</h4>
                <div><button type="button" class="btn btn-danger btn-sm mr-2" onclick="window.history.back()">Back</button><a href="{{ $addRoute }}" class="btn btn-{{ $isDeposit ? 'success' : 'warning' }} btn-sm">{{ $isDeposit ? 'Deposit Add' : 'Withdraw Add' }}</a></div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3 mb-2 mb-md-0"><div class="transaction-stat"><small>Filtered transactions</small><strong>{{ $transactions->total() }}</strong></div></div>
                    <div class="col-md-4"><div class="transaction-stat"><small>Filtered {{ $isDeposit ? 'deposits' : 'withdrawals' }}</small><strong>AED {{ number_format($totalAmount, 3) }}</strong></div></div>
                </div>
                <form method="GET" action="{{ $clearRoute }}" class="mb-4">
                    <div class="form-row align-items-end">
                        <div class="col-md-4 mb-2">
                            <label for="customer_id">Customer</label>
                            <select name="customer_id" id="customer_id" class="form-control select2-container common_select2">
                                <option value="">All customers</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" @selected((string) request('customer_id') === (string) $customer->id)>{{ $customer->name }} ({{ $customer->customer_code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="start_date">From date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="end_date">To date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-2 mb-2 d-flex">
                            <button type="submit" class="btn btn-primary mr-2">Filter</button>
                            <a href="{{ $clearRoute }}" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover transaction-list-table mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Reference &amp; Note</th>
                            <th class="text-right">Amount (AED)</th>
                            <th>Created by</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td>{{ $transactions->firstItem() + $loop->index }}</td>
                                    <td>{{ optional($transaction->created_at)->format('d M Y') }}</td>
                                    <td>{{ $transaction->customer->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="reference">{{ $transaction->reference_no ?? 'No reference' }}</span>
                                        <span class="note" title="{{ $transaction->note }}">{{ $transaction->note ?? '—' }}</span>
                                    </td>
                                    <td class="text-right amount">{{ number_format($transaction->transaction_amount, 3) }}</td>
                                    <td>{{ $transaction->created_by ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted">No {{ strtolower($title) }} found for these filters.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($transactions->hasPages())
                    <div class="mt-3 d-flex justify-content-end">{{ $transactions->links('pagination::bootstrap-4') }}</div>
                @endif
            </div>
        </div>
    </section>
@stop
