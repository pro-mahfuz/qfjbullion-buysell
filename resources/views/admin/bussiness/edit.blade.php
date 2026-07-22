@extends('layouts.master')
@section('title', 'Business Edit')

@section('content_header')
    <h1>Business Edit</h1>
@stop



@section('content')
    <ul class="breadcrumb breadcrumb-style">
		<li class="breadcrumb-item">
		  	<a href="{{route('admin.dashboard.buysell')}}">
				<i class="fas fa-home"></i>
			</a>
		</li>
		<li class="breadcrumb-item active">Business Edit</li>
	</ul>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                    <form action="{{ route('admin.bussiness.update', $bussiness->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Full Name <span>*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ $bussiness->name }}"
                                        required="">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Description <span>*</span></label>
                                    <input type="text" name="description" class="form-control"
                                        value="{{ $bussiness->description }}" required="">
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Phone <span>*</span></label>
                                    <input type="text" name="phone" class="form-control"
                                        value="{{ $bussiness->phone }}" required="">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Address <span>*</span></label>
                                    <input type="text" name="address" class="form-control"
                                        value="{{ $bussiness->address }}" required="">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Email <span>*</span></label>
                                    <input type="text" name="email" class="form-control"
                                        value="{{ $bussiness->email }}" required="">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- iamge upload --}}

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Image <span>*</span></label>
                                    <input type="file" name="image" class="form-control"
                                        value="{{ $bussiness->image }}" >
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Domain <span>*</span></label>
                                    <input type="text" name="domain" class="form-control"
                                        value="{{ $bussiness->domain }}" required="">
                                    @error('domain')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Privacy Policy <span>*</span></label>
                                    <textarea name="privacy" class="form-control" required="">{{ $bussiness->privacy }}</textarea>
                                    @error('privacy')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Terms and Conditions<span>*</span></label>
                                    <textarea name="terms" class="form-control" required="">{{ $bussiness->terms }}</textarea>
                                    @error('terms')
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
