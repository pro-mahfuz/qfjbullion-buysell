@extends('vendor.client.auth.auth-page', ['auth_type' => 'register'])

@php($login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login'))
@php($register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register'))


@php($login_url = route('client.login'))
@php($register_url = route('client.register.save'))


@section('auth_header', __('adminlte::adminlte.register_message'))

@section('auth_body')
    <form action="{{ $register_url }}" method="post">
        @csrf
        <input type="hidden" name="link" value="{{ request('link') }}">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="name">Full Name <span>*</span></label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name') }}"
                        placeholder="Enter Full Name">
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="type">Type <span>*</span></label>
                    <select name="type" class="form-control" required>
                        <option value="client" {{ old('type') == 'client' ? 'selected' : '' }}>IB
                        </option>
                        <option value="customer" {{ old('type') == 'customer' ? 'selected' : '' }}>
                            Customer</option>
                    </select>
                    @error('type')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <!-- Contact Information -->
            <div class="col-md-3">
                <div class="form-group">
                    <label for="address">Address<span>*</span></label>
                    <input type="text" name="address" class="form-control" value="{{ old('address') }}"
                        placeholder="Enter Address">
                    @error('address')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city') }}"
                        placeholder="Enter City">
                    @error('city')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="phone">Mobile Number<span>*</span></label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}"
                        placeholder="Enter Mobile Number">
                    @error('phone')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="land_phone">Land Phone</label>
                    <input type="text" name="land_phone" class="form-control" value="{{ old('land_phone') }}"
                        placeholder="Enter Land Phone">
                    @error('land_phone')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="country">Country</label>
                    <input type="text" name="country" class="form-control" value="{{ old('country') }}"
                        placeholder="Enter Country">
                    @error('country')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md-3">
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
            <div class="col-md-3">
                <div class="form-group">
                    <label for="id_proof">ID Proof <span>*</span></label>
                    <select name="id_proof" class="form-control">
                        <option value="emirates_id" {{ old('id_proof') == 'emirates_id' ? 'selected' : '' }}>
                            Emirates ID
                        </option>
                        <option value="passport" {{ old('id_proof') == 'passport' ? 'selected' : '' }}>
                            Passport</option>

                        <option value="nid" {{ old('id_proof') == 'nid' ? 'selected' : '' }}>
                            Bangladesh NID</option>

                        <option value="utility" {{ old('id_proof') == 'utility' ? 'selected' : '' }}>
                            Utility Bill</option>

                        <option value="bank_statement" {{ old('id_proof') == 'bank_statement' ? 'selected' : '' }}>
                            Bank Statement</option>

                        <option value="physical_from" {{ old('id_proof') == 'physical_from' ? 'selected' : '' }}>
                            Physical form</option>
                    </select>
                    @error('id_proof')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="id_number">ID Number<span>*</span></label>
                    <input type="text" name="id_number" class="form-control" value="{{ old('id_number') }}"
                        placeholder="Enter ID Number">
                    @error('id_number')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="valid_up_to">Valid Up To</label>
                    <input type="date" name="valid_up_to" class="form-control" value="{{ old('valid_up_to') }}">
                    @error('valid_up_to')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="document">Upload Document (PDF/Image)</label>
                    <input type="file" name="document" class="form-control" accept=".pdf,image/*">
                    @error('document')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <!-- Business Related Info -->
            <div class="col-md-3">
                <div class="form-group">
                    <label for="trn_no">TRN No</label>
                    <input type="text" name="trn_no" class="form-control" value="{{ old('trn_no') }}"
                        placeholder="Enter TRN No">
                    @error('trn_no')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="trade_license">Trade License</label>
                    <input type="text" name="trade_license" class="form-control" value="{{ old('trade_license') }}"
                        placeholder="Enter Trade License">
                    @error('trade_license')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>


            <div class="col-md-3">
                <div class="form-group">
                    <label for="password">password</label>

                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                        placeholder="{{ __('adminlte::adminlte.password') }}">

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>


            <div class="col-md-3">
                <label for="password_confirmation">Password Confirmation</label>

                <input type="password" name="password_confirmation"
                    class="form-control @error('password_confirmation') is-invalid @enderror"
                    placeholder="{{ __('adminlte::adminlte.retype_password') }}">


                @error('password_confirmation')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

        </div>
        <hr>
        <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
            <span class="fas fa-user-plus"></span>
            {{ __('adminlte::adminlte.register') }}
        </button>

    </form>
@stop
