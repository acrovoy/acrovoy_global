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

        if (!$user) return $data;

        $ctx = app(ActiveContextService::class);

        /**
         * =========================
         * COMPANY MODE
         * =========================
         */
        if ($ctx->isCompany()) {

            $companyId = $ctx->id();
            $companyType = $ctx->type();

            $membership = CompanyUser::where('user_id', $user->id)
                ->where('company_type', $companyType)
                ->where('company_id', $companyId)
                ->first();

            if (!$membership) {
                return $data;
            }

            // 🔹 SUPPLIER (ANY TYPE: TEAM OR INDIVIDUAL)
            if ($ctx->isSupplier()) {

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

            // 🔹 LOGISTICS COMPANY
            if ($ctx->type() === \App\Models\LogisticCompany::class) {
                // future logic
            }

            return $data;
        }

        /**
         * =========================
         * PERSONAL MODE (LEGACY)
         * =========================
         */

        if ($user->setting('platform_mode') === 'buyer') {

            $data['openDisputeCount'] = OrderDispute::whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
                ->whereIn('status', ['pending', 'supplier_offer', 'rejected', 'admin_review'])
                ->count();
        }

        if ($user->setting('platform_mode') === 'supplier' && $user->supplier) {

            $supplierId = $user->supplier->id;

            $data['openDisputeCount'] = OrderDispute::whereHas('order.items.product', function ($q) use ($supplierId) {
                $q->where('supplier_id', $supplierId);
            })
                ->whereIn('status', ['pending', 'supplier_offer', 'rejected', 'admin_review'])
                ->count();

            $data['acceptedOfferCount'] = RfqOffer::where('participant_id', $supplierId)
                ->where('status', 'accepted')
                ->count();
        }

        return $data;
    }

    public static function context($user): string
{
    if (!$user) return 'buyer';

    $ctx = app(ActiveContextService::class);

    /**
     * COMPANY MODE
     */
    if ($ctx->isCompany()) {

        if ($ctx->isSupplier()) {
            return 'supplier';
        }

        if ($ctx->type() === \App\Models\Buyer::class) {
            return 'buyer';
        }

        if ($ctx->type() === \App\Models\LogisticCompany::class) {
            return 'logistics';
        }
    }

    /**
     * PERSONAL MODE
     */
    if ($ctx->isPersonal()) {

        // 🔥 FIX 1: supplier individual
        if ($ctx->role() === 'supplier') {
            return 'supplier_individual';
        }

        // 🔥 FIX 2: buyer individual (явно)
        if ($ctx->role() === 'buyer') {
            return 'buyer_individual';
        }

        return 'buyer_individual';
    }

    return 'buyer_individual';
}




}