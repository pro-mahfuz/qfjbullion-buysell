@extends('layouts.app')


@section('title', 'Project Create')

@section('content_header')
    <h1>Project Create</h1>
@stop

@section('content')
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-end">
            <a href="{{ route('admin.project.list') }}" class="btn btn-danger">Back</a>
        </div>
    </div>


    <div class="col-md-12">
        <div class="card card-default">
            <div class="card-body">
                <form action="{{ route('admin.project.store') }}" method="POST" enctype="multipart/form-data">
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
                                <label for="description">Desciption <span>*</span></label>
                                <input type="text" name="description" id="description" class="form-control"
                                    value="{{ old('description') }}" required>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>



                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estimated_revenue">Estimated Revenue<span>*</span></label>
                                <input type="number" step="any" name="estimated_revenue" id="estimated_revenue"
                                    class="form-control" value="{{ old('estimated_revenue') }}">
                                @error('estimated_revenue')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="image">Image <span>*</span></label>
                                <input type="file" name="image" id="image" class="form-control"
                                    value="{{ old('image') }}">
                                @error('image')
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
