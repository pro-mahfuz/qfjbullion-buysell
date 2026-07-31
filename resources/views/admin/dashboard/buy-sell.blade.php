@extends('layouts.master')

@section('title', 'Buy/Sell Dashboard')
@section('dashboard', 'active')

@section('content')
    <style>
        .dashboard-hero { background: linear-gradient(120deg, #102a43, #1f5f74); border-radius: .75rem; color: #fff; padding: 1.5rem; }
        .dashboard-hero h3 { color: #fff; font-weight: 700; margin: 0; }
        .dashboard-search { background: #fff; border-radius: .55rem; box-shadow: 0 8px 22px rgba(16, 24, 40, .14); padding: .9rem; }
        .dashboard-search label { color: #344054; font-size: .76rem; font-weight: 700; margin-bottom: .3rem; text-transform: uppercase; }
        .dashboard-stat { background: #fff; border: 1px solid #e4e7ec; border-radius: .6rem; height: 100%; padding: 1rem; }
        .dashboard-stat small { color: #667085; display: block; font-size: .72rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
        .dashboard-stat strong { color: #102a43; display: block; font-size: 1.3rem; margin-top: .35rem; }
        .dashboard-stat .sub { color: #667085; font-size: .8rem; margin-top: .25rem; }
        .dashboard-panel { border: 1px solid #e4e7ec; border-radius: .6rem; height: 100%; }
        .dashboard-panel__header { border-bottom: 1px solid #e4e7ec; color: #102a43; font-weight: 700; padding: .9rem 1rem; }
        .dashboard-panel__body { padding: 1rem; }
        .quick-link { align-items: center; border: 1px solid #e4e7ec; border-radius: .45rem; color: #344054; display: flex; font-weight: 600; justify-content: space-between; margin-bottom: .65rem; padding: .7rem .8rem; }
        .quick-link:hover { background: #f8fafc; color: #102a43; text-decoration: none; }
    </style>

    <section class="section">
        <ul class="breadcrumb breadcrumb-style"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard.buysell') }}"><i class="fas fa-home"></i></a></li><li class="breadcrumb-item active">Dashboard</li></ul>
        <div class="dashboard-hero mb-4"><div class="row align-items-center"><div class="col-lg-6 mb-3 mb-lg-0"><h3>Buy / Sell Dashboard</h3><p class="mb-0 mt-1">A live overview of customers, open metal exposure, and cash movement.</p></div><div class="col-lg-6"><form action="{{ route('admin.buysell.customer.search') }}" method="GET" class="dashboard-search"><label for="customer">Customer search</label><div class="input-group"><input id="customer" type="text" name="customer" class="form-control" placeholder="Code, name, phone, or email" required><div class="input-group-append"><button type="submit" class="btn btn-primary"><i class="fa fa-search mr-1"></i> Search</button></div></div></form></div></div></div>

        <div class="row mb-4">
            @can('dashboard_customer_section')<div class="col-md-3 mb-3"><div class="dashboard-stat"><small>Total customers</small><strong>{{ $totalCustomers }}</strong><div class="sub">{{ $activeCusomer }} active · {{ $deactiveCusomer }} inactive</div></div></div>@endcan
            @can('dashboard_buy_sell_section')<div class="col-md-3 mb-3"><div class="dashboard-stat"><small>Net active TTB</small><strong>{{ number_format(abs($totalRunningActiveTTB), 3) }} {{ $totalRunningActiveTTB > 0 ? 'Buy' : ($totalRunningActiveTTB < 0 ? 'Sell' : '') }}</strong><div class="sub">Buy {{ number_format($totalRunningBuyTTB, 3) }} · Sell {{ number_format($totalRunningSellTTB, 3) }}</div></div></div>@endcan
            @can('dashboard_transection_section')<div class="col-md-3 mb-3"><div class="dashboard-stat"><small>Total cash movement</small><strong>AED {{ number_format($totalTransactionAmount, 3) }}</strong><div class="sub">Deposits + withdrawals</div></div></div><div class="col-md-3 mb-3"><div class="dashboard-stat"><small>Deposits / withdrawals</small><strong>AED {{ number_format($totalDepositAmount, 3) }}</strong><div class="sub">Withdrawals AED {{ number_format($totalWithDrawAmount, 3) }}</div></div></div>@endcan
        </div>

        <div class="row">
            <div class="col-md-6 mb-3"><div class="dashboard-panel"><div class="dashboard-panel__header">Trading shortcuts</div><div class="dashboard-panel__body"><a class="quick-link" href="{{ route('admin.buysell.customer.search') }}"><span>Buy / Sell workspace</span><i class="fa fa-arrow-right"></i></a><a class="quick-link" href="{{ route('admin.transaction.show.runningOpening', ['type' => '1']) }}"><span>Running Trade</span><i class="fa fa-arrow-right"></i></a><a class="quick-link" href="{{ route('admin.transaction.show.completed', ['type' => '0']) }}"><span>Closed Trade List</span><i class="fa fa-arrow-right"></i></a></div></div></div>
            <div class="col-md-6 mb-3"><div class="dashboard-panel"><div class="dashboard-panel__header">Cash-management shortcuts</div><div class="dashboard-panel__body"><a class="quick-link" href="{{ route('admin.buysell.deposit.list') }}"><span>Deposit List</span><i class="fa fa-arrow-right"></i></a><a class="quick-link" href="{{ route('admin.buysell.withdraw.list') }}"><span>Withdraw List</span><i class="fa fa-arrow-right"></i></a><a class="quick-link" href="{{ route('admin.customer.list') }}"><span>Customer List</span><i class="fa fa-arrow-right"></i></a></div></div></div>
        </div>
    </section>
@stop
