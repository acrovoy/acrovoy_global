<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use App\Models\SupplierCertificate;
use App\Services\ReputationService;

class ManufacturerController extends Controller
{
    /**
     * Показ страницы профиля компании
     */
    public function companyProfile()    
{

    $countries = Country::orderBy('name')->get();
    $company = auth()->user()->supplier; // или company(), если связь переименуешь

    if (!$company) {
        // Создаём пустую модель для формы, чтобы Blade работал без ошибок
        $company = new \App\Models\Supplier();
    }

    return view('dashboard.manufacturer.company-profile', compact('company','company', 'countries'));
}

    /**
     * Обновление профиля компании
     */
    public function updateCompany(Request $request)
{


    $company = auth()->user()->supplier; 

  

    // Валидация только полей из твоей формы
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'short_description' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'email' => 'required|email',
        'phone' => 'nullable|string|max:50',
        'country_id' => 'nullable|integer',
        'address' => 'nullable|string|max:500',
        'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // <=2MB
        'catalog_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'certificates.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,webp|max:4096'
    ]);

    // Обработка логотипа
    if ($request->hasFile('logo')) {
        $data['logo'] = $request->file('logo')->store('company-logos', 'public');
    }

    // Обработка сертификатов
    if ($request->hasFile('certificates')) {
        foreach ($request->file('certificates') as $file) {
            $path = $file->store('supplier-certificates', 'public');
            $company->certificates()->create([
                'file_path' => $path,
                'name' => $file->getClientOriginalName(),
            ]);
        }
    }

     // === CATALOG IMAGE ===
    if ($request->hasFile('catalog_image')) {

        if ($company->catalog_image) {
            Storage::disk('public')->delete($company->catalog_image);
        }

        $data['catalog_image'] = $request->file('catalog_image')
            ->store('company-catalog', 'public');
    }


    // Генерация уникального slug
    $slug = Str::slug($data['name'], '-');
    $originalSlug = $slug;
    $counter = 1;

    while (\App\Models\Supplier::where('slug', $slug)
            ->where('id', '!=', $company->id)
            ->exists()) {
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }
    $data['slug'] = $slug;

    // Обновление данных компании
    $company->update($data);

    // Редирект обратно с сообщением
    return redirect()->route('manufacturer.company.profile')
                     ->with('success', 'Company profile updated successfully.');
}

public function deleteCertificate(SupplierCertificate $certificate, ReputationService $reputationService)
{
    try {
        $supplier = auth()->user()->supplier;

        if (!$supplier) {
            return response()->json(['success' => false, 'message' => 'Supplier not found'], 404);
        }

        if ($certificate->supplier_id !== $supplier->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        // Удаляем файл, если существует
        if (Storage::disk('public')->exists($certificate->file_path)) {
            Storage::disk('public')->delete($certificate->file_path);
        }

        // Удаляем запись из базы
        $certificate->delete();

        // Пересчёт репутации
        $newScore = $reputationService->recalculate($supplier);

        return response()->json([
            'success' => true,
            'reputation' => $newScore,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}



public function uploadCertificate(Request $request, ReputationService $reputationService)
{
    $supplier = auth()->user()->supplier;

    $request->validate([
        'certificate' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:4096',
    ]);

    $file = $request->file('certificate');

    $path = $file->store('supplier-certificates', 'public');

    $certificate = $supplier->certificates()->create([
        'file_path' => $path,
        'name' => $file->getClientOriginalName(),
    ]);

    // пересчёт репутации
    $newScore = $reputationService->recalculate($supplier);

    return response()->json([
        'success' => true,
        'certificate' => [
            'id' => $certificate->id,
            'name' => $certificate->name,
            'url' => asset('storage/' . $certificate->file_path)
        ],
        'reputation' => $newScore,
    ]);
}





}
