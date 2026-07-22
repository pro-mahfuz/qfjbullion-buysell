@extends('layouts.master')




@section('title', 'Role Edit')

@section('content_header')
    <h1>Role Edit</h1>
@stop


@section('content')
<ul class="breadcrumb breadcrumb-style">
		<li class="breadcrumb-item">
		  	<a href="{{route('admin.dashboard.buysell')}}">
				<i class="fas fa-home"></i>
			</a>
		</li>
		<li class="breadcrumb-item active">Role Edit</li>
	</ul>
    <div class="row">
        <div class="col-md-12 col-sm-12">

            @if (Session::has('success'))
                <div class="alert alert-success">{{ Session::get('success') }}</div>
            @endif
            <form action="{{ route('admin.role.update', $role->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="col-md-6 mt-2">
                    <div class="form-group">
                        <label for="role_name">Role Name <span>*</span></label>
                        <input type="text" name="name" id="role_name" class="form-control"
                            value="{{ old('name', $role->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="permissions">Permissions <span>*</span></label>
                        <div class="permissions-container">
                            @php
                                $rolePermissions = [];
                                foreach ($role->permissions as $permission) {
                                    $rolePermissions[] = $permission->id;
                                }
                            @endphp
                            @foreach ($permissions as $permission)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="permission_id[]"
                                        value="{{ $permission->id }}" id="permission_{{ $permission->id }}"
                                        {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                        {{ ucwords(str_replace('_', ' ', $permission->name)) }}

                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('permission_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="ln_solid"></div>
                <div class="item form-group" style="float: right">
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <style>
        /* Make the permissions list scrollable if too long */
        .permissions-container {
            max-height: 400px;
            /* Adjust height as needed */
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        /* Multi-column layout for wider screens */
        @media (min-width: 768px) {
            .permissions-container {
                column-count: 2;
                /* Adjust the column count for wide screens */
                column-gap: 20px;
            }
        }

        @media (min-width: 1200px) {
            .permissions-container {
                column-count: 3;
                /* More columns for larger screens */
            }
        }

        /* Style individual checkboxes */
        .form-check-inline {
            break-inside: avoid;
            /* Ensure items don't break across columns */
            display: block;
            /* Make checkboxes stack vertically */
        }
    </style>
@stop
