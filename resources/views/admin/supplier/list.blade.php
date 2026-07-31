@extends('layouts.app')

@section('content_header')

    <h1>{{ Str::ucfirst($type == 0 ? 'Supplier' : 'Client') }} List</h1>
@stop

@section('content')

    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-end">

            <ul class="nav navbar-right panel_toolbox">
                <li>
                    {{-- <a class="btn btn-primary" href="{{ route('admin.supplier.deposit') }}"><i class="fa fa-plus"></i>/<i
                            class="fa fa-minus"></i> Deposit / Withdraw </a>
                    <a class="btn btn-success" href="{{ route('admin.purchase.create') }}"><i class="fa fa-plus"></i>
                        Purchase </a>
                    <a class="btn btn-primary" href="{{ route('admin.sale') }}"><i class="fa fa-plus"></i> Fix Gold
                    </a> --}}
                    @can('supplier_add')
                        <a href="#" class="btn btn-success load_modal" data-toggle="modal"
                            data-action="{{ route('admin.supplier.create', ['type' => $type]) }}"><i class="fa fa-plus"></i> New
                            {{ Str::ucfirst($type == 0 ? 'Supplier' : 'Client') }} </a>
                    @endcan
                </li>
            </ul>

        </div>
    </div>


    <div class="row">
        <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
                <div class="card">
                    <div class="card-body table-responsive">
                        <table id="common_table" class="table table-striped table-bordered common_datatable"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Full Nane</th>
                                    {{-- <th>Mobile</th> --}}
                                    <th>Address</th>
                                    {{-- <th>Email</th> --}}
                                    {{-- <th>TRN NO</th> --}}
                                    {{-- <th>Narretion</th> --}}
                                    <th style="text-align: right;">Initial</th>
                                    <th style="text-align: right;">Deposit</th>
                                    <th style="text-align: right;">Sell</th>
                                    <th style="text-align: right;">Balance</th>
                                    <th style="width: 50px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($suppliers) > 0)
                                    <?php $sl = 0; ?>
                                    @foreach ($suppliers as $row)
                                        <?php $sl++; ?>
                                        <tr>
                                            <td>{{ $sl }}</td>
                                            <td>
                                                <a class="link link-primary" target="_blank"
                                                    href="{{ route($type == 0 ? 'admin.supplier.details' : 'admin.client.details', $row->id) }}">{{ $row->full_name }}
                                                </a>


                                            </td>
                                            {{-- <td>{{ $row->mobile_number }}</td> --}}
                                            <td>{{ $row->address }}</td>
                                            {{-- <td>{{ $row->email }}</td> --}}
                                            {{-- <td>{{ $row->trn_no }}</td> --}}
                                            {{-- <td>{{ $row->narration }}</td> --}}
                                            <td style="text-align: right;">{{ $row->init_balance }}</td>
                                            <td style="text-align: right;">{{ $row->deposit_amount }}</td>
                                            <td style="text-align: right;">{{ $row->sell_amount }}</td>
                                            <td style="text-align: right;"><b>
                                                    {{ number_format($row->init_balance + $row->deposit_amount - $row->sell_amount, 3) }}
                                                </b></td>

                                            @can('supplier_edit')
                                                <td>
                                                    <a href="#" class="btn btn-success btn-sm load_modal"
                                                        data-toggle="modal"
                                                        data-action="{{ route('admin.supplier.update', $row->id) }}"><i
                                                            class="fa fa-edit"></i> Edit</a>
                                                </td>
                                            @endcan
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

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#common_table').DataTable();
        });
    </script>
@endpush
