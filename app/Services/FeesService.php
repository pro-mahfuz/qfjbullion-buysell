<?php

namespace App\Services;

use App\Constants\FeeType;
use App\Constants\PaymentStatus;
use App\Constants\ReferenceNumber;
use App\Models\FeesInvoice;
use App\Models\FeesTransaction;
use App\Models\FeesType;
use App\Models\Invoice;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class FeesService
{
    public static function getAllSchoolFeesTypes(array $condition)
    {
        $result = FeesType::where($condition)->orderBy('name')->get();
        return $result;
    }

    public static function getFilterSchoolFeesTypes(array $condition)
    {
        $result = FeesType::where($condition)->whereIn('type', ['one_time', 'monthly'])->orderBy('name')->get();

        return $result;
    }

    public static function createFeesType(array $data)
    {
        FeesType::create($data);
    }

    public static function findFeesType($id)
    {
        return FeesType::find($id);
    }

    public static function updateFeesType(array $data, $id)
    {
        FeesType::find($id)->update($data);
    }

    public static function deleteFeesType($id)
    {
        $findFeesType = FeesType::find($id);
        if ($findFeesType) {
            $findFeesType->delete();
        }
    }

    public static function generateAutoFeesInvoice(array $data)
    {
        // try {
        //     DB::beginTransaction();

        //     $type = $data['type'];
        //     $create_date = $data['create_date'];
        //     $due_date = $data['due_date'];

        //     $commonData = [
        //         'school_id' => $data['school_id'],
        //         'academic_year_id' => $data['academic_year_id'],
        //         'class_id' => $data['class_id'],
        //     ];

        //     $feesTypes = self::getAllSchoolFeesTypes(array_merge($commonData, ['type' => $type,'is_resident' => 1]));

        //     if (count($feesTypes) == 0) {
        //         return ['status' => false, 'message' => 'Sorry fees types not found for this class.'];
        //     }

        //     $createdInvoiceNumber = 0;
        //     $students = StudentService::getAllStudentsByConditions(array_merge($commonData, ['active_status' => 1,'is_resident' => 1]));

        //     if (count($students) > 0) {
        //         foreach ($students as $studentRow) {

        //                 foreach ($feesTypes as $feesTypeRow) {
        //                    $isExists = self::isAlreadyAutoInvoiceCreated(array_merge($commonData, ['student_id' => $studentRow->id,'fees_type_id' => $feesTypeRow->id, 'type' => $type]), $create_date);

        //             if ($isExists == false) {

        //                  $invoiceData = array_merge($commonData, [
        //                     'student_id' => $studentRow->id,                            
        //                     'fees_type_id' => $feesTypeRow->id,
        //                     'total_amount' => $feesTypeRow->amount,
        //                     'grand_total' => $feesTypeRow->amount,
        //                     'payment_status' => PaymentStatus::DUE,
        //                     'create_date' => $create_date,
        //                     'due_date' => $due_date,
        //                     'type' => $type
        //                 ]);
        //                 $feesInvoiceId = self::createFeesInvoice($invoiceData);

        //                     $invoiceChieldData = [
        //                         'fees_invoice_id' => $feesInvoiceId,
        //                         'fees_type_id' => $feesTypeRow->id,
        //                         'total_amount' => $feesTypeRow->amount,
        //                         'grand_total' => $feesTypeRow->amount,
        //                     ];
        //                     self::createFeesInvoiceChield($invoiceChieldData);
        //                 }

        //                 $createdInvoiceNumber++;
        //             }
        //         }
        //     }




        //     $_feesTypes = self::getAllSchoolFeesTypes(array_merge($commonData, ['type' => $type,'is_resident' => 0]));

        //     if (count($_feesTypes) == 0) {
        //         return ['status' => false, 'message' => 'Sorry fees types not found for this class.'];
        //     }

        //     $_createdInvoiceNumber = 0;
        //     $_students = StudentService::getAllStudentsByConditions(array_merge($commonData, ['active_status' => 1,'is_resident' => 0]));

        //     if (count($_students) > 0) {
        //         foreach ($_students as $studentRow) {

        //                 foreach ($feesTypes as $feesTypeRow) {
        //                    $isExists = self::isAlreadyAutoInvoiceCreated(array_merge($commonData, ['student_id' => $studentRow->id,'fees_type_id' => $feesTypeRow->id, 'type' => $type]), $create_date);

        //             if ($isExists == false) {

        //                  $invoiceData = array_merge($commonData, [
        //                     'student_id' => $studentRow->id,                            
        //                     'fees_type_id' => $feesTypeRow->id,
        //                     'total_amount' => $feesTypeRow->amount,
        //                     'grand_total' => $feesTypeRow->amount,
        //                     'payment_status' => PaymentStatus::DUE,
        //                     'create_date' => $create_date,
        //                     'due_date' => $due_date,
        //                     'type' => $type
        //                 ]);
        //                 $feesInvoiceId = self::createFeesInvoice($invoiceData);

        //                     $invoiceChieldData = [
        //                         'fees_invoice_id' => $feesInvoiceId,
        //                         'fees_type_id' => $feesTypeRow->id,
        //                         'total_amount' => $feesTypeRow->amount,
        //                         'grand_total' => $feesTypeRow->amount,
        //                     ];
        //                     self::createFeesInvoiceChield($invoiceChieldData);
        //                 }

        //                 $_createdInvoiceNumber++;
        //             }
        //         }
        //     }


        //     DB::commit();
        //     return ['status' => true, 'message' => $createdInvoiceNumber . ' invoices created'];
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return ['status' => false, 'message' => 'An error occurred while processing invoices.'];
        // }
    }

    public static function generateFeesInvoiceForStudent(array $data)
    {
        try {
            DB::beginTransaction();

            $create_date = $data['create_date'];
            $due_date = $data['due_date'];

            $commonData = [
                'school_id' => $data['school_id'],
                'academic_year_id' => $data['academic_year_id'],
                'class_id' => $data['class_id'],
            ];

            if ($data['is_resident'] == 0) {
                $commonData['is_resident'] = 0;
            }

            $feesTypes = self::getFilterSchoolFeesTypes($commonData);

            if ($data['is_resident'] == 1) {
                $commonData['is_resident'] = 1;
            }

            if (count($feesTypes) == 0) {
                return ['status' => false, 'message' => 'Sorry ! fees not found for this student.'];
            }

            $createdInvoiceNumber = 0;
            $studentRow = Student::find($data['student_id']);

            foreach ($feesTypes as $feesTypeRow) {
                $isExists = self::isAlreadyAutoInvoiceCreated(array_merge($commonData, ['student_id' => $data['student_id'], 'fees_type_id' => $feesTypeRow->id, 'type' => $feesTypeRow->type]), $create_date);

                if ($isExists == false) {
                    $invoiceData = array_merge($commonData, [
                        'student_id' => $studentRow->id,
                        'fees_type_id' => $feesTypeRow->id,
                        'total_amount' => $feesTypeRow->amount,
                        'grand_total' => $feesTypeRow->amount,
                        'payment_status' => PaymentStatus::DUE,
                        'create_date' => $create_date,
                        'due_date' => $due_date,
                        'type' => $feesTypeRow->type,
                    ]);

                    self::createFeesInvoice($invoiceData);

                    $createdInvoiceNumber++;
                }
            }

            DB::commit();
            return ['status' => true, 'message' => $createdInvoiceNumber . ' invoices created', 'createdInvoiceNumber' => $createdInvoiceNumber];
        } catch (\Exception $e) {
            DB::rollBack();
            //return ['status' => false, 'message' => $e->getMessage()];
            return ['status' => false, 'message' => 'An error occurred while processing invoices.'];
        }
    }

    public static function createFeesInvoice(array $data)
    {
        $feesInvoice = FeesInvoice::create($data);
        return $feesInvoice->id;
    }

    private static function isAlreadyAutoInvoiceCreated(array $condition, $create_date)
    {
        // dd($condition);
        unset($condition['is_resident']);

        $create_month = date('m', strtotime($create_date));
        $create_year = date('Y', strtotime($create_date));

        $query = FeesInvoice::where($condition)->select('id');
        if ($condition['type'] == FeeType::MONTHLY) {
            $query->whereMonth('create_date', $create_month)->whereYear('create_date', $create_year);
        }
        $result = $query->first();

        return $result ? true : false;
    }

    public static function makeMultipleInvoicePayment(array $data)
    {
        try {
            if (count($data['fees_invoices']) > 0) {
                DB::beginTransaction();

                //create invoice first
                $baseInvoice = Invoice::create([
                    'school_id' => $data['school_id'],
                    'academic_year_id' => $data['academic_year_id'],
                    'student_id' => $data['student_id'],
                    'account_number_id' => $data['account_number_id'],
                    'created_by' => auth()->user()->id,
                    'payment_method' => $data['payment_method'],
                    'payment_note' => trim($data['payment_note']),
                    'payment_attachment' => $data['payment_attachment'],
                    'reference_no' => getReferenceNo($data['school_id'], ReferenceNumber::INVOICE_REFERENCE),
                ]);

                $totalInvoicePayment = 0;
                $totalInvoiceDiscount = 0;

                foreach ($data['fees_invoices'] as $feesInvoiceId) {
                    //invoice model instance
                    $invoiceModel = FeesInvoice::find($feesInvoiceId);
                    $invoiceDiscount = !empty($data['item_discount'][$feesInvoiceId]) ? $data['item_discount'][$feesInvoiceId] : 0;
                    $invoicePaid = ($invoiceModel->grand_total - $invoiceModel->paid_amount);
                    $invoicePaid = ($invoicePaid - $invoiceDiscount);
                    $totalInvoicePayment += $invoicePaid;
                    $totalInvoiceDiscount += $invoiceDiscount;

                    //create fees transaction
                    FeesTransaction::create([
                        'fees_invoice_id' => $invoiceModel->id,
                        'student_id' => $data['student_id'],
                        'school_id' => $data['school_id'],
                        'academic_year_id' => $data['academic_year_id'],
                        'invoice_id' => $baseInvoice->id,
                        'total_paid' => $invoicePaid,
                        'discount' => $invoiceDiscount,
                    ]);

                    //update fees invoice table
                    $invoiceModel->paid_amount = ($invoiceModel->paid_amount + $invoicePaid);
                    $invoiceModel->discount_amount = ($invoiceModel->discount_amount + $invoiceDiscount);
                    $invoiceModel->grand_total = ($invoiceModel->grand_total - $invoiceDiscount);
                    $invoiceModel->payment_status = PaymentStatus::PAID;
                    $invoiceModel->save();
                }

                //update base invoice total amount
                Invoice::find($baseInvoice->id)->update([
                    'total_paid' => $totalInvoicePayment,
                    'total_discount' => $totalInvoiceDiscount,
                ]);
                Student::find($data['student_id'])->update([
                    'active_status' => 2
                ]);

                updateReferenceNo($data['school_id'], ReferenceNumber::INVOICE_REFERENCE);

                DB::commit();
                return ['status' => true, 'message' => 'Multiple Invoice Paid Successfully', 'invoice_id' => $baseInvoice->id];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Multiple Invoice Payment Failed.'];
            //return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public static function makeSingleInvoicePayment(array $data)
    {
        try {
            DB::beginTransaction();

            //create invoice first
            $baseInvoice = Invoice::create([
                'school_id' => $data['school_id'],
                'academic_year_id' => $data['academic_year_id'],
                'student_id' => $data['student_id'],
                'account_number_id' => $data['account_number_id'],
                'created_by' => auth()->user()->id,
                'payment_method' => $data['payment_method'],
                'payment_note' => trim($data['payment_note']),
                'payment_attachment' => $data['payment_attachment'],
                'reference_no' => getReferenceNo($data['school_id'], ReferenceNumber::INVOICE_REFERENCE),
            ]);

            $totalPaid = $data['total_paid'];
            $totalDiscount = $data['discount_amount'];
            $feesInvoiceId = $data['fees_invoice_id'];

            //invoice model instance
            $invoiceModel = FeesInvoice::find($feesInvoiceId);

            //create fees transaction
            FeesTransaction::create([
                'fees_invoice_id' => $invoiceModel->id,
                'student_id' => $data['student_id'],
                'school_id' => $data['school_id'],
                'academic_year_id' => $data['academic_year_id'],
                'invoice_id' => $baseInvoice->id,
                'total_paid' => $totalPaid,
                'discount' => $totalDiscount,
            ]);

            //update fees invoice table
            $subtotalPaid = ($invoiceModel->paid_amount + $totalPaid);
            $subtotalDiscount = ($invoiceModel->discount_amount + $totalDiscount);
            $subGrandTotal = ($invoiceModel->grand_total - $totalDiscount);
            $paymentStatus = ($subGrandTotal == $subtotalPaid) ? PaymentStatus::PAID : PaymentStatus::PARTIAL;

            $invoiceModel->paid_amount = $subtotalPaid;
            $invoiceModel->discount_amount = $subtotalDiscount;
            $invoiceModel->grand_total = $subGrandTotal;
            $invoiceModel->payment_status = $paymentStatus;
            $invoiceModel->save();


            //update base invoice total amount
            Invoice::find($baseInvoice->id)->update([
                'total_paid' => $totalPaid,
                'total_discount' => $totalDiscount,
            ]);
            Student::find($data['student_id'])->update([
                'active_status' => 2
            ]);

            updateReferenceNo($data['school_id'], ReferenceNumber::INVOICE_REFERENCE);

            DB::commit();
            return ['status' => true, 'message' => 'Invoice Paid Successfully', 'invoice_id' => $baseInvoice->id];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Invoice Payment Failed.'];
        }
    }

    public static function manualFeesInvoiceForStudent(array $data)
    {
        try {
            $createdInvoiceNumber = 0;
            foreach ($data['fees_types'] as $feesTypeId => $amount) {
                $invoiceData = [
                    'school_id' => $data['school_id'],
                    'academic_year_id' => $data['academic_year_id'],
                    'class_id' => $data['class_id'],
                    'student_id' => $data['student_id'],
                    'fees_type_id' => $feesTypeId,
                    'total_amount' => $amount,
                    'grand_total' => $amount,
                    'payment_status' => PaymentStatus::DUE,
                    'create_date' => $data['create_date'],
                    'due_date' => $data['due_date'],
                    'type' => FeeType::OTHERS,
                    'is_resident' => $data['is_resident']
                ];

                self::createFeesInvoice($invoiceData);
                $createdInvoiceNumber++;
            }
            return ['status' => true, 'message' => $createdInvoiceNumber . ' invoices created'];
        } catch (\Exception $e) {
            //return ['status' => false, 'message' => $e->getMessage()];
            return ['status' => false, 'message' => 'An error occurred while processing invoices.'];
        }
    }
}
