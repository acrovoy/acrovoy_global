<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


use App\Models\Country;
use Illuminate\Support\Str;

use App\Services\ReputationService;

use App\Domain\Media\Services\MediaService;
use App\Domain\Media\DTO\UploadMediaDTO;
use App\Domain\Media\Jobs\DeleteMediaJob;

class ManufacturerController extends Controller
{


    /**
 * Show company profile (read-only page)
 */
public function showCompanyProfile()
{
    $user = auth()->user();

    
    $company = optional($user)->supplier;

    if (!$company) {
        return redirect()
            ->route('manufacturer.company.profile')
            ->withErrors('Company profile not found.');
    }

    $company->load([
        'exportMarkets.translation',
        'supplierTypes.translation',
        'country',
        'certificates',
        'media'
    ]);

    $testPhotos = $company->media()
        ->where('collection', 'test_photos')
        ->get();

    return view(
        'dashboard.manufacturer.profile.show',
        compact('company', 'testPhotos')
    );
}



    /**
     * Показ страницы профиля компании
     */
    public function companyProfile()
    {
        $user = auth()->user();

        $company = optional($user)->supplier;

        if ($company) {
            $company->load([
                'exportMarkets.translation',
                'supplierTypes.translation',
                'country',
                'certificates',
                'media'
            ]);
        } else {
            $company = new \App\Models\Supplier();
        }

        $exportMarkets = \App\Models\ExportMarket::with('translations')->get();

        $supplierTypes = \App\Models\SupplierType::with('translations')->get();

        $countries = Country::withCurrentTranslation()
            ->orderBy('name')
            ->get();

        $selectedTypes = $company->supplierTypes?->pluck('id')->toArray() ?? [];

        $selectedMarkets = $company->exportMarkets?->pluck('id')->toArray() ?? [];

        return view('dashboard.manufacturer.company-profile', compact(
            'company',
            'countries',
            'exportMarkets',
            'supplierTypes',
            'selectedTypes',
            'selectedMarkets'
        ));
    }

    /**
     * Обновление профиля компании
     */
    public function updateCompany(Request $request, MediaService $mediaService)
    {
        $company = auth()->user()->supplier;

        if (!$company) {
            return back()->withErrors('Supplier not found');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:50',
            'country_id' => 'nullable|integer',
            'address' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'catalog_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        /** LOGO */
        if ($request->hasFile('logo')) {

            unset($data['logo']); 

            $oldLogo = $company->media()
                ->where('collection', 'company_logos')
                ->first();

            if ($oldLogo) {
                DeleteMediaJob::dispatch($oldLogo->uuid);
            }

            $file = $request->file('logo');

            $dto = new UploadMediaDTO(
                file: $file,
                model: $company,
                collection: 'company_logos',
                mediaRole: 'company_logo',
                private: false,
                originalFileName: $file?->getClientOriginalName()
            );

            $mediaService->upload($dto);
        }

        /** CATALOG IMAGE */
        if ($request->hasFile('catalog_image')) {

            if ($company->catalog_image) {
                Storage::disk('public')->delete($company->catalog_image);
            }

            $data['catalog_image'] = $request->file('catalog_image')
                ->store('company-catalog', 'public');
        }

        /** SLUG GENERATION */
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

        $company->update($data);

        /** SUPPLIER TYPES */
        if ($request->filled('supplier_types_selected')) {

            $typeIds = collect(
                explode(',', $request->supplier_types_selected)
            )->filter()->map(fn($id) => (int)$id)->values();

            $company->supplierTypes()->sync($typeIds);
        }

        /** EXPORT MARKETS */
        if ($request->filled('export_markets_selected')) {

            $marketIds = collect(
                explode(',', $request->export_markets_selected)
            )->filter()->map(fn($id) => (int)$id)->values();

            $company->exportMarkets()->sync($marketIds);
        }

        return redirect()
            ->route('manufacturer.company.profile')
            ->with('success', 'Company profile updated successfully.');
    }

    /**
     * Delete certificate
     */
    

    /**
     * Upload certificate
     */
    public function uploadCertificate(Request $request)
{
    $request->validate([
        'certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
    ]);

    $company = auth()->user()?->supplier;

    if (!$company) {
        return response()->json([
            'message' => 'Company profile not found.'
        ], 404);
    }

    // ✅ Decode metadata JSON from frontend
    $metadata = [];

    if ($request->filled('metadata')) {
        $metadata = json_decode($request->input('metadata'), true) ?? [];
    }

    $dto = new \App\Domain\Media\DTO\UploadMediaDTO(
        file: $request->file('certificate'),
        model: $company,
        collection: 'supplier_certificates',
        mediaRole: 'certificate',
        private: false,
        originalFileName: $request->file('certificate')->getClientOriginalName(),
        metadata: $metadata
    );

    $media = app(\App\Domain\Media\Services\MediaService::class)->upload($dto);

    return response()->json([
        'success' => true,
        'id' => $media->id,
        'name' => $media->original_file_name,
        'status' => $media->processing_status,
        'url' => $media->cdn_url
    ]);
}

    
public function deleteCertificate($id)
{
    try {

        $supplier = auth()->user()->supplier;

        $media = $supplier->media()
            ->where('collection', 'supplier_certificates')
            ->where('id', $id)
            ->firstOrFail();

        // State transition
        $media->update([
            'processing_status' => 'deleting'
        ]);

        // Async delete pipeline
        DeleteMediaJob::dispatch($media->uuid);

        return response()->json([
            'success' => true
        ]);

    } catch (\Throwable $e) {

        \Log::error($e);

        return response()->json([
            'success' => true
        ]);
    }
}

    
    public function uploadFactoryPhotos(Request $request, MediaService $mediaService)
{
    $supplier = auth()->user()->supplier;

    if (!$supplier) {
        return response()->json(['error' => 'Supplier not found'], 404);
    }

    $request->validate([
        'photos.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096'
    ]);

    try {

        foreach ($request->file('photos') ?? [] as $file) {

            $dto = new UploadMediaDTO(
                file: $file,
                model: $supplier,
                collection: 'factory_photos',
                mediaRole: 'factory_photo',
                private: false,
                originalFileName: $file->getClientOriginalName()
            );

            $mediaService->upload($dto);
        }

        return response()->json(['success' => true]);

    } catch (\Throwable $e) {

        \Log::error($e);

        return response()->json([
            'success' => false
        ], 500);
    }
}


public function deleteFactoryPhoto($id)
{
    $supplier = auth()->user()->supplier;

    $media = $supplier->media()
        ->where('collection', 'factory_photos')
        ->where('id', $id)
        ->firstOrFail();

    DeleteMediaJob::dispatch($media->uuid);

    return response()->json([
        'success' => true
    ]);
}

}