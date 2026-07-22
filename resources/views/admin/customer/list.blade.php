@extends('layouts.master')


@section('title', 'Customer List')

@section('content_header')

    <div class="row">
        <div class="col-md-8">
            <h1> <i class="fa fa-users"></i> Customer List</h1>
        </div>
        <div class="col-md-4 d-flex justify-content-end">
            <a href="{{ route('admin.customer.create') }}" class="btn btn-success">
                <i class="fa fa-plus"></i> Create Customer
            </a>
        </div>
    </div>


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
		<li class="breadcrumb-item active">Customer List</li>
	</ul>


    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Customer List</h5>
                    <a href="#" onclick="window.history.back()" class="btn btn-danger">Back</a>
                </div>
                <div class="card-body table-responsive">
                    <table id="customer-table" class="table table-striped table-bordered table-sm" style="width:100%">
                        <thead>
                            <tr>
                                <th style="text-align: center;">SL </th>
                                <th style="text-align: center;"> Code </th>
                                <th style="text-align: center;">Full Nane</th>
                                <th style="text-align: center;">Mobile</th>
                                {{-- <th>Email</th> --}}
                                <th style="text-align: center;">Status</th>
                                <th style="text-align: center;"> Deposit (AED)</th>
                                <th style="text-align: center;"> Buy TTB</th>
                                <th style="text-align: center;"> Sell TTB</th>
                                <th style="text-align: center;"> Active TTB</th>
                                <th style="text-align: center;">Balance (AED)</th>
                                <th style="text-align: center;">Equity (AED)</th>
                                <th style="text-align: center;">Margin Gap ($)</th>
                                <th style="text-align: center;">Cut Position (AED)</th>
                                @if (auth()->user()->can('users_edit') == true)
                                    <th style="text-align: center;">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($customers) > 0)
                                <?php $sl = 0; ?>
                                @foreach ($customers as $row)
                                    <?php $sl++; ?>
                                    <tr>
                                        <td style="text-align: center;">{{ $sl }}</td>
                                        <td> 
                                            @if (auth()->user()->can('customer_deatils') == true)
                                                <a href="{{ route('admin.buysell.customer.search', ['customer' => $row->customer_code]) }}" class="link link-primary">{{ $row->customer_code }} </a>
                                            @else
                                                {{ $row->customer_code }}
                                            @endif
                                        </td>
                                        <td style="text-align: center;"> {{ $row->name }} </td>
                                        <td style="text-align: center;">{{ $row->phone }}</td>
                                        <td style="text-align: center;">
                                            @if (auth()->user()->can('users_edit') == true)
                                                @if ($row->status == 'deactived')
                                                    <form action="{{ route('admin.customer.enable', ['id' => $row->id]) }}"
                                                        method="POST" style="margin-bottom:0px !important;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill ml-1">
                                                            Pending
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.customer.disable', ['id' => $row->id]) }}"
                                                        method="POST" style="margin-bottom:0px !important;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-success btn-sm rounded-pill ml-1">Approved</button>
                                                    </form>
                                                @endif
                                            @else
                                                @if ($row->status == 'deactived')
                                                    <span class="badge badge-danger">Pending</span>
                                                @else
                                                    <span class="badge badge-success">Approved</span>
                                                @endif
                                            @endif

                                        </td>
                                        <td style="text-align: center;">{{ $row->sum_of_deposit }}</td>
                                        <td style="text-align: center;">{{ $row->sum_of_running_buy_ttb }}</td>
                                        <td style="text-align: center;">{{ $row->sum_of_running_sell_ttb }}</td>
                                        <td style="text-align: center;">{{ abs($row->sum_of_running_buy_ttb - $row->sum_of_running_sell_ttb)}} {{ ($row->sum_of_running_buy_ttb - $row->sum_of_running_sell_ttb) > 0 ? ' - buy' : (($row->sum_of_running_buy_ttb - $row->sum_of_running_sell_ttb) < 0 ? ' - sell' : '') }}</td>
                                        <td style="text-align: center;">{{ number_format($row->current_balance, 2) }}</td>
                                        <td style="text-align: center;">{{ number_format($row->equity - $row->sum_of_running_service_charge)}}</td>
                                        <td style="text-align: center;">{{ number_format($row->margin_gap)}}</td>
                                        <td style="text-align: center;">{{ number_format($row->margin)}}</td>

                                        <td style="text-align: center;">
                                            @can('customer_deatils')
                                                <a href="{{ route('admin.buysell.customer.search', ['customer' => $row->customer_code]) }}"
                                                    class="btn btn-primary btn-sm p-1 ml-1"><i class="fa fa-eye fa-sm">Buy/Sell</i>
                                                </a>
                                            @endcan
                                            @can('users_edit')
                                                <a href="{{ route('admin.customer.edit', $row->id) }}"
                                                    class="btn btn-info btn-sm p-1"><i class="fa fa-edit">Edit</i>
                                                </a>
                                            @endcan

                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('page_js')

    <script>
        $(document).ready(function() {
            $('#customer-table').DataTable({
                responsive: true,
                pageLength: 10,
                lengthChange: true
            });
        });
    </script>
@endsection


