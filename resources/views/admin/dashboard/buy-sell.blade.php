@extends('layouts.master')
@section('title', 'Buy Sell Dashboard')

@section('content_header')
    <h1>Buy Sell Dashboard</h1>
@stop
@section('styles')
    <!-- Include Daterangepicker CSS -->
@stop

@section('content')
    <ul class="breadcrumb breadcrumb-style">
		<li class="breadcrumb-item">
		  	<a href="{{route('admin.dashboard.buysell')}}">
				<i class="fas fa-home"></i>
			</a>
		</li>
		<li class="breadcrumb-item active">Dashboard</li>
	</ul>
	
	<div class="row">
	    <div class="col-md-3 mt-2">
            <div class="card">
                <div class="card-header justify-content-center">
                    <h5>Customer Search</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.buysell.customer.search') }}" method="GET">
                        <div class="row">
                        </div>
                        <div class="row">
                            <input type="text" class="form-control" required="" name="customer"
                                style="line-height: 2rem;"
                                placeholder="Code / Name / Phone / Email">
                        </div>
                        <div class="row justify-content-center">
                            <button type="submit" class="btn btn-info mt-4 text-center" style="line-height: 17px !important;" title="Search"><i class="fa fa-search"></i> Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        @can('dashboard_customer_section')
        <div class="col-md-3 mt-2">
            <div class="card">
                <div class="card-header justify-content-center">
                    <h5>Customers</h5>
                </div>
                <div class="card-body">
                    <div class="row d-flex justify-content-between align-items-center" style="border-bottom: 1px dashed black;">
                        <p class="font-weight-bold pb-2 m-0">Total Customer:</p>
                        <p class="font-weight-bold pb-2 m-0">{{ $totalCustomers }}</p> 
                    </div>
                    <div class="row d-flex justify-content-between align-items-center" style="border-bottom: 1px dashed black;">
                        <p class="font-weight-bold py-2 m-0">Total Customer (Active):</p>
                        <p class="font-weight-bold py-2 m-0">{{ $activeCusomer }}</p> 
                    </div>
                    <div class="row d-flex justify-content-between align-items-center">
                        <p class="font-weight-bold pt-2 m-0">Total Customer (Inactive):</p>
                        <p class="font-weight-bold pt-2 m-0">{{$deactiveCusomer}}</p>
                    </div>
                </div>
            </div>
        </div>
        @endcan
        @can('dashboard_buy_sell_section')
        <div class="col-md-3 mt-2">
            <div class="card">
                <div class="card-header justify-content-center">
                    <h5>Buy & Sell TTB</h5>
                </div>
                <div class="card-body">
                    <div class="row d-flex justify-content-between align-items-center" style="border-bottom: 1px dashed black;">
                        <p class="font-weight-bold pb-2 m-0">Total Running TTB (Buy):</p>
                        <p class="font-weight-bold pb-2 m-0">{{ $totalRunningBuyTTB }}</p> 
                    </div>
                    <div class="row d-flex justify-content-between align-items-center" style="border-bottom: 1px dashed black;">
                        <p class="font-weight-bold py-2 m-0">Total Running TTB (Sell):</p>
                        <p class="font-weight-bold py-2 m-0">{{ $totalRunningSellTTB }}</p> 
                    </div>
                    <div class="row d-flex justify-content-between align-items-center">
                        <p class="font-weight-bold pt-2 m-0">Total Running TTB (Active):</p>
                        <p class="font-weight-bold pt-2 m-0">{{$totalRunningActiveTTB}} ({{ $totalRunningActiveTTB > 0 ? 'Buy' : 'Sell' }})</p>
                    </div>
                </div>
            </div>
        </div>
        @endcan
        @can('dashboard_transection_section')
        <div class="col-md-3 mt-2">
            <div class="card">
                <div class="card-header justify-content-center">
                    <h5>Transactions</h5>
                </div>
                <div class="card-body">
                    <div class="row d-flex justify-content-between align-items-center" style="border-bottom: 1px dashed black;">
                        <p class="font-weight-bold pb-2 m-0">Total Transaction:</p>
                        <p class="font-weight-bold pb-2 m-0">{{$totalTransactionAmount}}</p> 
                    </div>
                    <div class="row d-flex justify-content-between align-items-center" style="border-bottom: 1px dashed black;">
                        <p class="font-weight-bold py-2 m-0">Total Deposit:</p>
                        <p class="font-weight-bold py-2 m-0">{{$totalDepositAmount}}</p> 
                    </div>
                    <div class="row d-flex justify-content-between align-items-center">
                        <p class="font-weight-bold pt-2 m-0">Total Withdraw:</p>
                        <p class="font-weight-bold pt-2 m-0">{{$totalWithDrawAmount}}</p>
                    </div>
                </div>
            </div>
        </div>
        @endcan
	</div>
	
    
@stop


@section('js')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript">
        $(function() {
            $('#date_range').daterangepicker({
                opens: 'left',
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')],
                    'All': [moment().subtract(10, 'years'), moment().endOf('month')]
                },
                locale: {
                    format: 'DD-MM-YYYY'
                }
            });

            $('#date_range').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD-MM-YYYY') + ' to ' + picker.endDate.format(
                    'DD-MM-YYYY'));
            });
        });
    </script>
@stop
