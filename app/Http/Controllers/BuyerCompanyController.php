<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


use App\Models\Country;
use Illuminate\Support\Str;

use App\Services\ReputationService;
use Illuminate\Support\Facades\Log;

use App\Domain\Media\Services\MediaService;
use App\Domain\Media\DTO\UploadMediaDTO;
use App\Domain\Media\Jobs\DeleteMediaJob;

 use App\Domain\Company\Actions\UpdateBuyerOverviewAction;
 use App\Domain\Company\Actions\UpdateGeneralAction;
 use App\Domain\Company\Actions\UpdateManufacturingAction;
 use App\Domain\Company\Actions\UpdateContactsAction;
 use App\Domain\Company\Actions\UpdateAddressAction;

use App\Services\Company\ActiveContextService;

use App\Models\ExportMarket;

use App\Models\CompanyUser;
use App\Models\Buyer;
use App\Models\BusinessType;

class BuyerCompanyController extends Controller
{

public function __construct(
        private ActiveContextService $context,
        private MediaService $mediaService,
        private UpdateBuyerOverviewAction $updateBuyerOverviewAction,
        private UpdateGeneralAction $updateGeneralAction,
        private UpdateManufacturingAction $updateManufacturingAction,
        private UpdateContactsAction $updateContactsAction,
        private UpdateAddressAction $updateAddressAction
        

 
)
{

}
    /**
 * Show company profile (read-only page)
 */
public function showCompanyProfile()
{
    

$company = $this->context->buyerProfile();



abort_if(!$company, 404);

    $company->load([
        'businessTypes.translation',
        'country',
        'media',
        'profile',
        'contacts' => fn ($query) => $query->orderBy('sort_order'),
    ]);

    

    $catalogMedia = $company->catalogImageMedia()->first();

    $is_personal = $this->context->isPersonal();
    return view(
        'dashboard.buyer.company-profile.show',
        compact('company', 'catalogMedia', 'is_personal')
    );
}



    /**
     * Показ страницы профиля компании
     */
    public function companyProfile()
    {
        

    $company = $this->context->entity();

        if ($company) {
            $company->load([
                'exportMarkets.translation',
                'businessTypes.translation',
                'country',
                'media',
                'profile'
            ]);
        } else {
            $company = new \App\Models\Supplier();
        }

        $exportMarkets = \App\Models\ExportMarket::with('translations')->get();

        $manufacturingCapabilities = \App\Models\ManufacturingCapability::visible()
            ->ordered()
            ->with('translations')
            ->get();

        $supplierTypes = \App\Models\BusinessType::with('translations')->get();

        $countries = Country::withCurrentTranslation()
            ->orderBy('name')
            ->get();

        $selectedTypes = $company->businessTypes?->pluck('id')->toArray() ?? [];

        $selectedMarkets = $company->exportMarkets?->pluck('id')->toArray() ?? [];

        $profile = $company->profile;

$selectedmanufacturingCapabilities =
    $profile?->manufacturingCapabilities?->pluck('id')->toArray() ?? [];

        return view('dashboard.supplier.company-profile.company-profile', compact(
            'company',
            'countries',
            'exportMarkets',
            'businessTypes',
            'selectedTypes',
            'selectedMarkets',
            'manufacturingCapabilities',
            'selectedmanufacturingCapabilities'
        ));
    }

    /**
     * Обновление профиля компании
     */
    public function updateCompany(Request $request)
    {
       

    $company = $this->context->entity();
 
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
                originalFileName: $file?->getClientOriginalName(),
                sortOrder: 0,      
                isMain: true
            );

            $this->mediaService->upload($dto);
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

        //Saveing profile details
        $profileData = $request->only([
            'about_us_description',
            'founded_year',
            'total_employees',
            'manufacturing_description',
            'factory_area',
            'production_lines',
            'monthly_capacity',
            'moq',
            'lead_time_days',
            'annual_export_revenue',
            'registration_capital'
        ]);

        if (!empty(array_filter($profileData))) {

            $company->profile()->updateOrCreate(
                ['supplier_id' => $company->id],
                $profileData
            );
        }


