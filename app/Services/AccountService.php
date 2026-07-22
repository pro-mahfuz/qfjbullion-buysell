<?php

namespace App\Services;

use App\Constants\ReferenceNumber;
use App\Models\AccountHead;
use App\Models\AccountInvoice;
use App\Models\AccountInvoiceItem;
use App\Models\AccountNumber;
use App\Models\Supplier;

class AccountService
{
    public static function getAllAccountHeads($schoolId)
    {
        $result = AccountHead::where(['school_id' => $schoolId])->orderBy('title')->get();
        return $result;
    }

    public static function getAllAccountHeadsByType($schoolId, $type)
    {
        $result = AccountHead::where(['school_id' => $schoolId, 'type' => $type])->orderBy('title')->get();
        return $result;
    }

    public static function createAccountHead(array $data)
    {
        AccountHead::create($data);
    }

    public static function findAccountHead($id)
    {
        return AccountHead::find($id);
    }

    public static function updateAccountHead(array $data, $id)
    {
        AccountHead::find($id)->update($data);
    }

    public static function getAllAccountNumbers($schoolId)
    {
        $result = AccountNumber::where(['school_id' => $schoolId])->orderBy('account_title')->get();
        return $result;
    }

    public static function createAccountNumber(array $data)
    {
        AccountNumber::create($data);
    }

    public static function findAccountNumber($id)
    {
        return AccountNumber::find($id);
    }

    public static function updateAccountNumber(array $data, $id)
    {
        AccountNumber::find($id)->update($data);
    }

    public static function getAllAccountInvoicesByType($schoolId, $invoiceType = 'income')
    {
        $result = AccountInvoice::with('items')->where(['school_id' => $schoolId, 'invoice_type' => $invoiceType])->orderByDesc('id')->get();
        return $result;
    }

    public static function createAccountInvoice(array $data)
    {
        $invoiceItems = $data['invoice_item'];
        unset($data['invoice_item']);

        $accountInvoice = AccountInvoice::create($data);
        $totalAmount = 0;

        foreach($invoiceItems as $headId => $amount) {
            if(!empty($amount)) {
                $totalAmount += $amount;
                AccountInvoiceItem::create([
                    'account_invoice_id' => $accountInvoice->id,
                    'account_head_id' => $headId,
                    'amount' => $amount,
                ]);
            }
        }

        AccountInvoice::find($accountInvoice->id)->update([
            'total_amount' => $totalAmount, 'reference_no' => getReferenceNo($data['school_id'], ReferenceNumber::ACCOUNT_REFERENCE)
        ]);

        updateReferenceNo($data['school_id'], ReferenceNumber::ACCOUNT_REFERENCE);
    }


    public static function getAllSuppliers()
    {
        $result = Supplier::where(['business_id' => session()->get(key: 'bussinessId')])->get();
        return $result;
    }

}
