@extends('layouts.master')

@section('title', 'Referral')

@section('content_header')
    <h1>Referral Summary</h1>
@stop
<style>
  .table td, .table th {
    padding: .25rem !important;
    font-size: .85rem !important;
	height: 20px !important;
  }
</style>
@section('content')
    <ul class="breadcrumb breadcrumb-style">
		<li class="breadcrumb-item">
		  	<a href="{{route('admin.dashboard.buysell')}}">
				<i class="fas fa-home"></i>
			</a>
		</li>
		<li class="breadcrumb-item active">Referral Dashboard</li>
	</ul>
	
	
	<div class="row transactionContainer">
        @foreach ($referralSummary as $key => $detail)
        <div class="col-md-3 mt-2">
            <div
                class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <p class="font-weight-bold">{{ $key }}:</p>
                        <p class="font-weight-bold">{{ $detail }}</p> 
                    </div>
            </div>
        </div>
        @endforeach
        
    </div>

    <div class="row">
        <div class="col-md-12">
            

            <!-- Reward List Card -->
            <div class="card mt-4">
                <div class="card-header bg-gold text-light">
                    <h4 style="text-align: center; margin: 0;">Reward List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    {{-- <th>Referral Title</th> --}}
                                    <th>Referrer Name</th>
                                    <th>Total Referral Reward</th>
                                    <th>Referral Date (Last Referral)</th>
                                    <th> Transaction ID </th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($paginatedRewards as $key => $reward)
                                    {{-- @dd($reward->referrals) --}}
                                    <tr>
                                        <td>{{ $paginatedRewards->firstItem() + $key }}</td>
                                        {{-- <td>{{ $reward->referrals->title }}</td> --}}
                                        <td>{{ $reward->customer->name ?? 'N/A' }}</td>
                                        <td>{{ $reward->total_rewards }}</td>
                                        <td>{{ $reward->created_at->format('d-m-Y') }}</td>
                                        <td>{{ $reward->transaction_id }}</td>
                                        <td>
                                            <a href="{{ route('admin.refferal.reward.show', $reward->customer_id) }}"
                                                class="btn btn-info btn-sm">History</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end">
                        {{ $paginatedRewards->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