        /** SUPPLIER TYPES */
        if ($request->filled('supplier_types_selected')) {

            $typeIds = collect(
                explode(',', $request->supplier_types_selected)
            )->filter()->map(fn($id) => (int)$id)->values();

            $company->supplierTypes()->sync($typeIds);
        }


        
        /** MANUFACTURING CAPABILITIES */
        if ($request->manufacturing_capabilities_selected) {

            $ids = explode(',', $request->manufacturing_capabilities_selected);
            $profile = $company->profile;

             $profile->manufacturingCapabilities()->sync($ids);

        } else {
            $profile = $company->profile;

             $profile->manufacturingCapabilities()->detach();
        }


        /** EXPORT MARKETS */
        if ($request->filled('export_markets_selected')) {

            $marketIds = collect(
                explode(',', $request->export_markets_selected)
            )->filter()->map(fn($id) => (int)$id)->values();

            $company->exportMarkets()->sync($marketIds);
        }

        return redirect()
            ->route('supplier.company.profile')
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

    $supplier = $this->context->supplier();

    if (!$supplier) {

        $identity = $this->context->identity();

        $supplier = \App\Models\Supplier::where(
            'supplierable_type',
            $identity['entity_type']
        )->where(
            'supplierable_id',
            $identity['entity_id']
        )->first();
    }

    if (!$supplier) {
        return response()->json([
            'message' => 'Supplier profile not found.'
        ], 404);
    }

    // Decode metadata JSON from frontend
    $metadata = [];

    if ($request->filled('metadata')) {
        $metadata = json_decode($request->input('metadata'), true) ?? [];
    }

    $dto = new UploadMediaDTO(
        file: $request->file('certificate'),
        model: $supplier,
        collection: 'supplier_certificates',
        mediaRole: 'certificate',
        private: false,
        originalFileName: $request->file('certificate')->getClientOriginalName(),
        metadata: $metadata,
        sortOrder: 0,
        isMain: true
    );

    $media = $this->mediaService->upload($dto);

    return response()->json([
        'success' => true,
        'id' => $media->id,
        'name' => $media->original_file_name,
        'status' => $media->processing_status,
        'url' => $media->cdn_url,
    ]);
}

    
public function deleteCertificate($id)
{
    try {

        $supplier = $this->context->supplier();

        if (!$supplier) {

            $identity = $this->context->identity();

            $supplier = \App\Models\Supplier::where(
                'supplierable_type',
                $identity['entity_type']
            )->where(
                'supplierable_id',
                $identity['entity_id']
            )->first();
        }

        abort_unless($supplier, 404);

        $media = $supplier->media()
            ->where('collection', 'supplier_certificates')
            ->where('id', $id)
            ->firstOrFail();

        // State transition
        $media->update([
            'processing_status' => 'deleting',
        ]);

        // Async delete pipeline
        DeleteMediaJob::dispatch($media->uuid);

        return response()->json([
            'success' => true,
        ]);

    } catch (\Throwable $e) {

        Log::error($e);

        return response()->json([
            'success' => false,
        ], 500);
    }
}

    
    public function uploadFactoryPhotos(Request $request)
{
    $supplier = $this->context->supplier();

    if (!$supplier) {

        $identity = $this->context->identity();

        $supplier = \App\Models\Supplier::where(
            'supplierable_type',
            $identity['entity_type']
        )->where(
            'supplierable_id',
            $identity['entity_id']
        )->first();
    }

    if (!$supplier) {
        return response()->json([
            'error' => 'Supplier not found'
        ], 404);
    }

    $request->validate([
        'photos.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
    ]);

    try {

        foreach ($request->file('photos') ?? [] as $file) {

            $dto = new UploadMediaDTO(
                file: $file,
                model: $supplier,
                collection: 'factory_photos',
                mediaRole: 'factory_photo',
                private: false,
                originalFileName: $file->getClientOriginalName(),
                sortOrder: 0,
                isMain: true
            );

            $this->mediaService->upload($dto);
        }

        return response()->json([
            'success' => true
        ]);

    } catch (\Throwable $e) {

        Log::error($e);

        return response()->json([
            'success' => false
        ], 500);
    }
}

public function deleteFactoryPhoto($id)
{
    $supplier = $this->context->supplier();

    if (!$supplier) {

        $identity = $this->context->identity();

        $supplier = \App\Models\Supplier::where(
            'supplierable_type',
            $identity['entity_type']
        )->where(
            'supplierable_id',
            $identity['entity_id']
        )->first();
    }

    abort_unless($supplier, 404);

    $media = $supplier->media()
        ->where('collection', 'factory_photos')
        ->where('id', $id)
        ->firstOrFail();

    DeleteMediaJob::dispatch($media->uuid);

    return response()->json([
        'success' => true,
    ]);
}

