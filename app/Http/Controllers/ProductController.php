<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/* === REQUEST === */
use App\Http\Requests\UpdateProductRequest;

/* === MODELS === */
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductPriceTier;
use App\Models\ProductMaterial;
use App\Models\Material;
use App\Models\Specification;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Color;
use App\Models\ShippingTemplate;
use App\Http\Requests\StoreProductRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Country;
use App\Models\Language;
use App\Models\MessageThread;






class ProductController extends Controller
{

    private function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $count = Product::where('slug', 'LIKE', "{$slug}%")->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }

    public function index(Request $request)
    {
        $sort = $request->get('sort');
        $status = $request->get('status');
        $userFilter = $request->get('user');
        $supplier = Auth::user()->supplier;

        $query = Product::query()
            ->with([
                'user',
                'category',
                'images',
                'priceTiers',
                'mainImage',
            ])
            // Добавляем фильтр по текущему пользователю
            ->where('supplier_id', $supplier->id);

        // Фильтр по статусу
        if ($status) {
            $query->where('status', $status);
        }

        // Поиск по пользователю (можно оставить для админки)
        if ($userFilter) {
            $query->whereHas('user', function ($q) use ($userFilter) {
                $q->where('name', 'like', "%{$userFilter}%");
            });
        }

        // Сортировка
        match ($sort) {
            'oldest' => $query->orderBy('created_at', 'asc'),
            'status' => $query->orderBy('status'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $products = $query->get();

        return view('dashboard.manufacturer.products', compact(
            'products',
            'sort',
            'status',
            'userFilter'
        ));
    }


    public function show(string $slug)
    {

        $user = auth()->user();
        


        $product1 = Product::with(['images', 'mainImage', 'specifications', 'priceTiers', 'supplier', 'category', 'colors', 'colors.linkedProduct'])
            ->where('slug', $slug)
            ->firstOrFail();


       

        return view('product.show', compact('product1'));
    }


    public function create()
    {
        $categories = Category::all();

        $locale = app()->getLocale();

        $materials = Material::with(['translations' => function ($q) use ($locale) {
            $q->where('locale', $locale);
        }])->get();

        $countries = Country::where('is_active', true)->get();
        $shippingTemplates = ShippingTemplate::where('manufacturer_id', auth()->id())
            ->with('translations')
            ->get();


        return view('dashboard.manufacturer.add-product', compact('categories', 'materials', 'shippingTemplates', 'countries'));
    }

    public function store(StoreProductRequest  $request)
    {

        //dd($request->all());
        //dd(ini_get('post_max_size'), ini_get('upload_max_filesize'));


        DB::transaction(function () use ($request) {

            /* ===============================
         * 1. PRODUCT (base)
         * =============================== */

            // Берём первый язык как дефолт
            $defaultLocale = array_key_first($request->name);
            $defaultName = $request->name[$defaultLocale] ?? null;
            $defaultUndername = $request->undername[$defaultLocale] ?? null;
            $defaultDescription = $request->description[$defaultLocale] ?? null;

            $slug = $this->generateUniqueSlug($defaultName);
            $supplier = Supplier::where('user_id', auth()->id())->firstOrFail();
            $product = Product::create([
                'supplier_id' => $supplier->id,
                'name'        => $defaultName,
                'slug'        => $slug,
                'undername'   => $defaultUndername,
                'description' => $defaultDescription,
                'category_id' => $request->category,
                'moq'         => $request->moq,
                'lead_time'   => $request->lead_time,
                'customization' => $request->customization === 'available',
                'materials_selected' => $request->materials_selected,
                'country_id' => $request->country_id,
            ]);

            /* ===============================
         * 2. PRODUCT TRANSLATIONS
         * =============================== */

            if (is_array($request->name)) {
                foreach ($request->name as $locale => $name) {

                    if (
                        empty($name) &&
                        empty($request->undername[$locale] ?? null) &&
                        empty($request->description[$locale] ?? null)
                    ) {
                        continue;
                    }

                    \App\Models\ProductTranslation::create([
                        'product_id' => $product->id,
                        'locale'     => $locale,
                        'name'       => $name,
                        'undername'  => $request->undername[$locale] ?? null,
                        'description' => $request->description[$locale] ?? null,
                    ]);
                }
            }

            /* ===============================
         * 2. IMAGES (drag & drop + MAIN)
         * =============================== */


            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = 'products/no-photo.png';
                    // Сохраняем на диск 'public' в папку 'products'
                    $path = $image->store('products', 'public');


                    // $path теперь что-то вроде: "products/php652C.png"
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'sort_order' => $index,
                        'is_main' => $index === 0 ? 1 : 0,
                    ]);
                }
            }

            /* ===============================
         * 3. PRICE TIERS
         * =============================== */
            if ($request->price_tiers) {
                foreach ($request->price_tiers as $tier) {
                    if (!empty($tier['price'])) {
                        $product->priceTiers()->create([
                            'min_qty' => $tier['min_qty'] ?? null,
                            'max_qty' => $tier['max_qty'] ?? null,
                            'price' => $tier['price'],
                        ]);
                    }
                }
            }

            /* ===============================
         * 4. COLORS / TEXTURES
         * =============================== */
            if ($request->materials) {
                foreach ($request->materials as $material) {

                    // если ни цвета, ни текстуры — пропускаем
                    if (
                        empty($material['color']) &&
                        empty($material['texture'])
                    ) {
                        continue;
                    }

                    $texturePath = null;

                    if (!empty($material['texture'])) {
                        $texturePath = $material['texture']->store('textures', 'public');
                    }

                    if (!empty($material['linked_product_id'])) {
                        $linkedProductExists = Product::where('id', $material['linked_product_id'])->exists();
                        $linkedProductId = $linkedProductExists ? $material['linked_product_id'] : null;
                    } else {
                        $linkedProductId = null;
                    }

                    Color::create([
                        'product_id' => $product->id,
                        'color' => $material['color'] ?? null,
                        'texture_path' => $texturePath,
                        'linked_product_id' => $material['linked_product_id'] ?? null,
                    ]);
                }
            }



            /* ===============================
        * 5. MATERIALS (many-to-many)
        * =============================== */

            if ($request->filled('materials_selected')) {
                // materials_selected приходит как "1,2,3"
                $materialIds = explode(',', $request->materials_selected);

                // Сохраняем через pivot
                $product->materials()->sync($materialIds);
            }

            /* ===============================
 * 6. SHIPPING TEMPLATES
 * =============================== */
            if ($request->filled('shipping_templates')) {
                // shipping_templates приходит как массив
                $templateIds = $request->shipping_templates;

                // сохраняем привязку через pivot
                $product->shippingTemplates()->sync($templateIds);
            }

            /* ===============================
         * 5. SPECIFICATIONS
         * =============================== */
            if ($request->specs) {
                foreach ($request->specs as $specData) {

                    // создаём specification (без текста)
                    $spec = Specification::create([
                        'product_id' => $product->id,
                    ]);

                    // сохраняем переводы
                    foreach ($specData as $locale => $values) {
                        if (
                            !empty($values['key']) &&
                            !empty($values['value'])
                        ) {
                            $spec->translations()->create([
                                'locale' => $locale,
                                'key'    => $values['key'],
                                'value'  => $values['value'],
                            ]);
                        }
                    }
                }
            }
        });





        return redirect()
            ->route('manufacturer.products.index')
            ->with('success', 'Product created successfully');
    }


    public function edit(Product $product)
    {
        // 🔐 Защита: только владелец
        abort_if(
            $product->supplier_id !== auth()->user()->supplier->id,
            403
        );

        $product->load([
            'translations',
            'category',
            'materials',
            'priceTiers',
            'shippingTemplates',
            'images',
        ]);



        $categories = Category::all();
        // Получаем все активные языки
        $languages = Language::where('is_active', true)->get();


        // Подготовка массива переводов
        $translations = [];
        foreach ($languages as $language) {
            $translation = $product->translations->firstWhere('locale', $language->code);
            $translations[$language->code] = [
                'name' => $translation->name ?? '',
                'undername' => $translation->undername ?? '',
                'description' => $translation->description ?? '',
            ];
        }

        // Получаем спецификации с переводами
        $specsTranslations = [];
        foreach ($languages as $language) {
            $specsTranslations[$language->code] = [];

            foreach ($product->specifications as $i => $spec) {
                $translation = $spec->translations->firstWhere('locale', $language->code);
                $specsTranslations[$language->code][$i] = [
                    'key' => $translation->key ?? '',
                    'value' => $translation->value ?? '',
                ];
            }
        }

        // Получаем все страны (для select)
        $countries = Country::all();

        $shippingTemplates = ShippingTemplate::where('manufacturer_id', auth()->id())
            ->with('translations')
            ->get();


        // Загружаем материалы с переводами
        $materials = Material::with('translations')->get();

        // Подготавливаем массив для редактирования
        $materialsPrepared = [];

        foreach ($materials as $material) {
            $materialData = ['id' => $material->id, 'translations' => []];

            foreach ($languages as $language) {
                $translation = $material->translations->firstWhere('locale', $language->code);
                $materialData['translations'][$language->code] = [
                    'name' => $translation->name ?? '',
                ];
            }

            $materialsPrepared[] = $materialData;
        }

        // Получаем материалы, уже привязанные к продукту
        $selectedMaterials = $product->materials->pluck('id')->toArray();

        $mainImage = $product->images->firstWhere('is_main', 1)->order ?? null;

        return view('product.edit', compact(
            'product',
            'categories',
            'languages',
            'countries',
            'shippingTemplates',
            'materialsPrepared',  // все материалы с переводами
            'selectedMaterials',  // выбранные материалы для продукта
            'translations',
            'specsTranslations',
            'mainImage'
        ));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        abort_if(
            $product->supplier_id !== auth()->user()->supplier->id,
            403
        );
        DB::transaction(function () use ($request, $product) {

            /* ===============================
         * 1. Base product
         * =============================== */

            $defaultLocale = array_key_first($request->name);

            $product->update([
                'name'        => $request->name[$defaultLocale],
                'undername'   => $request->undername[$defaultLocale] ?? null,
                'description' => $request->description[$defaultLocale] ?? null,
                'category_id' => $request->category,
                'country_id'  => $request->country_id,
                'moq'         => $request->moq,
                'lead_time'   => $request->lead_time,
                'customization' => $request->boolean('customization'),

            ]);

            /* ===============================
         * 2. Translations
         * =============================== */

            foreach ($request->name as $locale => $name) {
                if (!$name) continue;

                $product->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'name'        => $name,
                        'undername'   => $request->undername[$locale] ?? null,
                        'description' => $request->description[$locale] ?? null,
                    ]
                );
            }


            /* ===============================
         * 2. IMAGES (drag & drop + MAIN)
         * =============================== */


            $main_image = $request->input('main_image', 0);

            $existingImages = $request->input('existing_images', []);
            $existingImagesIds = collect($existingImages)->pluck('id')->toArray();

            // Получаем текущие изображения продукта
            $currentImages = $product->images()->pluck('id')->toArray();

            // 1️⃣1. Удаляем те, которых нет в форме
            $toDelete = array_diff($currentImages, $existingImagesIds);
            foreach ($toDelete as $id) {
                $img = $product->images()->find($id);
                if ($img) {
                    Storage::disk('public')->delete($img->image_path);
                    $img->delete();
                }
            }
            // 1️⃣2. Обновляем порядок и is_main для оставшихся
            foreach ($existingImagesIds as $id) {
                $img = $product->images()->find($id);
                if ($img) {
                    $img->sort_order = $existingImages[$id]['order'] ?? 0;
                    $img->is_main = $existingImages[$id]['main'];
                    $img->save();
                }
            }

            // 2️⃣ Новые картинки
            $newFiles = $request->file('new_images', []);
            $newOrders = $request->input('new_images_order', []);
            $newMain = $request->input('new_images_main', []);

            if (is_array($newFiles)) {
                foreach ($newFiles as $index => $file) {
                    $path = $file->store('products', 'public');

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'sort_order' => $newOrders[$index] ?? 0,
                        'is_main' => $newMain[$index] ?? 0,
                    ]);
                }
            }


            /* ===============================
         * 3. Materials (many-to-many)
         * =============================== */
            // ⬅️ ТОЛЬКО ID материалов (как в store)
            if ($request->filled('materials_selected')) {
                $materialIds = explode(',', $request->materials_selected);
                $product->materials()->sync($materialIds);
            } else {
                $product->materials()->sync([]);
            }

            /* ===============================
         * 4. Colors / Textures
         * =============================== */
            // ⬅️ логика из store(), но с пересозданием
            $product->colors()->delete();

            if ($request->materials) {
                foreach ($request->materials as $material) {

                    if (
                        empty($material['color']) &&
                        empty($material['texture'])
                    ) {
                        continue;
                    }

                    $texturePath = null;

                    if (!empty($material['texture'])) {
                        $texturePath = $material['texture']->store('textures', 'public');
                    }

                    Color::create([
                        'product_id'        => $product->id,
                        'color'             => $material['color'] ?? null,
                        'texture_path'      => $texturePath,
                        'linked_product_id' => $material['linked_product_id'] ?? null,
                    ]);
                }
            }

            /* ===============================
         * 5. Price tiers
         * =============================== */

            $product->priceTiers()->delete();

            foreach ($request->price_tiers as $tier) {
                if (!empty($tier['price'])) {
                    $product->priceTiers()->create([
                        'min_qty' => $tier['min_qty'] ?? null,
                        'max_qty' => $tier['max_qty'] ?? null,
                        'price'   => $tier['price'],
                    ]);
                }
            }



            /* ===============================
            * 7. Specifications
            * =============================== */

            // удаляем старые спецификации и их переводы
            $product->specifications()->each(function ($spec) {
                $spec->translations()->delete();
                $spec->delete();
            });

            // создаём новые из формы
            if ($request->specs) {
                foreach ($request->specs as $specData) {

                    // создаём specification (без текста)
                    $spec = Specification::create([
                        'product_id' => $product->id,
                    ]);

                    // сохраняем переводы
                    foreach ($specData as $locale => $values) {

                        if (
                            !empty($values['key']) &&
                            !empty($values['value'])
                        ) {
                            $spec->translations()->create([
                                'locale' => $locale,
                                'key'    => $values['key'],
                                'value'  => $values['value'],
                            ]);
                        }
                    }
                }
            }


            /* ===============================
         * 6. Shipping templates
         * =============================== */

            $product->shippingTemplates()->sync(
                $request->shipping_templates ?? []
            );
        });

        return redirect()
            ->route('manufacturer.products.index', $product)
            ->with('success', 'Product updated successfully');
    }

    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        // Проверка, что товар принадлежит supplier'у
        if ($product->supplier_id !== auth()->user()->supplier->id) {
            abort(403);
        }

        $product->stock()->update([
            'quantity' => $request->stock
        ]);

        return response()->json([
            'success' => true,
            'stock' => $request->stock,
        ]);
    }

    public function destroy(Product $product)
    {
        // Получаем supplier_id текущего пользователя
        $supplierId = auth()->user()->supplier?->id;

        if (!$supplierId) {
            abort(403, 'Unauthorized action.');
        }

        // Находим продукт только своего поставщика
        $product = Product::where('id', $product->id)
            ->where('supplier_id', $supplierId)
            ->firstOrFail();

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.'
        ]);
    }
}
