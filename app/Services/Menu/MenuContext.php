<?php

namespace App\Services\Menu;

use App\Models\OrderDispute;
use App\Domain\Negotiation\Models\RfqOffer;

use App\Models\CompanyUser;
use App\Services\Company\ActiveContextService;
use App\Facades\ActiveContext;

class MenuContext
{
    public static function metrics($user): array
    {
        $data = [
            'openDisputeCount' => 0,
            'newOfferCount' => 0,
            'acceptedOfferCount' => 0,
        ];

        if(!$user) return $data;

        /**
         * ======================================
         * COMPANY-AWARE CONTEXT (NEW SYSTEM)
         * ======================================
         */
        if (ActiveContext::isCompany()) {

    $companyId = ActiveContext::id();
    $companyType = ActiveContext::type();

            $membership = CompanyUser::where('user_id', $user->id)
                ->where('company_type', $companyType)
                ->where('company_id', $companyId)
                ->first();

            if (!$membership) {
                return $data;
            }

            // 🔹 SUPPLIER COMPANY LOGIC
            if (str_contains($companyType, 'Supplier')) {

                $data['openDisputeCount'] = OrderDispute::whereHas('order.items.product', function ($q) use ($companyId) {
                    $q->where('supplier_id', $companyId);
                })
                ->whereIn('status', ['pending', 'supplier_offer', 'rejected', 'admin_review'])
                ->count();

                $data['acceptedOfferCount'] = RfqOffer::where('participant_type', \App\Models\Supplier::class)
    ->where('participant_id', $companyId)
    ->where('status', 'accepted')
    ->count();
            }

            // 🔹 LOGISTICS COMPANY LOGIC (готово под будущее)
            if (str_contains($companyType, 'LogisticCompany')) {
                // сюда позже добавишь shipping metrics
            }

            return $data;
        }

        /**
         * ======================================
         * FALLBACK LEGACY SYSTEM (OLD ROLE)
         * ======================================
         */

        // BUYER
        if($user->role === 'buyer') {

            $data['openDisputeCount'] = OrderDispute::whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->whereIn('status', ['pending', 'supplier_offer', 'rejected', 'admin_review'])
            ->count();

            // $data['newOfferCount'] = RfqOffer::whereHas('rfq', function ($q) use ($user) {
            //     $q->where('buyer_id', $user->id)
            //       ->where('status', 'active');
            // })
            // ->whereNull('buyer_viewed_at')
            // ->count();
        }

        // SUPPLIER (LEGACY LINKED MODEL)
        if($user->role === 'manufacturer' && $user->supplier) {

            $data['openDisputeCount'] = OrderDispute::whereHas('order.items.product', function ($q) use ($user) {
                $q->where('supplier_id', $user->supplier->id);
            })
            ->whereIn('status', ['pending', 'supplier_offer', 'rejected', 'admin_review'])
            ->count();

            $data['acceptedOfferCount'] = RfqOffer::where('supplier_id', $user->supplier->id)
                ->where('status', 'accepted')
                ->count();
        }

        return $data;
    }

    public static function context($user): string
{
    if (!$user) return 'buyer';

    /**
     * 1. COMPANY CONTEXT FIRST
     */
    if (\App\Facades\ActiveContext::isCompany()) {

        $type = \App\Facades\ActiveContext::type();

        if (str_contains($type, 'Supplier')) {
            return 'supplier';
        }

        if (str_contains($type, 'LogisticCompany')) {
            return 'logistics';
        }
    }

    /**
     * 2. FALLBACK ROLE CONTEXT
     */
    return match ($user->role) {
        'buyer' => 'buyer',
        'manufacturer' => 'supplier',
        default => 'buyer',
    };
}
}