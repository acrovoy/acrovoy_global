<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\ShippingTemplate;
use App\Models\ShippingTemplateTranslation;
use App\Models\Country;
use App\Models\Location;
use App\Models\Warehouse;
use App\Domain\Shipping\Models\DeliveryType;
use App\Services\Company\ActiveContextService;

class ShippingTemplateController extends Controller
{
    public function __construct(
    private ActiveContextService $context
) {
    
}

    /**
     * List all shipping templates
     */
    public function index()
    {


        $this->authorize('viewAny', ShippingTemplate::class);



        $entity = $this->context->entity();

        abort_unless($entity, 404);

        $warehouses = Warehouse::where('provider_type', $entity::class)
            ->where('provider_id', $entity->getKey())
            ->get();

        $templates = ShippingTemplate::with('locations', 'warehouse')
            ->where('provider_type', $entity::class)
            ->where('provider_id', $entity->getKey())
            ->get();

        return view('dashboard.supplier.shipping-templates.index', compact('templates', 'warehouses'));
    }

    /**
     * Show create form
     */
    public function create()
    {

    $this->authorize('create', ShippingTemplate::class);

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

        $deliveryTypes = DeliveryType::query()
    ->with('translations')
    ->where('is_active', true)
    ->orderBy('sort_order')
    ->orderBy('id')
    ->get();

        return view('dashboard.supplier.shipping-templates.create', compact(
            'shippingTemplate',
            'countries',
            'selectedLocations',
            'deliveryTypes',
        ));
    }

