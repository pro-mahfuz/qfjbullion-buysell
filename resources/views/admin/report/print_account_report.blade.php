<!DOCTYPE html>
<html>

<head>
    <title>{{ ucfirst($type) }} Report</title>
    <meta http-equiv="refresh" content="0;url={{ route('report.account_report', ['type' => $type, 'printView' => 0]).'?'.$query_params }}">
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
                                        <span class="nowrap">: {{ date('d F Y', strtotime($from_date)) }}</span>
                                    </p>
                                    <p>
                                        <span>To Date</span>
                                        <span class="nowrap">: {{ date('d F Y', strtotime($to_date)) }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-print-5">
                                <div class="addressleft_text">
                                    <p>
                                        <span>{{ ucfirst($type) }} Report</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-items">
                            @if(count($reports) > 0)
                            <table class="table table-striped table-bordered" style="width:100%">
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
    </section>
    <script>
        window.print();
    </script>
</body>
</html>