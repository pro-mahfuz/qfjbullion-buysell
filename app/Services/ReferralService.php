<?php


namespace App\Services;

use App\Models\Customer;
use App\Models\Referral;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Support\Collection;

class ReferralService
{
    public function getReferrals(): Collection
    {
        return Referral::where('is_active', 1)->get();
    }

    public function saveReferral(array $data): void
    {
        Referral::create(attributes: $data);
    }

    public function updateReferral(array $data, Referral $referral): void
    {
        $referral->update($data);
    }

    public function getReferralById(int $id): Referral
    {
        return Referral::findOrFail($id);
    }


}
