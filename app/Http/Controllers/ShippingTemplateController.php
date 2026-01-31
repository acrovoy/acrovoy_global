<?php

namespace App\Http\Controllers;

use App\Models\ShippingTemplate;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\ShippingTemplateTranslation;
use Illuminate\Support\Facades\DB;

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
        $templates = ShippingTemplate::with('countries')->where('manufacturer_id', auth()->id())->get();
        return view('dashboard.manufacturer.shipping-templates.index', compact('templates'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $shippingTemplate = new ShippingTemplate(); // пустой объект
    return view('dashboard.manufacturer.shipping-templates.create', compact('shippingTemplate'));
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
        'countries' => 'nullable|array',
        'countries.*' => 'exists:countries,id',
    ]);

    DB::transaction(function () use ($data) {

        // 1️⃣ Создаем базовую запись шаблона
        $template = ShippingTemplate::create([
            'manufacturer_id' => auth()->id(),
            'price' => $data['price'],
            'delivery_time' => $data['delivery_time'] ?? null,
        ]);

        // 2️⃣ Создаем мультиязычные переводы
        foreach ($data['title'] as $locale => $title) {
            \App\Models\ShippingTemplateTranslation::create([
                'shipping_template_id' => $template->id,
                'locale' => $locale,
                'title' => $title,
                'description' => $data['description'][$locale] ?? null,
            ]);
        }

        // 3️⃣ Привязка стран
        $template->countries()->sync($data['countries'] ?? []);
    });

    return redirect()->route('manufacturer.shipping-templates.index')
                     ->with('success', 'Shipping template created successfully');
}

    /**
     * Show edit form
     */
    public function edit(ShippingTemplate $shippingTemplate)
    {
        // Проверяем, что шаблон принадлежит текущему пользователю
        if($shippingTemplate->manufacturer_id !== auth()->id()){
            abort(403);
        }

        return view('dashboard.manufacturer.shipping-templates.edit', compact('shippingTemplate'));
    }

    /**
     * Update existing template
     */
    public function update(Request $request, ShippingTemplate $shippingTemplate)
{
    // Валидация
    $data = $request->validate([
        'title' => 'required|array',
        'description' => 'nullable|array',
        'price' => 'required|numeric|min:0',
        'delivery_time' => 'nullable|string|max:255',
        'countries' => 'nullable|array',
        'countries.*' => 'exists:countries,id',
    ]);

    DB::transaction(function () use ($shippingTemplate, $data) {

        // 1️⃣ Обновляем базовые поля шаблона
        $shippingTemplate->update([
            'price' => $data['price'],
            'delivery_time' => $data['delivery_time'] ?? null,
        ]);

        // 2️⃣ Обновляем мультиязычные переводы
        foreach ($data['title'] as $locale => $title) {

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
        $shippingTemplate->countries()->sync($data['countries'] ?? []);
    });

    return redirect()->route('manufacturer.shipping-templates.index')
                     ->with('success', 'Shipping template updated successfully');
}

    /**
     * Delete template
     */
    public function destroy(ShippingTemplate $shippingTemplate)
    {
        if($shippingTemplate->manufacturer_id !== auth()->id()){
            abort(403);
        }

        $shippingTemplate->countries()->detach();
        $shippingTemplate->delete();

        return redirect()->route('manufacturer.shipping-templates.index')
                         ->with('success', 'Shipping template deleted successfully');
    }
}