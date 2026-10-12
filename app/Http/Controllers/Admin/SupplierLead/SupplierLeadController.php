<?php

namespace App\Http\Controllers\Admin\SupplierLead;

use App\Domain\SupplierLead\Models\SupplierLead;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

use App\Models\Country;

class SupplierLeadController extends Controller
{
    public function index(Request $request): View
{
    $statuses = [
        'new' => 'New',
        'to_contact' => 'To Contact',
        'contacted' => 'Contacted',
        'in_discussion' => 'In Discussion',
        'interested' => 'Interested',
        'invited' => 'Invited',
        'registered' => 'Registered',
        'activated' => 'Activated',
        'on_hold' => 'On Hold',
        'not_interested' => 'Not Interested',
        'disqualified' => 'Disqualified',
    ];

    $companyTypes = [
        'manufacturer' => 'Manufacturer',
        'wholesaler' => 'Wholesaler',
        'distributor' => 'Distributor',
        'trading_company' => 'Trading Company',
        'brand' => 'Brand',
        'other' => 'Other',
    ];

    $priorities = [
        'low' => 'Low',
        'normal' => 'Normal',
        'high' => 'High',
    ];

    $filters = $request->validate([
        'search' => ['nullable', 'string', 'max:255'],
        'acquisition_status' => [
            'nullable',
            Rule::in(array_keys($statuses)),
        ],
        'company_type' => [
            'nullable',
            Rule::in(array_keys($companyTypes)),
        ],
        'priority' => [
            'nullable',
            Rule::in(array_keys($priorities)),
        ],
        'assigned_to' => [
            'nullable',
            'integer',
            Rule::exists('users', 'id'),
        ],
    ]);

    $query = SupplierLead::query()
        ->with(['assignedUser', 'supplier'])
        ->withCount(['contacts', 'activities']);

    $search = trim($filters['search'] ?? '');

    if ($search !== '') {
        $query->where(function ($q) use ($search) {
            $q->where('company_name', 'like', "%{$search}%")
                ->orWhere('legal_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('website', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%");
        });
    }

    if (!empty($filters['acquisition_status'])) {
        $query->where(
            'acquisition_status',
            $filters['acquisition_status']
        );
    }

    if (!empty($filters['company_type'])) {
        $query->where('company_type', $filters['company_type']);
    }

    if (!empty($filters['priority'])) {
        $query->where('priority', $filters['priority']);
    }

    if (!empty($filters['assigned_to'])) {
        $query->where('assigned_to', $filters['assigned_to']);
    }

    $supplierLeads = $query
        ->latest()
        ->paginate(20)
        ->withQueryString();

    // CRM overview statistics, independent of active filters.
    $statistics = [
        'total' => SupplierLead::count(),
        'new' => SupplierLead::where('acquisition_status', 'new')->count(),
        'in_discussion' => SupplierLead::where(
            'acquisition_status',
            'in_discussion'
        )->count(),
        'registered' => SupplierLead::whereIn(
            'acquisition_status',
            ['registered', 'activated']
        )->count(),
    ];

    $users = User::query()
        ->orderBy('name')
        ->get(['id', 'name']);

    return view('dashboard.admin.supplier-leads.index', compact(
        'supplierLeads',
        'statuses',
        'companyTypes',
        'priorities',
        'users',
        'statistics'
    ));
}



public function show(SupplierLead $supplierLead): View
{
    $supplierLead->load([
        'categories.translations',
        'supplier',
        'assignedUser',
        'creator',
        'country',
        'contacts',
    ]);

    return view('dashboard.admin.supplier-leads.show', [
        'supplierLead' => $supplierLead,
        'statuses' => $this->statuses(),
        'companyTypes' => $this->companyTypes(),
        'priorities' => $this->priorities(),
    ]);
}




    public function create(): View
{
    $supplierLead = new SupplierLead();

    $statuses = $this->statuses();
    $companyTypes = $this->companyTypes();
    $priorities = $this->priorities();

    $users = User::query()
        ->orderBy('name')
        ->get(['id', 'name']);

    $categories = Category::query()
        ->visible()
        ->selectable()
        ->leaf()
        ->with('translations')
        ->ordered()
        ->get();

    $countries = Country::withCurrentTranslation()
        ->orderBy('name')
        ->get();

    return view('dashboard.admin.supplier-leads.create', compact(
        'supplierLead',
        'statuses',
        'companyTypes',
        'priorities',
        'users',
        'categories',
        'countries'
    ));
}

    public function store(Request $request): RedirectResponse
{
    $validated = $this->validateLead($request);

    $categoryIds = $validated['categories'] ?? [];
    unset($validated['categories']);

    $validated['slug'] = $this->uniqueSlug($validated['company_name']);
    $validated['created_by'] = Auth::id();

    $supplierLead = SupplierLead::create($validated);

    $supplierLead->categories()->sync($categoryIds);

    return redirect()
        ->route('admin.supplier-leads.index', $supplierLead)
        ->with('success', 'Supplier lead created successfully.');
}

    public function edit(SupplierLead $supplierLead): View
{
    $supplierLead->load(['categories', 'supplier']);

    $statuses = $this->statuses();
    $companyTypes = $this->companyTypes();
    $priorities = $this->priorities();

    $users = User::query()
        ->orderBy('name')
        ->get(['id', 'name']);

    $categories = Category::query()
        ->visible()
        ->selectable()
        ->leaf()
        ->with('translations')
        ->ordered()
        ->get();

    $countries = Country::withCurrentTranslation()
        ->orderBy('name')
        ->get();

    return view('dashboard.admin.supplier-leads.edit', compact(
        'supplierLead',
        'statuses',
        'companyTypes',
        'priorities',
        'users',
        'categories',
        'countries'
    ));
}




    public function update(
    Request $request,
    SupplierLead $supplierLead
): RedirectResponse {
    $validated = $this->validateLead($request, $supplierLead);

    $categoryIds = $validated['categories'] ?? [];
    unset($validated['categories']);

    $supplierLead->fill($validated);

    if ($supplierLead->isDirty('company_name')) {
        $supplierLead->slug = $this->uniqueSlug(
            $supplierLead->company_name,
            $supplierLead->id
        );
    }

    $supplierLead->save();

    $supplierLead->categories()->sync($categoryIds);

    return redirect()
        ->route('admin.supplier-leads.edit', $supplierLead)
        ->with('success', 'Supplier lead updated successfully.');
}

    public function destroy(SupplierLead $supplierLead): RedirectResponse
    {
        $supplierLead->delete();

        return redirect()
            ->route('admin.supplier-leads.index')
            ->with('success', 'Supplier lead deleted successfully.');
    }

    private function validateLead(
    Request $request,
    ?SupplierLead $supplierLead = null
): array {
    return $request->validate([
        'company_name' => ['required', 'string', 'max:255'],
        'linkedin_url' => ['nullable', 'url', 'max:2048'],
        'legal_name' => ['nullable', 'string', 'max:255'],
        'website' => ['nullable', 'url', 'max:2048'],
        'email' => ['nullable', 'email', 'max:255'],
        'phone' => ['nullable', 'string', 'max:50'],

        'country_id' => [
            'nullable',
            'integer',
            Rule::exists('countries', 'id'),
        ],

        'city' => ['nullable', 'string', 'max:255'],
        'address' => ['nullable', 'string'],

        'company_type' => [
            'required',
            Rule::in(array_keys($this->companyTypes())),
        ],

        'acquisition_status' => [
            'required',
            Rule::in(array_keys($this->statuses())),
        ],

        'priority' => [
            'required',
            Rule::in(array_keys($this->priorities())),
        ],

        'source' => ['nullable', 'string', 'max:255'],

        'assigned_to' => [
            'nullable',
            'integer',
            'exists:users,id',
        ],

        'notes' => ['nullable', 'string'],

        'categories' => ['nullable', 'array'],

        'categories.*' => [
            'required',
            'integer',
            'distinct',
            Rule::exists('categories', 'id')
                ->where('is_visible', true)
                ->where('is_selectable', true)
                ->where('is_leaf', true),
        ],
    ]);
}

    private function uniqueSlug(
        string $companyName,
        ?int $ignoreId = null
    ): string {
        $baseSlug = Str::slug($companyName) ?: 'supplier';

        $slug = $baseSlug;
        $counter = 1;

        while (
            SupplierLead::withTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }

    private function statuses(): array
    {
        return [
            'new' => 'New',
            'to_contact' => 'To Contact',
            'contacted' => 'Contacted',
            'in_discussion' => 'In Discussion',
            'interested' => 'Interested',
            'invited' => 'Invited',
            'registered' => 'Registered',
            'activated' => 'Activated',
            'on_hold' => 'On Hold',
            'not_interested' => 'Not Interested',
            'disqualified' => 'Disqualified',
        ];
    }

    private function companyTypes(): array
    {
        return [
            'manufacturer' => 'Manufacturer',
            'wholesaler' => 'Wholesaler',
            'distributor' => 'Distributor',
            'trading_company' => 'Trading Company',
            'brand' => 'Brand',
            'other' => 'Other',
        ];
    }

    private function priorities(): array
    {
        return [
            'low' => 'Low',
            'normal' => 'Normal',
            'high' => 'High',
        ];
    }
}