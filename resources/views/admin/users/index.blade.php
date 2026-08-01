@extends('layouts.master')

@section('title', 'Users')

@section('content_header')
@stop

<style>
    .users-page { max-width: 1440px; margin: 0 auto; }
    .users-hero { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1.25rem 1.5rem; margin-bottom: 1.25rem; color: #fff; background: linear-gradient(135deg, #1f6fb2, #164f80); border-radius: .65rem; box-shadow: 0 10px 24px rgba(31, 111, 178, .18); }
    .users-hero h1 { margin: 0; color: #fff; font-size: 1.45rem; font-weight: 700; }
    .users-hero p { margin: .25rem 0 0; color: rgba(255,255,255,.82); font-size: .86rem; }
    .users-hero .btn { border: 0; font-weight: 600; white-space: nowrap; }
    .users-card { border: 0; border-radius: .65rem; overflow: hidden; box-shadow: 0 4px 18px rgba(16, 24, 40, .08); }
    .users-card__header { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; background: #fff; border-bottom: 1px solid #e7edf4; }
    .users-card__header h2 { margin: 0; font-size: 1rem; font-weight: 700; color: #1d2939; }
    .users-card__header span { color: #667085; font-size: .82rem; }
    #user-table { margin-bottom: 0 !important; }
    #user-table thead th { padding: .8rem .75rem; color: #fff; background: #1f6fb2; border-color: #2b7bbd; font-size: .75rem; font-weight: 700; letter-spacing: .02em; text-transform: uppercase; white-space: nowrap; }
    #user-table tbody td { padding: .75rem; vertical-align: middle; color: #344054; border-color: #edf1f5; }
    #user-table tbody tr:hover { background: #f6faff; }
    .user-name { font-weight: 700; color: #1d2939; }
    .role-badge { display: inline-block; padding: .25rem .55rem; color: #1f6fb2; background: #eaf4fc; border-radius: 999px; font-size: .75rem; font-weight: 700; }
    .role-badge--empty { color: #667085; background: #f2f4f7; }
    .users-actions { display: flex; gap: .4rem; }
    .users-actions .btn { width: 31px; height: 31px; padding: 0; line-height: 31px; border: 0; border-radius: .35rem; }
    .users-actions .btn-edit { color: #fff; background: #1f6fb2; }
    .users-actions .btn-delete { color: #fff; background: #d92d20; }
    @media (max-width: 575px) { .users-hero { align-items: flex-start; flex-direction: column; } }
</style>

@section('content')
 <ul class="breadcrumb breadcrumb-style">
		<li class="breadcrumb-item">
		  	<a href="{{route('admin.dashboard.buysell')}}">
				<i class="fas fa-home"></i>
			</a>
		</li>
		<li class="breadcrumb-item active">User List</li>
	</ul>

    <div class="users-page">
        <div class="users-hero">
            <div>
                <h1>User management</h1>
                <p>Manage team access, roles, and account details.</p>
            </div>
            @can('users_add')
                <a href="{{ route('admin.users.create') }}" class="btn btn-light"><i class="fa fa-plus mr-1"></i> Create User</a>
            @endcan
        </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card users-card">
                <div class="users-card__header">
                    <h2>Active users</h2>
                    <span>{{ $users->count() }} user{{ $users->count() === 1 ? '' : 's' }}</span>
                </div>
                <div class="card-body table-responsive">
                    <table id="user-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
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
                                    <td><span class="user-name">{{ $user->full_name }}</span></td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if ($user->roles->isNotEmpty())
                                            <span class="role-badge">{{ $user->roles->pluck('name')->join(', ') }}</span>
                                        @else
                                            <span class="role-badge role-badge--empty">No role</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at }}</td>
                                    @can('user_action')
                                    <td>
                                        <div class="users-actions">
                                        @can('users_edit')
                                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                                class="btn btn-edit" title="Edit user"><i class="fa fa-edit"></i></a>
                                        @endcan
                                        @can('user_delete')
                                            <a href="{{ route('admin.users.destroy', $user->id) }}"
                                                class="btn btn-delete" title="Delete user"><i class="fa fa-trash"></i></a>
                                        @endcan 
                                        </div>
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
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#user-table').DataTable();
        });
    </script>
@stop
