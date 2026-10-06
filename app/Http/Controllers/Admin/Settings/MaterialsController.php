<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\MaterialTranslation;
use App\Domain\Material\Models\MaterialGroup;
use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Domain\Media\Services\MediaService;
use App\Domain\Media\DTO\UploadMediaDTO;

class MaterialsController extends Controller
{

protected MediaService $mediaService;


public function __construct(MediaService $mediaService)
{
    $this->mediaService = $mediaService;
}

    public function index()
{
    $languages = Language::where('is_active', true)->get();

    $materials = Material::query()
    ->with([
        'translations',
        'materialGroup.translations',
        'photo',
    ])
    ->get();

    $groups = MaterialGroup::query()
        ->with([
            'translations',
            'materials',
        ])
        ->where('is_custom', false)
        ->orderBy('id')
        ->get();

    return view(
        'dashboard.admin.settings.materials.index',
        compact('materials', 'groups', 'languages')
    );
}

    public function create()
    {
        $languages = Language::where('is_active', true)->get();

        $groups = MaterialGroup::query()
        ->with([
            'translations',
            'materials',
        ])
        ->where('is_custom', false)
        ->orderBy('id')
        ->get();

        return view('dashboard.admin.settings.materials.create', compact('languages', 'groups'));
    }

    public function store(Request $request)
{
    $request->validate([
        'material_group_id' => 'required|exists:material_groups,id',
        'name.*' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:materials,slug',

        'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
    ]);

    $material = Material::create([
        'slug' => $request->slug,
        'material_group_id' => $request->material_group_id,
    ]);

    foreach ($request->name as $locale => $name) {
        $material->translations()->create([
            'locale' => $locale,
            'name' => $name,
        ]);
    }

    // Photo
    if ($request->hasFile('photo')) {

        $file = $request->file('photo');

        $metadata = $this->mediaService->extractMetadata($file);

        $dto = new UploadMediaDTO(
            file: $file,
            model: $material,
            collection: 'material_photos',
            mediaRole: 'material_photo',
            private: false,
            originalFileName: $file->getClientOriginalName(),
            metadata: $metadata,
            sortOrder: 0,
            isMain: true
        );

        $this->mediaService->upload($dto);
    }

    return redirect()
        ->route('admin.settings.materials.index')
        ->with('success', 'Материал добавлен');
}

    public function edit(Material $material)
{
    $languages = Language::where('is_active', true)->get();

    $groups = MaterialGroup::query()
        ->with([
            'translations',
            'materials',
        ])
        ->where('is_custom', false)
        ->orderBy('id')
        ->get();

    $material->load('photo');

    return view(
        'dashboard.admin.settings.materials.edit',
        compact('material', 'languages', 'groups')
    );
}

    public function update(Request $request, Material $material)
{
    $request->validate([
        'material_group_id' => 'required|exists:material_groups,id',
        'name.*' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:materials,slug,' . $material->id,

        'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
    ]);

    $material->update([
        'slug' => $request->slug,
        'material_group_id' => $request->material_group_id,
    ]);

    foreach ($request->name as $locale => $name) {
        $material->translations()->updateOrCreate(
            ['locale' => $locale],
            ['name' => $name]
        );
    }

    // Replace material photo
    if ($request->hasFile('photo')) {

        // Delete existing photo
        if ($material->photo) {
            $this->mediaService->delete($material->photo);
        }

        $file = $request->file('photo');

        $metadata = $this->mediaService->extractMetadata($file);

        $dto = new UploadMediaDTO(
            file: $file,
            model: $material,
            collection: 'material_photos',
            mediaRole: 'material_photo',
            private: false,
            originalFileName: $file->getClientOriginalName(),
            metadata: $metadata,
            sortOrder: 0,
            isMain: true
        );

        $this->mediaService->upload($dto);
    }

    return redirect()
        ->route('admin.settings.materials.index')
        ->with('success', 'Материал обновлён');
}

    public function destroy(Material $material)
    {
        $material->delete();
        return redirect()->route('admin.settings.materials.index')->with('success', 'Материал удалён');
    }


