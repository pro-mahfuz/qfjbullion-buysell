@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <div class="card-body">

        <div class="row">
            @foreach ($active_businesses as $key => $row)
                <div class="col-md-3 mb-3">
                    <div class="card h-100 d-flex flex-column">
                        <div class="card-header @if (Session::get('bussinessId') == $row->id) bg-success text-light @endif">
                            Business <span class="pull-right">0{{ $key + 1 }}</span>
                        </div>
                        <div class="card-body flex-grow-1">
                            <h5 class="card-title"><strong>{{ $row->name }}</strong></h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the
                                bulk of the card's content.</p>

                            @if($row->id != $bussinessId)
                                <form action="{{ route('admin.change.bussiness') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <input name="id" type="hidden" value="{{ $row->id }}">
                                    <input name="name" type="hidden" value="{{ $row->name }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-building"></i> Switch
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
@stop
