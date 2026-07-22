<!DOCTYPE html>
<html>

<head>
    <title>Account Balance Sheet Report Print</title>
    <meta http-equiv="refresh" content="0;url={{ route('report.account_balance_sheet', 0) . '?' . $query_params }}">
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
        $ledger_id = isset($_GET['ledger_id']) ? $_GET['ledger_id'] : '';
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
                                        <span>Balance Sheet</span>
                                    </p>
                                    @if(!empty($ledger_id))
                                    <p>
                                        <span>Account Ledger</span>
                                        <span class="nowrap">: {{ getAccountLedgerTitle($ledger_id) }}</span>
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="invoice-items">
                            @if(count($reports) > 0)
                            <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr> 
                                        <th>Date</th> 
                                        <th>Particulars</th> 
                                        <th>Invoice No</th>
                                        <th>Debit</th>  
                                        <th>Credit</th>  
                                        <th>Balance</th>  
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr> 
                                        <td></td>
                                        <td>B/F</td>
                                        <td></td>
                                        <td>{{ number_format($initExpense, 2) }}</td>
                                        <td>{{ number_format($initIncome, 2) }}</td>
                                        <td>{{ number_format(($initIncome - $initExpense), 2) }}</td>
                                    </tr>
                                    <?php
                                        $totalExpenseAmount = $initExpense;
                                        $totalIncomeAmount = $initIncome;
                                    ?>
                                    @foreach($reports as $row)
                                        <?php
                                            if(isset($row['total_paid'])) {
                                                $totalIncomeAmount += $row['total_paid'];
                                            } else {
                                                if(($row['invoice_type'] == 'expense')) {
                                                    $totalExpenseAmount += $row['total_amount'];
                                                } else {
                                                    $totalIncomeAmount += $row['total_amount'];
                                                }
                                            }
            
                                            $inlineBalance = ($totalIncomeAmount - $totalExpenseAmount);
                                        ?>
            
                                        @if(isset($row['total_paid']))
                                        <tr> 
                                            <td>{{ date('d-M-Y', strtotime($row['created_at'])) }}</td>
                                            <td>
                                                <ol style="margin: 0; padding: 0 0 0 15px;">
                                                    @foreach($row['fees_invoices'] as $item)
                                                        <li>{{ getFeesTypeTitleByFeesInvoiceId($item['fees_invoice_id']) }}</li>
                                                    @endforeach
                                                </ol>
                                            </td>
                                            <td>{{ sprintf('%06d', $row['reference_no'] ?? $row['id']) }}</td>
                                            <td>{{ number_format(0, 2) }}</td>
                                            <td>{{ number_format($row['total_paid'], 2) }}</td>
                                            <td>{{ number_format($inlineBalance, 2) }}</td>
                                        </tr>
                                        @else
                                        <tr> 
                                            <td>{{ date('d-M-Y', strtotime($row['create_date'])) }}</td>
                                            <td>
                                                <ol style="margin: 0; padding: 0 0 0 15px;">
                                                    @foreach($row['items'] as $item)
                                                        <li>{{ getAccountHeadTitle($item['account_head_id']) }}</li>
                                                    @endforeach
                                                </ol>
                                            </td>
                                            <td>{{ sprintf('%06d', $row['reference_no'] ?? $row['id']) }}</td>
                                            @if(($row['invoice_type'] == 'expense'))
                                            <td>{{ number_format($row['total_amount'], 2) }}</td>
                                            <td>{{ number_format(0, 2) }}</td>
                                            @else
                                            <td>{{ number_format(0, 2) }}</td>
                                            <td>{{ number_format($row['total_amount'], 2) }}</td>
                                            @endif
                                            <td>{{ number_format($inlineBalance, 2) }}</td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td style="text-align: right;" colspan="3"><strong>Total Amount</strong></td>
                                        <td>{{ number_format($totalExpenseAmount, 2) }}</td>
                                        <td>{{ number_format($totalIncomeAmount, 2) }}</td>
                                        <td>{{ number_format(($totalIncomeAmount - $totalExpenseAmount), 2) }}</td>
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