@extends('layouts.master')

@section('title', 'Unmatch Trade')
@section('UnmatchTrade', 'active')

@section('content')
    <style>
        .unmatch-hero { background: linear-gradient(120deg, #102a43, #1f5f74); border-radius: .75rem; color: #fff; padding: 1.5rem; }
        .unmatch-hero h3 { color: #fff; font-weight: 700; }
        .unmatch-search { background: #fff; border-radius: .65rem; box-shadow: 0 8px 22px rgba(16, 24, 40, .12); padding: 1.1rem; }
        .trade-summary-card { border: 0; box-shadow: 0 5px 18px rgba(16, 24, 40, .08); }
        .trade-meta { border: 1px solid #e4e7ec; border-radius: .5rem; padding: .8rem; height: 100%; }
        .trade-meta small { color: #667085; display: block; font-size: .72rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
        .trade-meta strong { color: #101828; display: block; font-size: 1rem; margin-top: .3rem; }
        .trade-leg { border: 1px solid #e4e7ec; border-radius: .6rem; padding: 1rem; height: 100%; }
        .trade-leg--open { border-top: 3px solid #17a2b8; }
        .trade-leg--close { border-top: 3px solid #e63946; }
        .trade-leg dt { color: #667085; font-size: .76rem; font-weight: 600; margin-bottom: .15rem; }
        .trade-leg dd { color: #101828; font-size: .92rem; margin-bottom: .75rem; }
    </style>

    <section class="section">
        <ul class="breadcrumb breadcrumb-style">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.buysell') }}"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item active">Unmatch Trade</li>
        </ul>

        <div class="unmatch-hero mb-4">
            <div class="row align-items-center">
                <div class="col-lg-5 mb-3 mb-lg-0">
                    <div class="d-flex justify-content-between align-items-start"><div><h3 class="mb-1">Unmatch Trade</h3><p class="mb-0">Find a completed trade by reference number and safely reverse its match.</p></div><button type="button" class="btn btn-danger btn-sm ml-2" onclick="window.history.back()">Back</button></div>
                </div>
                <div class="col-lg-7">
                    <form action="{{ route('admin.transaction.show.search') }}" method="GET" class="unmatch-search">
                        <label for="ticketNo" class="font-weight-bold text-dark mb-2">Trade reference number</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="ticketNo" name="ticketNo" value="{{ request('ticketNo') }}" placeholder="Enter reference number" required autofocus>
                            <div class="input-group-append"><button type="submit" class="btn btn-primary"><i class="fa fa-search mr-1"></i> Search</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if ($transaction)
            <div class="card trade-summary-card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Matched trade details</h5>
                        <small class="text-muted">Review both sides before unmatching this trade.</small>
                    </div>
                    <span class="badge badge-success px-3 py-2">{{ strtoupper($transaction->transaction_type) }}</span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4 mb-2"><div class="trade-meta"><small>Customer</small><strong>{{ $transaction->customer->name ?? 'N/A' }}</strong></div></div>
                        <div class="col-md-4 mb-2"><div class="trade-meta"><small>Trade reference</small><strong>{{ $transaction->reference_no }}</strong></div></div>
                        <div class="col-md-4 mb-2"><div class="trade-meta"><small>Quantity</small><strong>{{ $transaction->quantity }} TTB</strong></div></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="trade-leg trade-leg--open">
                                <h6 class="font-weight-bold text-info">Opening trade</h6>
                                <dl class="mb-0">
                                    <dt>Trade type</dt><dd>{{ ucfirst($transaction->linked_buy->type ?? 'N/A') }}</dd>
                                    <dt>Opened at</dt><dd>{{ optional($transaction->linked_buy?->created_at)->format('d M Y, H:i') ?? 'N/A' }}</dd>
                                    <dt>Opening rate</dt><dd>{{ isset($transaction->linked_buy?->current_rate) ? number_format($transaction->linked_buy->current_rate, 2) . ' AED' : 'N/A' }}</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="trade-leg trade-leg--close">
                                <h6 class="font-weight-bold text-danger">Closing trade</h6>
                                <dl class="mb-0">
                                    <dt>Trade type</dt><dd>{{ ucfirst($transaction->transaction_type) }}</dd>
                                    <dt>Closed at</dt><dd>{{ optional($transaction->created_at)->format('d M Y, H:i') ?? 'N/A' }}</dd>
                                    <dt>Closing rate</dt><dd>{{ number_format($transaction->current_rate, 2) }} AED</dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center border-top pt-3 mt-1">
                        <div><span class="text-muted mr-2">Realised profit/loss</span><strong class="h5 mb-0">AED {{ number_format($transaction->transaction_amount, 2) }}</strong></div>
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#unmatchTradeModal"><i class="fa fa-unlink mr-1"></i> Unmatch Trade</button>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="unmatchTradeModal" tabindex="-1" role="dialog" aria-labelledby="unmatchTradeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="unmatchTradeModalLabel">Confirm Unmatch Trade</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-2">Are you sure you want to unmatch trade <strong>{{ $transaction->reference_no }}</strong>?</p>
                            <div class="alert alert-warning mb-0">This reverses the linked transaction and recalculates the customer’s open position.</div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                            <form action="{{ route('admin.transaction.delete') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $transaction->id }}">
                                <button type="submit" class="btn btn-danger">Yes, Unmatch Trade</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(request()->filled('ticketNo'))
            <div class="alert alert-warning">No matched trade was found for reference <strong>{{ request('ticketNo') }}</strong>.</div>
        @endif
    </section>
@stop
