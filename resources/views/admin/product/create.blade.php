@extends('layouts.app')


@section('title', 'Product Create')

@section('content_header')
    <h1>Product Create</h1>
@stop

@section('content')
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-end">
            <a href="{{ route('admin.product.list') }}" class="btn btn-danger">Back</a>
        </div>
    </div>


    <div class="col-md-12">
        <div class="card card-default">
            <div class="card-body">
                <form action="{{ route('admin.product.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <!-- Title Field -->
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
                        <!-- Purity Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="purity">Purity (%) <span>*</span></label>
                                <input type="number" step="any" name="purity" id="purity" class="form-control"
                                    value="{{ old('purity') }}" required>
                                @error('purity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Description Field -->




                        <!-- Price AED Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price_aed">Price (AED) <span>*</span></label>
                                <input type="number" step="any" name="price_aed" id="price_aed" class="form-control"
                                    value="{{ old('price_aed') }}">
                                @error('price_aed')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Price OZ Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price_oz">Price (OZ) <span>*</span></label>
                                <input type="number" step="any" name="price_oz" id="price_oz" class="form-control"
                                    value="{{ old('price_oz') }}" >
                                @error('price_oz')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Price USD Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price_usd">Price (USD) <span>*</span></label>
                                <input type="number" step="any" name="price_usd" id="price_usd" class="form-control"
                                    value="{{ old('price_usd') }}" >
                                @error('price_usd')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Tax Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tax">Tax (%) <span>*</span></label>
                                <input type="number" step="any" name="tax" id="tax" class="form-control"
                                    value="{{ old('tax') }}" >
                                @error('tax')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">Description <span>*</span></label>
                                <textarea name="description" id="description" class="form-control" required>{{ old('description') }}</textarea>
                                @error('description')
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
