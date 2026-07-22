@extends('layouts.master')
@section('title', 'Customer Create')

@section('content_header')
    <h1>Customer Create</h1>
@stop


@section('content')
    <ul class="breadcrumb breadcrumb-style">
		<li class="breadcrumb-item">
		  	<a href="{{route('admin.dashboard.buysell')}}">
				<i class="fas fa-home"></i>
			</a>
		</li>
		<li class="breadcrumb-item active">Customer Add</li>
	</ul>
	
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('admin.customer.store') }}" method="POST" enctype="multipart/form-data">
                <div class="card card-default">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Customer Add</h5>
                        <a href="{{ route('admin.customer.list') }}" class="btn btn-danger">Back</a>
                    </div>
                    <div class="card-body">

                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="customer_code">Customer Code<span>*</span></label>
                                    <input type="text" name="customer_code" class="form-control" required
                                        value="{{ $generatedCodeWithPrefixSJ }}" placeholder="Enter Customer Code">
                                    @error('customer_code')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">Full Name <span>*</span></label>
                                    <input type="text" name="name" class="form-control" required
                                        value="{{ old('name') }}" placeholder="Enter Full Name">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="type">Type <span>*</span></label>
                                    <select name="type" class="form-control" required>
                                        <option value="customer" {{ old('type') == 'customer' ? 'selected' : '' }}>
                                            Customer</option>
                                            <option value="client" {{ old('type') == 'client' ? 'selected' : '' }}>IB
                                            </option>
                                         
                                    </select>
                                    @error('type')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="address">Address<span>*</span></label>
                                    <input type="text" name="address" class="form-control" value="{{ old('address') }}"
                                        placeholder="Enter Address">
                                    @error('address')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" name="city" class="form-control" value="{{ old('city') }}"
                                        placeholder="Enter City">
                                    @error('city')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="phone">Mobile Number<span>*</span></label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}"
                                        placeholder="Enter Mobile Number">
                                    @error('phone')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="land_phone">Land Phone</label>
                                    <input type="text" name="land_phone" class="form-control"
                                        value="{{ old('land_phone') }}" placeholder="Enter Land Phone">
                                    @error('land_phone')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="country">Country</label>
                                    <input type="text" name="country" class="form-control" value="{{ old('country') }}"
                                        placeholder="Enter Country">
                                    @error('country')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                        placeholder="Enter Email">
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- ID Proof and Related Info -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="id_proof">ID Proof <span>*</span></label>
                                    <select name="id_proof" class="form-control">
                                        <option value="emirates_id"
                                            {{ old('id_proof') == 'emirates_id' ? 'selected' : '' }}>
                                            Emirates ID
                                        </option>
                                        <option value="passport" {{ old('id_proof') == 'passport' ? 'selected' : '' }}>
                                            Passport</option>

                                        <option value="nid" {{ old('id_proof') == 'nid' ? 'selected' : '' }}>
                                            Bangladesh NID</option>

                                        <option value="utility" {{ old('id_proof') == 'utility' ? 'selected' : '' }}>
                                            Utility Bill</option>

                                        <option value="bank_statement"
                                            {{ old('id_proof') == 'bank_statement' ? 'selected' : '' }}>
                                            Bank Statement</option>

                                        <option value="physical_from"
                                            {{ old('id_proof') == 'physical_from' ? 'selected' : '' }}>
                                            Physical form</option>
                                    </select>
                                    @error('id_proof')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="id_number">ID Number<span>*</span></label>
                                    <input type="text" name="id_number" class="form-control"
                                        value="{{ old('id_number') }}" placeholder="Enter ID Number">
                                    @error('id_number')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="valid_up_to">Valid Up To</label>
                                    <input type="date" name="valid_up_to" class="form-control"
                                        value="{{ old('valid_up_to') }}">
                                    @error('valid_up_to')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="document">Upload Document (PDF/Image)</label>
                                    <input type="file" name="document" class="form-control" accept=".pdf,image/*">
                                    @error('document')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Business Related Info -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="trn_no">TRN No</label>
                                    <input type="text" name="trn_no" class="form-control"
                                        value="{{ old('trn_no') }}" placeholder="Enter TRN No">
                                    @error('trn_no')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="trade_license">Trade License</label>
                                    <input type="text" name="trade_license" class="form-control"
                                        value="{{ old('trade_license') }}" placeholder="Enter Trade License">
                                    @error('trade_license')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Referrer and Other Info -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="referrer">Select Referrer:</label>
                                    <select name="referrer" class="form-control select2-container common_select2">
                                        <option value="">Select Referrer</option>
                                        @foreach ($customers as $user)
                                            <option value="{{ $user->id }}"
                                                {{ old('referrer') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('referrer')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            {{-- @dd($referrals) --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="referral_code"> Referrer Package </label>
                                    <select name="referral_code" class="form-control  select2-container common_select2">
                                        <option value="">Select Referrer</option>
                                        @foreach ($referrals as $referral)
                                            <option value="{{ $referral->referral_id }}"
                                                {{ old('referral_code') == $referral->referral_id ? 'selected' : '' }}>
                                                {{ $referral->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('referrer')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="maxtt_per_K">Max TT Per Thousand</label>
                                    <input type="number" name="maxtt_per_K" class="form-control"
                                        value="{{ old('maxtt_per_K') }}" placeholder="Enter Max TT Per Thousand">
                                    @error('maxtt_per_K')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="service_charge">Service Charge</label>
                                    <input type="number" name="service_charge" class="form-control" step="0.001"
                                        value="{{ old('service_charge') }}" placeholder="Enter Service Charge">
                                    @error('service_charge')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="button" class="btn btn-secondary">Close</button>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
