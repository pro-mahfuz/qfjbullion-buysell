@extends('layouts.app')
@section('content_header')
    <h1>Deposit</h1>
@stop


@section('content')

    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-end">

            <a class="btn btn-primary" href="{{ route('admin.supplier.deposit') }}"><i class="fa fa-plus"></i>
                Create New Deposit </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
                <div class="x_content">
                    <div class="card-box table-responsive">
                        <table class="table table-striped table-bordered common_datatable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Ref No </th>
                                    <th>Supplier</th>
                                    <th>Description</th>
                                    <th>Deposit</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($deposits) > 0)
                                    <?php $sl = 0; ?>
                                    @foreach ($deposits as $row)
                                        <?php $sl++; ?>
                                        <tr>
                                            <td>{{ $sl }}</td>

                                            <td>{{ date('d/M/Y H:i', strtotime($row->created_at)) }}</td>
                                            <td>{{ strtoupper($row->type) }}</td>
                                            <td>{{ $row->ref_no }}</td>
                                            <td>{{ $row->supplier->full_name }}</td>
                                            <td>{{ $row->note }}</td>
                                            <td>{{ number_format($row->deposit_amount, 3) }} AED</td>
                                            <td>
                                                <a href="{{ route('admin.supplier.deposit.edit', $row->id) }}"
                                                    class="btn btn-info btn-sm"><i class="fa fa-edit"></i></a>
                                                <form action="{{ route('admin.deposit-remove', $row->id) }}" method="POST"
                                                    style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Are you sure want to delete?');"><i
                                                            class="fa fa-trash"></i></button>
                                                </form>

                                            </td>

                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
