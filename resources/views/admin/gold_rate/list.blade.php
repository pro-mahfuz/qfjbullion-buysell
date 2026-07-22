@extends('layouts.app')


@section('title', 'Customer List')

@section('content_header')

    <div class="row">
        <div class="col-md-8">
            <h1> <i class="fas fa-coins"></i> Gold Rate List</h1>
        </div>
        <div class="col-md-4 d-flex justify-content-end">
            <a href="{{ route('admin.gold-rates.update.view') }}" class="btn btn-success">
                <i class="fa fa-plus"></i> Update Gold Price
            </a>
        </div>
    </div>


@stop

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-responsive">
                    <table id="customer-table" class="table table-striped table-bordered table-sm" style="width:100%">
                        <thead>
                            <tr>
                                <th>SL </th>
                                <th>Product Id</th>
                                <th>Product Slug</th>
                                <th>Adjusment Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($goldRates) > 0)
                                <?php $sl = 0; ?>
                                @foreach ($goldRates as $row)
                                    <?php $sl++; ?>
                                    <tr>
                                        <td style="text-align: center;">{{ $sl }}</td>
                                        <td> {{ $row->product_id }} </td>
                                        <td> {{ $row->product_slug }} </td>
                                        <td>{{ $row->adjustment_amount }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
