@extends('layouts.app')

@section('content_header')
    <h1>Unfixed Purchase List</h1>
@stop


@section('content')

    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-end">
            <a class="btn btn-success" href="{{ route('admin.purchase.create') }}"><i class="fa fa-plus"></i>
                Create Unfixed Purchase </a>
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
                                    <th>Supplier</th>
                                    <th>Narration</th>

                                    <th style="text-align:right;">Amount (AED)</th>
                                    <th style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($purchases) > 0)
                                    <?php $sl = 0; ?>
                                    @foreach ($purchases as $row)
                                        <?php $sl++; ?>
                                        <tr>
                                            <td>{{ $sl }}</td>
                                            <td>{{ date('d/M/Y', strtotime($row->created_at)) }}</td>
                                            <td>{{ $row->supplier->full_name }}</td>
                                            <td>{{ $row->note }}</td>
                                            <td style="text-align:right;">{{ $row->unfix_total }}</td>
                                            <td>
                                                <a href="{{ route('admin.purchase.edit', $row->id) }}"
                                                    class="btn btn-info btn-sm"><i class="fa fa-pencil"></i> Edit</a>
                                                {{-- <a href="{{ route('admin.purchase.delete', $row->id) }}"
                                                    class="btn btn-danger btn-xs"
                                                    onclick="return confirm('Are you sure you want to delete this item?');"><i
                                                        class="fa fa-trash"></i> Delete</a> --}}

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

@push('js')
    <script>
        $(document).ready(function() {
            $('.common_datatable').DataTable();
        });
    </script>
@endpush
