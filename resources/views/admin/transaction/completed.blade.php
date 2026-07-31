@extends('layouts.master')

@section('title', 'Closed Trade List')
@section('ClosedTrade', 'active')

@section('content')
    <style>
        .closed-hero { background: linear-gradient(120deg, #102a43, #1f5f74); border-radius: .75rem; color: #fff; padding: 1.35rem 1.5rem; }
        .closed-hero h3 { color: #fff; font-weight: 700; margin: 0; }
        .closed-stat { background: #fff; border: 1px solid #e4e7ec; border-radius: .55rem; height: 100%; padding: .9rem 1rem; }
        .closed-stat small { color: #667085; display: block; font-size: .72rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
        .closed-stat strong { color: #102a43; display: block; font-size: 1.18rem; margin-top: .3rem; }
        .closed-table { font-size: .84rem; }
        .closed-table th { background: #f1f5f9; border-color: #dbe4ee; color: #334155; font-size: .7rem; letter-spacing: .04em; text-transform: uppercase; white-space: nowrap; }
        .closed-table td { border-color: #e8eef5; vertical-align: middle; }
        .closed-table .number { font-variant-numeric: tabular-nums; text-align: right; white-space: nowrap; }
    </style>

    <section class="section">
        <ul class="breadcrumb breadcrumb-style"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard.buysell') }}"><i class="fas fa-home"></i></a></li><li class="breadcrumb-item active">Closed Trade List</li></ul>
        <div class="closed-hero mb-4 d-flex justify-content-between align-items-center flex-wrap"><div><h3>Closed Trade List</h3><p class="mb-0 mt-1">Review completed trades, their opening position, closing details, and realised profit or loss.</p></div><button type="button" class="btn btn-danger mt-3 mt-md-0" onclick="window.history.back()">Back</button></div>

        <div class="row mb-4">
            <div class="col-md-3 mb-2"><div class="closed-stat"><small>Closed trades</small><strong>{{ $summary->total_trades ?? 0 }}</strong></div></div>
            <div class="col-md-3 mb-2"><div class="closed-stat"><small>Buy closes</small><strong>{{ $summary->buy_count ?? 0 }}</strong></div></div>
            <div class="col-md-3 mb-2"><div class="closed-stat"><small>Sell closes</small><strong>{{ $summary->sell_count ?? 0 }}</strong></div></div>
            <div class="col-md-3 mb-2"><div class="closed-stat"><small>Realised P/L</small><strong>AED {{ number_format($summary->total_profit_loss ?? 0, 3) }}</strong></div></div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">Completed trade ledger</h5></div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.transaction.show.completed') }}" class="mb-4">
                    <div class="form-row align-items-end">
                        <div class="col-md-4 mb-2"><label for="customer_id">Customer</label><select name="customer_id" id="customer_id" class="form-control select2-container common_select2"><option value="">All customers</option>@foreach ($customers as $customer)<option value="{{ $customer->id }}" @selected((string) request('customer_id') === (string) $customer->id)>{{ $customer->name }} ({{ $customer->customer_code }})</option>@endforeach</select></div>
                        <div class="col-md-2 mb-2"><label for="trade_type">Closing type</label><select name="trade_type" id="trade_type" class="form-control"><option value="">Buy & Sell</option><option value="buy" @selected(request('trade_type') === 'buy')>Buy</option><option value="sell" @selected(request('trade_type') === 'sell')>Sell</option></select></div>
                        <div class="col-md-2 mb-2"><label for="start_date">From date</label><input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}"></div>
                        <div class="col-md-2 mb-2"><label for="end_date">To date</label><input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}"></div>
                        <div class="col-md-2 mb-2 d-flex"><button type="submit" class="btn btn-primary mr-2">Filter</button><a href="{{ route('admin.transaction.show.completed') }}" class="btn btn-outline-secondary">Clear</a></div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover closed-table mb-0">
                        <thead><tr><th>#</th><th>Reference</th><th>Customer</th><th>Opened</th><th>Quantity</th><th>Open type/rate</th><th>Closed</th><th>Close type/rate</th><th class="number">P/L (AED)</th></tr></thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td>{{ $transactions->firstItem() + $loop->index }}</td>
                                    <td>{{ $transaction->reference_no ?? '—' }}</td>
                                    <td>{{ $transaction->customer->name ?? 'N/A' }}</td>
                                    <td>{{ optional($transaction->linked_buy?->created_at)->format('d M Y') ?? 'N/A' }}</td>
                                    <td>{{ number_format($transaction->quantity, 3) }}</td>
                                    <td><span class="badge badge-{{ ($transaction->linked_buy?->type ?? '') === 'buy' ? 'info' : 'danger' }}">{{ strtoupper($transaction->linked_buy?->type ?? 'N/A') }}</span> {{ isset($transaction->linked_buy?->current_rate) ? number_format($transaction->linked_buy->current_rate, 3) : '—' }}</td>
                                    <td>{{ optional($transaction->created_at)->format('d M Y') }}</td>
                                    <td><span class="badge badge-{{ $transaction->transaction_type === 'buy' ? 'info' : 'danger' }}">{{ strtoupper($transaction->transaction_type) }}</span> {{ number_format($transaction->current_rate, 3) }}</td>
                                    <td class="number font-weight-bold">{{ number_format($transaction->transaction_amount, 3) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center text-muted">No closed trades match these filters.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($transactions->hasPages())<div class="d-flex justify-content-end mt-3">{{ $transactions->links('pagination::bootstrap-4') }}</div>@endif
            </div>
        </div>
    </section>
@stop
