@extends('layouts.master')
@section('title', 'Business Create')

@section('content_header')
    <h1>Business Create</h1>
@stop



@section('content')
    <ul class="breadcrumb breadcrumb-style">
		<li class="breadcrumb-item">
		  	<a href="{{route('admin.dashboard.buysell')}}">
				<i class="fas fa-home"></i>
			</a>
		</li>
		<li class="breadcrumb-item active">Business Add</li>
	</ul>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                    <form action="{{ route('admin.bussiness.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Bussiness Name <span>*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}"
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
                                        value="{{ old('description') }}" required="">
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- phone -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Phone <span>*</span></label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}"
                                        required="">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Address <span>*</span></label>
                                    <input type="text" name="address" class="form-control" value="{{ old('address') }}"
                                        required="">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Email <span>*</span></label>
                                    <input type="text" name="email" class="form-control" value="{{ old('email') }}"
                                        required="">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Image <span>*</span></label>
                                    <input type="file" name="image" class="form-control" value="{{ old('image') }}"
                                        required="">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Domain Name <span>*</span></label>
                                    <input type="text" name="domain" class="form-control"
                                        value="{{ old('domain') }}" required="">
                                    @error('domain_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>


                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="currency">Select Currency <span>*</span></label>
                                    <select name="currency[]" id="currency" class="form-control common_select2" required
                                        multiple>
                                        <option value="">-- Select Currency --</option>
                                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>US Dollar
                                            (USD)</option>
                                        <option value="AED" {{ old('currency') == 'AED' ? 'selected' : '' }}>United Arab
                                            Emirates Dirham (AED)</option>
                                        <option value="BDT" {{ old('currency') == 'BDT' ? 'selected' : '' }}>Bangladeshi
                                            Taka (BDT)</option>
                                        <option value="INR" {{ old('currency') == 'INR' ? 'selected' : '' }}>Indian
                                            Rupee
                                            (INR)</option>
                                        <option value="SAR" {{ old('currency') == 'SAR' ? 'selected' : '' }}>Saudi Riyal
                                            (SAR)</option>
                                    </select>
                                    @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
