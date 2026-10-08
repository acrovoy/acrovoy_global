<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Domain\Payment\Models\PaymentTerm;
use App\Domain\Payment\Models\PaymentTermTranslation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Language;

class PaymentTermController extends Controller
{
    /**
     * Display a listing of payment terms.
     */
    public function index()
    {
        $paymentTerms = PaymentTerm::with('translations')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view(
            'dashboard.admin.settings.payment-terms.index',
            compact('paymentTerms')
        );
    }

    /**
     * Show the form for creating a new payment term.
     */
    public function create()
    {
        $languages = $this->getLanguages();

        return view(
            'dashboard.admin.settings.payment-terms.create',
            compact('languages')
        );
    }

    /**
     * Store a newly created payment term.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                'unique:payment_terms,code',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
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
            $paymentTerm = PaymentTerm::create([
                'code' => $validated['code'],
                'is_active' => $validated['is_active'] ?? true,
                'sort_order' => $validated['sort_order'] ?? 0,
            ]);

            foreach ($validated['translations'] ?? [] as $locale => $translation) {
                if (
                    empty($translation['name'] ?? null)
                    && empty($translation['description'] ?? null)
                ) {
                    continue;
                }

                PaymentTermTranslation::create([
                    'payment_term_id' => $paymentTerm->id,
                    'locale' => $locale,
                    'name' => $translation['name'] ?? '',
                    'description' => $translation['description'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('dashboard.admin.settings.payment-terms.index')
            ->with('success', 'Payment term created successfully.');
    }

    /**
     * Show the form for editing the specified payment term.
     */
    public function edit(PaymentTerm $paymentTerm)
    {
        $paymentTerm->load('translations');

        $languages = $this->getLanguages();

        return view(
            'dashboard.admin.settings.payment-terms.edit',
            compact('paymentTerm', 'languages')
        );
    }

    /**
     * Update the specified payment term.
     */
    public function update(
        Request $request,
        PaymentTerm $paymentTerm
    ) {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('payment_terms', 'code')
                    ->ignore($paymentTerm->id),
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
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

        DB::transaction(function () use (
            $validated,
            $paymentTerm
        ) {
            $paymentTerm->update([
                'code' => $validated['code'],
                'is_active' => $validated['is_active'] ?? false,
                'sort_order' => $validated['sort_order'] ?? 0,
            ]);

            foreach ($validated['translations'] ?? [] as $locale => $translation) {
                if (
                    empty($translation['name'] ?? null)
                    && empty($translation['description'] ?? null)
                ) {
                    PaymentTermTranslation::where(
                        'payment_term_id',
                        $paymentTerm->id
                    )
                        ->where('locale', $locale)
                        ->delete();

                    continue;
                }

                PaymentTermTranslation::updateOrCreate(
                    [
                        'payment_term_id' => $paymentTerm->id,
                        'locale' => $locale,
                    ],
                    [
                        'name' => $translation['name'] ?? '',
                        'description' => $translation['description'] ?? null,
                    ]
                );
            }
        });

        return redirect()
            ->route('dashboard.admin.settings.payment-terms.index')
            ->with('success', 'Payment term updated successfully.');
    }

    /**
     * Remove the specified payment term.
     */
    public function destroy(PaymentTerm $paymentTerm)
    {
        $paymentTerm->delete();

        return redirect()
            ->route('dashboard.admin.settings.payment-terms.index')
            ->with('success', 'Payment term deleted successfully.');
    }

    /**
     * Toggle active status.
     */
    public function toggle(PaymentTerm $paymentTerm)
    {
        $paymentTerm->update([
            'is_active' => ! $paymentTerm->is_active,
        ]);

        return back()->with(
            'success',
            'Payment term status updated successfully.'
        );
    }

    protected function getLanguages()
    {
        return Language::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }
}