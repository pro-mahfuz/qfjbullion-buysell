@extends('layouts.master')


@section('title', 'Permisison Create')

@section('content_header')
    <h1>Permisison Create</h1>
@stop


@section('content')
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-end">
            <a href="{{ route('admin.permission.list') }}" class="btn btn-danger">Back</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                    <form action="{{ route('admin.permission.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Permission Name <span>*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                        required="">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            {{-- <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Description <span>*</span></label>
                                    <input type="text" name="description" class="form-control"
                                        value="{{ old('description') }}" required="">
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}
                        </div>
                        <div class="ln_solid"></div>
                        <div class="item form-group" style="float: right">
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