public function uploadCatalogImage(Request $request)
{
    $request->validate([
        'catalog_image' => 'required|image|max:10240',
    ]);

    $company = $this->context->supplier();

    if (!$company) {

        $identity = $this->context->identity();

        $company = \App\Models\Supplier::where(
            'supplierable_type',
            $identity['entity_type']
        )->where(
            'supplierable_id',
            $identity['entity_id']
        )->first();
    }

    if (!$company) {
        return response()->json([
            'message' => 'Supplier not found'
        ], 404);
    }

    // Удаляем старую картинку каталога
    $company->catalogImageMedia()->delete();

    $dto = new UploadMediaDTO(
        file: $request->file('catalog_image'),
        model: $company,
        collection: 'catalog_images',
        mediaRole: 'catalog_image',
        private: false,
        originalFileName: $request->file('catalog_image')->getClientOriginalName(),
        sortOrder: 0,
        isMain: true
    );

    $media = $this->mediaService->upload($dto);

    return response()->json([
        'success' => true,
        'url' => $media->cdn_url,
    ]);
}


public function updateLogo(Request $request)
{
    $company = $this->context->buyer();

    if (!$company) {

        $identity = $this->context->identity();

        $company = \App\Models\Buyer::where(
            'buyerable_type',
            $identity['entity_type']
        )
        ->where(
            'buyerable_id',
            $identity['entity_id']
        )
        ->first();
    }

    if (!$company) {
        return response()->json([
            'message' => 'Buyer not found'
        ], 404);
    }

    $request->validate([
        'logo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

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
        originalFileName: $file->getClientOriginalName(),
        sortOrder: 0,
        isMain: true
    );

    $media = $this->mediaService->upload($dto);

    return response()->json([
        'success' => true,
        'message' => 'Company logo updated successfully.',
        'url' => $media->cdn_url,
    ]);
}

public function drawer(string $section)
{
     $participant = $this->context->entity();


if ($participant instanceof \App\Models\User) {

    $company = Buyer::where(
        'buyerable_type',
        get_class($participant)
    )
    ->where(
        'buyerable_id',
        $participant->id
    )
    ->first();

} else {

    $company = $participant;
}


abort_unless($company,404);
Log::info('Buyer update: final company', [
        'id' => $company->id,
        'name' => $company->name,
        
        
    ]);

    $data = [
        'company' => $company,
        'is_personal' => $this->context->isPersonal(),
    ];

    if (in_array($section, ['overview', 'address'])) {
        $data['countries'] = Country::orderBy('name')->get();
    }


    if ($section === 'overview') {
               
        $targetType = $this->context->isPersonal()
    ? 'buyer_individual'
    : 'buyer_company';

$data['businessTypes'] = BusinessType::with('translation')
    ->where('target_type', $targetType)
    ->orderBy('slug')
    ->get();

    }

    
   

    if ($section === 'members' && $this->context->isCompany()) {
        $data['members'] = $company->members()
            ->with('user')
            ->get();
    }

    return view(
        "dashboard.buyer.company-profile.drawers.$section",
        $data
    );
}

public function update(Request $request, string $section)
{
    $company = $this->context->buyer();

    Log::info('Buyer update: context->buyer()', [
        'company_id' => $company?->id,
        'class' => $company ? get_class($company) : null,
    ]);

    if (!$company) {

        $identity = $this->context->identity();

        Log::info('Buyer update: identity()', $identity);

        $company = \App\Models\Buyer::where(
            'buyerable_type',
            $identity['entity_type']
        )->where(
            'buyerable_id',
            $identity['entity_id']
        )->first();

        Log::info('Buyer update: fallback buyer', [
            'company_id' => $company?->id,
            'class' => $company ? get_class($company) : null,
        ]);
    }

    abort_unless($company, 404);

    Log::info('Buyer update: final company', [
        'id' => $company->id,
        'name' => $company->name,
        
        
    ]);

    return match ($section) {

        'overview' => ($this->updateBuyerOverviewAction)(
            $request,
            $company
        ),

        'general' => ($this->updateGeneralAction)(
            $request,
            $company
        ),

        'manufacturing' => ($this->updateManufacturingAction)(
            $request,
            $company
        ),

        'contacts' => ($this->updateContactsAction)(
            $request,
            $company
        ),

        'address' => ($this->updateAddressAction)(
            $request,
            $company
        ),

        default => abort(404),
    };
}

}