<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PremiumSellerPlan;
use App\Models\PlanFeature;
use App\Models\Feature;

class PremiumPlanController extends Controller
{
    public function index()
    {
        // Загружаем все планы с их фичами и связанными фичами
        $plans = PremiumSellerPlan::with('planFeatures.feature')->get();

        // Подготовим удобный массив для отображения
        $plans = $plans->map(function($plan) {
            $features = [];

            foreach($plan->planFeatures as $pf) {
                if($pf->feature) {
                    $features[] = ($pf->feature->slug === 'products-limit')
                        ? (is_null($pf->value) || $pf->value === '' ? 'Unlimited products' : 'Up to '.$pf->value.' products')
                        : ucwords(str_replace(['-','_','support:-','analytics:-','visibility:-','priority-placement:-'], [' ',' ','',' ','',''], $pf->feature->slug));
                }
            }

            return [
                'id' => $plan->id,
                'name' => $plan->name,
                'price' => $plan->price,
                'popular' => $plan->popular,
                'features' => $features,
            ];
        });

        return view('dashboard.admin.premium-plans.index', compact('plans'));
    }

    public function create()
    {
        return view('dashboard.admin.premium-plans.create');
    }

    public function store(Request $request)
{
    // Валидация
    $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|string|max:255',
        'popular' => 'nullable|boolean',
        'features' => 'nullable|array',
    ]);

    // Создаем план
    $plan = PremiumSellerPlan::create([
        'name' => $request->name,
        'price' => $request->price,
        'popular' => $request->has('popular'),
    ]);

    // Привязываем фичи с их значениями
    if ($request->has('features')) {
        foreach ($request->features as $featureId => $data) {
            if (!empty($data['enabled'])) {
                PlanFeature::create([
                    'plan_id' => $plan->id,
                    'feature_id' => $featureId,
                    'value' => $data['value'] ?? null,
                ]);
            }
        }
    }

    return redirect()->route('admin.premium-plans.index')
                     ->with('success', 'Plan created successfully.');
}

    public function edit($id)
{
    $plan = PremiumSellerPlan::with('planFeatures.feature')->findOrFail($id);
    $features = Feature::all();

    // Подготавливаем массив для удобного отображения в форме
    $planFeatures = [];
    foreach ($features as $feature) {
        $pf = $plan->planFeatures->firstWhere('feature_id', $feature->id);
        $planFeatures[$feature->id] = [
            'enabled' => $pf ? true : false,
            'value' => $pf ? $pf->value : ''
        ];
    }

    return view('dashboard.admin.premium-plans.edit', compact('plan', 'features', 'planFeatures'));
}

    public function update(Request $request, $id)
{
    $plan = PremiumSellerPlan::findOrFail($id);

    $plan->name = $request->name;
    $plan->price = $request->price;
    $plan->popular = $request->has('popular');
    $plan->save();

    // Обновляем фичи
    $plan->planFeatures()->delete(); // Удаляем старые
    if($request->has('features')) {
        foreach($request->features as $featureId => $data) {
            if(isset($data['enabled'])) {
                $plan->planFeatures()->create([
                    'feature_id' => $featureId,
                    'value' => $data['value'] ?? null
                ]);
            }
        }
    }

    return redirect()->route('admin.premium-plans.index')->with('success', 'Plan updated successfully.');
}

    public function destroy($id)
{
    // Находим план
    $plan = PremiumSellerPlan::findOrFail($id);

    // Удаляем связанные фичи
    $plan->planFeatures()->delete();

    // Удаляем сам план
    $plan->delete();

    return redirect()->route('admin.premium-plans.index')
                     ->with('success', 'Plan deleted successfully.');
}
}
