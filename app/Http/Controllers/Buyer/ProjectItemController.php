<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ProjectItemController extends Controller
{
    public function store(Request $request)
{
    $user = auth()->user();

    if (!$user) {
        return redirect()->back()->with('error', 'You must be logged in to add products to a project.');
    }

    $request->validate([
        'project_id' => 'required|exists:projects,id',
        'product_id' => 'required|exists:products,id',
    ]);

    // Получаем проект пользователя в статусе draft
    $project = Project::where('id', $request->project_id)
                      ->where('buyer_id', $user->id)
                      ->where('status', 'draft')
                      ->firstOrFail();

    // Получаем продукт с зависимостями
    $product = Product::with(['specifications.translations', 'materials', 'colors', 'translations'])
                      ->findOrFail($request->product_id);

    // Проверяем, есть ли уже этот продукт в проекте
    $exists = ProjectItem::where('project_id', $project->id)
                         ->where('product_id', $product->id)
                         ->exists();



    if ($exists) {
    return redirect()->back()->with('warning', 'This product is already added to the selected project.');
}

    if (!$exists) {
        DB::transaction(function() use ($project, $product, $user) {

            // 1️⃣ Создаём ProjectItem
            $item = ProjectItem::create([
                'project_id' => $project->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => 1,
            ]);

            // 2️⃣ Копируем спецификации продукта → project_item_specifications
            foreach ($product->specifications as $spec) {
                // ищем перевод под локаль пользователя
                $tr = $spec->translations->firstWhere('locale', $user->language);
                $tr ??= $spec->translations->first();

                $item->specifications()->create([
                    'parameter' => $tr?->key ?? 'N/A',
                    'value'     => $tr?->value ?? 'N/A',
                ]);
            }

            // 3️⃣ Копируем материалы продукта
            foreach ($product->materials as $material) {
                $item->materials()->attach($material->id);
            }

            // 5️⃣ Копируем медиа (картинки) продукта
foreach ($product->images as $image) {
    $item->media()->create([
        'image_path' => $image->image_path,
        'is_main'    => $image->is_main, // сохраняем флаг главной картинки
    ]);
}

            // 5️⃣ Копируем описание продукта → project_item_descriptions
            $productTranslation = $product->translations->firstWhere('locale', $user->language);
            $productTranslation ??= $product->translations->first();

            if ($productTranslation) {
                $item->descriptions()->create([
                    'type' => 'general',
                    'description' => $productTranslation->description ?? 'N/A',
                ]);
            }
        });
    }

    return redirect()->back()->with('success', 'Product has been successfully added to the project.');
}




}
