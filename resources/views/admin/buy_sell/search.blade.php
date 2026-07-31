@extends('layouts.master')

@section('title', 'Buy Sell')

@section('content_header')
    <!--<h1>Customer Search</h1>-->
@stop

<style>
	.recent-report__chart{
		margin-left: -30px;
	}
	.apexcharts-legend{
		margin-right: 30px;
		margin-top: 45px;
		margin-left: 10px;
	}
	#navbar_search_box{
		margin-top:10px;
	}

</style>
<style>
  .table td, .table th {
    padding: .25rem !important;
    font-size: .85rem !important;
	height: 20px !important;
  }

  .modal-title{
      color: #000000 !important;
  }
  .pagination {
      margin-top: -25px;
  }

  .page-link {
    padding: 0.2rem 0.5rem !important;
  }
</style>
<style>
  .custom-bordered th,
  .custom-bordered td,
  .custom-bordered {
    border: 1px solid black !important;
  }
</style>
@section('content')
    <ul class="breadcrumb breadcrumb-style">
		<li class="breadcrumb-item">
		  	<a href="{{route('admin.dashboard.buysell')}}">
				<i class="fas fa-home"></i>
			</a>
		</li>
		<li class="breadcrumb-item active">Trade Summary</li>
	</ul>

    <div class="row">
        <div class="col-md-12 col-sm-12 ">
            <div>
                <form action="{{ route('admin.buysell.customer.search') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4 d-flex align-items-center justify-content-end">
                            <h6>Customer Search</h6>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" style="margin-top: 0px;">
                                <input type="text" class="form-control" required="" name="customer"
                                    style="line-height: 2rem;"
                                    @if ($customer) value="{{ $customer->name }}" @endif
                                    placeholder="Code / Name / Phone / Email">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group" style="margin-top: 0px;">

                                <button type="submit" class="btn btn-info" title="Search"><i class="fa fa-search"></i>
                                    Search</button>
                            </div>
                        </div>
                        
                    </div>
                </form>
            </div>
            @if ($customer)
            
                <div class="row mt-5">
                    <div class="col-md-4" style="text-align: left;">
                        <div class="info-box-content">
                            <h5>Customer: {{ $customer->name }} (  {{ $customer->customer_code }} )</h5>
                            <input type="hidden" class="form-control" id="customer_id" value="{{$customer->id}}" name="customer_id" >
                        </div>
                    </div>
                    
                    @if ($customer)
                        <div class="col-md-8 d-flex justify-content-end" style="text-align: right;">
                            @can('buysell')
                            <form action="{{ route('admin.buysell.customer.buysell') }}" method="GET">
                                <input type="hidden" class="form-control" required="" name="customer"
                                    @if ($customer) value="{{ $customer->name }}" @endif>
                                <button type="submit" class="btn btn-info mr-2">BUY & SELL</button>
                            </form>
                            @endcan
                            @can('satement_send')
                            <button type="button" onclick="showStatement('statement')" class="btn btn-primary mr-2" style="height: 34px;">
                                Statement
                            </button>
                            @endcan
                            @can('withdraw_list')
                            <a href="{{ route('admin.buysell.show.preview', ['id' => $customer->id, 'type' => 'withdraw']) }}" class="btn btn-danger mr-2" style="height: 34px;">
                                Withdraw
                            </a>
                            @endcan
                            @can('deposit_list')
                            <a href="{{ route('admin.buysell.show.preview', ['id' => $customer->id, 'type' => 'deposit']) }}" class="btn btn-success mr-2" style="height: 34px;">
                                Deposit
                            </a>
                            @endcan
                        </div>
                    @endif
                </div>


                @include('admin.buy_sell.bid', [
                    'customer' => $customer,
                    'current_amount' => $current_amount,
                    'maxtt_per_K' => $customer->maxtt_per_K,
                    'deposit' => $deposit,
                    'withdraw' => $withdraw,
                ])
            @else
                <!--<div class="alert alert-danger">No customer found</div>-->
            @endif

            @can('closed_trade_history')
                @if ($lastTen->isNotEmpty())
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span>Last 10 Closed Transactions</span>
                                    <span class="badge badge-light">{{ $lastTen->count() }} trade{{ $lastTen->count() === 1 ? '' : 's' }}</span>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead style="background-color: #1f6fb2; color: #ffffff;">
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Reference</th>
                                                    <th scope="col">Opened</th>
                                                    <th scope="col">Open</th>
                                                    <th scope="col">Closed</th>
                                                    <th scope="col">Close</th>
                                                    <th scope="col" class="text-right">TTB Qty</th>
                                                    <th scope="col" class="text-right">P/L (AED)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($lastTen as $index => $transaction)
                                                    @php($openingTrade = $transaction->linked_buy)
                                                    @php($openingType = $openingTrade?->type ?? $transaction->transaction_type)
                                                    @php($closingType = $openingType === 'buy' ? 'sell' : 'buy')
                                                    <tr>
                                                        <th scope="row">{{ $index + 1 }}</th>
                                                        <td><span class="font-weight-bold">{{ $transaction->reference_no ?: '—' }}</span></td>
                                                        <td>
                                                            <div>{{ $openingTrade?->created_at?->format('d M Y') ?? '—' }}</div>
                                                            <small class="text-muted">{{ $openingTrade?->current_rate ? number_format($openingTrade->current_rate, 3) : '—' }}</small>
                                                        </td>
                                                        <td><span class="badge badge-{{ $openingType === 'buy' ? 'info' : 'danger' }}">{{ strtoupper($openingType) }}</span></td>
                                                        <td>
                                                            <div>{{ $transaction->created_at?->format('d M Y, H:i') }}</div>
                                                            <small class="text-muted">{{ number_format($transaction->current_rate, 3) }}</small>
                                                        </td>
                                                        <td><span class="badge badge-{{ $closingType === 'buy' ? 'info' : 'danger' }}">{{ strtoupper($closingType) }}</span></td>
                                                        <td class="text-right">{{ number_format($transaction->quantity, 3) }}</td>
                                                        <td class="text-right font-weight-bold {{ $transaction->calculated_profit_loss < 0 ? 'text-danger' : 'text-success' }}">
                                                            {{ number_format($transaction->calculated_profit_loss, 3) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endcan

        </div>
    </div>
@stop

@section('scripts')
    

@stop
