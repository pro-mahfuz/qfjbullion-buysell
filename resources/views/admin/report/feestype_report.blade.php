@extends('layouts.app')

@section('content')
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 ">
        <div class="x_panel">
            <div class="x_title">
                <h2>Fees Type Wise Report</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="student-list search-form">
                    <form action="{{ route('report.feestype_report') }}" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <select name="class_id" class="form-control select2-container
 common_select2" required="">
                                        <option value="ALL">All Class</option>
                                        @if(count($class_list) > 0)
                                            @foreach($class_list as $row)
                                            <option @if($isSearchAble && $_GET['class_id'] == $row->id) selected="" @endif value="{{ $row->id }}">{{ $row->class_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <select name="selected_month" class="form-control select2-container
 common_select2" required="">
                                        <option value="">Select month</option>
                                        @foreach($months_array as $monthKey => $monthValue)
                                        <option @if($isSearchAble && $_GET['selected_month'] == $monthKey) selected="" @endif value="{{ $monthKey }}">{{ $monthValue }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @if(count($user_list) > 0)
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select name="user_id" id="select_user" class="form-control select2-container
 common_select2" required="">
                                        <option value="ALL">All User</option>
                                        @foreach($user_list as $_row)
                                        <option @if(isset($query_params['user_id']) && $query_params['user_id'] == $_row->id) selected="" @endif value="{{ $_row->id }}">{{ $_row->full_name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>

                            @endif
                            <div class="col-md-2">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-info" title="Search"><i class="fa fa-search"></i> Search </button>
                                    @if($isSearchAble)
                                    <a href="{{ route('report.class_wise') }}" title="Reset" class="btn btn-danger"><i class="fa fa-times"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                @if($isSearchAble)
                <hr>
                @endif
                @if($isSearchAble)
                <div class="row">
                    <div class="col-md-4"><h4>Toatal amount: {{ number_format($total_amount, 3) }}</h4></div>
                    <div class="col-md-4"><h4>Toatal Paid: {{ number_format($total_paid, 3) }}</h4></div>
                    <div class="col-md-4"><h4>Toatal Due: {{ number_format(($total_amount - $total_paid), 3) }}</h4></div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@stop
