@extends('client.layouts.app')

@section('title', 'Deposit List')

@section('content_header')
    <h1>
        @if ($type == 'deposit')
            Deposit List
        @else
            Withdraw List
        @endif
    </h1>
@stop


@section('content')

    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-end">
            {{-- <a href="{{ route('client.deposit.create') }}" class="btn btn-success">
                <i class="fa fa-plus"></i> Create Deposit
            </a> --}}
            @if ($type == 'deposit')
                <a href="#" class="btn btn-sm btn-primary load_modal" data-toggle="modal"
                    data-action="{{ route('client.deposit.create', ['type' => 'deposit']) }}">
                    New Deposit
                </a>
            @else
                <a href="#" class="btn btn-sm btn-primary load_modal" data-toggle="modal"
                    data-action="{{ route('client.deposit.create', ['type' => 'withdraw']) }}">
                    New Withdraw
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-responsive">
                    <table id="customer-table" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>
                                    @if ($type == 'deposit')
                                        Deposit
                                    @else
                                        Withdraw
                                    @endif
                                </th>
                                <th>Status</th>
                                {{-- <th style="width: 150px;">Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @if ($deposits)
                                <?php $sl = 0; ?>
                                @foreach ($deposits as $row)
                                    <?php $sl++; ?>
                                    <tr>
                                        <td>{{ $sl }}</td>
                                        <td>{{ $row['transaction_date'] }}</td>
                                        <td>{{ $row['actual_amount'] }}</td>
                                        <td>
                                            @if ($row['status'] == 1)
                                                <span class="badge badge-primary">Success</span>
                                            @else
                                                <span class="badge badge-danger">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center">No data found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop


@section('js')
    <script>
        $(document).ready(function() {
            $('#customer-table').DataTable();
        });
    </script>
@stop
