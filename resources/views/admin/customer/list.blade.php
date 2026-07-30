@extends('layouts.master')

@section('title', 'Customer List')
@section('customer_list', 'active')

@section('content')
    <style>
        .customer-hero { background: linear-gradient(120deg, #102a43, #1f5f74); border-radius: .75rem; color: #fff; padding: 1.35rem 1.5rem; }
        .customer-hero h3 { color: #fff; font-weight: 700; margin: 0; }
        .customer-stat { background: #fff; border: 1px solid #e4e7ec; border-radius: .55rem; height: 100%; padding: .9rem 1rem; }
        .customer-stat small { color: #667085; display: block; font-size: .72rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
        .customer-stat strong { color: #102a43; display: block; font-size: 1.2rem; margin-top: .3rem; }
        .customer-table { font-size: .85rem; }
        .customer-table th { background: #f1f5f9; border-color: #dbe4ee; color: #334155; font-size: .71rem; letter-spacing: .04em; text-transform: uppercase; white-space: nowrap; }
        .customer-table td { border-color: #e8eef5; vertical-align: middle; }
        .customer-table .metric { display: block; color: #667085; font-size: .74rem; }
        .customer-table .metric strong { color: #1e293b; font-size: .86rem; }
    </style>

    @php
        $customerCount = count($customers);
        $activeCustomers = collect($customers)->where('status', '!=', 'deactived')->count();
        $totalBalance = collect($customers)->sum(fn ($customer) => (float) str_replace(',', '', $customer->current_balance ?? 0));
    @endphp

    <section class="section">
        <ul class="breadcrumb breadcrumb-style">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.buysell') }}"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item active">Customer List</li>
        </ul>

        <div class="customer-hero d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div><h3>Customers</h3><p class="mb-0 mt-1">Customer accounts, balances, and live position exposure.</p></div>
            <div class="mt-3 mt-md-0"><button type="button" class="btn btn-danger mr-2" onclick="window.history.back()">Back</button>@can('customer_add')<a href="{{ route('admin.customer.create') }}" class="btn btn-light"><i class="fa fa-plus mr-1"></i> Customer Add</a>@endcan</div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3 mb-2"><div class="customer-stat"><small>Total customers</small><strong>{{ $customerCount }}</strong></div></div>
            <div class="col-md-3 mb-2"><div class="customer-stat"><small>Active customers</small><strong>{{ $activeCustomers }}</strong></div></div>
            <div class="col-md-4 mb-2"><div class="customer-stat"><small>Combined cash balance</small><strong>AED {{ number_format($totalBalance, 2) }}</strong></div></div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center"><h5 class="mb-0">Customer directory</h5><small class="text-muted">Use search to quickly find a customer or phone number.</small></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="customer-table" class="table table-hover customer-table mb-0" style="width:100%">
                        <thead><tr><th>#</th><th>Customer</th><th>Contact</th><th>Status</th><th>Cash position</th><th>Gold position</th><th>Equity &amp; margin</th><th>Actions</th></tr></thead>
                        <tbody>
                            @foreach ($customers as $row)
                                @php $netTtb = $row->sum_of_running_buy_ttb - $row->sum_of_running_sell_ttb; @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><strong>{{ $row->name }}</strong><span class="metric">{{ $row->customer_code }}</span></td>
                                    <td>{{ $row->phone ?: '—' }}<span class="metric">{{ $row->email ?: '' }}</span></td>
                                    <td>
                                        @if ($row->status === 'deactived')<span class="badge badge-warning">Pending</span>@else<span class="badge badge-success">Active</span>@endif
                                    </td>
                                    <td><span class="metric">Deposit <strong>AED {{ $row->sum_of_deposit }}</strong></span><span class="metric">Balance <strong>AED {{ number_format($row->current_balance, 2) }}</strong></span></td>
                                    <td><span class="metric">Buy <strong>{{ $row->sum_of_running_buy_ttb }}</strong> · Sell <strong>{{ $row->sum_of_running_sell_ttb }}</strong></span><span class="metric">Net <strong>{{ abs($netTtb) }} {{ $netTtb > 0 ? 'Buy' : ($netTtb < 0 ? 'Sell' : '') }}</strong></span></td>
                                    <td><span class="metric">Equity <strong>AED {{ number_format($row->equity - $row->sum_of_running_service_charge, 2) }}</strong></span><span class="metric">Margin <strong>{{ number_format($row->margin_gap, 2) }}</strong></span></td>
                                    <td class="text-nowrap">
                                        @can('customer_deatils')<a href="{{ route('admin.buysell.customer.search', ['customer' => $row->customer_code]) }}" class="btn btn-outline-primary btn-sm">Trade</a>@endcan
                                        @can('users_edit')<a href="{{ route('admin.customer.edit', $row->id) }}" class="btn btn-outline-info btn-sm">Edit</a>@endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@stop

@section('page_js')
    <script>$(function () { $('#customer-table').DataTable({ responsive: true, pageLength: 25, lengthChange: true, order: [[1, 'asc']] }); });</script>
@endsection
