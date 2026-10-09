<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Domain\Returns\Models\ReturnPolicy;
use App\Domain\Returns\Models\ReturnPolicyReason;
use App\Domain\Returns\Models\ReturnPolicyResolution;
use App\Domain\Returns\Models\SupplierReturnPolicySetting;
use App\Models\Language;
use App\Facades\ActiveContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SupplierReturnPolicyController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    |
    | Supplier sees:
    |
    | 1. Platform return policies
    | 2. Supplier's own custom return policies
    |
    | Platform policies are read-only.
    | Supplier policies can be edited/deleted.
    |
    | The supplier's default policy is stored separately in
    | supplier_return_policy_settings.
    |
    */

    public function index()
    {

    


        $identity = $this->supplierIdentity();

        
$returnPolicies = ReturnPolicy::query()
    ->with([
        'translations',
        'reasons.translations',
        'resolutions.translations',
    ])
    ->where(function ($query) use ($identity) {

        /*
        |--------------------------------------------------------------------------
        | PLATFORM POLICIES
        |--------------------------------------------------------------------------
        |
        | Platform policies are always read-only for suppliers.
        | Only active platform policies should be visible.
        |
        */

        $query->where(function ($query) {

            $query->where(function ($query) {
                $query->whereNull('owner_type')
                    ->whereNull('owner_id');
            })
            ->orWhere(function ($query) {
                $query->where('owner_type', '')
                    ->where('owner_id', 0);
            });

            $query->where('is_active', true);
        });

        /*
        |--------------------------------------------------------------------------
        | SUPPLIER CUSTOM POLICIES
        |--------------------------------------------------------------------------
        |
        | Supplier-owned policies are shown regardless of active status,
        | because the supplier must be able to activate/deactivate them.
        |
        */

        $query->orWhere(function ($query) use ($identity) {
            $query->where('owner_type', $identity['entity_type'])
                ->where('owner_id', $identity['entity_id']);
        });
    })
    ->orderByDesc('is_active')
    ->orderByDesc('is_default')
    ->orderBy('name')
    ->orderBy('id')
    ->get();



        /*
        |--------------------------------------------------------------------------
        | Supplier default policy
        |--------------------------------------------------------------------------
        */

        $supplierSetting = SupplierReturnPolicySetting::query()
            ->with([
                'defaultReturnPolicy.translations',
            ])
            ->where('supplier_id', $identity['entity_id'])
            ->first();

        $defaultReturnPolicyId = $supplierSetting?->default_return_policy_id;

        return view(
            'dashboard.supplier.return-policies.index',
            compact(
                'returnPolicies',
                'supplierSetting',
                'defaultReturnPolicyId'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
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
            'dashboard.supplier.return-policies.create',
            compact(
                'languages',
                'reasons',
                'resolutions'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    |
    | Only supplier-owned custom policies can be created here.
    |
    */

    public function store(Request $request)
    {
        $identity = $this->supplierIdentity();

        $validated = $this->validatePolicy($request);

        DB::transaction(function () use (
            $request,
            $validated,
            $identity
        ) {

            /*
            |--------------------------------------------------------------------------
            | Create policy
            |--------------------------------------------------------------------------
            */

            $policy = ReturnPolicy::create([
                'name' => $validated['name'],
                'code' => $validated['code'],

                'owner_type' => $identity['entity_type'],
                'owner_id' => $identity['entity_id'],

                /*
                |--------------------------------------------------------------------------
                | Supplier custom policies are not platform defaults.
                |--------------------------------------------------------------------------
                */

                'is_default' => false,
                'is_active' => $request->boolean('is_active', true),

                'return_window_days' => $validated['return_window_days'] ?? null,
                'return_shipping_payer' => $validated['return_shipping_payer'] ?? null,

                'restocking_fee_enabled' => $request->boolean(
                    'restocking_fee_enabled'
                ),

                'restocking_fee_percent' => $validated['restocking_fee_percent'] ?? null,

                'custom_products_returnable' => $request->boolean(
                    'custom_products_returnable'
                ),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Translations
            |--------------------------------------------------------------------------
            */

            $this->syncTranslations(
                $policy,
                $request->input('translations', [])
            );

            /*
            |--------------------------------------------------------------------------
            | Reasons
            |--------------------------------------------------------------------------
            */

            $this->syncReasons(
                $policy,
                $request->input('reasons', [])
            );

            /*
            |--------------------------------------------------------------------------
            | Resolutions
            |--------------------------------------------------------------------------
            */

            $this->syncResolutions(
                $policy,
                $request->input('resolutions', [])
            );
        });

        return redirect()
            ->route('supplier.return-policies.index')
            ->with('success', 'Return policy created successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    |
    | Supplier can edit only policies created by this supplier.
    | Platform policies are read-only.
    |
    */

    public function edit(ReturnPolicy $returnPolicy)
    {
        $this->ensureSupplierOwned($returnPolicy);

        $returnPolicy->load([
            'translations',
            'reasons.translations',
            'resolutions.translations',
        ]);

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

        $selectedReasonIds = $returnPolicy
            ->reasons
            ->pluck('id')
            ->toArray();

        $selectedResolutionIds = $returnPolicy
            ->resolutions
            ->pluck('id')
            ->toArray();

        return view(
            'dashboard.supplier.return-policies.edit',
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

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        ReturnPolicy $returnPolicy
    ) {
        $this->ensureSupplierOwned($returnPolicy);

        $validated = $this->validatePolicy(
            $request,
            $returnPolicy
        );

        DB::transaction(function () use (
            $request,
            $validated,
            $returnPolicy
        ) {

            /*
            |--------------------------------------------------------------------------
            | Update policy
            |--------------------------------------------------------------------------
            */

            $returnPolicy->update([
                'name' => $validated['name'],
                'code' => $validated['code'],

                'return_window_days' => $validated['return_window_days'] ?? null,
                'return_shipping_payer' => $validated['return_shipping_payer'] ?? null,

                'restocking_fee_enabled' => $request->boolean(
                    'restocking_fee_enabled'
                ),

                'restocking_fee_percent' => $validated['restocking_fee_percent'] ?? null,

                'custom_products_returnable' => $request->boolean(
                    'custom_products_returnable'
                ),

                'is_active' => $request->boolean(
                    'is_active'
                ),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Translations
            |--------------------------------------------------------------------------
            */

            $this->syncTranslations(
                $returnPolicy,
                $request->input('translations', [])
            );

            /*
            |--------------------------------------------------------------------------
            | Reasons
            |--------------------------------------------------------------------------
            */

            $this->syncReasons(
                $returnPolicy,
                $request->input('reasons', [])
            );

            /*
            |--------------------------------------------------------------------------
            | Resolutions
            |--------------------------------------------------------------------------
            */

            $this->syncResolutions(
                $returnPolicy,
                $request->input('resolutions', [])
            );
        });

        return redirect()
            ->route('supplier.return-policies.index')
            ->with('success', 'Return policy updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | TOGGLE ACTIVE
    |--------------------------------------------------------------------------
    |
    | Only supplier-owned policies can be activated/deactivated.
    |
    */

    public function toggleActive(ReturnPolicy $returnPolicy)
    {
        $this->ensureSupplierOwned($returnPolicy);

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

    /*
    |--------------------------------------------------------------------------
    | SET DEFAULT
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | We DO NOT modify return_policies.is_default here.
    |
    | A platform policy can be the default for many different suppliers.
    | Therefore the supplier-specific default is stored in:
    |
    | supplier_return_policy_settings
    |
    */

    public function setDefault(ReturnPolicy $returnPolicy)
    {
        $identity = $this->supplierIdentity();

        /*
        |--------------------------------------------------------------------------
        | Make sure the policy is available to this supplier
        |--------------------------------------------------------------------------
        |
        | It can be:
        |
        | - a platform policy
        | - this supplier's own custom policy
        |
        */

        $this->ensureAvailableToSupplier(
            $returnPolicy,
            $identity
        );

        /*
        |--------------------------------------------------------------------------
        | Only active policies can become the supplier default.
        |--------------------------------------------------------------------------
        */

        if (!$returnPolicy->is_active) {
            return redirect()
                ->back()
                ->withErrors([
                    'return_policy' => 'An inactive return policy cannot be set as the supplier default.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Store supplier-specific default
        |--------------------------------------------------------------------------
        */

        SupplierReturnPolicySetting::updateOrCreate(
            [
                'supplier_id' => $identity['entity_id'],
            ],
            [
                'default_return_policy_id' => $returnPolicy->id,
            ]
        );

        return redirect()
            ->back()
            ->with(
                'success',
                'Return policy set as your default successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE DEFAULT
    |--------------------------------------------------------------------------
    |
    | Optional helper.
    |
    | If your routes/view do not use this action yet, it can simply remain
    | unused. It allows the supplier to return to "no explicit default".
    |
    */

    public function removeDefault()
    {
        $identity = $this->supplierIdentity();

        SupplierReturnPolicySetting::query()
            ->where('supplier_id', $identity['entity_id'])
            ->delete();

        return redirect()
            ->back()
            ->with(
                'success',
                'Supplier default return policy removed successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    |
    | Platform policies cannot be deleted.
    | Supplier can delete only own custom policies.
    |
    | A policy that is currently used as supplier default cannot be deleted
    | without first removing/changing the default.
    |
    */

    public function destroy(ReturnPolicy $returnPolicy)
    {
        $identity = $this->supplierIdentity();

        $this->ensureSupplierOwned($returnPolicy);

        /*
        |--------------------------------------------------------------------------
        | Prevent deleting a policy assigned to products
        |--------------------------------------------------------------------------
        */

        if ($returnPolicy->productPolicies()->exists()) {
            return redirect()
                ->back()
                ->withErrors([
                    'return_policy' => 'This return policy cannot be deleted because it is assigned to one or more products.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent deleting the current supplier default
        |--------------------------------------------------------------------------
        */

        $isSupplierDefault = SupplierReturnPolicySetting::query()
            ->where('supplier_id', $identity['entity_id'])
            ->where('default_return_policy_id', $returnPolicy->id)
            ->exists();

        if ($isSupplierDefault) {
            return redirect()
                ->back()
                ->withErrors([
                    'return_policy' => 'This return policy is currently your default policy. Please select another default policy before deleting it.',
                ]);
        }

        DB::transaction(function () use ($returnPolicy) {

            /*
            |--------------------------------------------------------------------------
            | Detach relationships
            |--------------------------------------------------------------------------
            */

            $returnPolicy->reasons()->detach();
            $returnPolicy->resolutions()->detach();

            /*
            |--------------------------------------------------------------------------
            | Delete translations
            |--------------------------------------------------------------------------
            */

            $returnPolicy->translations()->delete();

            /*
            |--------------------------------------------------------------------------
            | Delete policy
            |--------------------------------------------------------------------------
            */

            $returnPolicy->delete();
        });

        return redirect()
            ->route('supplier.return-policies.index')
            ->with(
                'success',
                'Return policy deleted successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    protected function validatePolicy(
        Request $request,
        ?ReturnPolicy $returnPolicy = null
    ): array {
        $codeRule = [
            'required',
            'string',
            'max:100',
            'alpha_dash',
        ];

        if ($returnPolicy) {
            $codeRule[] = Rule::unique('return_policies', 'code')
                ->ignore($returnPolicy->id);
        } else {
            $codeRule[] = Rule::unique('return_policies', 'code');
        }

        return $request->validate([
            /*
            |--------------------------------------------------------------------------
            | Base fields
            |--------------------------------------------------------------------------
            */

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'code' => $codeRule,

            'return_window_days' => [
                'nullable',
                'integer',
                'min:0',
                'max:3650',
            ],

            'return_shipping_payer' => [
                'nullable',
                'string',
                'max:50',
            ],

            'restocking_fee_percent' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            /*
            |--------------------------------------------------------------------------
            | Translations
            |--------------------------------------------------------------------------
            */

            'translations' => [
                'nullable',
                'array',
            ],

            'translations.*' => [
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

            /*
            |--------------------------------------------------------------------------
            | Reasons
            |--------------------------------------------------------------------------
            */

            'reasons' => [
                'nullable',
                'array',
            ],

            'reasons.*' => [
                'integer',
                'exists:return_policy_reasons,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Resolutions
            |--------------------------------------------------------------------------
            */

            'resolutions' => [
                'nullable',
                'array',
            ],

            'resolutions.*' => [
                'integer',
                'exists:return_policy_resolutions,id',
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TRANSLATIONS
    |--------------------------------------------------------------------------
    */

    protected function syncTranslations(
        ReturnPolicy $returnPolicy,
        array $translations
    ): void {
        /*
        |--------------------------------------------------------------------------
        | Delete old translations
        |--------------------------------------------------------------------------
        */

        $returnPolicy->translations()->delete();

        /*
        |--------------------------------------------------------------------------
        | Create submitted translations
        |--------------------------------------------------------------------------
        */

        foreach ($translations as $locale => $translation) {

            if (!is_array($translation)) {
                continue;
            }

            $name = trim(
                (string)($translation['name'] ?? '')
            );

            $description = trim(
                (string)($translation['description'] ?? '')
            );

            /*
            |--------------------------------------------------------------------------
            | Skip completely empty translation rows
            |--------------------------------------------------------------------------
            */

            if ($name === '' && $description === '') {
                continue;
            }

            $returnPolicy->translations()->create([
                'locale' => $locale,
                'name' => $name !== ''
                    ? $name
                    : $returnPolicy->name,
                'description' => $description !== ''
                    ? $description
                    : null,
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | REASONS
    |--------------------------------------------------------------------------
    */

    protected function syncReasons(
        ReturnPolicy $returnPolicy,
        array $reasonIds
    ): void {
        $reasonIds = collect($reasonIds)
            ->filter()
            ->map(fn ($id) => (int)$id)
            ->unique()
            ->values()
            ->toArray();

        $syncData = [];

        foreach ($reasonIds as $index => $reasonId) {
            $syncData[$reasonId] = [
                'sort_order' => $index,
            ];
        }

        $returnPolicy->reasons()->sync($syncData);
    }

    /*
    |--------------------------------------------------------------------------
    | RESOLUTIONS
    |--------------------------------------------------------------------------
    */

    protected function syncResolutions(
        ReturnPolicy $returnPolicy,
        array $resolutionIds
    ): void {
        $resolutionIds = collect($resolutionIds)
            ->filter()
            ->map(fn ($id) => (int)$id)
            ->unique()
            ->values()
            ->toArray();

        $syncData = [];

        foreach ($resolutionIds as $index => $resolutionId) {
            $syncData[$resolutionId] = [
                'sort_order' => $index,
            ];
        }

        $returnPolicy->resolutions()->sync($syncData);
    }

    /*
    |--------------------------------------------------------------------------
    | LANGUAGES
    |--------------------------------------------------------------------------
    */

    protected function getLanguages()
    {
        return Language::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | SUPPLIER IDENTITY
    |--------------------------------------------------------------------------
    |
    | We use ActiveContext::identity() instead of the old:
    |
    | $context->type()
    | $context->id()
    |
    */

    protected function supplierIdentity(): array
    {
        $identity = ActiveContext::identity();

        if (
            empty($identity['entity_type']) ||
            empty($identity['entity_id'])
        ) {
            abort(
                403,
                'A valid supplier context is required.'
            );
        }

        return $identity;
    }

    /*
    |--------------------------------------------------------------------------
    | ENSURE SUPPLIER OWNED
    |--------------------------------------------------------------------------
    |
    | Used for edit/update/toggle/delete.
    |
    | Platform policies have:
    |
    | owner_type = NULL
    | owner_id   = NULL
    |
    | and therefore cannot pass this check.
    |
    */

    protected function ensureSupplierOwned(
        ReturnPolicy $returnPolicy
    ): void {
        $identity = $this->supplierIdentity();

        $isOwned = $returnPolicy->owner_type === $identity['entity_type']
            && (int)$returnPolicy->owner_id === (int)$identity['entity_id'];

        if (!$isOwned) {
            abort(
                403,
                'You cannot modify this return policy.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ENSURE AVAILABLE TO SUPPLIER
    |--------------------------------------------------------------------------
    |
    | A supplier can use:
    |
    | - any platform policy
    | - any policy created by this supplier
    |
    | But cannot use another supplier's custom policy.
    |
    */

    
protected function ensureAvailableToSupplier(
    ReturnPolicy $returnPolicy,
    array $identity
): void {

    /*
    |--------------------------------------------------------------------------
    | PLATFORM POLICY
    |--------------------------------------------------------------------------
    |
    | Platform policies are not owned by a supplier.
    |
    | Depending on the existing database records, platform ownership
    | can be represented as either:
    |
    | owner_type = NULL, owner_id = NULL
    |
    | or:
    |
    | owner_type = '', owner_id = 0
    |
    */

    $isPlatformPolicy =
        (
            is_null($returnPolicy->owner_type)
            && is_null($returnPolicy->owner_id)
        )
        ||
        (
            (string) $returnPolicy->owner_type === ''
            && (int) $returnPolicy->owner_id === 0
        );


    /*
    |--------------------------------------------------------------------------
    | SUPPLIER OWNED POLICY
    |--------------------------------------------------------------------------
    */

    $isOwnPolicy =
        (string) $returnPolicy->owner_type === (string) $identity['entity_type']
        && (int) $returnPolicy->owner_id === (int) $identity['entity_id'];


    /*
    |--------------------------------------------------------------------------
    | ACCESS CHECK
    |--------------------------------------------------------------------------
    |
    | Supplier may use:
    |
    | - any platform policy
    | - any policy owned by the current supplier
    |
    */

    if (!$isPlatformPolicy && !$isOwnPolicy) {

        abort(
            403,
            'This return policy is not available to your supplier account.'
        );
    }
}





}
