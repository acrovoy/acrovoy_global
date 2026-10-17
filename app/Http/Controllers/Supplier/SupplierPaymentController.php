<?php

namespace App\Http\Controllers\Supplier;

use App\Domain\Payment\Models\PaymentMethod;
use App\Domain\Payment\Models\PaymentTerm;
use App\Http\Controllers\Controller;
use App\Services\Company\ActiveContextService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SupplierPaymentController extends Controller
{
    public function __construct(
        protected ActiveContextService $context
    ) {}

    /**
     * Payment Methods и Payment Terms.
     */
    public function index()
    {
        abort_unless(
            auth()->check() && $this->context->isSupplier(),
            403
        );

        $supplier = $this->context->supplier();

        abort_unless($supplier, 403);

        $supplierId = $supplier->id;

        $paymentMethods = PaymentMethod::query()
            ->where('is_active', true)
            ->with('translations')
            ->orderBy('sort_order')
            ->get();

        $paymentTerms = PaymentTerm::query()
            ->where('is_active', true)
            ->with('translations')
            ->orderBy('sort_order')
            ->get();

        $selectedMethodIds = DB::table('supplier_payment_methods')
            ->where('supplier_id', $supplierId)
            ->pluck('payment_method_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $defaultPaymentMethodId = DB::table('supplier_payment_methods')
            ->where('supplier_id', $supplierId)
            ->where('is_default', true)
            ->value('payment_method_id');

        $defaultPaymentMethodId = $defaultPaymentMethodId !== null
            ? (int) $defaultPaymentMethodId
            : null;

        $selectedPaymentTermId = DB::table('supplier_payment_settings')
            ->where('supplier_id', $supplierId)
            ->value('default_payment_term_id');

        $selectedPaymentTermId = $selectedPaymentTermId !== null
            ? (int) $selectedPaymentTermId
            : null;

        return view('dashboard.supplier.payments.index', [
            'supplier' => $supplier,
            'paymentMethods' => $paymentMethods,
            'paymentTerms' => $paymentTerms,
            'selectedMethodIds' => $selectedMethodIds,
            'defaultPaymentMethodId' => $defaultPaymentMethodId,
            'selectedPaymentTermId' => $selectedPaymentTermId,
        ]);
    }

    /**
     * Сохранить выбранные способы оплаты.
     */
    public function updateMethods(Request $request)
    {
        abort_unless(
            auth()->check() && $this->context->isSupplier(),
            403
        );

        $supplier = $this->context->supplier();

        abort_unless($supplier, 403);

        $validated = $request->validate([
            'payment_method_ids' => ['nullable', 'array'],
            'payment_method_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:payment_methods,id',
            ],
        ]);

        $methodIds = collect($validated['payment_method_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $activeMethodIds = PaymentMethod::query()
            ->where('is_active', true)
            ->whereIn('id', $methodIds)
            ->pluck('id')
            ->map(fn ($id) => (int) $id);

        if ($activeMethodIds->count() !== $methodIds->count()) {
            throw ValidationException::withMessages([
                'payment_method_ids' =>
                    'Один или несколько способов оплаты недоступны.',
            ]);
        }

        DB::transaction(function () use ($supplier, $methodIds) {
            $supplierId = $supplier->id;

            $currentDefaultId = DB::table('supplier_payment_methods')
                ->where('supplier_id', $supplierId)
                ->where('is_default', true)
                ->value('payment_method_id');

            $keepDefault = $currentDefaultId !== null
                && $methodIds->contains((int) $currentDefaultId);

            DB::table('supplier_payment_methods')
                ->where('supplier_id', $supplierId)
                ->delete();

            if ($methodIds->isNotEmpty()) {
                $now = now();

                $rows = $methodIds->map(
                    function (int $methodId, int $index) use (
                        $supplierId,
                        $currentDefaultId,
                        $keepDefault,
                        $now
                    ) {
                        return [
                            'supplier_id' => $supplierId,
                            'payment_method_id' => $methodId,
                            'is_default' => $keepDefault
                                && $methodId === (int) $currentDefaultId,
                            'sort_order' => $index,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                )->all();

                DB::table('supplier_payment_methods')->insert($rows);
            }
        });

        return redirect()
            ->route('supplier.payments.index')
            ->with('success', 'Способы оплаты сохранены.');
    }

    /**
     * Установить основной способ оплаты.
     */
    public function setDefaultMethod(
        Request $request,
        PaymentMethod $paymentMethod
    ) {
        abort_unless(
            auth()->check() && $this->context->isSupplier(),
            403
        );

        $supplier = $this->context->supplier();

        abort_unless($supplier, 403);

        abort_unless($paymentMethod->is_active, 404);

        DB::transaction(function () use ($supplier, $paymentMethod) {
            $supplierId = $supplier->id;

            $isSelected = DB::table('supplier_payment_methods')
                ->where('supplier_id', $supplierId)
                ->where('payment_method_id', $paymentMethod->id)
                ->exists();

            if (!$isSelected) {
                throw ValidationException::withMessages([
                    'payment_method_id' =>
                        'Сначала выберите этот способ оплаты и сохраните список.',
                ]);
            }

            DB::table('supplier_payment_methods')
                ->where('supplier_id', $supplierId)
                ->update([
                    'is_default' => false,
                    'updated_at' => now(),
                ]);

            DB::table('supplier_payment_methods')
                ->where('supplier_id', $supplierId)
                ->where('payment_method_id', $paymentMethod->id)
                ->update([
                    'is_default' => true,
                    'updated_at' => now(),
                ]);
        });

        return redirect()
            ->route('supplier.payments.index')
            ->with('success', 'Основной способ оплаты обновлён.');
    }

    /**
     * Сохранить условие оплаты по умолчанию.
     */
    public function updateTerm(Request $request)
    {
        abort_unless(
            auth()->check() && $this->context->isSupplier(),
            403
        );

        $supplier = $this->context->supplier();

        abort_unless($supplier, 403);

        $validated = $request->validate([
            'payment_term_id' => [
                'nullable',
                'integer',
                'exists:payment_terms,id',
            ],
        ]);

        $termId = isset($validated['payment_term_id'])
            ? (int) $validated['payment_term_id']
            : null;

        if ($termId !== null) {
            $isActive = PaymentTerm::query()
                ->whereKey($termId)
                ->where('is_active', true)
                ->exists();

            if (!$isActive) {
                throw ValidationException::withMessages([
                    'payment_term_id' =>
                        'Выбранное условие оплаты недоступно.',
                ]);
            }
        }

        DB::table('supplier_payment_settings')->updateOrInsert(
            ['supplier_id' => $supplier->id],
            [
                'default_payment_term_id' => $termId,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return redirect()
            ->route('supplier.payments.index')
            ->with('success', 'Условие оплаты по умолчанию сохранено.');
    }
}