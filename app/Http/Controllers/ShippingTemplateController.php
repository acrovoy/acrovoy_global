<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\ShippingTemplate;
use App\Models\ShippingTemplateTranslation;
use App\Models\Country;
use App\Models\Location;
use App\Models\Warehouse;
use App\Services\Company\ActiveContextService;

class ShippingTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // если есть middleware проверки роли:
        // $this->middleware('role:manufacturer');
    }

    /**
     * List all shipping templates
     */
    public function index()
    {


        $context = app(ActiveContextService::class);



        $supplierId = $context->id();
        $supplierType = $context->type();

        $warehouses = Warehouse::where('provider_id', $supplierId)
        ->where('provider_type', $supplierType)
        ->get();

        $templates = ShippingTemplate::with('locations', 'warehouse')
            ->where('provider_type', $supplierType)
            ->where('provider_id', $supplierId)
            ->get();

        return view('dashboard.supplier.shipping-templates.index', compact('templates', 'warehouses'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $countries = Country::withCurrentTranslation()
            ->orderBy('name')->get();
        $allLocations = Location::orderBy('id')->get();
        $children = $allLocations->groupBy('parent_id');

        // Привязка регионов и городов к странам
        $countries = $countries->map(function ($country) use ($allLocations, $children) {
            // регионы этой страны
            $regions = $allLocations->where('country_id', $country->id)->where('parent_id', null);

            $regions = $regions->map(function ($region) use ($children) {
                $region->children_recursive = $children->get($region->id) ?? collect();
                return $region;
            });

            $country->locations = $regions;
            return $country;
        });

        $shippingTemplate = new ShippingTemplate();
        $selectedLocations = $shippingTemplate->locations->pluck('id')->toArray();

        return view('dashboard.supplier.shipping-templates.create', compact(
            'shippingTemplate',
            'countries',
            'selectedLocations'
        ));
    }

    /**
     * Store new shipping template
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|array',
            'description' => 'nullable|array',
            'price' => 'required|numeric|min:0',
            'delivery_time' => 'nullable|string|max:255',
            'locations' => 'nullable|array',
            'locations.*' => 'exists:locations,id',
            'price_unit' => 'required|in:per_item,per_kg,per_cubic_meter,flat',
        ]);

        DB::transaction(function () use ($data) {

            $context = app(ActiveContextService::class);



            $supplierId = $context->id();
            $supplierType = $context->type();


            // 1️⃣ Создаем базовую запись шаблона
            $template = ShippingTemplate::create([
                'provider_id' => $supplierId,
                'provider_type' => $supplierType,
                'price' => $data['price'],
                'price_unit' => $data['price_unit'],
                'delivery_time' => $data['delivery_time'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // 2️⃣ Создаем мультиязычные переводы
            foreach ($data['title'] as $locale => $title) {

                // ❗ если title пустой — пропускаем язык
                if (empty($title)) {
                    continue;
                }

                ShippingTemplateTranslation::create([
                    'shipping_template_id' => $template->id,
                    'locale' => $locale,
                    'title' => $title,
                    'description' => $data['description'][$locale] ?? null,
                ]);
            }

            // 3️⃣ Привязка стран
            $template->locations()->sync($data['locations'] ?? []);
        });

        return redirect()->route('supplier.shipping-templates.index')
            ->with('success', 'Shipping template created successfully');
    }

    /**
     * Show edit form
     */
    public function edit(ShippingTemplate $shippingTemplate)
    {
        $context = app(ActiveContextService::class);



        $supplierId = $context->id();


        // Получаем все страны
        $countries = Country::withCurrentTranslation()
            ->orderBy('name')->get();

        // Получаем все локации
        $allLocations = Location::orderBy('id')->get();

        // Группируем дочерние локации по parent_id
        $children = $allLocations->groupBy('parent_id');

        // Привязка регионов и городов к странам
        $countries = $countries->map(function ($country) use ($allLocations, $children) {
            // Регионы этой страны
            $regions = $allLocations->where('country_id', $country->id)->where('parent_id', null);

            $regions = $regions->map(function ($region) use ($children) {
                $region->children_recursive = $children->get($region->id) ?? collect();
                return $region;
            });

            $country->locations = $regions;
            return $country;
        });

        // Определяем выбранные локации
        $selectedLocations = $shippingTemplate->locations->pluck('id')->toArray();

        return view('dashboard.supplier.shipping-templates.edit', compact(
            'shippingTemplate',
            'countries',
            'selectedLocations'
        ));
    }

    /**
     * Update existing template
     */
    public function update(Request $request, ShippingTemplate $shippingTemplate)
    {

        $context = app(ActiveContextService::class);







        // Валидация
        $data = $request->validate([
            'title' => 'required|array',
            'description' => 'nullable|array',
            'price' => 'required|numeric|min:0',
            'delivery_time' => 'nullable|string|max:255',
            'locations' => 'nullable|array',
            'locations.*' => 'exists:locations,id',
            'price_unit' => 'required|in:per_item,per_kg,per_cubic_meter,flat',

        ]);

        DB::transaction(function () use ($shippingTemplate, $data) {

            // 1️⃣ Обновляем базовые поля шаблона
            $shippingTemplate->update([
                'price' => $data['price'],
                'price_unit' => $data['price_unit'],
                'delivery_time' => $data['delivery_time'] ?? null,
                'updated_by' => auth()->id(),
            ]);

            // 2️⃣ Обновляем мультиязычные переводы
            foreach ($data['title'] as $locale => $title) {


                // ❗ если title пустой — пропускаем язык
                if (empty($title)) {
                    continue;
                }

                // Если перевод уже существует, обновляем
                $translation = $shippingTemplate->translations()->where('locale', $locale)->first();
                if ($translation) {
                    $translation->update([
                        'title' => $title,
                        'description' => $data['description'][$locale] ?? null,
                    ]);
                } else {
                    // Если перевода нет, создаем
                    ShippingTemplateTranslation::create([
                        'shipping_template_id' => $shippingTemplate->id,
                        'locale' => $locale,
                        'title' => $title,
                        'description' => $data['description'][$locale] ?? null,
                    ]);
                }
            }

            // 3️⃣ Обновляем привязку стран
            $shippingTemplate->locations()->sync($data['locations'] ?? []);
        });

        return redirect()->route('supplier.shipping-templates.index')
            ->with('success', 'Shipping template updated successfully');
    }

    /**
     * Delete template
     */
    public function destroy(ShippingTemplate $shippingTemplate)
    {
        $context = app(ActiveContextService::class);





        $shippingTemplate->locations()->detach();
        $shippingTemplate->delete();

        return redirect()->route('supplier.shipping-templates.index')
            ->with('success', 'Shipping template deleted successfully');
    }


    public function attachWarehouse(Request $request, ShippingTemplate $template)
{
    $data = $request->validate([
        'warehouse_id' => 'nullable|integer|exists:warehouses,id',
    ]);


    

    $template->update([
        'warehouse_id' => $data['warehouse_id'],
    ]);



    return back()->with('success', 'Warehouse linked to shipping template');
}

public function toggleActive(ShippingTemplate $template)
{
    $template->update([
        'is_active' => !$template->is_active,
    ]);

    return back()->with('success', 'Status updated successfully');
}



}
