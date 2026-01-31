<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Supplier;
use App\Models\Country;
use App\Models\SupplierCertificate;

class AdminSellersController extends Controller
{
    /**
     * Display a listing of sellers.
     */
    public function index(Request $request)
    {
        $query = Supplier::query();

        /* ===============================
         * Фильтр по статусу
         * =============================== */
        $status = $request->get('status');
        if ($status) {
            $query->where('status', $status);
        }

        /* ===============================
         * Поиск по имени / email
         * =============================== */
        $search = $request->get('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        /* ===============================
         * Сортировка (по дате)
         * =============================== */
        $sellers = $query
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.admin.sellers.index', [
            'sellers'      => $sellers,
            'status'       => $status,
            'searchFilter' => $search,
        ]);
    }


    public function show(Supplier $seller)
{
    $seller->load(['products.category', 'certificates', 'country', 'reviews.user']); // загружаем отзывы с пользователями

    $reputation = $seller->reviews()->avg('rating') ?? 0; // средняя оценка
    $reviews = $seller->reviews()->latest()->take(5)->get(); // последние 5 отзывов

    // Кол-во отзывов для блока репутации
    $reviewsCount = $seller->reviews()->count();

    return view('dashboard.admin.sellers.show', [
        'seller' => $seller,
        'reputation' => $reputation,
        'reviews' => $reviews,
        'reviewsCount' => $reviewsCount,
    ]);
}


    public function edit($id)
{
    $seller = Supplier::with(['certificates', 'country'])->findOrFail($id);
    $countries = Country::orderBy('name')->get();

    return view('dashboard.admin.sellers.edit', compact('seller', 'countries'));
}

public function update(Request $request, $id)
{
     $seller = Supplier::findOrFail($id);

    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'phone' => 'nullable|string|max:50',
        'address' => 'nullable|string',
        'country_id' => 'nullable|exists:countries,id',
        'short_description' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'status' => 'required|in:active,inactive,blocked',
        'logo' => 'nullable|image|max:2048',
        'catalog_image' => 'nullable|image|max:4096',
    ]);

    /* =====================
     * LOGO
     * ===================== */
    if ($request->hasFile('logo')) {

        // удаляем старый логотип
        if ($seller->logo && Storage::disk('public')->exists($seller->logo)) {
            Storage::disk('public')->delete($seller->logo);
        }

        $data['logo'] = $request->file('logo')
            ->store('company-logos', 'public');
    }

    /* =====================
     * CATALOG IMAGE
     * ===================== */
    if ($request->hasFile('catalog_image')) {

        if ($seller->catalog_image && Storage::disk('public')->exists($seller->catalog_image)) {
            Storage::disk('public')->delete($seller->catalog_image);
        }

        $data['catalog_image'] = $request->file('catalog_image')
            ->store('company-catalog', 'public');
    }

    $seller->update($data);

    return back()->with('success', 'Company updated');
}

public function uploadCertificate(Request $request, Supplier $seller)
{
    $request->validate([
        'certificates.*' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:10240'
    ]);

    $uploaded = [];

    foreach ($request->file('certificates') as $file) {
        $path = $file->store('certificates', 'public');

        $certificate = $seller->certificates()->create([
            'name' => $file->getClientOriginalName(),
            'file_path' => $path,
        ]);

        $uploaded[] = [
            'id' => $certificate->id,
            'name' => $certificate->name,
            'url' => asset('storage/' . $certificate->file_path),
        ];
    }

    return response()->json([
        'success' => true,
        'certificates' => $uploaded
    ]);
}

public function listCertificates(Supplier $seller)
{
    $certificates = $seller->certificates->map(fn($c) => [
        'id' => $c->id,
        'name' => $c->name,
        'url' => asset('storage/' . $c->file_path),
    ]);

    return response()->json(['success' => true, 'certificates' => $certificates]);
}

public function deleteCertificate($certificateId)
{
    $certificate = \App\Models\SupplierCertificate::findOrFail($certificateId);
    $seller = $certificate->seller;

    \Storage::disk('public')->delete($certificate->file_path);
    $certificate->delete();

    return response()->json([
        'success' => true,
        'reputation' => $seller->reputation,
    ]);
}




}
