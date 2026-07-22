@extends('layouts.app')


@section('title', 'Roles')

@section('content_header')
    <h1>Product List</h1>
@stop

@section('content')

    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-end">
            <a href="{{ route('admin.product.create') }}" class="btn btn-success">
                <i class="fa fa-plus"></i> Create Product
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-responsive">
                    <table id="role-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Price AED</th>
                                <th>Price Oz</th>
                                <th>Price USD</th>
                                <th>Tax</th>
                                <th>Purity</th>


                                {{-- @can('roles_edit') --}}
                                <th>Action</th>
                                {{-- @endcan --}}
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @dd($result) --}}
                            @if (count($result) > 0)
                                <?php $sl = 0; ?>
                                @foreach ($result as $row)
                                    <?php $sl++; ?>
                                    <tr>
                                        <td>{{ $sl }}</td>
                                        <td>{{ $row->title }}</td>
                                        <td>{{ $row->price_aed }}</td>
                                        <td>{{ $row->price_oz }}</td>
                                        <td>{{ $row->price_usd }}</td>
                                        <td>{{ $row->tax }}</td>
                                        <td>{{ $row->purity }}</td>

                                        <td>
                                            {{-- @dd( $row->id) --}}
                                            <form action="{{ route('admin.product.delete', $row->id) }}" method="POST"
                                                style="display: inline;"
                                                onsubmit="return confirm('Are you sure you want to delete this referral?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>


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


@section('js')
    <script>
        $(document).ready(function() {
            $('#role-table').DataTable();
        });
    </script>
@stop
