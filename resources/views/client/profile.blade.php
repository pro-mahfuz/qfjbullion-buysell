@extends('client.layouts.app')

@section('title', 'Profile')

@section('content_header')
    <h1 class="mb-0">Profile</h1>
@stop
@section('content')
    @php
        $name = session()->get('name');
        $completed = session()->get('is_completed');
    @endphp

    <p>Hello <span><strong>{{ $name }}</strong></span></p>
    <hr>
    <div class="card">
        <div class="card-body">
            <form method="post" action="{{ route('client.profile.save') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="client">
                <div class="row">
                    <!-- Address -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="address">Address<span>*</span></label>
                            <input type="text" name="address" class="form-control" value="{{ old('address') }}"
                                placeholder="Enter Address" required>
                            @error('address')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- City -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city') }}"
                                placeholder="Enter City" required>
                            @error('city')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="phone">Mobile Number<span>*</span></label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}"
                                placeholder="Enter Mobile Number" required>
                            @error('phone')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Land Phone -->
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
                </div>

                <div class="row">
                    <!-- Country -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="country">Country</label>
                            <input type="text" name="country" class="form-control" value="{{ old('country') }}"
                                placeholder="Enter Country" required>
                            @error('country')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- ID Proof -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="id_proof">ID Proof<span>*</span></label>
                            <select name="id_proof" class="form-control" required>
                                <option value="emirates_id" {{ old('id_proof') == 'emirates_id' ? 'selected' : '' }}>
                                    Emirates ID
                                </option>
                                <option value="passport" {{ old('id_proof') == 'passport' ? 'selected' : '' }}>
                                    Passport
                                </option>
                                <option value="nid" {{ old('id_proof') == 'nid' ? 'selected' : '' }}>
                                    Bangladesh NID
                                </option>
                                <option value="utility" {{ old('id_proof') == 'utility' ? 'selected' : '' }}>
                                    Utility Bill
                                </option>
                                <option value="bank_statement" {{ old('id_proof') == 'bank_statement' ? 'selected' : '' }}>
                                    Bank Statement
                                </option>
                                <option value="physical_from" {{ old('id_proof') == 'physical_from' ? 'selected' : '' }}>
                                    Physical Form
                                </option>
                            </select>
                            @error('id_proof')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- ID Number -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="id_number">ID Number<span>*</span></label>
                            <input type="text" name="id_number" class="form-control" value="{{ old('id_number') }}"
                                placeholder="Enter ID Number" required>
                            @error('id_number')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Valid Up To -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="valid_up_to">Valid Up To</label>
                            <input type="date" name="valid_up_to" class="form-control" value="{{ old('valid_up_to') }}"
                                required>
                            @error('valid_up_to')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Document -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="document">Upload Document (PDF/Image)</label>
                            <input type="file" name="document" class="form-control" accept=".pdf,image/*">
                            @error('document')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- TRN No -->
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

                    <!-- Trade License -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="trade_license">Trade License</label>
                            <input type="text" name="trade_license" class="form-control"
                                value="{{ old('trade_license') }}" placeholder="Enter Trade License">
                            @error('trade_license')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="currency">Currency<span>*</span></label>
                            <select name="currency" class="form-control" required>

                                @foreach ($currencies as $id => $currency)
                                    <option value="{{ $currency }}">
                                        {{ $currency }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_proof')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                </div>

                <button type="submit" class="btn btn-primary btn-block mt-3">
                    <span class="fas fa-user-plus"></span> Update
                </button>
            </form>
        </div>
    </div>
@stop
