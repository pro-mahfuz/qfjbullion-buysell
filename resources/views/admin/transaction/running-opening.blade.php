@extends('layouts.master')

@section('title', 'Running Trade')
@section('RunningTrade', 'active')

@section('content')
    <style>
        .running-hero { background: linear-gradient(120deg, #102a43, #1f5f74); border-radius: .75rem; color: #fff; padding: 1.35rem 1.5rem; }
        .running-hero h3 { color: #fff; font-weight: 700; margin: 0; }
        .running-stat { background: #fff; border: 1px solid #e4e7ec; border-radius: .55rem; height: 100%; padding: 1rem; }
        .running-stat small { color: #667085; display: block; font-size: .72rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
        .running-stat strong { color: #102a43; display: block; font-size: 1.25rem; margin-top: .3rem; }
        .running-table { font-size: .87rem; }
        .running-table th { background: #f1f5f9; border-color: #dbe4ee; color: #334155; font-size: .72rem; letter-spacing: .04em; text-transform: uppercase; white-space: nowrap; }
        .running-table td { border-color: #e8eef5; vertical-align: middle; }
        .running-table .number { font-variant-numeric: tabular-nums; text-align: right; white-space: nowrap; }
    </style>

    <section class="section">
        <ul class="breadcrumb breadcrumb-style">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.buysell') }}"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item active">Running Trade</li>
        </ul>

        <div class="running-hero mb-4 d-flex justify-content-between align-items-center flex-wrap">
            <div><h3>Running Trade</h3><p class="mb-0 mt-1">Monitor active positions and narrow the view by customer, trade direction, or date.</p></div>
            <button type="button" class="btn btn-danger mt-3 mt-md-0" onclick="window.history.back()">Back</button>
        </div>

        <div class="row mb-4">
            <div class="col-md-3 mb-2"><div class="running-stat"><small>Open trades</small><strong>{{ $summary->total_orders ?? 0 }}</strong></div></div>
            <div class="col-md-3 mb-2"><div class="running-stat"><small>Open buy TTB</small><strong>{{ number_format($summary->buy_ttb ?? 0, 2) }}</strong></div></div>
            <div class="col-md-3 mb-2"><div class="running-stat"><small>Open sell TTB</small><strong>{{ number_format($summary->sell_ttb ?? 0, 2) }}</strong></div></div>
            <div class="col-md-3 mb-2"><div class="running-stat"><small>Net open TTB</small><strong>{{ number_format(($summary->buy_ttb ?? 0) - ($summary->sell_ttb ?? 0), 2) }}</strong></div></div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">Active position list</h5></div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.transaction.show.runningOpening') }}" class="mb-4">
                    <input type="hidden" name="type" value="{{ $type }}">
                    <div class="form-row align-items-end">
                        <div class="col-md-4 mb-2">
                            <label for="customer_id">Customer</label>
                            <select name="customer_id" id="customer_id" class="form-control select2-container common_select2"><option value="">All customers</option>@foreach ($customers as $customer)<option value="{{ $customer->id }}" @selected((string) request('customer_id') === (string) $customer->id)>{{ $customer->name }} ({{ $customer->customer_code }})</option>@endforeach</select>
                        </div>
                        <div class="col-md-2 mb-2"><label for="trade_type">Direction</label><select name="trade_type" id="trade_type" class="form-control"><option value="">Buy & Sell</option><option value="buy" @selected(request('trade_type') === 'buy')>Buy</option><option value="sell" @selected(request('trade_type') === 'sell')>Sell</option></select></div>
                        <div class="col-md-2 mb-2"><label for="start_date">From date</label><input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}"></div>
                        <div class="col-md-2 mb-2"><label for="end_date">To date</label><input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}"></div>
                        <div class="col-md-2 mb-2 d-flex"><button type="submit" class="btn btn-primary mr-2">Filter</button><a href="{{ route('admin.transaction.show.runningOpening', ['type' => $type]) }}" class="btn btn-outline-secondary">Clear</a></div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover running-table mb-0">
                        <thead><tr><th>#</th><th>Opened</th><th>Customer</th><th>Reference</th><th>Direction</th><th class="number">Open TTB</th><th class="number">Open rate</th><th class="number">Open value (AED)</th></tr></thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                @php $openQuantity = $transaction->tt_quantity - $transaction->close_quanntity; @endphp
                                <tr>
                                    <td>{{ $transactions->firstItem() + $loop->index }}</td>
                                    <td>{{ optional($transaction->created_at)->format('d M Y') }}</td>
                                    <td>{{ $transaction->customer->name ?? 'N/A' }}</td>
                                    <td>{{ $transaction->reference_no }}</td>
                                    <td><span class="badge badge-{{ $transaction->type === 'buy' ? 'info' : 'danger' }}">{{ strtoupper($transaction->type) }}</span></td>
                                    <td class="number">{{ number_format($openQuantity, 2) }}</td>
                                    <td class="number">{{ number_format($transaction->current_rate, 2) }}</td>
                                    <td class="number">{{ number_format($transaction->current_rate * $openQuantity * 13.7628, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center text-muted">No running trades match these filters.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($transactions->hasPages())<div class="d-flex justify-content-end mt-3">{{ $transactions->links('pagination::bootstrap-4') }}</div>@endif
            </div>
        </div>
    </section>
@stop
