<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Domain\Returns\Models\ReturnPolicy;
use App\Domain\Returns\Models\ReturnPolicyReason;
use App\Domain\Returns\Models\ReturnPolicyResolution;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReturnPolicyController
{
    /**
     * Display a listing of return policies.
     */
    public function index()
    {
        $returnPolicies = ReturnPolicy::query()
            ->with([
                'translations',
                'reasons.translations',
                'resolutions.translations',
            ])
            ->withCount('productPolicies')
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->orderBy('id')
            ->get();

        return view(
            'dashboard.admin.settings.return-policies.index',
            compact('returnPolicies')
        );
    }

    /**
     * Show the form for creating a new return policy.
     */
    public function create()
    {
        $languages = $this->getLanguages();

        $reasons = ReturnPolicyReason::query()
            ->with('translations')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $resolutions = ReturnPolicyResolution::query()
            ->with('translations')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view(
            'dashboard.admin.settings.return-policies.create',
            compact('languages', 'reasons', 'resolutions')
        );
    }

    /**
     * Store a newly created return policy.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'code' => [
                'nullable',
                'string',
                'max:100',
                'alpha_dash',
                'unique:return_policies,code',
            ],

            'is_default' => [
                'nullable',
                'boolean',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            'return_window_days' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'return_shipping_payer' => [
                'nullable',
                'string',
                'in:buyer,supplier,depends_on_reason',
            ],

            'restocking_fee_enabled' => [
                'nullable',
                'boolean',
            ],

            'restocking_fee_percent' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            'custom_products_returnable' => [
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

            'reasons' => [
                'nullable',
                'array',
            ],

            'reasons.*' => [
                'integer',
                'exists:return_policy_reasons,id',
            ],

            'resolutions' => [
                'nullable',
                'array',
            ],

            'resolutions.*' => [
                'integer',
                'exists:return_policy_resolutions,id',
            ],
        ]);

        DB::transaction(function () use ($validated) {
            /*
             * Only one policy can be default.
             */
            if (!empty($validated['is_default'])) {
                ReturnPolicy::query()
                    ->where('is_default', true)
                    ->update([
                        'is_default' => false,
                    ]);
            }

            $returnPolicy = ReturnPolicy::create([
                'name' => $validated['name'],
                'code' => $validated['code'] ?? null,
                'is_default' => $validated['is_default'] ?? false,
                'is_active' => $validated['is_active'] ?? true,
                'return_window_days' => $validated['return_window_days'] ?? null,
                'return_shipping_payer' => $validated['return_shipping_payer'] ?? null,
                'restocking_fee_enabled' => $validated['restocking_fee_enabled'] ?? false,
                'restocking_fee_percent' => $validated['restocking_fee_percent'] ?? null,
                'custom_products_returnable' => $validated['custom_products_returnable'] ?? false,
            ]);

            $this->syncTranslations(
                $returnPolicy,
                $validated['translations'] ?? []
            );

            $this->syncReasons(
                $returnPolicy,
                $validated['reasons'] ?? []
            );

            $this->syncResolutions(
                $returnPolicy,
                $validated['resolutions'] ?? []
            );
        });

        return redirect()
            ->route('admin.settings.return-policies.index')
            ->with('success', 'Return policy created successfully.');
    }

    /**
     * Show the form for editing the specified return policy.
     */
    public function edit(ReturnPolicy $returnPolicy)
    {
        $returnPolicy->load([
            'translations',
            'reasons.translations',
            'resolutions.translations',
        ]);

        $languages = $this->getLanguages();

        $reasons = ReturnPolicyReason::query()
            ->with('translations')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $resolutions = ReturnPolicyResolution::query()
            ->with('translations')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $selectedReasonIds = $returnPolicy->reasons
            ->pluck('id')
            ->toArray();

        $selectedResolutionIds = $returnPolicy->resolutions
            ->pluck('id')
            ->toArray();

        return view(
            'dashboard.admin.settings.return-policies.edit',
            compact(
                'returnPolicy',
                'languages',
                'reasons',
                'resolutions',
                'selectedReasonIds',
                'selectedResolutionIds'
            )
        );
    }

    /**
     * Update the specified return policy.
     */
    public function update(Request $request, ReturnPolicy $returnPolicy)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'code' => [
                'nullable',
                'string',
                'max:100',
                'alpha_dash',
                Rule::unique('return_policies', 'code')
                    ->ignore($returnPolicy->id),
            ],

            'is_default' => [
                'nullable',
                'boolean',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            'return_window_days' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'return_shipping_payer' => [
                'nullable',
                'string',
                'in:buyer,supplier,depends_on_reason',
            ],

            'restocking_fee_enabled' => [
                'nullable',
                'boolean',
            ],

            'restocking_fee_percent' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            'custom_products_returnable' => [
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

            'reasons' => [
                'nullable',
                'array',
            ],

            'reasons.*' => [
                'integer',
                'exists:return_policy_reasons,id',
            ],

            'resolutions' => [
                'nullable',
                'array',
            ],

            'resolutions.*' => [
                'integer',
                'exists:return_policy_resolutions,id',
            ],
        ]);

        DB::transaction(function () use ($returnPolicy, $validated) {
            /*
             * Only one policy can be default.
             */
            if (!empty($validated['is_default'])) {
                ReturnPolicy::query()
                    ->where('id', '!=', $returnPolicy->id)
                    ->where('is_default', true)
                    ->update([
                        'is_default' => false,
                    ]);
            }

            $returnPolicy->update([
                'name' => $validated['name'],
                'code' => $validated['code'] ?? null,
                'is_default' => $validated['is_default'] ?? false,
                'is_active' => $validated['is_active'] ?? false,
                'return_window_days' => $validated['return_window_days'] ?? null,
                'return_shipping_payer' => $validated['return_shipping_payer'] ?? null,
                'restocking_fee_enabled' => $validated['restocking_fee_enabled'] ?? false,
                'restocking_fee_percent' => $validated['restocking_fee_percent'] ?? null,
                'custom_products_returnable' => $validated['custom_products_returnable'] ?? false,
            ]);

            $this->syncTranslations(
                $returnPolicy,
                $validated['translations'] ?? []
            );

            $this->syncReasons(
                $returnPolicy,
                $validated['reasons'] ?? []
            );

            $this->syncResolutions(
                $returnPolicy,
                $validated['resolutions'] ?? []
            );
        });

        return redirect()
            ->route('admin.settings.return-policies.index')
            ->with('success', 'Return policy updated successfully.');
    }

    /**
     * Remove the specified return policy.
     */
    public function destroy(ReturnPolicy $returnPolicy)
    {
        if ($returnPolicy->productPolicies()->exists()) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'This return policy cannot be deleted because it is used by products.'
                );
        }

        DB::transaction(function () use ($returnPolicy) {
            $returnPolicy->reasons()->detach();
            $returnPolicy->resolutions()->detach();
            $returnPolicy->translations()->delete();
            $returnPolicy->delete();
        });

        return redirect()
            ->route('admin.settings.return-policies.index')
            ->with('success', 'Return policy deleted successfully.');
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(ReturnPolicy $returnPolicy)
    {
        $returnPolicy->update([
            'is_active' => !$returnPolicy->is_active,
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                $returnPolicy->is_active
                    ? 'Return policy activated successfully.'
                    : 'Return policy deactivated successfully.'
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
        ReturnPolicy $returnPolicy,
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
                $returnPolicy->translations()
                    ->where('locale', $locale)
                    ->delete();

                continue;
            }

            $returnPolicy->translations()->updateOrCreate(
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

    /**
     * Sync policy reasons.
     */
    protected function syncReasons(
        ReturnPolicy $returnPolicy,
        array $reasonIds
    ): void {
        $syncData = [];

        foreach (array_values($reasonIds) as $index => $reasonId) {
            $syncData[$reasonId] = [
                'sort_order' => $index + 1,
            ];
        }

        $returnPolicy->reasons()->sync($syncData);
    }

    /**
     * Sync policy resolutions.
     */
    protected function syncResolutions(
        ReturnPolicy $returnPolicy,
        array $resolutionIds
    ): void {
        $syncData = [];

        foreach (array_values($resolutionIds) as $index => $resolutionId) {
            $syncData[$resolutionId] = [
                'sort_order' => $index + 1,
            ];
        }

        $returnPolicy->resolutions()->sync($syncData);
    }
}