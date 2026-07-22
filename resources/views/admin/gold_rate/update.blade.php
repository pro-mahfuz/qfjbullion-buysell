@extends('layouts.app')


@section('title', 'Customer Edit')

@section('content_header')
    <h1>Gold Update</h1>
@stop

@section('content')
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-end">
            <a href="{{ route('admin.gold-rates.list') }}" class="btn btn-danger">Back</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                    <form action="{{ route('admin.gold-rates.update.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                @foreach ($goldRates as $adjustment)
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="product_slug_{{ $adjustment->id }}"></label>
                                            {{-- <input type="text" name="adjustments[{{ $adjustment->id }}][product_slug]"
                                                id="product_slug_{{ $adjustment->id }}"
                                                value="{{ $adjustment->product_slug }}" class="form-control" readonly> --}}
                                            <h3><strong>{{ $adjustment->product_slug }} :: </strong></h3>
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="adjustment_amount_{{ $adjustment->id }}">Adjustment Amount:</label>
                                            <input type="number" step="0.01"
                                                name="adjustments[{{ $adjustment->id }}][adjustment_amount]"
                                                id="adjustment_amount_{{ $adjustment->id }}"
                                                value="{{ $adjustment->adjustment_amount }}" class="form-control">

                                            <input type="hidden" name="adjustments[{{ $adjustment->id }}][id]"
                                                value="{{ $adjustment->id }}">
                                        </div>
                                    </div>
                                    <div class="w-100"></div>
                                @endforeach
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Submit </button>
                            </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@stop
