@extends('layouts.master')

@section('title', 'Customer Add')
@section('customer_create', 'active')

@section('content')
    <style>
        .customer-form-hero { background: linear-gradient(120deg, #102a43, #1f5f74); border-radius: .75rem; color: #fff; padding: 1.35rem 1.5rem; }
        .customer-form-hero h3 { color: #fff; font-weight: 700; margin: 0; }
        .form-section { border: 1px solid #e4e7ec; border-radius: .6rem; margin-bottom: 1.25rem; overflow: hidden; }
        .form-section__title { background: #f8fafc; border-bottom: 1px solid #e4e7ec; color: #102a43; font-size: .9rem; font-weight: 700; padding: .75rem 1rem; }
        .form-section__body { padding: 1rem; }
        .customer-create-form label { color: #344054; font-size: .82rem; font-weight: 600; }
        .customer-create-form .form-control { min-height: 38px; }
    </style>

    <section class="section">
        <ul class="breadcrumb breadcrumb-style">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.buysell') }}"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.customer.list') }}">Customer List</a></li>
            <li class="breadcrumb-item active">Customer Add</li>
        </ul>

        <div class="customer-form-hero d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div><h3>Customer Add</h3><p class="mb-0 mt-1">Create a customer account and configure its trading terms.</p></div>
            <div class="mt-3 mt-md-0"><button type="button" class="btn btn-danger mr-2" onclick="window.history.back()">Back</button><a href="{{ route('admin.customer.list') }}" class="btn btn-light">Customer List</a></div>
        </div>

        <form action="{{ route('admin.customer.store') }}" method="POST" enctype="multipart/form-data" class="customer-create-form">
            @csrf
            <div class="card"><div class="card-body">
                <div class="form-section">
                    <div class="form-section__title">Account information</div>
                    <div class="form-section__body"><div class="row">
                        <div class="col-md-4"><div class="form-group"><label for="customer_code">Customer code <span class="text-danger">*</span></label><input type="text" name="customer_code" id="customer_code" class="form-control" value="{{ old('customer_code', $generatedCodeWithPrefixSJ) }}" required>@error('customer_code')<small class="text-danger">{{ $message }}</small>@enderror</div></div>
                        <div class="col-md-4"><div class="form-group"><label for="name">Full name <span class="text-danger">*</span></label><input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>@error('name')<small class="text-danger">{{ $message }}</small>@enderror</div></div>
                        <div class="col-md-4"><div class="form-group"><label for="type">Account type <span class="text-danger">*</span></label><select name="type" id="type" class="form-control" required><option value="customer" @selected(old('type', 'customer') === 'customer')>Customer</option><option value="client" @selected(old('type') === 'client')>IB</option></select>@error('type')<small class="text-danger">{{ $message }}</small>@enderror</div></div>
                    </div></div>
                </div>

                <div class="form-section">
                    <div class="form-section__title">Contact information</div>
                    <div class="form-section__body"><div class="row">
                        <div class="col-md-6"><div class="form-group"><label for="address">Address <span class="text-danger">*</span></label><input type="text" name="address" id="address" class="form-control" value="{{ old('address') }}" required>@error('address')<small class="text-danger">{{ $message }}</small>@enderror</div></div>
                        <div class="col-md-3"><div class="form-group"><label for="city">City</label><input type="text" name="city" id="city" class="form-control" value="{{ old('city') }}"></div></div>
                        <div class="col-md-3"><div class="form-group"><label for="country">Country</label><input type="text" name="country" id="country" class="form-control" value="{{ old('country') }}"></div></div>
                        <div class="col-md-4"><div class="form-group"><label for="phone">Mobile number <span class="text-danger">*</span></label><input type="tel" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" required>@error('phone')<small class="text-danger">{{ $message }}</small>@enderror</div></div>
                        <div class="col-md-4"><div class="form-group"><label for="land_phone">Land phone</label><input type="tel" name="land_phone" id="land_phone" class="form-control" value="{{ old('land_phone') }}"></div></div>
                        <div class="col-md-4"><div class="form-group"><label for="email">Email</label><input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}"></div></div>
                    </div></div>
                </div>

                <div class="form-section">
                    <div class="form-section__title">Identification and documents</div>
                    <div class="form-section__body"><div class="row">
                        <div class="col-md-3"><div class="form-group"><label for="id_proof">ID proof <span class="text-danger">*</span></label><select name="id_proof" id="id_proof" class="form-control" required>@foreach (['emirates_id' => 'Emirates ID', 'passport' => 'Passport', 'nid' => 'Bangladesh NID', 'utility' => 'Utility Bill', 'bank_statement' => 'Bank Statement', 'physical_from' => 'Physical form'] as $value => $label)<option value="{{ $value }}" @selected(old('id_proof', 'emirates_id') === $value)>{{ $label }}</option>@endforeach</select>@error('id_proof')<small class="text-danger">{{ $message }}</small>@enderror</div></div>
                        <div class="col-md-3"><div class="form-group"><label for="id_number">ID number <span class="text-danger">*</span></label><input type="text" name="id_number" id="id_number" class="form-control" value="{{ old('id_number') }}" required>@error('id_number')<small class="text-danger">{{ $message }}</small>@enderror</div></div>
                        <div class="col-md-3"><div class="form-group"><label for="valid_up_to">Valid up to</label><input type="date" name="valid_up_to" id="valid_up_to" class="form-control" value="{{ old('valid_up_to') }}"></div></div>
                        <div class="col-md-3"><div class="form-group"><label for="document">Document (PDF/image)</label><input type="file" name="document" id="document" class="form-control" accept=".pdf,image/*"></div></div>
                    </div></div>
                </div>

                <div class="form-section">
                    <div class="form-section__title">Business, referral and trading terms</div>
                    <div class="form-section__body"><div class="row">
                        <div class="col-md-3"><div class="form-group"><label for="trn_no">TRN number</label><input type="text" name="trn_no" id="trn_no" class="form-control" value="{{ old('trn_no') }}"></div></div>
                        <div class="col-md-3"><div class="form-group"><label for="trade_license">Trade license</label><input type="text" name="trade_license" id="trade_license" class="form-control" value="{{ old('trade_license') }}"></div></div>
                        <div class="col-md-3"><div class="form-group"><label for="referrer">Referrer</label><select name="referrer" id="referrer" class="form-control select2-container common_select2"><option value="">No referrer</option>@foreach ($customers as $user)<option value="{{ $user->id }}" @selected(old('referrer') == $user->id)>{{ $user->name }}</option>@endforeach</select></div></div>
                        <div class="col-md-3"><div class="form-group"><label for="referral_code">Referrer package</label><select name="referral_code" id="referral_code" class="form-control select2-container common_select2"><option value="">No package</option>@foreach ($referrals as $referral)<option value="{{ $referral->referral_id }}" @selected(old('referral_code') == $referral->referral_id)>{{ $referral->title }}</option>@endforeach</select></div></div>
                        <div class="col-md-3"><div class="form-group"><label for="maxtt_per_K">Max TT per thousand <span class="text-danger">*</span></label><input type="number" inputmode="decimal" step="0.001" name="maxtt_per_K" id="maxtt_per_K" class="form-control" value="{{ old('maxtt_per_K') }}" required>@error('maxtt_per_K')<small class="text-danger">{{ $message }}</small>@enderror</div></div>
                        <div class="col-md-3"><div class="form-group"><label for="service_charge">Service charge ($) <span class="text-danger">*</span></label><input type="number" inputmode="decimal" step="0.001" name="service_charge" id="service_charge" class="form-control" value="{{ old('service_charge') }}" required>@error('service_charge')<small class="text-danger">{{ $message }}</small>@enderror</div></div>
                    </div></div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end"><a href="{{ route('admin.customer.list') }}" class="btn btn-outline-secondary mr-2">Cancel</a><button type="submit" class="btn btn-success px-4">Create Customer</button></div></div>
        </form>
    </section>
@stop
