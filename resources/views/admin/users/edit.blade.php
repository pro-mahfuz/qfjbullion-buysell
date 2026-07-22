@extends('layouts.master')


@section('title', 'Users Update')

@section('content_header')
    <h1>Users Update</h1>
@stop


@section('content')
<ul class="breadcrumb breadcrumb-style">
		<li class="breadcrumb-item">
		  	<a href="{{route('admin.dashboard.buysell')}}">
				<i class="fas fa-home"></i>
			</a>
		</li>
		<li class="breadcrumb-item active">User Edit</li>
	</ul>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Full Name <span>*</span></label>
                                    <input type="text" name="full_name" class="form-control"
                                        value="{{ $user->full_name }}" required="">
                                    @error('full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Email <span>*</span></label>
                                    <input type="email" name="email" class="form-control" value="{{ $user->email }}"
                                        required="">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Role <span>*</span></label>
                                    @php
                                        $role_id = null;
                                        if (count($user->roles) > 0) {
                                            $role_id = $user->roles[0]->id;
                                        }
                                    @endphp
                                    <select name="role_id" class="form-control" required="">
                                        <option value="">Select Role</option>

                                        @if (count($roles) > 0)
                                            @foreach ($roles as $role)
                                                <option @if ($role_id == $role->id) selected="" @endif
                                                    value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('role_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            {{-- @dd($user) --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Map Business <span>*</span></label>
                                    <select name="business_id" class="form-control" required="">
                                        <option value="">Select Business</option>
                                        @if (count($businesses) > 0)
                                            @foreach ($businesses as $business)
                                                <option @if ($bussiness_id == $business->id) selected="" @endif
                                                    value="{{ $business->id }}">
                                                    {{ $business->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('role_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            {{-- user ?Password --}}
                            <div class="col-md-4">
                                <div class="form-group
                                    ">
                                    <label for="">Password</label>
                                    <input type="password" name="password" class="form-control" value="">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <div class="ln_solid"></div>
                        <div class="item form-group" style="float: right">
                            <button type="submit" class="btn btn-success">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
