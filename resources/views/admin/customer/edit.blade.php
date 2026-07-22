@extends('layouts.master')


@section('title', 'Customer Edit')

@section('content_header')
    <h1>Customer Edit</h1>
@stop

@section('content')
<ul class="breadcrumb breadcrumb-style">
		<li class="breadcrumb-item">
		  	<a href="{{route('admin.dashboard.buysell')}}">
				<i class="fas fa-home"></i>
			</a>
		</li>
		<li class="breadcrumb-item active">Customer Edit</li>
	</ul>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Customer Edit</h5>
                    <a href="{{ route('admin.customer.list') }}" class="btn btn-danger">Back</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.customer.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" class="form-control" value="{{ $customer->id }}">

                        <div class="card-body">
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customer_code">Customer Code</label>
                                        <input type="text" name="customer_code" class="form-control"
                                            value="{{ $customer->customer_code ?? null }} " readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Full Name <span>*</span></label>
                                        <input type="text" name="name" value="{{ $customer->name }}"
                                            class="form-control" required="">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="type">Type<span>*</span></label>
                                        <select name="type" class="form-control" required>
                                            <option value="client" {{ $customer->type == 'client' ? 'selected' : '' }}>
                                                IB</option>
                                            <option value="customer" {{ $customer->type == 'customer' ? 'selected' : '' }}>
                                                Customer
                                            </option>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <input type="text" name="address" value="{{ $customer->address }}"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="city">City</label>
                                        <input type="text" name="city" value="{{ $customer->city }}"
                                            class="form-control">
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Mobile Number </label>
                                        <input type="text" name="phone" value="{{ $customer->phone }}"
                                            class="form-control">
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="land_phone">Land Phone</label>
                                        <input type="text" name="land_phone" value="{{ $customer->land_phone }}"
                                            class="form-control">
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="country">Country</label>
                                        <input type="text" name="country" value="{{ $customer->country }}"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ $customer->email }}">
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_proof">ID Proof<span>*</span></label>
                                        <select name="id_proof" class="form-control" required>



                                            <option value="emirates_id"
                                                {{ $customer->id_proof == 'emirates_id' ? 'selected' : '' }}>
                                                Emirates ID
                                            </option>
                                            <option value="passport"
                                                {{ $customer->id_proof == 'passport' ? 'selected' : '' }}>
                                                Passport</option>

                                            <option value="nid" {{ $customer->id_proof == 'nid' ? 'selected' : '' }}>
                                                Bangladesh NID</option>

                                            <option value="utility"
                                                {{ $customer->id_proof == 'utility' ? 'selected' : '' }}>
                                                Utility Bill</option>

                                            <option value="bank_statement"
                                                {{ $customer->id_proof == 'bank_statement' ? 'selected' : '' }}>
                                                Bank Statement</option>

                                            <option value="physical_from"
                                                {{ $customer->id_proof == 'physical_from' ? 'selected' : '' }}>
                                                Physical form</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="valid_up_to">Valid Up To</label>
                                        <input type="date" name="valid_up_to" class="form-control"
                                            value="{{ $customer->valid_up_to }}">
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_number">ID Number</label>
                                        <input type="text" name="id_number" class="form-control"
                                            value="{{ $customer->id_number }}">
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="trn_no">TRN No</label>
                                        <input type="text" name="trn_no" class="form-control"
                                            value="{{ $customer->trn_no }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="trade_license">Trade Licence</label>
                                        <input type="text" name="trade_license" class="form-control"
                                            value="{{ $customer->trade_license }}">
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="referrer">Referrer</label>
                                        <input type="text" name="referrer" class="form-control"
                                            value="{{ $customer->refer_user->name ?? null }} " disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="maxtt_per_K">Max TT Per Thousand</label>
                                        <input type="text" name="maxtt_per_K" class="form-control"
                                            value="{{ $customer->maxtt_per_K }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="service_charge">Service charge</label>
                                        <input name="service_charge" type="number" placeholder="Enter service charge"
                                            step="0.001" value="{{ $customer->service_charge }}" class="form-control">
                                        @error('service_charge')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="modal-footer">

                            <button type="submit" class="btn btn-success">Submit </button>
                        </div>
                    </form>
                    <button class="btn btn-danger" onclick="openDeleteModal({{ $customer->id }})">
                        Delete Customer
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
    <script>
        function openDeleteModal(customerId) {
            Swal.fire({
                title: 'Enter Password',
                input: 'password',
                inputAttributes: {
                    autocapitalize: 'off',
                    placeholder: 'Enter your password'
                },
                showCancelButton: true,
                confirmButtonText: 'Delete',
                showLoaderOnConfirm: true,
                preConfirm: (password) => {
                    if (!password) {
                        Swal.showValidationMessage('Password is required.');
                        return false; // Prevent modal from closing
                    }

                    // Verify password using AJAX
                    return $.ajax({
                        url: '{{ route('admin.password.check') }}',
                        type: 'POST',
                        data: {
                            password: password,
                            _token: '{{ csrf_token() }}'
                        }
                    }).done(function(response) {
                        if (!response.success) {
                            Swal.fire({
                                icon: 'error',
                                text: 'Password is incorrect.'
                            });
                        } else {
                            deleteCustomer(customerId); // Proceed to delete
                        }
                    }).fail(function() {
                        Swal.fire({
                            icon: 'error',
                            text: 'An error occurred while verifying the password.'
                        });
                    });
                }
            });
        }

        // Function to delete the customer after successful password verification
        function deleteCustomer(customerId) {
            $.ajax({
                url: '{{ route('admin.customer.delete', '') }}/' + customerId,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        text: 'Customer deleted successfully.'
                    }).then(() => {
                        window.location.href = '{{ route('admin.customer.list') }}';
                    });
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        text: 'An error occurred while deleting the customer.'
                    });
                }
            });
        }
    </script>
@endpush
