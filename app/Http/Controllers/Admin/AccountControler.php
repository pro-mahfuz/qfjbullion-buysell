<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AccountInvoiceRequest;
use App\Models\AccountHead;
use App\Models\AccountNumber;
use App\Models\Bussiness;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Deposit;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Item;
use App\Models\PurchaseItem;
use App\Services\AccountService;
use App\Services\CustomerService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class AccountControler extends Controller
{

    public function __construct(private CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function accountHeads()
    {
        $data['result'] = AccountService::getAllAccountHeads(Session::get('currentSchoolId'));
        return view('admin.account.account_heads', $data);
    }

    public function createAccountHead()
    {
        return view('admin.account.create_head');
    }

    public function storeAccountHead(Request $request)
    {

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string'],
        ]);

        $postData = array_merge($request->except('_token'), ['school_id' => Session::get('currentSchoolId')]);

        AccountService::createAccountHead($postData);

        return redirect()->back()->with('success', 'Created Successfully');
    }

    public function editAccountHead(AccountHead $accountHead)
    {
        $data['accountHead'] = $accountHead;
        return view('admin.account.edit_head', $data);
    }

    public function updateAccountHead(Request $request, AccountHead $accountHead)
    {

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string'],
        ]);

        $postData = $request->except(['_token', '_method']);

        AccountService::updateAccountHead($postData, $accountHead->id);

        return redirect()->back()->with('success', 'Updated Successfully');
    }

    public function accountNumbers()
    {
        $data['result'] = AccountService::getAllAccountNumbers(Session::get('currentSchoolId'));
        return view('admin.account.account_numbers', $data);
    }

    public function createAccountNumber()
    {
        return view('admin.account.create_account');
    }

    public function storeAccountNumber(Request $request)
    {

        $request->validate([
            'account_title' => ['required', 'string', 'max:255'],
        ]);

        $postData = array_merge($request->except('_token'), ['school_id' => Session::get('currentSchoolId')]);

        AccountService::createAccountNumber($postData);

        return redirect()->back()->with('success', 'Created Successfully');
    }

    public function editAccountNumber(AccountNumber $accountNumber)
    {



        $data['accountNumber'] = $accountNumber;
        return view('admin.account.edit_account', $data);
    }

    public function updateAccountNumber(Request $request, AccountNumber $accountNumber)
    {



        $request->validate([
            'account_title' => ['required', 'string', 'max:255']
        ]);

        $postData = $request->except(['_token', '_method']);

        AccountService::updateAccountNumber($postData, $accountNumber->id);

        return redirect()->back()->with('success', 'Updated Successfully');
    }

    public function accountInvoices($type)
    {

        if (!in_array($type, ['income', 'expense'])) {
            abort(404);
        }

        $data['result'] = AccountService::getAllAccountInvoicesByType(Session::get('currentSchoolId'), $type);

        $data['invoiceType'] = $type;
        return view('admin.account.account_invoices', $data);
    }

    public function createAccountInvoice($type)
    {

        $schoolId = Session::get('currentSchoolId');

        $data['account_heads'] = AccountService::getAllAccountHeadsByType($schoolId, $type);
        $data['account_numbers'] = AccountService::getAllAccountNumbers($schoolId);
        $data['invoice_type'] = $type;

        return view('admin.account.create_invoice', $data);
    }

    public function storeAccountInvoice(AccountInvoiceRequest $request)
    {

        $attachemtUrl = FileUploadService::handleFileUpload($request, 'attachment', 'account_attachments/');

        $postData = array_merge($request->except('_token'), [
            'school_id' => Session::get('currentSchoolId'),
            'academic_year_id' => getCurrentAcademicYearId(),
            'attachments' => $attachemtUrl,
            'created_by' => auth()->user()->id,
        ]);

        AccountService::createAccountInvoice($postData);

        return redirect()->back()->with('success', 'Created Successfully');
    }






    public function suplierCreate(Request $request)
    {
        // dd($request->all());
        // $type = $request->type;
        return view('admin.supplier.create')->with('type', $request->type);
    }

    public function supplierUpdate($id)
    {

        $data['supplier'] = Supplier::find($id);
        return view('admin.supplier.edit', $data);
    }


    public function supplierEdit(Request $request)
    {

        $request->validate([
            'full_name' => ['required', 'string', 'max:255']
        ]);

        $postData = array_merge($request->except('_token'));

        $supplier = Supplier::find($postData['id']);

        if ($supplier) {
            $supplier->update($postData);
        } else {
            dd($$postData['supplier_id']);
        }

        return redirect()->back()->with('success', 'Created Successfully');
    }



    public function suplierStore(Request $request)
    {


        $request->validate([
            'full_name' => ['required', 'string', 'max:255']
        ]);

        $postData = array_merge($request->except('_token'));

        $postData['business_id'] = \Request::session()->get(key: 'bussinessId');

        try {
            Supplier::create($postData);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Created Successfully');
    }


    public function suplierList(Request $request)
    {
        $data['suppliers'] = Supplier::where(['business_id' => session()->get(key: 'bussinessId')])
            ->where('type', $request->type)->get();
        $data['type'] = $request->type;
        return view('admin.supplier.list', $data);
    }

    public function suplierDetails($supplier_id)
    {


        $data['supplier'] = Supplier::findOrFail($supplier_id);

        $deposits = Deposit::select('id', 'created_at', DB::raw('"deposit" as type'))
            ->where('supplier_id', $supplier_id);

        $purchaseItems = PurchaseItem::select('id', 'created_at', DB::raw('"buysell" as type'))
            ->where('supplier_id', $supplier_id);

        $accounts = $deposits->union($purchaseItems)
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($accounts as $account) {
            if ($account->type === 'deposit') {
                $account->data = Deposit::find($account->id);
            } else {
                $account->data = PurchaseItem::find($account->id);
            }
        }



        $data['accounts'] = $accounts;

        $business = Bussiness::where('id', session()->get(key: 'bussinessId'))->first();
        $data['business'] = $business;
        return view('admin.supplier.details', $data);
    }


    public function suplierView($supplier_id)
    {


        $data['supplier'] = Supplier::findOrFail($supplier_id);

        $deposits = Deposit::select('id', 'created_at', DB::raw('"deposit" as type'))
            ->where('supplier_id', $supplier_id);

        $purchaseItems = PurchaseItem::select('id', 'created_at', DB::raw('"buysell" as type'))
            ->where('supplier_id', $supplier_id);

        $accounts = $deposits->union($purchaseItems)
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($accounts as $account) {
            if ($account->type === 'deposit') {
                $account->data = Deposit::find($account->id);
            } else {
                $account->data = PurchaseItem::find($account->id);
            }
        }




        $data['accounts'] = $accounts;

        return view('admin.supplier.view', $data);
    }

    public function purchaseCreate()
    {
        $data['suppliers'] = AccountService::getAllSuppliers();
        $data['products'] = Product::all();
        $data['account_numbers'] = AccountService::getAllAccountNumbers(Session::get('currentSchoolId'));

        $data['PurchaseItems'] = Item::all();
        return view('admin.purchase.create', $data);
    }


    public function depositlist()
    {


        $data['deposits'] = Deposit::where(['type' => "deposit"])->get();

        return view('admin.deposit.list', $data);
    }



    public function withdrawlist()
    {


        $data['deposits'] = Deposit::where(['type' => "withdraw"])->get();

        return view('admin.withdraw.list', $data);
    }

    public function depositCreate()
    {

        $suppliers = [
            0 => Supplier::where('type', 0)->get(),
            1 => Supplier::where('type', 1)->get()
        ];
        $data['suppliers'] = $suppliers;

        return view('admin.deposit.create', $data);
    }


    public function depositEdit($id)
    {
        $data['deposit'] = Deposit::find($id);
        $data['deposit_list'] = Deposit::where('supplier_id', $data['deposit']->supplier_id)->get();
        $data['suppliers'] = AccountService::getAllSuppliers();
        $data['supplier'] = Supplier::find($data['deposit']->supplier_id);
        return view('admin.deposit.edit', $data);
    }



    public function depositStore(Request $request)
    {

        $request->validate([
            'supplier_id' => ['required', 'string', 'max:255'],
            'deposit_amount' => ['required'],
            'type' => ['required'],
            'payment_account_id' => ['required', 'integer'],
        ]);

        $postData = $request->except('_token');

        try {
            DB::transaction(function () use ($postData) {

                $supplier = Supplier::find($postData['supplier_id']);
                if ($postData['type'] == "widthdraw") {
                    $postData['withdraw_amount'] = $postData['deposit_amount'];
                    $postData['deposit_amount'] = 0;
                }


                Deposit::create($postData);

                $dd = Deposit::select(DB::raw('SUM(deposit_amount) as total_deposit_amount'), DB::raw('SUM(withdraw_amount) as total_withdraw_amount'))->where('supplier_id', $postData['supplier_id'])->first();

                $supplier->deposit_amount = $dd->total_deposit_amount;
                //   $supplier->balance = $dd->total_deposit_amount - $dd->total_withdraw_amount;
                $supplier->save();
            });


        } catch (\Exception $e) {
            return redirect()->route('admin.depositlist')->with('error', $e->getMessage());
        }

        if ($request->has('submit_and_continue')) {
            return redirect()->back()->withInput()
                ->with('success', 'Saved successfully.');
        }

        return redirect()->route('admin.depositlist')->with('success', 'Successfully Deposit saved');

    }

    public function depositUpdate(Request $request, $id)
    {
        $request->validate([
            'supplier_id' => ['required', 'string', 'max:255'],
            'deposit_amount' => ['required'],
            'type' => ['required'],
            'payment_account_id' => ['required', 'integer'],
        ]);

        $postData = $request->except('_token');
        try {
            DB::transaction(function () use ($postData, $id) {

                // Find the existing deposit record
                $deposit = Deposit::findOrFail($id);

                if ($postData['type'] == "withdraw") {
                    $postData['withdraw_amount'] = $postData['deposit_amount'];
                    $postData['deposit_amount'] = 0;
                }

                // Update the deposit record
                $deposit->update($postData);

                // Recalculate the total deposit and withdrawal amounts for the supplier
                $dd = Deposit::select(DB::raw('SUM(deposit_amount) as total_deposit_amount'), DB::raw('SUM(withdraw_amount) as total_withdraw_amount'))
                    ->where('supplier_id', $postData['supplier_id'])
                    ->first();

                // Update the supplier's deposit amount based on the new totals
                $supplier = Supplier::find($postData['supplier_id']);
                $supplier->deposit_amount = $dd->total_deposit_amount - $dd->total_withdraw_amount;
                $supplier->save();

            });

            return redirect()->route('admin.depositlist')->with('success', 'Updated Successfully');

        } catch (\Exception $e) {
            return redirect()->route('admin.depositlist')->with('error', $e->getMessage());
        }
    }


    public function withdrawCreate()
    {
        $suppliers = [
            0 => Supplier::where('type', 0)->get(),
            1 => Supplier::where('type', 1)->get()
        ];
        $data['suppliers'] = $suppliers;

        return view('admin.withdraw.create', $data);
    }


    public function withdrawEdit($id)
    {
        $data['withdraw'] = Deposit::find($id);
        $data['withdraw_list'] = Deposit::where('supplier_id', $data['withdraw']->supplier_id)->get();
        $data['suppliers'] = AccountService::getAllSuppliers();
        $data['supplier'] = Supplier::find($data['withdraw']->supplier_id);
        return view('admin.withdraw.edit', $data);
    }

    public function withdrawStore(Request $request)
    {

        $request->validate([
            'supplier_id' => ['required', 'string', 'max:255'],
            'deposit_amount' => ['required'],
            'type' => ['required'],
            'payment_account_id' => ['required', 'integer'],
        ]);

        $postData = $request->except('_token');

        try {

            DB::transaction(function () use ($postData) {

                $supplier = Supplier::find($postData['supplier_id']);
                if ($postData['type'] == "withdraw") {
                    $postData['withdraw_amount'] = $postData['deposit_amount'];
                    $postData['deposit_amount'] = 0;
                }



                Deposit::create($postData);

                $dd = Deposit::select(DB::raw('SUM(withdraw_amount) as total_withdraw_amount'))->where('supplier_id', $postData['supplier_id'])->first();

                $supplier->withdraw_amount = $dd->total_withdraw_amount;
                $supplier->save();
            });


        } catch (\Exception $e) {
            return redirect()->route('admin.withdrawlist')->with('error', $e->getMessage());
        }


        if ($request->has('submit_and_continue')) {
            return redirect()->back()->withInput()
                ->with('success', 'Saved successfully.');
        }

        return redirect()->route('admin.withdrawlist')->with('success', 'Created Successfully');
    }


    public function withdrawUpdate(Request $request, $id)
    {
        // Validate the incoming data
        $request->validate([
            'supplier_id' => ['required', 'string', 'max:255'],
            'deposit_amount' => ['required'],
            'type' => ['required'],
            'payment_account_id' => ['required', 'integer'],
        ]);

        // Get the post data, except the '_token'
        $postData = $request->except('_token');

        try {
            DB::transaction(function () use ($postData, $id) {

                // Retrieve the deposit record by ID
                $deposit = Deposit::findOrFail($id);
                $supplier = Supplier::find($postData['supplier_id']);

                // Check if the transaction type is 'withdraw' and handle the update accordingly
                if ($postData['type'] == "withdraw") {
                    $postData['withdraw_amount'] = $postData['deposit_amount'];
                    $postData['deposit_amount'] = 0;
                }

                // Update the deposit record
                $deposit->update($postData);

                // Recalculate the supplier's deposit amount after the update
                $dd = Deposit::select(DB::raw('SUM(deposit_amount) as total_deposit_amount'), DB::raw('SUM(withdraw_amount) as total_withdraw_amount'))
                    ->where('supplier_id', $postData['supplier_id'])
                    ->first();

                // Update supplier's deposit balance
                $supplier->deposit_amount = $dd->total_deposit_amount - $dd->total_withdraw_amount;
                $supplier->save();
            });

            // Redirect to the withdraw list with a success message
            return redirect()->route('admin.withdrawlist')->with('success', 'Updated Successfully');
        } catch (\Exception $e) {
            // Return error message in case of failure
            return redirect()->route('admin.withdrawlist')->with('error', $e->getMessage());
        }
    }


    public function withdrawBySupplier(Request $request)
    {


        $supplier_id = $request['supplier_id'];

        $data['supplier'] = Supplier::where('id', $supplier_id)->first();
        $data['deposit_list'] = Deposit::where(['supplier_id' => $supplier_id])->get();
        //  $data['account_numbers'] = AccountService::getAllAccountNumbers(Session::get('currentSchoolId'));

        return view('admin.withdraw.viewSuplier', $data);
    }




    public function depositListBySupplier(Request $request)
    {


        $supplier_id = $request['supplier_id'];

        $data['supplier'] = Supplier::where('id', $supplier_id)->first();

        $data['deposit_list'] = Deposit::where(['supplier_id' => $supplier_id])->get();
        //     $data['account_numbers'] = AccountService::getAllAccountNumbers(Session::get('currentSchoolId'));

        return view('admin.deposit.viewSuplier', $data);
    }


    public function depositRemove($id)
    {

        $findClass = Deposit::find($id);
        if ($findClass) {
            $findClass->delete();
        }

        return redirect()->back()->with('success', 'Remove Successfully');
    }


    public function purchaseAddItem(Request $request)
    {

        $request->validate([
            'product_id' => ['required'],
        ]);
        $postData = $request->except('_token');
        try {

            DB::transaction(function () use ($postData) {
                Item::create($postData);
            });

            $data['PurchaseItems'] = Item::all();
            //  dd($data['PurchaseItems']);
            return view('admin.purchase.itemlist', $data)->render();


            // return response()->json([
            //     'success' => true,
            //     'html' => view('admin.buy_sell.trade-list', $data)->render()
            // ]);
        } catch (\Exception $e) {
            Log::error('Purchase Store Error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'An error occurred while creating the purchase.');
        }
    }


    public function purchaseRemoveItem(Request $request)
    {

        $request->validate([
            'id' => ['required'],
        ]);
        $postData = $request->except('_token');
        $findClass = Item::find($request['id']);
        if ($findClass) {
            $findClass->delete();
        }
        $data['PurchaseItems'] = Item::all();
        //  dd($data['PurchaseItems']);
        return view('admin.purchase.itemlist', $data)->render();


    }





    public function purchaseStore(Request $request)
    {

        $request->validate([
            'supplier_id' => ['required', 'string', 'max:255']
        ]);

        $postData = $request->except('_token');

        try {

            DB::transaction(function () use ($postData) {


                $inv = Purchase::orderBy('id', 'desc')->first();
                $new_invoice_id = $inv ? $inv->id + 1 : 1;
                $postData['invoice_no'] = "USG-" . sprintf("%06d", $new_invoice_id);

                $purchase = Purchase::create($postData);

                if (count($postData['items']) > 0) {

                    foreach ($postData['items'] as $item) {
                        if (!is_null($item['quantity'])) {

                            $item['supplier_id'] = $postData['supplier_id'];
                            $item['purchase_id'] = $purchase->id;
                            $item['created_at'] = $postData['created_at'];
                            PurchaseItem::create($item);
                            if (!is_null($item['discount_aed'])) {

                                $Deposit['created_at'] = $postData['created_at'];
                                $Deposit['supplier_id'] = $postData['supplier_id'];
                                $Deposit['purchase_id'] = $purchase->id;
                                $Deposit['payment_account_id'] = 2;
                                $Deposit['type'] = "Premium";
                                $Deposit['note'] = "PRE/DIS for unfix purchase.";
                                $Deposit['ref_no'] = $postData['ref_no'] ?? "N/A";
                                if ($item['discount_aed'] < 0) {
                                    $Deposit['withdraw_amount'] = 0;
                                    $Deposit['deposit_amount'] = abs($item['discount_aed']);
                                } else {
                                    $Deposit['deposit_amount'] = 0;
                                    $Deposit['withdraw_amount'] = $item['discount_aed'];
                                }

                                Deposit::create($Deposit);
                                $supplier = Supplier::find($postData['supplier_id']);

                                if ($supplier) {

                                    $dd = Deposit::select(DB::raw('SUM(deposit_amount) as total_deposit_amount'), DB::raw('SUM(withdraw_amount) as total_withdraw_amount'))->where('supplier_id', $postData['supplier_id'])->first();

                                    $supplier->deposit_amount = $dd->total_deposit_amount - $dd->total_withdraw_amount;
                                    $supplier->save();
                                } else {
                                    dd($postData['supplier_id']);
                                }
                            }
                        }
                    }
                }
            });

        } catch (\Exception $e) {
            return redirect()->route('admin.purchase.list')->with('error', $e->getMessage());
        }

        if ($request->has('submit_and_continue')) {
            return redirect()->back()->withInput()
                ->with('success', 'Saved successfully. You can continue new .');
        }
        return redirect()->route('admin.purchase.list')
            ->with('success', 'Saved successfully.');
    }


    public function purchaseRemove($id)
    {

        DB::transaction(function () use ($id) {
            $findClass = Purchase::find($id);
            if ($findClass) {
                $findClass->delete();
            }
            PurchaseItem::where("purchase_id", $id)->delete();

            Deposit::where("purchase_id", $id)->delete();

            return redirect()->back()->with('success', 'Remove Successfully');
        });
    }

    public function saleStore(Request $request)
    {
        $request->validate([
            'supplier_id' => ['required', 'string', 'max:255'],
            'deposit_amount' => ['required'],
        ]);

        $postData = $request->except('_token');

        try {

            DB::transaction(function () use ($postData) {


                $inv = Purchase::orderBy('id', 'desc')->first();
                $new_invoice_id = $inv ? $inv->id + 1 : 1;
                $saleData['invoice_no'] = "PFG-" . sprintf("%06d", $new_invoice_id);
                $saleData['type'] = "sale";
                $saleData['supplier_id'] = $postData['supplier_id'];
                $saleData['deposit_amount'] = $postData['deposit_amount'];
                $saleData['unfix_total'] = $postData['deposit_amount'];
                $saleData['note'] = $postData['note'] ?? "GOLD FIXED";
                $saleData['created_at'] = $postData['created_at'];

                $purchase = Purchase::create($saleData);

                $item['created_at'] = $postData['created_at'];
                $item['supplier_id'] = $postData['supplier_id'];
                $item['product_id'] = $postData['product_id'];
                $item['product_name'] = Product::find($postData['product_id'])->title;
                $item['quantity'] = $postData['quantity'];
                $item['pure_quantity'] = $postData['pure_quantity'];
                $item['unfix_subtotal'] = $postData['deposit_amount'];
                $item['purchase_id'] = $purchase->id;
                $item['type'] = "sale";
                PurchaseItem::create($item);

                $supplier = Supplier::find($postData['supplier_id']);

                if ($supplier) {

                    $dd = Purchase::select(DB::raw('SUM(unfix_total) as total_fix_amount'))->where(['supplier_id' => $postData['supplier_id'], 'type' => "sale"])->first();

                    $supplier->sell_amount = $dd->total_fix_amount;
                    $supplier->save();

                } else {
                    dd($$postData['supplier_id']);
                }
            });

        } catch (\Exception $e) {
            return redirect()->route('admin.fixedpurchase.list')->with('error', $e->getMessage());
        }


        if ($request->has('submit_and_continue')) {
            return redirect()->back()->withInput()
                ->with('success', 'Saved successfully.');
        }


        return redirect()->route('admin.fixedpurchase.list')->with('success', 'Successfully fix saved');
    }

    public function sale()
    {
        $suppliers = [
            0 => Supplier::where('type', 0)->get(),
            1 => Supplier::where('type', 1)->get()
        ];
        $data['suppliers'] = $suppliers;
        $data['products'] = Product::all();

        return view('admin.purchase.sale', $data);
    }




    public function purchaseList()
    {
        // dd(Purchase::all());
        $data['purchases'] = Purchase::where("type", "buy")->orderBy('created_at', 'desc')
            ->get();
        return view('admin.purchase.list', $data);
    }



    public function fixedPurchaseList()
    {
        $data['purchases'] = Purchase::where("type", "sale")->get();
        return view('admin.purchase.fixedpurchaselist', $data);
    }



    public function viewBySupplier(Request $request)
    {

        $supplier_id = $request['supplier_id'];

        $data['supplier'] = Supplier::where('id', $supplier_id)->first();
        $data['purchases'] = PurchaseItem::select(
            'product_id',
            'product_name',
            DB::raw('SUM(CASE WHEN type = "buy" THEN unfix_subtotal ELSE 0 END) as total_buy_unfix_subtotal'),
            DB::raw('SUM(CASE WHEN type = "buy" THEN quantity ELSE 0 END) as total_buy_quantity'),
            DB::raw('SUM(CASE WHEN type = "sale" THEN unfix_subtotal ELSE 0 END) as total_sale_unfix_subtotal'),
            DB::raw('SUM(CASE WHEN type = "sale" THEN quantity ELSE 0 END) as total_sale_quantity')
        )->where('supplier_id', $supplier_id)->groupBy('product_id', 'product_name')->get();

        $data['purities'] = Product::select('id', 'purity','title')->get();
        // foreach ($purities as $purity) {
        //     $data['purities'][$purity->id] = $purity->purity;
        // }


        return view('admin.purchase.viewSuplier', $data);
    }


    public function customerCreate()
    {

        return view('admin.customer.create');
    }



    public function customerStore(Request $request)
    {

        // country
        // type
        // code
        // address
        // city
        // land phone
        // id proof
        // valid_up_to
        // id_nmumebr
        // trade_license
        // margin
        // ib_column
        // buy_rate
        // sell_rate
        // refferer
        // refferer_code
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'land_phone' => ['required', 'string', 'max:255'],
            'id_proof' => ['required', 'string', 'max:255'],
            'valid_up_to' => ['required', 'string', 'max:255'],
            'trn_no' => ['required', 'string', 'max:255'],
            'id_number' => ['required', 'string', 'max:255'],
            'trade_license' => ['required', 'string', 'max:255'],
            'margin' => ['required', 'numeric', 'max:255'],
            'ib_commision' => ['required', 'numeric', 'max:255'],
            'buy_rate' => ['required', 'numeric', 'max:255'],
            'sell_rate' => ['required', 'numeric', 'max:255']

        ]);
        $postData = array_merge($request->except('_token'));

        $this->customerService->saveCustomer($postData);

        return redirect()->back()->with('success', 'Created Successfully');
    }


    public function customerList()
    {

        $data['customers'] = Customer::all();
        return view('admin.customer.list', $data);
    }

    public function customerUpdate($id)
    {


        $data['customer'] = Customer::find($id);
        return view('admin.customer.edit', $data);
    }


    public function customerEdit(Request $request)
    {

        $request->validate([
            'name' => ['required', 'string', 'max:255']
        ]);

        $postData = array_merge($request->except('_token'));

        $customer = Customer::find($postData['id']);

        if ($customer) {
            $customer->update($postData);
        } else {
            dd($$postData['customer_id']);
        }

        return redirect()->back()->with('success', 'Created Successfully');
    }



    public function customerDetails($customer_id)
    {


        $data['customer'] = Customer::findOrFail($customer_id);

        // $deposits = Deposit::select('id', 'created_at', DB::raw('"deposit" as type'))
        //     ->where('customer_id', $customer_id);
        // $purchaseItems = PurchaseItem::select('id', 'created_at', DB::raw('"buysell" as type'))
        //     ->where('customer_id', $customer_id);
        // $accounts = $deposits->union($purchaseItems)
        //     ->orderBy('created_at', 'asc')
        //     ->get();
        // foreach ($accounts as $account) {
        //     if ($account->type === 'deposit') {
        //         $account->data = Deposit::find($account->id);
        //     } else {
        //         $account->data = PurchaseItem::find($account->id);
        //     }
        // }
        // $data['accounts'] = $accounts;

        return view('admin.customer.details', $data);
    }


    public function customerView($customer_id)
    {


        $data['customer'] = Customer::findOrFail($customer_id);

        // $deposits = Deposit::select('id', 'created_at', DB::raw('"deposit" as type'))
        //     ->where('customer_id', $customer_id);
        // $purchaseItems = PurchaseItem::select('id', 'created_at', DB::raw('"buysell" as type'))
        //     ->where('customer_id', $customer_id);
        // $accounts = $deposits->union($purchaseItems)
        //     ->orderBy('created_at', 'asc')
        //     ->get();
        // foreach ($accounts as $account) {
        //     if ($account->type === 'deposit') {
        //         $account->data = Deposit::find($account->id);
        //     } else {
        //         $account->data = PurchaseItem::find($account->id);
        //     }
        // }
        // $data['accounts'] = $accounts;

        return view('admin.customer.deposit', $data);
    }


    public function purchaseEdit($id)
    {

        $data['purchase'] = Purchase::find($id);
        $data['suppliers'] = AccountService::getAllSuppliers();
        $data['products'] = Product::all();
        $data['account_numbers'] = AccountService::getAllAccountNumbers(Session::get('currentSchoolId'));
        $data['PurchaseItems'] = PurchaseItem::where('purchase_id', $id)->get();
        return view('admin.purchase.unfix-edit', $data);
    }

    public function purchaseUpdate(Request $request)
    {
        $id = $request->id;
        $request->validate([
            'supplier_id' => ['required', 'string', 'max:255']
        ]);

        $postData = $request->except('_token');

        try {
            DB::transaction(function () use ($postData, $id) {

                // Fetch existing purchase
                $purchase = Purchase::findOrFail(id: $id);

                // Update purchase details
                $purchase->update($postData);

                // Remove old purchase items
                PurchaseItem::where('purchase_id', $id)->delete();

                // Re-add purchase items
                if (isset($postData['items']) && count($postData['items']) > 0) {
                    foreach ($postData['items'] as $item) {
                        if (!is_null($item['quantity'])) {

                            $item['supplier_id'] = $postData['supplier_id'];
                            $item['purchase_id'] = $id;
                            $item['created_at'] = $postData['created_at'];

                            PurchaseItem::create($item);

                            // Handle deposits or withdrawals
                            if (!is_null($item['discount_aed'])) {
                                $Deposit['created_at'] = $postData['created_at'];
                                $Deposit['supplier_id'] = $postData['supplier_id'];
                                $Deposit['purchase_id'] = $id;
                                $Deposit['payment_account_id'] = 2;
                                $Deposit['type'] = "Premium";
                                $Deposit['note'] = "PRE/DIS for updated unfix purchase.";
                                $Deposit['ref_no'] = $postData['ref_no'] ?? "N/A";
                                if ($item['discount_aed'] < 0) {
                                    $Deposit['withdraw_amount'] = 0;
                                    $Deposit['deposit_amount'] = abs($item['discount_aed']);
                                } else {
                                    $Deposit['deposit_amount'] = 0;
                                    $Deposit['withdraw_amount'] = $item['discount_aed'];
                                }

                                Deposit::create($Deposit);

                                // Update supplier's deposit amount
                                $supplier = Supplier::find($postData['supplier_id']);
                                if ($supplier) {
                                    $dd = Deposit::select(
                                        DB::raw('SUM(deposit_amount) as total_deposit_amount'),
                                        DB::raw('SUM(withdraw_amount) as total_withdraw_amount')
                                    )->where('supplier_id', $postData['supplier_id'])->first();

                                    $supplier->deposit_amount = $dd->total_deposit_amount - $dd->total_withdraw_amount;
                                    $supplier->save();
                                }
                            }
                        }
                    }
                }
            });

            return redirect()->route('admin.purchase.list')->with('success', 'Updated Successfully');
        } catch (\Exception $e) {
            return redirect()->route('admin.purchase.list')->with('error', $e->getMessage());
        }
    }


    // public function saleEdit()
    // {

    //     $data['suppliers'] = AccountService::getAllSuppliers();
    //     $data['products'] = Product::all();
    //     return view('admin.purchase.sale-edit', $data);
    // }

    public function saleEdit($id)
    {
        // Fetch the purchase record to edit
        $purchase = Purchase::findOrFail($id);

        // Get related purchase items for this specific purchase
        $purchaseItem = PurchaseItem::where('purchase_id', $purchase->id)->first();

        $purchases = PurchaseItem::select(
            'product_id',
            'product_name',
            DB::raw('SUM(CASE WHEN type = "buy" THEN unfix_subtotal ELSE 0 END) as total_buy_unfix_subtotal'),
            DB::raw('SUM(CASE WHEN type = "buy" THEN quantity ELSE 0 END) as total_buy_quantity'),
            DB::raw('SUM(CASE WHEN type = "sale" THEN unfix_subtotal ELSE 0 END) as total_sale_unfix_subtotal'),
            DB::raw('SUM(CASE WHEN type = "sale" THEN quantity ELSE 0 END) as total_sale_quantity')
        )->where('supplier_id', $purchase->supplier_id)->groupBy('product_id', 'product_name')->get();

        // Get the supplier and related data
        $supplier = Supplier::find($purchase->supplier_id);
        $products = Product::all(); // Assuming this is used in a dropdown or elsewhere
        // Retrieve purities for products (if needed)
        $pur = Product::select('id', 'purity')->get();
        $purities = [];
        foreach ($pur as $purity) {
            $purities[$purity->id] = $purity->purity;
        }

        // Pass everything to the view
        return view('admin.purchase.sale-edit', compact('purchase', 'purities', 'purchases', 'purchaseItem', 'supplier', 'products', ));
    }



    public function editStore(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'supplier_id' => ['required', 'string', 'max:255'],
            'deposit_amount' => ['required'],
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'numeric'],
        ]);

        $postData = $request->except('_token');

        try {
            DB::transaction(function () use ($postData, $id) {
                // Find the purchase record to update
                $purchase = Purchase::findOrFail($id);
                $purchase->supplier_id = $postData['supplier_id'];
                $purchase->deposit_amount = $postData['deposit_amount'];
                $purchase->unfix_total = $postData['deposit_amount'];
                $purchase->note = $postData['note'] ?? "GOLD FIXED";
                $purchase->created_at = $postData['created_at'];
                $purchase->save();

                // Update the purchase item (assuming one product can be updated in a single sale)
                $purchaseItem = PurchaseItem::where('purchase_id', $id)->first();
                $purchaseItem->product_id = $postData['product_id'];
                $purchaseItem->product_name = Product::find($postData['product_id'])->title;
                $purchaseItem->quantity = $postData['quantity'];
                $purchaseItem->pure_quantity = $postData['pure_quantity'];
                $purchaseItem->unfix_subtotal = $postData['deposit_amount'];
                $purchaseItem->save();

                // Update the supplier's sell amount
                $supplier = Supplier::find($postData['supplier_id']);
                if ($supplier) {
                    $totalFixAmount = Purchase::where(['supplier_id' => $postData['supplier_id'], 'type' => "sale"])->sum('unfix_total');
                    $supplier->sell_amount = $totalFixAmount;
                    $supplier->save();
                }

            });

            return redirect()->route('admin.fixedpurchase.list')->with('success', 'Sale record updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.fixedpurchase.list')->with('error', 'An error occurred while updating the record: ' . $e->getMessage());
        }
    }




    ///////////////////////////////////////////////////////////


    public function clientDetails($client_id)
    {


        $data['supplier'] = Supplier::findOrFail($client_id);

        $deposits = Deposit::select('id', 'created_at', DB::raw('"deposit" as type'))
            ->where('supplier_id', $client_id);

        $purchaseItems = PurchaseItem::select('id', 'created_at', DB::raw('"buysell" as type'))
            ->where('supplier_id', $client_id);

        $accounts = $deposits->union($purchaseItems)
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($accounts as $account) {
            if ($account->type === 'deposit') {
                $account->data = Deposit::find($account->id);
            } else {
                $account->data = PurchaseItem::find($account->id);
            }
        }



        $data['accounts'] = $accounts;

        $business = Bussiness::where('id', session()->get(key: 'bussinessId'))->first();
        $data['business'] = $business;
        return view('admin.client.details', $data);
    }





    ///////////////////////////////////////////////////////////

}
