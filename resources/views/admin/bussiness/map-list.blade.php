@extends('layouts.master')

@section('title', 'Business Map List')

@section('content_header')
    <h1>Business Map List</h1>
@stop


<style>
  .table td, .table th {
    padding: .25rem !important;
    font-size: .85rem !important;
	height: 20px !important;
  }
</style>
@section('content')
    <ul class="breadcrumb breadcrumb-style">
		<li class="breadcrumb-item">
		  	<a href="{{route('admin.dashboard.buysell')}}">
				<i class="fas fa-home"></i>
			</a>
		</li>
		<li class="breadcrumb-item active">Business Map List</li>
	</ul>


    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-end">
            {{-- <button class="load_modal" href="{{ route('admin.bussiness.create_map') }}" class="btn btn-danger">Business Map
                Create</button> --}}
            <a href="#" class="btn btn-primary btn-sm load_modal" data-toggle="modal"
                data-action="{{ route('admin.bussiness.create_map') }}">
                + Cretae Business Map
            </a>
        </div>
    </div>




    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                    <div class="card-box table-responsive">
                        <table class="table table-striped table-bordered common_datatable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Business</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($result) > 0)
                                    <?php $sl = 0; ?>
                                    @foreach ($result as $row)
                                        <?php $sl++; ?>
                                        <tr>
                                            <td>{{ $sl }}</td>
                                            <td>{{ $row->user_name }}</td>
                                            <td>{{ $row->bussiness_name }}</td>
                                            <td>
                                                {{-- <a href="{{ route('bussiness.edit', $row->id) }}"
                                                    class="btn btn-success btn-sm"><i class="fa fa-edit"></i></a> --}}

                                                <a href="{{ route('admin.bussiness.delete_map', $row->id) }}"
                                                    class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
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
