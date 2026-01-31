<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PremiumSellerPlan;

class PremiumSellerPlanController extends Controller
{
    public function index()
    {
        // Загружаем все планы с их фичами
        $plans = PremiumSellerPlan::with('planFeatures.feature')->get();




        $currentPlanId = auth()->user()->premium_plan_id ?? null;

        return view(
            'dashboard.manufacturer.premium-seller-plans',
            compact('plans', 'currentPlanId')
        );
    }

    public function compare()
    {
        // Загружаем все планы с их фичами
        $plans = PremiumSellerPlan::with('planFeatures.feature')->get();

        return view(
            'dashboard.manufacturer.premium-seller-plans-compare',
            compact('plans')
        );
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:premium_seller_plans,id',
        ]);

        $user = $request->user();

        // Если выбрали Free, сбрасываем подписку
        if ($request->plan_id == 1) {
            $user->premium_plan_id = null;
        } else {
            $user->premium_plan_id = $request->plan_id;
        }

        $user->save();

        return redirect()
            ->route('manufacturer.premium-plans.index')
            ->with('success', 'Plan updated successfully!');
    }
}
