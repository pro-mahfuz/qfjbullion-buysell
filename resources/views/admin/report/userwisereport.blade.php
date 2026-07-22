@extends('layouts.app')

@section('content')
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 ">
        <div class="x_panel">
            <div class="x_title">
                <h2>Reports</h2>
                 @if(count($reports) > 0)
                 <ul class="nav navbar-right panel_toolbox">
                    <li><h2>Total Collection <i class="fa fa-money"></i> {{$total_collection}}</h2>  </li>
                </ul>
                @endif
                 <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="student-list search-form">
                    <form action="{{ route('report.index', 0) }}" method="GET">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select name="result_type" id="result_type" class="form-control select2-container
 common_select2" required="">
                                    <option @if(isset($query_params['result_type']) && $query_params['result_type'] == 'invoice') selected="" @endif  value="invoice">Invoice Wise</option>
                                    <option @if(isset($query_params['result_type']) && $query_params['result_type'] == 'daily') selected="" @endif  value="daily">Day Wise</option>
                                    {{-- <option @if(isset($query_params['result_type']) && $query_params['result_type'] == 'monthly') selected="" @endif  value="monthly">Month Wise</option>
                                    <option @if(isset($query_params['result_type']) && $query_params['result_type'] == 'fees') selected="" @endif  value="fees">Fees Type Wise</option>  --}}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="text" class="form-control onchange-invoice" name="invoice_number" @if(isset($query_params['invoice_number'])) value="{{ $query_params['invoice_number'] }}" @endif placeholder="Search by invoice">
                                </div>
                            </div>
                            @if(count($user_list) > 0)
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <select name="user_id" id="select_class" class="form-control select2-container
 common_select2" required="">
                                            <option value="0">All User</option>
                                            @foreach($user_list as $_row)
                                            <option @if(isset($query_params['user_id']) && $query_params['user_id'] == $_row->id) selected="" @endif value="{{ $_row->id }}">{{ $_row->full_name }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="date" class="form-control from_date_input" @if(isset($query_params['invoice_number']) && !empty($query_params['invoice_number'])) @elseif(!isset($query_params['invoice_number'])) required="" @else required="" @endif name="form_date" @if(isset($query_params['form_date'])) value="{{ $query_params['form_date'] }}" @endif placeholder="Start Date">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="date" class="form-control to_date_input" @if(isset($query_params['invoice_number']) && !empty($query_params['invoice_number'])) @elseif(!isset($query_params['invoice_number'])) required="" @else required="" @endif name="to_date" @if(isset($query_params['to_date'])) value="{{ $query_params['to_date'] }}" @endif  placeholder="End Date">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-info" title="Search"><i class="fa fa-search"></i> Search </button>
                                    @if(count($reports) > 0)
                                    <a href="{{ route('report.index', 1).'?'.$query_params_build }}" title="Print" class="btn btn-success"><i class="fa fa-print"></i></a>
                                    <a href="{{ route('report.index', 0) }}" title="Reset" class="btn btn-danger"><i class="fa fa-times"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                @if(count($reports) > 0)
                <hr>
                @endif
                <div class="card-box table-responsive">
                    @include('admin.partials.alert_messages')
                </div>
                @if(count($reports) > 0 && $query_params['result_type']=="invoice")
                <br>
                <table class="table table-striped table-bordered common_datatable" style="width:100%">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Invoice</th>
                            <th>Student</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sl = 0; ?>
                        @foreach($reports as $row)
                        <?php $sl++; ?>
                        <tr>
                            <td>
                                {{ dateConvert($row->created_at) }}  <br>
                                Invoice : <a target="_blank" href="{{ route('invoice.view', $row->id) }}">{{ getInvoicePrefix() }}{{ sprintf('%06d', $row->reference_no ?? $row->id) }} <i class="fa fa-eye"></i></a><br>
                                PaymentBy : {{ $row->user->full_name }}
                            </td>
                            <td>
                                <ol>
                                @foreach($row->feesInvoices as $inv)
                                    <li> {{ $inv->feesInvoice->feesType->name }} {{ $inv->feesInvoice->feesType->type == "monthly" ? date(' - F', strtotime($inv->feesInvoice->create_date)) :"" }} </li>
                                @endforeach
                                </ol>
                            </td>
                            <td>
                                <a  target="_blank" href="{{ route('student.view', $row->students->id) }}">{{ $row->students->full_name }} <i class="fa fa-eye"></i></a>
                            </td>
                            <td>
                                Paid : {{ $row->total_paid }}  <br>
                                Disc : {{ $row->total_discount??0 }}
                                <br>
                                {{ $row->payment_method   }} Payment
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                @if(count($reports) > 0 && $query_params['result_type']=="daily")
                <br>
                <table class="table table-striped table-bordered common_datatable" style="width:100%">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Discount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sl = 0; ?>
                        @foreach($reports as $row)
                        <?php $sl++; ?>
                        <tr>
                            <td>{{ dateConvert($row->createdDate) }}</td>
                            <td>{{ $row->total }}</td>
                            <td>0</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.onchange-invoice').on('change', function() {
            if ($(this).val() == '') {
                $('.from_date_input').prop('required', true);
                $('.to_date_input').prop('required', true);
            } else {
                $('.from_date_input').prop('required', false);
                $('.to_date_input').prop('required', false);
            }
        });
    });
</script>
@stop
