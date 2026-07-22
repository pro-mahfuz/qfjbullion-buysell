<?php

namespace App\Services;

use App\Constants\AccountType;
use App\Models\AccountInvoice;
use App\Models\Invoice;

class ReportService
{
    public static function getBalanceSheetReportData(array $condition, $fromDate, $toDate)
    {
        $expenseCondition = array_merge($condition, ['invoice_type' => AccountType::EXPENSE]);
        $incomeCondition = array_merge($condition, ['invoice_type' => AccountType::INCOME]);

        $initExpense = AccountInvoice::where($expenseCondition)->where('create_date', '<', $fromDate)->sum('total_amount');
        $initIncome = AccountInvoice::where($incomeCondition)->where('create_date', '<', $fromDate)->sum('total_amount');
        $initPaidInvoice = Invoice::where($condition)->where('created_at', '<', $fromDate)->sum('total_paid');

        $incomeInvoices = AccountInvoice::with('items')
            ->where($incomeCondition)->where('create_date', '>=', $fromDate)->where('create_date', '<=', $toDate)->get();

        $expenseInvoices = AccountInvoice::with('items')
            ->where($expenseCondition)->where('create_date', '>=', $fromDate)->where('create_date', '<=', $toDate)->get();

        $paidInvoices = Invoice::with('feesInvoices')
            ->where($condition)->where('created_at', '>=', $fromDate)->where('created_at', '<=', $toDate . ' 23:59:59')->get();

        $mergedReports = array_merge($incomeInvoices->toArray(), $expenseInvoices->toArray(), $paidInvoices->toArray());

        usort($mergedReports, function ($a, $b) {
            $dateA = strtotime($a['create_date'] ?? $a['created_at']);
            $dateB = strtotime($b['create_date'] ?? $b['created_at']);
            return $dateA - $dateB;
        });

        return [
            'reports' => $mergedReports,
            'initExpense' => $initExpense,
            'initIncome' =>  ($initIncome + $initPaidInvoice)
        ];
    }
}
