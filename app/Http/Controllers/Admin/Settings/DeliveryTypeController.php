<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Domain\Shipping\Models\DeliveryType;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DeliveryTypeController
{
    /**
     * Display a listing of delivery types.
     */
    public function index()
    {
        $deliveryTypes = DeliveryType::query()
            ->with('translations')
            ->withCount('shippingTemplates')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view(
            'dashboard.admin.settings.delivery-types.index',
            compact('deliveryTypes')
        );
    }

    /**
     * Show the form for creating a new delivery type.
     */
    public function create()
    {
        $languages = $this->getLanguages();

        return view(
            'dashboard.admin.settings.delivery-types.create',
            compact('languages')
        );
    }

    /**
     * Store a newly created delivery type.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:100',
                'alpha_dash',
                'unique:delivery_types,code',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            'translations' => [
                'nullable',
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

        DB::transaction(function () use ($validated) {
            $deliveryType = DeliveryType::create([
                'code' => $validated['code'],
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $this->syncTranslations(
                $deliveryType,
                $validated['translations'] ?? []
            );
        });

        return redirect()
            ->route('dashboard.admin.settings.delivery-types.index')
            ->with('success', 'Delivery type created successfully.');
    }

    /**
     * Show the form for editing the specified delivery type.
     */
    public function edit(DeliveryType $deliveryType)
    {
        $deliveryType->load('translations');

        $languages = $this->getLanguages();

        return view(
            'dashboard.admin.settings.delivery-types.edit',
            compact('deliveryType', 'languages')
        );
    }

    /**
     * Update the specified delivery type.
     */
    public function update(Request $request, DeliveryType $deliveryType)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:100',
                'alpha_dash',
                Rule::unique('delivery_types', 'code')
                    ->ignore($deliveryType->id),
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            'translations' => [
                'nullable',
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

        DB::transaction(function () use ($deliveryType, $validated) {
            $deliveryType->update([
                'code' => $validated['code'],
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'] ?? false,
            ]);

            $this->syncTranslations(
                $deliveryType,
                $validated['translations'] ?? []
            );
        });

        return redirect()
            ->route('dashboard.admin.settings.delivery-types.index')
            ->with('success', 'Delivery type updated successfully.');
    }

    /**
     * Remove the specified delivery type.
     */
    public function destroy(DeliveryType $deliveryType)
    {
        if ($deliveryType->shippingTemplates()->exists()) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'This delivery type cannot be deleted because it is used by shipping templates.'
                );
        }

        DB::transaction(function () use ($deliveryType) {
            $deliveryType->translations()->delete();
            $deliveryType->delete();
        });

        return redirect()
            ->route('dashboard.admin.settings.delivery-types.index')
            ->with('success', 'Delivery type deleted successfully.');
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(DeliveryType $deliveryType)
    {
        $deliveryType->update([
            'is_active' => !$deliveryType->is_active,
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                $deliveryType->is_active
                    ? 'Delivery type activated successfully.'
                    : 'Delivery type deactivated successfully.'
            );
    }

    /**
     * Get active languages.
     */
    protected function getLanguages()
    {
        return Language::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Sync translations.
     */
    protected function syncTranslations(
        DeliveryType $deliveryType,
        array $translations
    ): void {
        $languages = $this->getLanguages();

        foreach ($languages as $language) {
            $locale = $language->code;

            $translation = $translations[$locale] ?? null;

            if (!$translation) {
                continue;
            }

            $name = trim($translation['name'] ?? '');
            $description = trim($translation['description'] ?? '');

            /*
             * If the name was cleared, remove the existing translation.
             */
            if ($name === '') {
                $deliveryType->translations()
                    ->where('locale', $locale)
                    ->delete();

                continue;
            }

            $deliveryType->translations()->updateOrCreate(
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
    }
}