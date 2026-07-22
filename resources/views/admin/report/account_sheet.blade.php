@extends('layouts.app')

@section('content')
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 ">
        <div class="x_panel">
            <div class="x_title">
                <h2>Account Balance Sheet</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php
                    $from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
                    $to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';
                    $ledger_id = isset($_GET['ledger_id']) ? $_GET['ledger_id'] : '';
                ?>
                <div class="student-list search-form">
                    <form action="{{ route('report.account_balance_sheet', 0) }}" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Account Ledger</label>
                                    <select name="ledger_id" class="form-control select2-container
 common_select2">
                                        <option value="">All Ledger</option>
                                        @if(count($account_numbers) > 0)
                                            @foreach($account_numbers as $row)
                                            <option @if($ledger_id == $row->id) selected="" @endif value="{{ $row->id }}">{{ $row->account_title }} ({{ $row->account_number }})</option>
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
                                    <a href="{{ route('report.account_balance_sheet', 1) . '?' . $query_params }}" title="Print" class="btn btn-success"><i class="fa fa-print"></i></a>
                                    <a href="{{ route('report.account_balance_sheet' , 0) }}" title="Reset" class="btn btn-danger"><i class="fa fa-times"></i></a>
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
                                <td>
                                    <a target="_blank" href="{{ route('invoice.view', $row['id']) }}">{{ sprintf('%06d', $row['reference_no'] ?? $row['id']) }} <i class="fa fa-eye"></i></a>
                                </td>
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
@stop