    /**
     * Store new shipping template
     */
   public function store(Request $request)
{
    $this->authorize('create', ShippingTemplate::class);

    $data = $request->validate([
        'title' => 'required|array',
        'description' => 'nullable|array',
        'price' => 'required|numeric|min:0',
        'price_unit' => 'required|in:per_item,per_kg,per_cubic_meter,flat',
        'delivery_time' => 'nullable|string|max:255',
        'locations' => 'nullable|array',
        'locations.*' => 'exists:locations,id',
        'delivery_options' => 'nullable|array',
        'delivery_options.*.price' => 'nullable|numeric|min:0',
        'delivery_options.*.price_unit' => 'nullable|in:per_item,per_kg,per_cubic_meter,flat',
    ]);

    DB::transaction(function () use ($data) {

        $entity = $this->context->entity();

        abort_unless($entity, 404);

        $deliveryOptions = $data['delivery_options'] ?? [];

        // Первый настроенный тип доставки для старой схемы.
        // Пустая цена = опция не настроена.
        // 0 = бесплатная доставка и считается активной.
        $legacyDeliveryType = 'door_to_door';
        $legacyPrice = 0;
        $legacyPriceUnit = 'flat';

        foreach ($deliveryOptions as $deliveryTypeId => $option) {

            $rawPrice = $option['price'] ?? null;

            // Пустое значение = тип доставки не используется
            if ($rawPrice === null || $rawPrice === '') {
                continue;
            }

            $deliveryType = \App\Domain\Shipping\Models\DeliveryType::find($deliveryTypeId);

            if (!$deliveryType) {
                continue;
            }

            $legacyDeliveryType = $deliveryType->code;
            $legacyPrice = (float) $rawPrice;
            $legacyPriceUnit = $option['price_unit'] ?? 'flat';

            break;
        }

        // 1. Создаем базовую запись шаблона
        $template = ShippingTemplate::create([
            'provider_type' => $entity::class,
            'provider_id' => $entity->getKey(),

            // Старая схема
            'price' => $legacyPrice,
            'price_unit' => $legacyPriceUnit,
            'delivery_type' => $legacyDeliveryType,

            'delivery_time' => $data['delivery_time'] ?? null,
            'created_by' => auth()->id(),
        ]);

        // 2. Создаем мультиязычные переводы
        foreach ($data['title'] as $locale => $title) {

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

        // 3. Новая схема: сохраняем каждый настроенный тип доставки отдельно
        foreach ($deliveryOptions as $deliveryTypeId => $option) {

            $rawPrice = $option['price'] ?? null;

            // Пустая цена = тип доставки отключен
            // 0 = бесплатная доставка, поэтому НЕ пропускаем
            if ($rawPrice === null || $rawPrice === '') {
                continue;
            }

            $deliveryType = \App\Domain\Shipping\Models\DeliveryType::find($deliveryTypeId);

            if (!$deliveryType) {
                continue;
            }

            $template->deliveryOptions()->create([
                'delivery_type_id' => $deliveryType->id,
                'price' => (float) $rawPrice,
                'price_unit' => $option['price_unit'] ?? 'flat',
                'delivery_time' => $data['delivery_time'] ?? null,
                'is_active' => true,
                'sort_order' => $deliveryType->sort_order,
            ]);
        }

        // 4. Привязка стран / регионов / городов
        $template->locations()->sync($data['locations'] ?? []);
    });

    return redirect()
        ->route('supplier.shipping-templates.index')
        ->with('success', 'Shipping template created successfully');
}




    /**
     * Show edit form
     */
    public function edit(ShippingTemplate $shippingTemplate)
    {
        

$this->authorize('update', $shippingTemplate);

        


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

        $deliveryTypes = DeliveryType::query()
    ->with('translations')
    ->where('is_active', true)
    ->orderBy('sort_order')
    ->orderBy('id')
    ->get();

    $shippingTemplate->load([
    'translations',
    'locations',
    'deliveryOptions.deliveryType.translations',
]);

        return view('dashboard.supplier.shipping-templates.edit', compact(
            'shippingTemplate',
            'countries',
            'selectedLocations',
            'deliveryTypes',
        ));
    }

    /**
     * Update existing template
     */
    public function update(Request $request, ShippingTemplate $shippingTemplate)
{
    $this->authorize('update', $shippingTemplate);

    $data = $request->validate([
        'title' => 'required|array',
        'description' => 'nullable|array',
        'price' => 'required|numeric|min:0',
        'price_unit' => 'required|in:per_item,per_kg,per_cubic_meter,flat',
        'delivery_time' => 'nullable|string|max:255',
        'locations' => 'nullable|array',
        'locations.*' => 'exists:locations,id',
        'delivery_options' => 'nullable|array',
        'delivery_options.*.price' => 'nullable|numeric|min:0',
        'delivery_options.*.price_unit' => 'nullable|in:per_item,per_kg,per_cubic_meter,flat',
    ]);

    DB::transaction(function () use ($shippingTemplate, $data) {

        $deliveryOptions = $data['delivery_options'] ?? [];

        // Первый настроенный delivery type для старых полей.
        // Пустая цена = опция не настроена.
        // 0 = бесплатная доставка и считается активной.
        $legacyDeliveryType = 'door_to_door';
        $legacyPrice = 0;
        $legacyPriceUnit = 'flat';

        foreach ($deliveryOptions as $deliveryTypeId => $option) {

            $rawPrice = $option['price'] ?? null;

            // Пустая цена = тип доставки не используется
            if ($rawPrice === null || $rawPrice === '') {
                continue;
            }

            $deliveryType = \App\Domain\Shipping\Models\DeliveryType::find($deliveryTypeId);

            if (!$deliveryType) {
                continue;
            }

            $legacyDeliveryType = $deliveryType->code;
            $legacyPrice = (float) $rawPrice;
            $legacyPriceUnit = $option['price_unit'] ?? 'flat';

            break;
        }

        // 1. Обновляем базовый шаблон
        $shippingTemplate->update([
            'price' => $legacyPrice,
            'price_unit' => $legacyPriceUnit,
            'delivery_type' => $legacyDeliveryType,
            'delivery_time' => $data['delivery_time'] ?? null,
            'updated_by' => auth()->id(),
        ]);

        // 2. Обновляем переводы
        foreach ($data['title'] as $locale => $title) {

            if (empty($title)) {
                continue;
            }

            $shippingTemplate->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'title' => $title,
                    'description' => $data['description'][$locale] ?? null,
                ]
            );
        }

        // 3. Обновляем delivery options
        foreach ($deliveryOptions as $deliveryTypeId => $option) {

            $rawPrice = $option['price'] ?? null;

            // Пустая цена = удалить / отключить опцию.
            // 0 = бесплатная доставка, поэтому сохраняем.
            if ($rawPrice === null || $rawPrice === '') {

                $shippingTemplate->deliveryOptions()
                    ->where('delivery_type_id', $deliveryTypeId)
                    ->delete();

                continue;
            }

            $deliveryType = \App\Domain\Shipping\Models\DeliveryType::find($deliveryTypeId);

            if (!$deliveryType) {
                continue;
            }

            $shippingTemplate->deliveryOptions()->updateOrCreate(
                [
                    'delivery_type_id' => $deliveryType->id,
                ],
                [
                    'price' => (float) $rawPrice,
                    'price_unit' => $option['price_unit'] ?? 'flat',
                    'delivery_time' => $data['delivery_time'] ?? null,
                    'is_active' => true,
                    'sort_order' => $deliveryType->sort_order,
                ]
            );
        }

        // 4. Обновляем страны / регионы / города
        $shippingTemplate->locations()->sync($data['locations'] ?? []);
    });

    return redirect()
        ->route('supplier.shipping-templates.index')
        ->with('success', 'Shipping template updated successfully');
}



    /**
     * Delete template
     */
    public function destroy(ShippingTemplate $shippingTemplate)
    {

    $this->authorize('delete', $shippingTemplate);
     
        $shippingTemplate->locations()->detach();
        $shippingTemplate->delete();

        return redirect()->route('supplier.shipping-templates.index')
            ->with('success', 'Shipping template deleted successfully');
    }


    public function attachWarehouse(Request $request, ShippingTemplate $template)
{

 $this->authorize('update', $template);

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

 $this->authorize('update', $template);

 
    $template->update([
        'is_active' => !$template->is_active,
    ]);

    return back()->with('success', 'Status updated successfully');
}



}
