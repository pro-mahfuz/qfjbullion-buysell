@extends('layouts.app')


@section('title', 'Roles')

@section('content_header')
    <h1>Project List</h1>
@stop

@section('content')

    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-end">
            <a href="{{ route('admin.project.create') }}" class="btn btn-success">
                <i class="fa fa-plus"></i> Create Project
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
                                <th>Decription</th>
                                <th>Estimated Revenue</th>
                                <th>Created AT</th>
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
                                        <td>{{ $row->description }}</td>
                                        <td>{{ $row->estimated_revenue }}</td>
                                        <td>{{ $row->created_at }}</td>

                                        <td>
                                            {{-- // edit --}}
                                            <a href="{{ route('admin.project.edit', $row->id) }}" class="btn btn-primary">
                                                <i class="fa fa-edit">Edit</i>
                                            </a>
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
@stop


@section('js')
    <script>
        $(document).ready(function() {
            $('#role-table').DataTable();
        });
    </script>
@stop