    public function storeGroup(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'slug' => ['required', 'string', 'max:255', 'unique:material_groups,slug'],
        'brand' => ['nullable', 'string', 'max:255'],
        'brand_url' => ['nullable', 'url', 'max:2048'],

        'brand_logo' => [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp,svg',
            'max:5120',
        ],

        'translations' => ['required', 'array'],
        'translations.*.name' => ['nullable', 'string', 'max:255'],
        'translations.*.description' => ['nullable', 'string'],
    ]);

    $group = MaterialGroup::create([
        'slug' => $validated['slug'],
        'brand' => $validated['brand'] ?? null,
        'brand_url' => $validated['brand_url'] ?? null,
        'is_active' => true,
        'is_custom' => false,
    ]);


    /*
    |--------------------------------------------------------------------------
    | BRAND LOGO
    |--------------------------------------------------------------------------
    */

    if ($request->hasFile('brand_logo')) {

    /*
    |--------------------------------------------------------------------------
    | REMOVE EXISTING LOGO
    |--------------------------------------------------------------------------
    */

    $existingLogo = $group->logo;

    if ($existingLogo) {
        $existingLogo->delete();
    }


    /*
    |--------------------------------------------------------------------------
    | UPLOAD NEW LOGO
    |--------------------------------------------------------------------------
    */

    $file = $request->file('brand_logo');

    $metadata = $this->mediaService->extractMetadata($file);

    $dto = new UploadMediaDTO(
        file: $file,
        model: $group,
        collection: 'material_group_logos',
        mediaRole: 'material_group_logo',
        private: false,
        originalFileName: $file->getClientOriginalName(),
        metadata: $metadata,
        sortOrder: 0,
        isMain: true
    );

    $this->mediaService->upload($dto);
}


    /*
    |--------------------------------------------------------------------------
    | TRANSLATIONS
    |--------------------------------------------------------------------------
    */

    foreach ($validated['translations'] ?? [] as $locale => $translation) {

        if (
            empty($translation['name']) &&
            empty($translation['description'])
        ) {
            continue;
        }

        $group->translations()->create([
            'locale' => $locale,
            'name' => $translation['name'] ?? '',
            'description' => $translation['description'] ?? null,
        ]);
    }


    return redirect()
        ->route('admin.settings.materials.index')
        ->with('success', 'Material group created successfully.');
}

    public function updateGroup(Request $request, MaterialGroup $group): RedirectResponse
{
    $validated = $request->validate([
        'slug' => [
            'required',
            'string',
            'max:255',
            'unique:material_groups,slug,' . $group->id,
        ],

        'brand' => [
            'nullable',
            'string',
            'max:255',
        ],

        'brand_url' => [
            'nullable',
            'url',
            'max:2048',
        ],

        'brand_logo' => [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp,svg',
            'max:5120',
        ],

        'translations' => [
            'required',
            'array',
        ],

        'translations.*.name' => [
            'nullable',
            'string',
            'max:255',
        ],

        'translations.*.description' => [
            'nullable',
            'string',
        ],
    ]);


    /*
    |--------------------------------------------------------------------------
    | UPDATE GROUP
    |--------------------------------------------------------------------------
    */

    $group->update([
        'slug' => $validated['slug'],
        'brand' => $validated['brand'] ?? null,
        'brand_url' => $validated['brand_url'] ?? null,
    ]);


    /*
    |--------------------------------------------------------------------------
    | BRAND LOGO
    |--------------------------------------------------------------------------
    */

    if ($request->hasFile('brand_logo')) {

        /*
        |--------------------------------------------------------------------------
        | DELETE EXISTING LOGO
        |--------------------------------------------------------------------------
        */

        if ($group->logo) {

            $this->mediaService->delete(
                $group->logo
            );
        }


        /*
        |--------------------------------------------------------------------------
        | UPLOAD NEW LOGO
        |--------------------------------------------------------------------------
        */

        $file = $request->file('brand_logo');

        $metadata = $this->mediaService->extractMetadata($file);

        $dto = new UploadMediaDTO(
            file: $file,
            model: $group,
            collection: 'material_group_logos',
            mediaRole: 'material_group_logo',
            private: false,
            originalFileName: $file->getClientOriginalName(),
            metadata: $metadata,
            sortOrder: 0,
            isMain: true
        );

        $this->mediaService->upload($dto);
    }


    /*
    |--------------------------------------------------------------------------
    | TRANSLATIONS
    |--------------------------------------------------------------------------
    */

    foreach ($validated['translations'] ?? [] as $locale => $translation) {

        $name = trim($translation['name'] ?? '');
        $description = trim($translation['description'] ?? '');

        /*
        |--------------------------------------------------------------------------
        | DELETE EMPTY TRANSLATION
        |--------------------------------------------------------------------------
        */

        if ($name === '' && $description === '') {

            $group->translations()
                ->where('locale', $locale)
                ->delete();

            continue;
        }


        /*
        |--------------------------------------------------------------------------
        | CREATE / UPDATE TRANSLATION
        |--------------------------------------------------------------------------
        */

        $group->translations()->updateOrCreate(
            [
                'locale' => $locale,
            ],
            [
                'name' => $name,
                'description' => $description !== ''
                    ? $description
                    : null,
            ]
        );
    }


    return redirect()
        ->route('admin.settings.materials.index')
        ->with('success', 'Material group updated successfully.');
}

public function destroyGroup(MaterialGroup $group): RedirectResponse
{
    DB::transaction(function () use ($group) {

        $materials = $group->materials()->get();

        foreach ($materials as $material) {
            $material->translations()->delete();
        }

        $group->materials()->delete();

        $group->translations()->delete();

        $group->delete();
    });

    return redirect()
        ->route('admin.settings.materials.index')
        ->with('success', 'Material group deleted successfully.');
}



}
