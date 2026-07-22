<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Validator;

class ProductController extends Controller
{

    public function list()
    {
        $result = Product::all();
        return view('admin.product.list', compact('result'));
    }

    public function create()
    {
        return view('admin.product.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price_aed' => 'required|numeric|min:0',
            'price_oz' => 'required|numeric|min:0',
            'price_usd' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0|max:100',
            'purity' => 'required|numeric|min:0|max:100',
        ]);


        Product::create([
            'title' => $request->title,
            'description' => $request->description,
            'price_aed' => $request->price_aed,
            'price_oz' => $request->price_oz,
            'price_usd' => $request->price_usd,
            'tax' => $request->tax,
            'purity' => $request->purity,
        ]);

        return redirect()->route('admin.product.list')
            ->with('success', 'Product created successfully.');
    }



    public function destroy($id)
    {
        Product::destroy($id);
        return redirect()->route('admin.product.list')->with('success', 'Product deleted successfully');
    }

    public function shopList()
    {
        $result = Product::where('is_shop', 1)->get();
        return view('admin.product.shop-list', compact('result'));
    }

    public function shopCreate()
    {
        $categories = Category::all();

        return view('admin.product.shop-create', compact('categories'));
    }

    public function shopStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'weight' => 'required|numeric|min:0|max:10000',
            'weight_type' => 'required|string',
            'qty' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        // dd($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('images/shop'), $imageName);

        $product = Product::create([
            'title' => $request->title,
            'description' => $request->description,
            'weight' => $request->weight,
            'qty' => $request->qty,
            'price_usd' => $request->price_usd,
            'image' => $imageName,
            'is_shop' => 1,
            'weight_type' => $request->weight_type,
        ]);

        $product->categories()->attach($request->category_id);

        return redirect()->route('admin.product.shop.list')
            ->with('success', 'Product created successfully.');
    }

    public function shopDestroy($id)
    {
        Product::destroy($id);
        return redirect()->route('admin.product.shop.list')->with('success', 'Product deleted successfully');
    }

    public function updateQty(Request $request)
    {

        $request->validate([
            'id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
        ]);

        $product = Product::find($request->id);
        $product->qty = $request->qty;
        $product->save();

        return response()->json(['success' => true]);
    }

}
