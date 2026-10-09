<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Domain\Payment\Models\PaymentMethod;
use App\Domain\Payment\Models\PaymentMethodTranslation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Language;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of payment methods.
     */
    public function index()
    {
        $paymentMethods = PaymentMethod::with('translations')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view(
            'dashboard.admin.settings.payment-methods.index',
            compact('paymentMethods')
        );
    }

    /**
     * Show the form for creating a new payment method.
     */
    public function create()
    {
        $languages = $this->getLanguages();

        return view(
            'dashboard.admin.settings.payment-methods.create',
            compact('languages')
        );
    }

    /**
     * Store a newly created payment method.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                'unique:payment_methods,code',
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
            $paymentMethod = PaymentMethod::create([
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

                PaymentMethodTranslation::create([
                    'payment_method_id' => $paymentMethod->id,
                    'locale' => $locale,
                    'name' => $translation['name'] ?? '',
                    'description' => $translation['description'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('admin.settings.payment-methods.index')
            ->with('success', 'Payment method created successfully.');
    }

    /**
     * Show the form for editing the specified payment method.
     */
    public function edit(PaymentMethod $paymentMethod)
    {
        $paymentMethod->load('translations');

        $languages = $this->getLanguages();

        return view(
            'dashboard.admin.settings.payment-methods.edit',
            compact('paymentMethod', 'languages')
        );
    }

    /**
     * Update the specified payment method.
     */
  
public function update(
    Request $request,
    PaymentMethod $paymentMethod
) {
    $validated = $request->validate([
        'code' => [
            'required',
            'string',
            'max:255',
            'alpha_dash',
            Rule::unique('payment_methods', 'code')
                ->ignore($paymentMethod->id),
        ],

        'icon_svg' => [
            'nullable',
            'string',
            'max:1000000',
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
        $paymentMethod
    ) {
        $paymentMethod->update([
            'code' => $validated['code'],
            'icon_svg' => $validated['icon_svg'] ?? null,
            'is_active' => $validated['is_active'] ?? false,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        foreach ($validated['translations'] ?? [] as $locale => $translation) {
            if (
                empty($translation['name'] ?? null)
                && empty($translation['description'] ?? null)
            ) {
                PaymentMethodTranslation::where(
                    'payment_method_id',
                    $paymentMethod->id
                )
                    ->where('locale', $locale)
                    ->delete();

                continue;
            }

            PaymentMethodTranslation::updateOrCreate(
                [
                    'payment_method_id' => $paymentMethod->id,
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
        ->route('admin.settings.payment-methods.index')
        ->with('success', 'Payment method updated successfully.');
}


    /**
     * Remove the specified payment method.
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();

        return redirect()
            ->route('dashboard.admin.settings.payment-methods.index')
            ->with('success', 'Payment method deleted successfully.');
    }

    /**
     * Toggle active status.
     */
    public function toggle(PaymentMethod $paymentMethod)
    {
        $paymentMethod->update([
            'is_active' => ! $paymentMethod->is_active,
        ]);

        return back()->with(
            'success',
            'Payment method status updated successfully.'
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