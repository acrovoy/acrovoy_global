<?php

namespace App\Http\Controllers\Buyer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;



use App\Models\Project;
use App\Models\ProjectItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;

class BuyerProjectController extends Controller
{
    /**
     * Список проектов покупателя
     */
    public function index()
{
    $projects = Project::where('buyer_id', auth()->id())
        // Подсчет позиций проекта
        ->withCount('items')
        // Можно заранее загрузить позиции и их детали для быстрого доступа в UI
        ->with([
            'items.descriptions',
            'items.specifications',
            'items.materials',
            'items.colors',
        ])
        ->latest()
        ->get();

    return view('dashboard.buyer.projects.index', compact('projects'));
}


    public function show(Project $project)
{
    $this->authorize('view', $project);

    // Загружаем только необходимые связи
    $project->load([
        'items.specifications',
        'items.materials',
        'items.colors',
        'items.descriptions', // если выводим description
    ]);

    return view('dashboard.buyer.projects.show', compact('project'));
}
    /**
     * Форма создания проекта
     */
    public function create()
{
    $locale = App::getLocale();

    $categories = Category::with(['translations' => fn($q) => $q->where('locale', $locale)])
                          ->where('type', 'project')
                          ->orderBy('name')
                          ->get();

    return view('dashboard.buyer.projects.create', compact('categories'));
}

    /**
     * Сохранение проекта
     */
    public function store(Request $request)
{
    // Получаем текущий язык (для фильтрации категорий, если потребуется)
    $locale = app()->getLocale();

    // Валидация
    $data = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'category_id' => [
            'nullable',
            'exists:categories,id,type,project' // только категории типа 'project'
        ],
    ]);

    // Привязка покупателя и статус по умолчанию
    $data['buyer_id'] = auth()->id();
    $data['status'] = 'draft';

    // Создание проекта
    $project = Project::create($data);

    // Редирект на страницу просмотра с сообщением
    return redirect()->route('buyer.projects.show', $project)
                     ->with('success', 'Project created successfully.');
}

    /**
     * Просмотр проекта и его позиций
     */
    

    /**
     * Форма редактирования проекта
     */
    public function edit(Project $project)
{
    $this->authorize('update', $project);

    $locale = App::getLocale();

    $categories = Category::with(['translations' => fn($q) => $q->where('locale', $locale)])
                          ->where('type', 'project')
                          ->orderBy('name')
                          ->get();

    return view('dashboard.buyer.projects.edit', compact('project', 'categories'));
}

    /**
     * Обновление проекта
     */
    public function update(Request $request, Project $project)
{
    // Проверяем права пользователя
    $this->authorize('update', $project);

    // Валидация данных
    $data = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'category_id' => [
            'nullable',
            'exists:categories,id,type,project' // только категории типа 'project'
        ],
    ]);

    // Обновление проекта
    $project->update($data);

    // Редирект с сообщением
    return redirect()->route('buyer.projects.show', $project)
                     ->with('success', 'Project updated successfully.');
}
    /**
     * Удаление проекта
     */
    public function destroy(Project $project)
{
    // Авторизация
    $this->authorize('delete', $project);

    // Удаляем все позиции и их зависимости
    $project->items()->each(function ($item) {
        $item->delete(); // срабатывает booted() в ProjectItem
    });

    // Удаляем сам проект
    $project->delete();

    // Редирект с сообщением
    return redirect()->route('buyer.projects.index')
                     ->with('success', 'Project deleted successfully.');
}


public function storeCustomization(Request $request)
{
    $user = auth()->user();

    if (!$user) {
        abort(403);
    }

    $request->validate([
        'product_id' => 'required|exists:products,id',
    ]);

    // 1️⃣ Получаем продукт
    $product = Product::findOrFail($request->product_id);

    // 2️⃣ Создаём проект
    $project = Project::create([
        'buyer_id' => $user->id,
        'title'    => 'Customization: ' . $product->name,
        'status'   => 'draft',
    ]);

    // 3️⃣ КОПИРУЕМ ПРОДУКТ В PROJECT ITEM
    // ЛОГИКА ПОЛНОСТЬЮ СКОПИРОВАНА ИЗ ProjectItemController

    $product = Product::with([
        'specifications.translations',
        'materials',
        'colors',
        'translations',
        'images',
    ])->findOrFail($product->id);

    DB::transaction(function () use ($project, $product, $user) {

        $item = ProjectItem::create([
            'project_id'   => $project->id,
            'product_id'   => $product->id,
            'product_name' => $product->name,
            'quantity'     => 1,
        ]);

        // 🔹 Specifications
        foreach ($product->specifications as $spec) {
            $tr = $spec->translations->firstWhere('locale', $user->language)
                ?? $spec->translations->first();

            $item->specifications()->create([
                'parameter' => $tr?->key ?? 'N/A',
                'value'     => $tr?->value ?? 'N/A',
            ]);
        }

        // 🔹 Materials
        foreach ($product->materials as $material) {
            $item->materials()->attach($material->id);
        }

        // 🔹 Images
        foreach ($product->images as $image) {
            $item->media()->create([
                'image_path' => $image->image_path,
                'is_main'    => $image->is_main,
            ]);
        }

        // 🔹 Description
        $productTranslation = $product->translations
            ->firstWhere('locale', $user->language)
            ?? $product->translations->first();

        if ($productTranslation) {
            $item->descriptions()->create([
                'type'        => 'general',
                'description' => $productTranslation->description ?? 'N/A',
            ]);
        }
    });

    // 4️⃣ Редирект сразу в проект
    return redirect()
        ->route('buyer.projects.show', $project)
        ->with('success', 'Customization project created successfully.');
}


}
