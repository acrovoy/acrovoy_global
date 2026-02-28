<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


use App\Models\Country;
use Illuminate\Support\Str;
use App\Models\SupplierCertificate;
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

            $dto = new UploadMediaDTO(
                file: $request->file('logo'),
                model: $company,
                collection: 'company_logos',
                private: false
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
    public function deleteCertificate(
        SupplierCertificate $certificate,
        ReputationService $reputationService
    ) {
        try {

            $supplier = auth()->user()->supplier;

            if (!$supplier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Supplier not found'
                ], 404);
            }

            if ($certificate->supplier_id !== $supplier->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden'
                ], 403);
            }

            if (Storage::disk('public')->exists($certificate->file_path)) {
                Storage::disk('public')->delete($certificate->file_path);
            }

            $certificate->delete();

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

    /**
     * Upload certificate
     */
    public function uploadCertificate(
        Request $request,
        ReputationService $reputationService
    ) {

        $supplier = auth()->user()->supplier;

        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier not found'
            ], 404);
        }

        $request->validate([
            'certificate' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:4096',
        ]);

        $file = $request->file('certificate');

        $path = $file->store('supplier-certificates', 'public');

        $certificate = $supplier->certificates()->create([
            'file_path' => $path,
            'name' => $file->getClientOriginalName(),
        ]);

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

    
    
    public function uploadFactoryPhotos(Request $request)
{
    $supplier = auth()->user()->supplier;

    if (!$supplier) {
        return response()->json(['error' => 'Supplier not found'], 404);
    }

    $request->validate([
        'photos.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096'
    ]);

    try {

        $manager = new ImageManager(new Driver());

        foreach ($request->file('photos') ?? [] as $file) {

            $uuid = Str::uuid()->toString();

            /** Original */
            $originalPath = "factory-photos/original/{$uuid}.jpg";

            Storage::disk('public')->put(
                $originalPath,
                file_get_contents($file->getRealPath())
            );

            /** Thumbnail */
            $thumbnailPath = "factory-photos/thumbnail/{$uuid}.jpg";

            $thumbnail = $manager
                ->read($file->getRealPath())
                ->cover(400, 400);

            Storage::disk('public')->put(
                $thumbnailPath,
                (string) $thumbnail->toJpeg(85)
            );

            $supplier->factoryPhotos()->create([
                'path' => $originalPath,
                'thumbnail_path' => $thumbnailPath,
            ]);
        }

        return response()->json([
            'success' => true
        ]);

    } catch (\Throwable $e) {

        \Log::error($e);

        return response()->json([
            'success' => false,
            'message' => 'Upload failed'
        ], 500);
    }
}


public function deleteFactoryPhoto($id)
{
    $supplier = auth()->user()->supplier;

    $photo = $supplier->factoryPhotos()->findOrFail($id);

    try {

        $disk = \Storage::disk('public');

        // Delete original
        if ($photo->path && $disk->exists($photo->path)) {
            $disk->delete($photo->path);
        }

        // Delete thumbnail
        if ($photo->thumbnail_path && $disk->exists($photo->thumbnail_path)) {
            $disk->delete($photo->thumbnail_path);
        }

        $photo->delete();

        return response()->json([
            'success' => true
        ]);

    } catch (\Throwable $e) {

        \Log::error($e);

        return response()->json([
            'success' => false
        ], 500);
    }
}

}