<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Domain\Returns\Models\ReturnPolicyResolution;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReturnPolicyResolutionController
{
    /**
     * Display a listing of return policy resolutions.
     */
    public function index()
    {
        $resolutions = ReturnPolicyResolution::query()
            ->with('translations')
            ->withCount('policies')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view(
            'dashboard.admin.settings.return-policy-resolutions.index',
            compact('resolutions')
        );
    }

    /**
     * Show the form for creating a new return policy resolution.
     */
    public function create()
    {
        $languages = $this->getLanguages();

        return view(
            'dashboard.admin.settings.return-policy-resolutions.create',
            compact('languages')
        );
    }

    /**
     * Store a newly created return policy resolution.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:100',
                'alpha_dash',
                'unique:return_policy_resolutions,code',
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
            $resolution = ReturnPolicyResolution::create([
                'code' => $validated['code'],
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $this->syncTranslations(
                $resolution,
                $validated['translations'] ?? []
            );
        });

        return redirect()
            ->route('admin.settings.return-policy-resolutions.index')
            ->with('success', 'Return resolution created successfully.');
    }

    /**
     * Show the form for editing the specified return policy resolution.
     */
    public function edit(ReturnPolicyResolution $resolution)
    {
        $resolution->load('translations');

        $languages = $this->getLanguages();

        return view(
            'dashboard.admin.settings.return-policy-resolutions.edit',
            compact('resolution', 'languages')
        );
    }

    /**
     * Update the specified return policy resolution.
     */
    public function update(
        Request $request,
        ReturnPolicyResolution $resolution
    ) {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:100',
                'alpha_dash',
                Rule::unique('return_policy_resolutions', 'code')
                    ->ignore($resolution->id),
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

        DB::transaction(function () use ($resolution, $validated) {
            $resolution->update([
                'code' => $validated['code'],
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'] ?? false,
            ]);

            $this->syncTranslations(
                $resolution,
                $validated['translations'] ?? []
            );
        });

        return redirect()
            ->route('admin.settings.return-policy-resolutions.index')
            ->with('success', 'Return resolution updated successfully.');
    }

    /**
     * Remove the specified return policy resolution.
     */
    public function destroy(ReturnPolicyResolution $resolution)
    {
        if ($resolution->policies()->exists()) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'This return resolution cannot be deleted because it is used by return policies.'
                );
        }

        DB::transaction(function () use ($resolution) {
            $resolution->translations()->delete();
            $resolution->delete();
        });

        return redirect()
            ->route('admin.settings.return-policy-resolutions.index')
            ->with('success', 'Return resolution deleted successfully.');
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(ReturnPolicyResolution $resolution)
    {
        $resolution->update([
            'is_active' => !$resolution->is_active,
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                $resolution->is_active
                    ? 'Return resolution activated successfully.'
                    : 'Return resolution deactivated successfully.'
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
        ReturnPolicyResolution $resolution,
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
                $resolution->translations()
                    ->where('locale', $locale)
                    ->delete();

                continue;
            }

            $resolution->translations()->updateOrCreate(
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