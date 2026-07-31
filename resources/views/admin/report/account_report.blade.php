@extends('layouts.app')

@section('content')
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 ">
        <div class="x_panel">
            <div class="x_title">
                <h2>{{ ucfirst($type) }} Report</h2>
                 <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php
                    $head_id = isset($_GET['head_id']) ? $_GET['head_id'] : '';
                    $from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
                    $to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';
                ?>
                <div class="student-list search-form">
                    <form action="{{ route('report.account_report', ['type' => $type, 'printView' => 0]) }}" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Account Head</label>
                                    <select name="head_id" class="form-control select2-container
 common_select2">
                                        <option value="">All</option>
                                        @if(count($account_heads) > 0)
                                            @foreach($account_heads as $row)
                                            <option @if($head_id == $row->id) selected="" @endif value="{{ $row->id }}">{{ $row->title }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">From Date <span>*</span></label>
                                    <input type="date" class="form-control" required="" name="from_date" value="{{ $from_date }}" placeholder="Start Date">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">To Date <span>*</span></label>
                                    <input type="date" class="form-control" required="" name="to_date" value="{{ $to_date }}" placeholder="End Date">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" style="margin-top: 26px;">
                                    <button type="submit" class="btn btn-info" title="Search"><i class="fa fa-search"></i> Search </button>
                                    @if(count($reports) > 0)
                                    <a href="{{ route('report.account_report', ['type' => $type, 'printView' => 1]).'?'.$query_params }}" title="Print" class="btn btn-success"><i class="fa fa-print"></i></a>
                                    <a href="{{ route('report.account_report', ['type' => $type, 'printView' => 0]) }}" title="Reset" class="btn btn-danger"><i class="fa fa-times"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                @if(count($reports) > 0)
                <hr>
                @endif

                @if(count($reports) > 0)
                <br>
                <table class="table table-striped table-bordered common_datatable" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Item Name</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <?php $sl = 0; $total_amount = 0; ?>
                    <tbody>
                        @foreach($reports as $row)
                        <?php $sl++; $total_amount += $row->amount; ?>
                        <tr>
                            <td>{{ $sl }}</td>
                            <td>{{ date('d-M-Y', strtotime($row->create_date)) }}</td>
                            <td>{{ $row->title }}</td>
                            <td>{{ number_format($row->amount, 3) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td style="text-align: right;" colspan="3"><strong>Total Amount</strong></td>
                            <td>{{ number_format($total_amount, 3) }}</td>
                        </tr>
                    </tfoot>
                </table>
                @endif
            </div>
        </div>
    </div>
</div>
@stop
