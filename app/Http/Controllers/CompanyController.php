<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Facades\ActiveContext;

class CompanyController extends Controller
{
    private array $map = [
        'buyer' => 'buyers',
        'supplier' => 'suppliers',
        'logistics' => 'logistic_companies',
    ];

    /**
     * INDEX
     */
   public function index()
{
    $userId = auth()->id();

    $relations = DB::table('company_users')
        ->where('user_id', $userId)
        ->where('role', 'owner')
        ->get(['company_type', 'company_id']);

    $buyerIds = $relations->where('company_type', 'App\Models\Buyer')->pluck('company_id');
    $supplierIds = $relations->where('company_type', 'App\Models\Supplier')->pluck('company_id');
    $logisticsIds = $relations->where('company_type', 'App\Models\LogisticCompany')->pluck('company_id');

    $buyers = DB::table('buyers')
        ->whereIn('id', $buyerIds)
        ->select('id','name','slug','status', DB::raw("'buyer' as type"), 'created_at');

    $suppliers = DB::table('suppliers')
        ->whereIn('id', $supplierIds)
        ->select('id','name','slug','status', DB::raw("'supplier' as type"), 'created_at');

    $logistics = DB::table('logistic_companies')
        ->whereIn('id', $logisticsIds)
        ->select('id','name','slug','status', DB::raw("'logistics' as type"), 'created_at');

    $companies = $buyers->get()
        ->merge($suppliers->get())
        ->merge($logistics->get())
        ->sortByDesc('created_at')
        ->values();

    // 👇 ВАЖНО: разделяем тут, не в blade
    $activeCompanies = $companies->whereIn('status', ['active', 'pending']);
    $inactiveCompanies = $companies->whereIn('status', ['blocked', 'deleted','inactive']);

    return view('dashboard.companies.index', compact(
        'activeCompanies',
        'inactiveCompanies'
    ));
}

    /**
     * CREATE
     */
    public function create(Request $request)
    {

    
    $contextType = auth()->user()->setting('platform_mode', 'buyer');
    

        $type = $request->get('type', $contextType);

        abort_unless(isset($this->map[$type]), 404);

        return view('dashboard.companies.create', compact('type'));
    }

    /**
     * STORE
     */
    public function store(Request $request)
{
    $type = $request->get('type');

    abort_unless(isset($this->map[$type]), 404);

    $table = $this->map[$type];

    $data = $this->validateBase($request, $type);

    $data['slug'] = Str::slug($data['name']);
    $data['created_at'] = now();
    $data['updated_at'] = now();
    

    DB::beginTransaction();

    try {

        // create company
        $companyId = DB::table($table)->insertGetId($data);

        $modelClass = $this->getModelClass($type);

        DB::table('company_users')->insert([
            'user_id' => auth()->id(),
            'company_type' => $modelClass,
            'company_id' => $companyId,
            'role' => 'owner',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::commit();

        return redirect()
            ->route('dashboard.companies.index')
            ->with('success', 'Company created successfully');

    } catch (\Throwable $e) {
        DB::rollBack();
        throw $e;
    }
}

    /**
     * EDIT
     */
    public function edit(int $id)
{
    $company = $this->findCompany($id);

    abort_if(!$company, 404);

    return view('dashboard.companies.edit', compact('company'));
}

    /**
     * UPDATE
     */
    public function update(Request $request, int $id)
{
    $company = $this->findCompany($id);

    abort_if(!$company, 404);

    $table = $this->map[$company->type];

    $data = $this->validateBase($request, $company->type);

    // защита от смены типа
    unset($data['type']);

    $data['slug'] = Str::slug($data['name']);
    $data['updated_at'] = now();

    DB::table($table)
        ->where('id', $id)
        ->update($data);

    return redirect()
        ->route('dashboard.companies.index')
        ->with('success', 'Company updated successfully');
}

    /**
     * DELETE
     */
    /**
 * DELETE (SOFT DELETE)
 */
/**
 * DELETE (SOFT DELETE)
 */
public function destroy(int $id)
{
    $company = $this->findCompany($id);

    abort_if(!$company, 404);

    $table = $this->map[$company->type];

    DB::beginTransaction();

    try {

        DB::table($table)
            ->where('id', $id)
            ->update([
                'status' => 'inactive', // enum: active | pending | blocked
                'updated_at' => now(),
            ]);

        DB::table('company_users')
            ->where('company_type', match ($company->type) {
                'buyer' => \App\Models\Buyer::class,
                'supplier' => \App\Models\Supplier::class,
                'logistics' => \App\Models\LogisticCompany::class,
            })
            ->where('company_id', $id)
            ->update([
                'status' => 'inactive',
                'updated_at' => now(),
            ]);

        DB::commit();

        return back()->with('success', 'Company deactivated');

    } catch (\Throwable $e) {
        DB::rollBack();
        throw $e;
    }
}
    /**
     * VALIDATION
     */
    private function validateBase(Request $request, string $type): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function getModelClass(string $type): string
{
    return match ($type) {
        'buyer' => \App\Models\Buyer::class,
        'supplier' => \App\Models\Supplier::class,
        'logistics' => \App\Models\LogisticCompany::class,
        default => throw new \Exception("Unknown type"),
    };
}

private function findCompany($id)
{
    foreach ($this->map as $type => $table) {
        $company = DB::table($table)->where('id', $id)->first();

        if ($company) {
            $company->type = $type;
            $company->table = $table;
            return $company;
        }
    }

    return null;
}


public function transferOwner(Request $request, int $companyId)
{
    $request->validate([
        'user_id' => 'required|integer|exists:users,id',
    ]);

    DB::beginTransaction();

    try {

        // 1. найти модель компании (buyer/supplier/logistics)
        $company = $this->findCompany($companyId);
        abort_if(!$company, 404);

        $companyType = match($company->type) {
            'buyer' => 'App\Models\Buyer',
            'supplier' => 'App\Models\Supplier',
            'logistics' => 'App\Models\LogisticCompany',
            default => null,
        };

        abort_if(!$companyType, 404);

        // 2. снять старого owner
        DB::table('company_users')
            ->where('company_id', $companyId)
            ->where('company_type', $companyType)
            ->where('role', 'owner')
            ->update([
                'role' => 'administrator',
                'updated_at' => now(),
            ]);

        // 3. назначить нового owner
        DB::table('company_users')->updateOrInsert(
            [
                'company_id' => $companyId,
                'company_type' => $companyType,
                'user_id' => $request->user_id,
            ],
            [
                'role' => 'owner',
                'status' => 'active',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        DB::commit();

        return back()->with('success', 'Owner transferred successfully');

    } catch (\Throwable $e) {
        DB::rollBack();
        throw $e;
    }
}
   
}