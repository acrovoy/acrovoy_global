<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\ShippingTemplate;
use App\Models\ShippingTemplateTranslation;
use App\Models\Country;
use App\Models\Location;
use App\Models\Supplier;


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


    $supplier = Supplier::where('user_id', auth()->id())->first();    

        $templates = ShippingTemplate::with('locations')->where('manufacturer_id', $supplier->id)->get();
        return view('dashboard.manufacturer.shipping-templates.index', compact('templates'));
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
    $countries = $countries->map(function($country) use ($allLocations, $children) {
        // регионы этой страны
        $regions = $allLocations->where('country_id', $country->id)->where('parent_id', null);

        $regions = $regions->map(function($region) use ($children) {
            $region->children_recursive = $children->get($region->id) ?? collect();
            return $region;
        });

        $country->locations = $regions;
        return $country;
    });

    $shippingTemplate = new ShippingTemplate();
    $selectedLocations = $shippingTemplate->locations->pluck('id')->toArray();

    return view('dashboard.manufacturer.shipping-templates.create', compact(
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
    ]);

    DB::transaction(function () use ($data) {

    $supplier = Supplier::where('user_id', auth()->id())->first();
        // 1️⃣ Создаем базовую запись шаблона
        $template = ShippingTemplate::create([
            'manufacturer_id' => $supplier->id,
            'price' => $data['price'],
            'delivery_time' => $data['delivery_time'] ?? null,
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

    return redirect()->route('manufacturer.shipping-templates.index')
                     ->with('success', 'Shipping template created successfully');
}

    /**
     * Show edit form
     */
    public function edit(ShippingTemplate $shippingTemplate)
{
    $supplier = Supplier::where('user_id', auth()->id())->first();  
    // Проверяем, что шаблон принадлежит текущему пользователю
    if ($shippingTemplate->manufacturer_id !== $supplier->id) {
        abort(403);
    }

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

    return view('dashboard.manufacturer.shipping-templates.edit', compact(
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

$supplier = Supplier::where('user_id', auth()->id())->firstOrFail();

if ($shippingTemplate->manufacturer_id !== $supplier->id) {
    abort(403);
}

    // Валидация
    $data = $request->validate([
        'title' => 'required|array',
        'description' => 'nullable|array',
        'price' => 'required|numeric|min:0',
        'delivery_time' => 'nullable|string|max:255',
        'locations' => 'nullable|array',
        'locations.*' => 'exists:locations,id',
    ]);

    DB::transaction(function () use ($shippingTemplate, $data) {

        // 1️⃣ Обновляем базовые поля шаблона
        $shippingTemplate->update([
            'price' => $data['price'],
            'delivery_time' => $data['delivery_time'] ?? null,
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

    return redirect()->route('manufacturer.shipping-templates.index')
                     ->with('success', 'Shipping template updated successfully');
}

    /**
     * Delete template
     */
    public function destroy(ShippingTemplate $shippingTemplate)
    {
        $supplier = Supplier::where('user_id', auth()->id())->first();

        if($shippingTemplate->manufacturer_id !== $supplier->id){
            abort(403);
        }

        $shippingTemplate->locations()->detach();
        $shippingTemplate->delete();

        return redirect()->route('manufacturer.shipping-templates.index')
                         ->with('success', 'Shipping template deleted successfully');
    }
}