@extends('layouts.master')


@section('title', 'User Create')

@section('content_header')
    <h1>User Create</h1>
@stop



@section('content')
<ul class="breadcrumb breadcrumb-style">
		<li class="breadcrumb-item">
		  	<a href="{{route('admin.dashboard.buysell')}}">
				<i class="fas fa-home"></i>
			</a>
		</li>
		<li class="breadcrumb-item active">User Add</li>
	</ul>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Full Name <span>*</span></label>
                                    <input type="text" name="full_name" class="form-control"
                                        value="{{ old('full_name') }}" required="">
                                    @error('full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Email <span>*</span></label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                        required="">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Role <span>*</span></label>
                                    <select name="role_id" class="form-control" required="">
                                        <option value="">Select Role</option>
                                        @if (count($roles) > 0)
                                            @foreach ($roles as $role)
                                                <option @if (old('role_id') == $role->id) selected="" @endif
                                                    value="{{ $role->id }}">
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('role_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Map Business <span>*</span></label>
                                    <select name="business_id" class="form-control" required="">
                                        <option value="">Select Business</option>
                                        @if (count($businesses) > 0)
                                            @foreach ($businesses as $business)
                                                <option value="{{ $business->id }}">
                                                    {{ $business->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('business_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Password <span>*</span></label>
                                    <input type="password" name="password" class="form-control" required="">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Confirm Password <span>*</span></label>
                                    <input type="password" name="password_confirmation" class="form-control" required="">
                                </div>
                            </div>
                        </div>
                        <div class="ln_solid"></div>
                        <div class="item form-group" style="float: right">
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
