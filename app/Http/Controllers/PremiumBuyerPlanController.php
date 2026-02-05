<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PremiumSellerPlan;

class PremiumBuyerPlanController extends Controller
{
    public function index()
    {
        $user = auth()->user();

    // 🔹 Определяем тип планов по роли пользователя
    // По умолчанию — supplier (старая логика)
    $targetType = 'buyer';

     // 🔹 Получаем планы нужного типа
    $plans = PremiumSellerPlan::where('target_type', $targetType)
        ->with('planFeatures')
        ->get();

    // 🔹 currentPlanId (КРИТИЧНО)
    $currentPlanId = null;

    if ($user) {
        $currentPlanId = $targetType === 'buyer'
            ? $user->buyer_premium_plan_id
            : $user->premium_plan_id;
    }

    return view('dashboard.buyer.premium-buyer-plans', compact(
        'plans',
        'currentPlanId'
    ));
    }

    public function compare()
    {
        // Загружаем все планы с их фичами
        $plans = PremiumSellerPlan::where('target_type', 'buyer')
        ->with('planFeatures.feature')->get();

        return view(
            'dashboard.buyer.premium-buyer-plans-compare',
            compact('plans')
        );
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:premium_buyer_plans,id',
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
            ->route('buyer.premium-plans.index')
            ->with('success', 'Plan updated successfully!');
    }
}
