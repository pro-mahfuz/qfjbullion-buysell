@extends('layouts.master')


@section('title', 'Referral Create')

@section('content_header')
    <h1>Referral Create</h1>
@stop

@section('content')
    <ul class="breadcrumb breadcrumb-style">
		<li class="breadcrumb-item">
		  	<a href="{{route('admin.dashboard.buysell')}}">
				<i class="fas fa-home"></i>
			</a>
		</li>
		<li class="breadcrumb-item active">Referral Add</li>
	</ul>
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-end">
            <a href="{{ route('admin.refferal.list') }}" class="btn btn-danger">Back</a>
        </div>
    </div>


    <div class="col-md-12">
        <div class="card card-default">
            <div class="card-body">
                <form action="{{ route('admin.refferal.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="title">Title <span>*</span></label>
                                <input type="text" name="title" id="title" class="form-control"
                                    value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="total_referral_amount">Total Referral Amount <span>*</span></label>
                                <input type="number" step="any" name="total_referral_amount" id="total_referral_amount"
                                    class="form-control" value="{{ old('total_referral_amount') }}" required>
                                @error('total_referral_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="percentage">Percentage <span>*</span></label>
                                <input type="number" step="any" name="percentage" id="percentage" class="form-control"
                                    value="{{ old('percentage') }}" required>
                                @error('percentage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-4 text-right">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    </div>
@stop



@section('styles')
    <style>
        .permissions-container {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        @media (min-width: 768px) {
            .permissions-container {
                column-count: 2;
                column-gap: 20px;
            }
        }

        @media (min-width: 1200px) {
            .permissions-container {
                column-count: 3;
            }
        }

        .form-check-inline {
            break-inside: avoid;
            display: block;
        }
    </style>

@stop
