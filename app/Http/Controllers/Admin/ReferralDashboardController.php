<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\Reward;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
class ReferralDashboardController extends Controller
{

    public function show()
    {
        list($referralSummary, $uniqueByCustomer) = $this->getReferralSummary();
        $perPage = 20;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $items = $uniqueByCustomer->forPage($currentPage, $perPage);

        $paginatedRewards = new LengthAwarePaginator(
            $items,
            $uniqueByCustomer->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );


        return view('admin.referral.dashboard', compact('referralSummary', 'paginatedRewards'));
    }


    private function getReferralSummary()
    {
        $referral = Referral::all();
        // dd($referral);
        $reward = Reward::with(['customer', 'referrals'])->get();
        // dd($reward);
        $referralSummary = [
            'Total Referrals' => $referral->count(),
            'Total Customer' => collect($reward)->groupBy('customer_id')->count(),
            'Total Reward Amount' => collect($reward)->sum('reward_amount'),
            'Total Referral amount' => collect($referral)->sum('total_referral_amount'),
        ];

        $uniqueByCustomer = collect($reward)
            ->groupBy('customer_id')
            ->map(function ($group) {
                $firstReward = $group->first();
                $firstReward->total_rewards = $group->sum('reward_amount');
                $firstReward->referrals = $group->first()->referrals;
                return $firstReward;
            })
            ->values();

        return [$referralSummary, $uniqueByCustomer];
    }

    public function history($id)
    {
        $rewards = Reward::with(['customer', 'referrals', 'transaction'])
            ->where('customer_id', operator: $id)->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.referral.history', compact('rewards'));
    }

    public function listReferral()
    {
        $result = Referral::all();
        return view('admin.referral.list', compact('result'));
    }


    public function create()
    {
        return view('admin.referral.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'total_referral_amount' => 'required|numeric',
            'percentage' => 'required|numeric',
        ]);

        Referral::create([
            'title' => $request->title,
            'total_referral_amount' => $request->total_referral_amount,
            'first_person_amount' => $request->total_referral_amount,
            'percentage' => $request->percentage,
        ]);

        return redirect()->route('admin.refferal.list')->with('success', 'Referral created successfully');
    }

    public function destroy($id)
    {
        Referral::destroy($id);
        return redirect()->route('admin.refferal.list')->with('success', 'Referral deleted successfully');
    }

}
