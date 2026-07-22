@extends('layouts.master')

@section('title', 'Business List')

@section('content_header')
    <h1>Business List</h1>
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
		<li class="breadcrumb-item active">Business Settings</li>
	</ul>

    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-end">
            <a href="{{ route('admin.bussiness.create') }}" class="btn btn-success">
                <i class="fa fa-plus"></i> Create Business
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-striped table-bordered common_datatable" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>name</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($result) > 0)
                                <?php $sl = 0; ?>
                                @foreach ($result as $row)
                                    <?php $sl++; ?>
                                    <tr>
                                        <td>{{ $sl }}</td>
                                        <td>{{ $row->name }}</td>
                                        <td>{{ $row->email }}</td>
                                        <td>{{ $row->address }}</td>
                                        <td>
                                            <a href="{{ route('admin.bussiness.edit', $row->id) }}"
                                                class="btn btn-success btn-sm"><i class="fa fa-edit"></i></a>

                                            <a href="{{ route('admin.bussiness.delete', $row->id) }}"
                                                class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
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

