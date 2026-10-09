<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Domain\Returns\Models\ReturnPolicyReason;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReturnPolicyReasonController
{
    /**
     * Display a listing of return policy reasons.
     */
    public function index()
    {
        $reasons = ReturnPolicyReason::query()
            ->with('translations')
            ->withCount('policies')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view(
            'dashboard.admin.settings.return-policy-reasons.index',
            compact('reasons')
        );
    }

    /**
     * Show the form for creating a new return policy reason.
     */
    public function create()
    {
        $languages = $this->getLanguages();

        return view(
            'dashboard.admin.settings.return-policy-reasons.create',
            compact('languages')
        );
    }

    /**
     * Store a newly created return policy reason.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:100',
                'alpha_dash',
                'unique:return_policy_reasons,code',
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
            $reason = ReturnPolicyReason::create([
                'code' => $validated['code'],
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $this->syncTranslations(
                $reason,
                $validated['translations'] ?? []
            );
        });

        return redirect()
            ->route('admin.settings.return-policy-reasons.index')
            ->with('success', 'Return reason created successfully.');
    }

    /**
     * Show the form for editing the specified return policy reason.
     */
    public function edit(ReturnPolicyReason $reason)
    {
        $reason->load('translations');

        $languages = $this->getLanguages();

        return view(
            'dashboard.admin.settings.return-policy-reasons.edit',
            compact('reason', 'languages')
        );
    }

    /**
     * Update the specified return policy reason.
     */
    public function update(
        Request $request,
        ReturnPolicyReason $reason
    ) {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:100',
                'alpha_dash',
                Rule::unique('return_policy_reasons', 'code')
                    ->ignore($reason->id),
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

        DB::transaction(function () use ($reason, $validated) {
            $reason->update([
                'code' => $validated['code'],
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'] ?? false,
            ]);

            $this->syncTranslations(
                $reason,
                $validated['translations'] ?? []
            );
        });

        return redirect()
            ->route('admin.settings.return-policy-reasons.index')
            ->with('success', 'Return reason updated successfully.');
    }

    /**
     * Remove the specified return policy reason.
     */
    public function destroy(ReturnPolicyReason $reason)
    {
        if ($reason->policies()->exists()) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'This return reason cannot be deleted because it is used by return policies.'
                );
        }

        DB::transaction(function () use ($reason) {
            $reason->translations()->delete();
            $reason->delete();
        });

        return redirect()
            ->route('admin.settings.return-policy-reasons.index')
            ->with('success', 'Return reason deleted successfully.');
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(ReturnPolicyReason $reason)
    {
        $reason->update([
            'is_active' => !$reason->is_active,
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                $reason->is_active
                    ? 'Return reason activated successfully.'
                    : 'Return reason deactivated successfully.'
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
        ReturnPolicyReason $reason,
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
                $reason->translations()
                    ->where('locale', $locale)
                    ->delete();

                continue;
            }

            $reason->translations()->updateOrCreate(
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