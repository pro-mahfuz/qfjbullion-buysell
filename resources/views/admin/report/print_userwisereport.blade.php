<!DOCTYPE html>
<html>

<head>
    <title>User Wise Report</title>
    <meta http-equiv="refresh" content="0;url={{ route('report.index', 0).'?'.$query_params_build }}">
    <link href="{{ asset('assets/backend/vendors') }}/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('assets/backend') }}/css/custom_style.css?{{ time() }}" rel="stylesheet">
    <style>
        @media print {
            .table-bordered td,
            .table-bordered th {
                border: 1px solid #000 !important;
                font-family: 'solaimanlipi';
            }
        }
        p,
        span,
        a {
            font-family: 'solaimanlipi';
            font-size: 20px;
        }
        .row {
            display: block !important;
        }
    </style>
</head>

<!--body onload="window.print()" -->
<body>   
    <?php
        $from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
        $to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';
    ?>
    <section class="print-invoice-section" style="width: 90%;margin:0 5%">
        <div class="row" style="margin:0px">
                <div class="col-print-12">
                    <div class="invoice-view-wrapper">
                        <div class="invoice-header">
                            <img src="{{ getInvoiceHeaderImage() }}">
                        </div>
                        <hr />
                        <div class="invoice-summary-info">
                            <div class="col-print-7">
                                <div class="addressleft_text">
                                    <p>
                                        <span>From Date</span>
                                        <span class="nowrap">: {{ date('d F Y', strtotime($query_params['form_date'])) }}</span>
                                    </p>
                                    <p>
                                        <span>To Date</span>
                                        <span class="nowrap">: {{ date('d F Y', strtotime($query_params['to_date'])) }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-print-5">
                                <div class="addressleft_text">
                                    <p>
                                        <span>User Wise Report Report</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-items">
                            @if(count($reports) > 0 && $query_params['result_type']=="invoice")
                            <table class="table table-striped table-bordered" style="width:100%">
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
                                            Invoice : {{ getInvoicePrefix() }}{{ sprintf('%06d', $row->reference_no ?? $row->id) }} <br>
                                            PaymentBy : {{ $row->user->full_name }}  
                                        </td>
                                        <td>
                                            <ol>
                                            @foreach($row->feesInvoices as $inv)
                                                <li> {{ $inv->feesInvoice->feesType->name }} {{ $inv->feesInvoice->feesType->type == "monthly" ? date(' - F', strtotime($inv->feesInvoice->create_date)) :"" }} </li>
                                            @endforeach
                                            </ol>
                                        </td>
                                        <td>{{ $row->students->full_name }}</td>
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
                            <table class="table table-striped table-bordered" style="width:100%">
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
    </section>
    <script>
        window.print();
    </script>
</body>
</html>