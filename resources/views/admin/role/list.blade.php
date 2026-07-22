@extends('layouts.master')


@section('title', 'Roles')

@section('content_header')
    <h1>Role List</h1>
@stop

@section('content')
<ul class="breadcrumb breadcrumb-style">
		<li class="breadcrumb-item">
		  	<a href="{{route('admin.dashboard.buysell')}}">
				<i class="fas fa-home"></i>
			</a>
		</li>
		<li class="breadcrumb-item active">Role List</li>
	</ul>

    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-end">
            <a href="{{ route('admin.role.create') }}" class="btn btn-success">
                <i class="fa fa-plus"></i> Create Role
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
                                <th>name</th>
                                {{-- <th>Descption</th> --}}
                                @can('roles_edit')
                                    <th>Action</th>
                                @endcan
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
                                        @can('roles_edit')
                                            <td>
                                                <a href="{{ route('admin.role.edit', $row->id) }}" class="btn btn-success btn-sm"><i
                                                        class="fa fa-edit"></i></a>
                                            </td>
                                        @endcan

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
