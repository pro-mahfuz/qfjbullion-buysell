@extends('layouts.master')


@section('title', 'Role Create')

@section('content_header')
    <h1>Role Create</h1>
@stop

@section('content')
<ul class="breadcrumb breadcrumb-style">
		<li class="breadcrumb-item">
		  	<a href="{{route('admin.dashboard.buysell')}}">
				<i class="fas fa-home"></i>
			</a>
		</li>
		<li class="breadcrumb-item active">Role Add</li>
	</ul>
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-end">
            <a href="{{ route('admin.role.list') }}" class="btn btn-danger">Back</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                    <form action="{{ route('admin.role.store') }}" method="POST">
                        @csrf
                        <div class="col-md-6 mt-2">
                            <div class="form-group">
                                <label for="role_name">Role Name <span>*</span></label>
                                <input type="text" name="name" id="role_name" class="form-control"
                                    value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="permissions">Permissions <span>*</span></label>
                                <div class="permissions-container">
                                    @foreach ($permissions as $permission)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="permission_id[]"
                                                value="{{ $permission->id }}" id="permission_{{ $permission->id }}">
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

                        <div class="form-group mt-4 text-right">
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop



@section('styles')
    <style>
        .permissions-container {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        @media (min-width: 768px) {
            .permissions-container {
                column-count: 2;
                column-gap: 20px;
            }
        }

        @media (min-width: 1200px) {
            .permissions-container {
                column-count: 3;
            }
        }

        .form-check-inline {
            break-inside: avoid;
            display: block;
        }
    </style>

@stop
