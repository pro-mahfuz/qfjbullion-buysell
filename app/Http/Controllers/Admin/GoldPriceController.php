<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoldRates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GoldPriceController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sell_price' => 'required|numeric',
            'buy_price' => 'required|numeric',
        ]);
    
        $goldPrice = $validated['sell_price'];
    
        if (!$goldPrice) {
            return response()->json(['error' => 'No gold price provided'], 400);
        }
    
        // Save in cache
        Cache::put('live_gold_price', $goldPrice, 60); // 60 seconds cache
    
        return response()->json(['status' => 'Gold price received', 'gold_price' => $goldPrice]);
    }
    
    public function getgoldprice()
    {
        $goldPrice = Cache::get('live_gold_price');

        if (!$goldPrice) {
            return response()->json(['error' => 'Gold price not available'], 404);
        }
    
        return response()->json(['gold_sell_price' => $goldPrice]);
    }
    
    
    

    public function getGoldRatesApi()
    {
        $goldRates = GoldRates::select('id', 'product_id', 'product_slug', 'adjustment_amount')->get();
        return response()->json(['data' => $goldRates], 200);
    }

    public function getGoldRates()
    {
        $goldRates = GoldRates::select('id', 'product_id', 'product_slug', 'adjustment_amount')->get();
        return view('admin.gold_rate.list', compact('goldRates'));
    }

    public function goldUpdateView()
    {
        $goldRates = GoldRates::select('id', 'product_id', 'product_slug', 'adjustment_amount')->get();
        return view('admin.gold_rate.update', compact('goldRates'));
    }

    public function updateGoldRates(Request $request)
    {
        foreach ($request->adjustments as $adjustment) {
            GoldRates::where('id', $adjustment['id'])
                ->update([
                    'adjustment_amount' => $adjustment['adjustment_amount'],
                ]);
        }

        return redirect()->route('admin.gold-rates.list')->with('success', 'Adjustments updated successfully!');
    }
}
