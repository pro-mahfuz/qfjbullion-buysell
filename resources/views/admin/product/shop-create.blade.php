@extends('layouts.app')


@section('title', ' Shop Product Create')

@section('content_header')
    <h1>Shop Product Create</h1>
@stop

@section('content')
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-end">
            <a href="{{ route('admin.product.shop.list') }}" class="btn btn-danger">Back</a>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card card-default">
            <div class="card-body">
                <form action="{{ route('admin.product.shop.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- Categotry -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category_id">Category <span>*</span></label>
                                <select name="category_id" id="category_id" class="form-control" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
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
                        <!-- weight Field -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="weight">Weight <span>*</span></label>
                                <input type="number" step="any" name="weight" id="weight" class="form-control"
                                    value="{{ old('weight') }}" required>
                                @error('weight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Weight Type eg: kg , gm -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="weight_type">Weight Type
                                    <span>*</span></label>
                                <select name="weight_type" id="weight_type" class="form-control" required>
                                    <option value="">Select Weight Type</option>
                                    <option value="kg">Kg</option>
                                    <option value="gm">Gm</option>
                                </select>
                                @error('weight_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Qty -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="qty">Qty <span>*</span></label>
                                <input type="number" name="qty" id="qty" class="form-control"
                                    value="{{ old('qty') }}" required>
                                @error('qty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        <!-- Image upload -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="image">Image <span>*</span></label>
                                <input type="file" name="image" id="image"
                                    class="form-control @error('image') is-invalid @enderror" required>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <input type="hidden" name="is_shop" value="1">

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
