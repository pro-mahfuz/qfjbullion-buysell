@extends('layouts.master')

@section('title', 'Users')

@section('content_header')
    @can('users_add')
    <div class="row mb-3">
        <div class="col-md-12">
            <h1 style="text-align: left">Users</h1>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary" style="float: right;margin-top: -35px;">Create User</a>
        </div>
    </div>
@endcan
@stop

@section('content')
 <ul class="breadcrumb breadcrumb-style">
		<li class="breadcrumb-item">
		  	<a href="{{route('admin.dashboard.buysell')}}">
				<i class="fas fa-home"></i>
			</a>
		</li>
		<li class="breadcrumb-item active">User List</li>
	</ul>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-responsive">
                    <table id="user-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Email</th>
                                <!--<th>Role</th>-->
                                <th>Created At</th>
                                @can('user_action')
                                <th>Actions</th>
                                @endcan 
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $i => $user)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $user->full_name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <!--<td>{{ isset($user->role->name)? $user->role->name : "N/A" }}</td>-->
                                    <td>{{ $user->created_at }}</td>
                                    @can('user_action')
                                    <td> 
                                        @can('users_edit')
                                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                                class="btn btn-success btn-sm"><i class="fa fa-edit"></i></a>
                                        @endcan
                                        @can('user_delete')
                                            <a href="{{ route('admin.users.destroy', $user->id) }}"
                                                class="btn btn-success btn-sm"><i class="fa fa-trash"></i></a>
                                        @endcan 
                                    </td>
                                    @endcan 
                                </tr>
                            @endforeach
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
            $('#user-table').DataTable();
        });
    </script>
@stop
