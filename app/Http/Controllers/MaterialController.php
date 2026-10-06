<?php

namespace App\Http\Controllers;

use App\Domain\Material\Models\MaterialGroup;
use App\Models\Language;
use App\Models\Material;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Services\Company\ActiveContextService;
use App\Domain\Media\Services\MediaService;
use App\Domain\Media\DTO\UploadMediaDTO;


class MaterialController extends Controller
{



public function __construct(
    private ActiveContextService $context,
    private MediaService $mediaService
) {
    
}

    public function index(): View
{
    $languages = Language::where('is_active', true)->get();

    $identity = $this->context->identity();

    $groups = MaterialGroup::query()
        ->with([
            'translations',
            'materials.translations',
            'materials.photo',
        ])
        ->where('is_custom', true)
        ->where('owner_type', $identity['entity_type'])
        ->where('owner_id', $identity['entity_id'])
        ->orderBy('id')
        ->get();

    return view(
        'material.index',
        compact('groups', 'languages')
    );
}



public function create()
    {
        $identity = $this->context->identity();

        $languages = Language::where('is_active', true)->get();

        $groups = MaterialGroup::query()
        ->with([
            'translations',
            'materials',
        ])
        ->where('is_custom', true)
        ->where('owner_type', $identity['entity_type'])
        ->where('owner_id', $identity['entity_id'])
        ->orderBy('id')
        ->get();

        return view('material.create', compact('languages', 'groups'));
    }

    public function store(Request $request): RedirectResponse
{
    $identity = $this->context->identity();

    $validated = $request->validate([
        'material_group_id' => 'required|exists:material_groups,id',
        'name.*' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:materials,slug',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
    ]);

    $group = MaterialGroup::query()
        ->where('id', $validated['material_group_id'])
        ->where('is_custom', true)
        ->where('owner_type', $identity['entity_type'])
        ->where('owner_id', $identity['entity_id'])
        ->firstOrFail();

    $material = Material::create([
        'slug' => $validated['slug'],
        'material_group_id' => $group->id,
    ]);

    foreach ($validated['name'] as $locale => $name) {
        $material->translations()->create([
            'locale' => $locale,
            'name' => $name,
        ]);
    }

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
        ->route('supplier.materials.index')
        ->with('success', 'Материал добавлен');
}

    public function edit(Material $material)
{

$identity = $this->context->identity();

    $languages = Language::where('is_active', true)->get();

    $groups = MaterialGroup::query()
        ->with([
            'translations',
            'materials',
        ])
        ->where('owner_type', $identity['entity_type'])
        ->where('owner_id', $identity['entity_id'])
        ->orderBy('id')
        ->get();

    $material->load('photo');

    return view(
        'material.edit',
        compact('material', 'languages', 'groups')
    );
}

    public function update(
    Request $request,
    Material $material
): RedirectResponse {
    $identity = $this->context->identity();

    $material->load('materialGroup');

    abort_unless(
        $material->materialGroup
        && $material->materialGroup->is_custom
        && $material->materialGroup->owner_type === $identity['entity_type']
        && (int) $material->materialGroup->owner_id === (int) $identity['entity_id'],
        404
    );

    $validated = $request->validate([
        'material_group_id' => 'required|exists:material_groups,id',
        'name.*' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:materials,slug,' . $material->id,
        'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
    ]);

    $group = MaterialGroup::query()
        ->where('id', $validated['material_group_id'])
        ->where('is_custom', true)
        ->where('owner_type', $identity['entity_type'])
        ->where('owner_id', $identity['entity_id'])
        ->firstOrFail();

    $material->update([
        'slug' => $validated['slug'],
        'material_group_id' => $group->id,
    ]);

    foreach ($validated['name'] as $locale => $name) {
        $material->translations()->updateOrCreate(
            ['locale' => $locale],
            ['name' => $name]
        );
    }

    if ($request->hasFile('photo')) {
        $material->load('photo');

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
        ->route('supplier.materials.index')
        ->with('success', 'Материал обновлён');
}

    public function destroy(Material $material)
    {
        $material->delete();
        return redirect()->route('supplier.materials.index')->with('success', 'Материал удалён');
    }






    public function storeGroup(Request $request): RedirectResponse
    {

    
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:255', 'unique:material_groups,slug'],
            'brand' => ['nullable', 'string', 'max:255'],

            'translations' => ['required', 'array'],

            'translations.*.name' => ['nullable', 'string', 'max:255'],
            'translations.*.description' => ['nullable', 'string'],
        ]);

        $entity = $this->context->entity();


        $group = MaterialGroup::create([
            'slug' => $validated['slug'],
            'brand' => $validated['brand'] ?? null,
            'is_active' => true,
            'is_custom' => true,
            'owner_type' => $entity::class,
            'owner_id'   => $entity->getKey(),
        ]);

        foreach ($validated['translations'] ?? [] as $locale => $translation) {
            if (empty($translation['name']) && empty($translation['description'])) {
                continue;
            }

            $group->translations()->create([
                'locale' => $locale,
                'name' => $translation['name'] ?? '',
                'description' => $translation['description'] ?? null,
                
            ]);
        }

        return redirect()
            ->route('supplier.materials.index')
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

    $group->update([
        'slug' => $validated['slug'],
        'brand' => $validated['brand'] ?? null,
    ]);

    foreach ($validated['translations'] ?? [] as $locale => $translation) {

        $name = trim($translation['name'] ?? '');
        $description = trim($translation['description'] ?? '');

        // Если оба поля пустые — удаляем перевод, если он существует.
        if ($name === '' && $description === '') {

            $group->translations()
                ->where('locale', $locale)
                ->delete();

            continue;
        }

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
        ->route('supplier.materials.index')
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
        ->route('supplier.materials.index')
        ->with('success', 'Material group deleted successfully.');
}

}