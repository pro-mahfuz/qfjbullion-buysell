@extends('layouts.master')

@section('title', 'Edit Customer')
@section('customer_list', 'active')

@section('content')
    <style>
        .customer-edit-hero { background: linear-gradient(120deg, #102a43, #1f5f74); border-radius: .75rem; color: #fff; padding: 1.35rem 1.5rem; }
        .customer-edit-hero h3 { color: #fff; font-weight: 700; margin: 0; }
        .customer-edit-hero p { color: rgba(255,255,255,.78); }
        .customer-edit-card { border: 0; border-radius: .75rem; box-shadow: 0 8px 24px rgba(16, 42, 67, .08); }
        .customer-edit-form label { color: #344054; font-size: .82rem; font-weight: 600; }
        .customer-edit-form .form-control { min-height: 40px; border-color: #d0d5dd; }
        .customer-edit-form .form-control:focus { border-color: #1f5f74; box-shadow: 0 0 0 .2rem rgba(31, 95, 116, .12); }
        .customer-edit-form .form-control[readonly] { background: #f8fafc; color: #667085; }
        .customer-edit-section { border: 1px solid #e4e7ec; border-radius: .6rem; margin-bottom: 1.25rem; overflow: hidden; }
        .customer-edit-section__title { background: #f8fafc; border-bottom: 1px solid #e4e7ec; color: #102a43; font-size: .9rem; font-weight: 700; padding: .75rem 1rem; }
        .customer-edit-section__body { padding: 1rem; }
        .customer-edit-danger { background: #fff7f7; border: 1px solid #fecdca; border-radius: .6rem; padding: 1rem; }
        .customer-edit-danger h6 { color: #b42318; font-weight: 700; margin: 0 0 .2rem; }
    </style>

    <section class="section">
        <ul class="breadcrumb breadcrumb-style">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.buysell') }}"><i class="fas fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.customer.list') }}">Customer List</a></li>
            <li class="breadcrumb-item active">Edit Customer</li>
        </ul>

        <div class="customer-edit-hero d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h3>Edit Customer</h3>
                <p class="mb-0 mt-1">Update {{ $customer->name }}’s account and trading details.</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('admin.customer.list') }}" class="btn btn-light">Customer List</a>
            </div>
        </div>

        <form action="{{ route('admin.customer.update') }}" method="POST" class="customer-edit-form">
            @csrf
            <input type="hidden" name="id" value="{{ $customer->id }}">
            <input type="hidden" name="customer_code" value="{{ $customer->customer_code }}">

            <div class="card customer-edit-card">
                <div class="card-body p-4">
                    <div class="customer-edit-section">
                        <div class="customer-edit-section__title">Account information</div>
                        <div class="customer-edit-section__body"><div class="row">
                            <div class="col-md-4"><div class="form-group"><label for="customer_code">Customer code</label><input type="text" id="customer_code" class="form-control" value="{{ $customer->customer_code }}" readonly></div></div>
                            <div class="col-md-4"><div class="form-group"><label for="name">Customer name <span class="text-danger">*</span></label><input type="text" name="name" id="name" class="form-control" value="{{ old('name', $customer->name) }}" required>@error('name')<small class="text-danger">{{ $message }}</small>@enderror</div></div>
                            <div class="col-md-4"><div class="form-group"><label for="type">Account type <span class="text-danger">*</span></label><select name="type" id="type" class="form-control" required><option value="customer" @selected(old('type', $customer->type) === 'customer')>Customer</option><option value="client" @selected(old('type', $customer->type) === 'client')>IB</option></select>@error('type')<small class="text-danger">{{ $message }}</small>@enderror</div></div>
                        </div></div>
                    </div>

                    <div class="customer-edit-section">
                        <div class="customer-edit-section__title">Contact information</div>
                        <div class="customer-edit-section__body"><div class="row">
                            <div class="col-md-6"><div class="form-group"><label for="address">Address <span class="text-danger">*</span></label><input type="text" name="address" id="address" class="form-control" value="{{ old('address', $customer->address) }}" required>@error('address')<small class="text-danger">{{ $message }}</small>@enderror</div></div>
                            <div class="col-md-3"><div class="form-group"><label for="city">City</label><input type="text" name="city" id="city" class="form-control" value="{{ old('city', $customer->city) }}"></div></div>
                            <div class="col-md-3"><div class="form-group"><label for="country">Country</label><input type="text" name="country" id="country" class="form-control" value="{{ old('country', $customer->country) }}"></div></div>
                            <div class="col-md-4"><div class="form-group"><label for="phone">Mobile number <span class="text-danger">*</span></label><input type="tel" name="phone" id="phone" class="form-control" value="{{ old('phone', $customer->phone) }}" required>@error('phone')<small class="text-danger">{{ $message }}</small>@enderror</div></div>
                            <div class="col-md-4"><div class="form-group"><label for="land_phone">Land phone</label><input type="tel" name="land_phone" id="land_phone" class="form-control" value="{{ old('land_phone', $customer->land_phone) }}"></div></div>
                            <div class="col-md-4"><div class="form-group"><label for="email">Email</label><input type="email" name="email" id="email" class="form-control" value="{{ old('email', $customer->email) }}"></div></div>
                        </div></div>
                    </div>

                    <div class="customer-edit-section">
                        <div class="customer-edit-section__title">Identification and documents</div>
                        <div class="customer-edit-section__body"><div class="row">
                            <div class="col-md-4"><div class="form-group"><label for="id_proof">ID proof <span class="text-danger">*</span></label><select name="id_proof" id="id_proof" class="form-control" required>@foreach (['emirates_id' => 'Emirates ID', 'passport' => 'Passport', 'nid' => 'Bangladesh NID', 'utility' => 'Utility Bill', 'bank_statement' => 'Bank Statement', 'physical_from' => 'Physical form'] as $value => $label)<option value="{{ $value }}" @selected(old('id_proof', $customer->id_proof) === $value)>{{ $label }}</option>@endforeach</select>@error('id_proof')<small class="text-danger">{{ $message }}</small>@enderror</div></div>
                            <div class="col-md-4"><div class="form-group"><label for="id_number">ID number <span class="text-danger">*</span></label><input type="text" name="id_number" id="id_number" class="form-control" value="{{ old('id_number', $customer->id_number) }}" required>@error('id_number')<small class="text-danger">{{ $message }}</small>@enderror</div></div>
                            <div class="col-md-4"><div class="form-group"><label for="valid_up_to">Valid up to</label><input type="date" name="valid_up_to" id="valid_up_to" class="form-control" value="{{ old('valid_up_to', $customer->valid_up_to) }}"></div></div>
                        </div></div>
                    </div>

                    <div class="customer-edit-section">
                        <div class="customer-edit-section__title">Business and trading terms</div>
                        <div class="customer-edit-section__body"><div class="row">
                            <div class="col-md-3"><div class="form-group"><label for="trn_no">TRN number</label><input type="text" name="trn_no" id="trn_no" class="form-control" value="{{ old('trn_no', $customer->trn_no) }}"></div></div>
                            <div class="col-md-3"><div class="form-group"><label for="trade_license">Trade license</label><input type="text" name="trade_license" id="trade_license" class="form-control" value="{{ old('trade_license', $customer->trade_license) }}"></div></div>
                            <div class="col-md-3"><div class="form-group"><label for="referrer_name">Referrer</label><input type="text" id="referrer_name" class="form-control" value="{{ $customer->refer_user->name ?? 'No referrer' }}" readonly></div></div>
                            <div class="col-md-3"><div class="form-group"><label for="maxtt_per_K">Max TT per 1000 AED <span class="text-danger">*</span></label><input type="number" inputmode="decimal" step="0.001" name="maxtt_per_K" id="maxtt_per_K" class="form-control" value="{{ old('maxtt_per_K', $customer->maxtt_per_K) }}" required>@error('maxtt_per_K')<small class="text-danger">{{ $message }}</small>@enderror</div></div>
                            <div class="col-md-3"><div class="form-group mb-0"><label for="service_charge">Service charge ($) <span class="text-danger">*</span></label><input type="number" inputmode="decimal" step="0.001" name="service_charge" id="service_charge" class="form-control" value="{{ old('service_charge', $customer->service_charge) }}" required>@error('service_charge')<small class="text-danger">{{ $message }}</small>@enderror</div></div>
                        </div></div>
                    </div>

                    <div class="customer-edit-danger d-flex flex-wrap justify-content-between align-items-center">
                        <div><h6>Delete customer</h6><small class="text-muted">Deletion is only available when the customer has no buy/sell, deposit, or withdrawal records.</small></div>
                        <button type="button" class="btn btn-outline-danger mt-2 mt-md-0" onclick="openDeleteModal({{ $customer->id }})">Delete Customer</button>
                    </div>
                </div>
                <div class="card-footer bg-white border-top d-flex justify-content-end">
                    <a href="{{ route('admin.customer.list') }}" class="btn btn-outline-secondary mr-2">Cancel</a>
                    <button type="submit" class="btn btn-success px-4">Save Changes</button>
                </div>
            </div>
        </form>
    </section>
@stop

@section('page_js')
    <script>
        function openDeleteModal(customerId) {
            Swal.fire({
                title: 'Enter Password', input: 'password', inputAttributes: { autocapitalize: 'off', placeholder: 'Enter your password' }, showCancelButton: true, confirmButtonText: 'Delete', showLoaderOnConfirm: true,
                preConfirm: (password) => {
                    if (!password) { Swal.showValidationMessage('Password is required.'); return false; }
                    return $.ajax({ url: '{{ route('admin.password.check') }}', type: 'POST', data: { password: password, _token: '{{ csrf_token() }}' } }).then(function(response) {
                        if (!response.success) { Swal.showValidationMessage(response.message || 'Password is incorrect.'); return false; }
                        return response;
                    }, function(xhr) {
                        var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Unable to verify the password.';
                        Swal.showValidationMessage(message); return false;
                    });
                }
            }).then(function(result) { if (result.isConfirmed) { deleteCustomer(customerId); } });
        }

        function deleteCustomer(customerId) {
            $.ajax({
                url: '{{ route('admin.customer.delete', '') }}/' + customerId, type: 'DELETE', data: { _token: '{{ csrf_token() }}' },
                success: function() { Swal.fire({ icon: 'success', text: 'Customer deleted successfully.' }).then(function() { window.location.href = '{{ route('admin.customer.list') }}'; }); },
                error: function(xhr) {
                    var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred while deleting the customer.';
                    Swal.fire({ icon: 'error', text: message });
                }
            });
        }
    </script>
@endsection
