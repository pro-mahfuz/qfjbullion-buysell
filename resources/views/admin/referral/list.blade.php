@extends('layouts.master')


@section('title', 'Roles')

@section('content_header')
    <h1>Referral List</h1>
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
		<li class="breadcrumb-item active">Referral List</li>
	</ul>

    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-end">
            <a href="{{ route('admin.refferal.create') }}" class="btn btn-success">
                <i class="fa fa-plus"></i> Create Referral
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-responsive">
                    <table id="role-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Referral Amount</th>
                                <th>Percentage</th>
                                <th>Status</th>


                                {{-- @can('roles_edit') --}}
                                <th>Action</th>
                                {{-- @endcan --}}
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @dd($result) --}}
                            @if (count($result) > 0)
                                <?php $sl = 0; ?>
                                @foreach ($result as $row)
                                    <?php $sl++; ?>
                                    <tr>
                                        <td>{{ $sl }}</td>
                                        <td>{{ $row->title }}</td>
                                        <td>{{ $row->total_referral_amount }}</td>
                                        <td>{{ $row->percentage }}</td>
                                        {{-- <td>{{ $row->is_active }}</td> --}}
                                        <td>
                                            @if ($row->is_active == 1)
                                                <button class="btn btn-primary btn-sm">Active</button>
                                            @else
                                                <button class="btn btn-secondary btn-sm">Inactive</button>
                                            @endif
                                        </td>
                                        <td>
                                            {{-- @dd( $row->id) --}}
                                            <form action="{{ route('admin.refferal.delete', $row->referral_id) }}"
                                                method="POST" style="display: inline;"
                                                onsubmit="return confirm('Are you sure you want to delete this referral?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>


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


@section('js')
    <script>
        $(document).ready(function() {
            $('#role-table').DataTable();
        });
    </script>
@stop
