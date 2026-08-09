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

 use App\Domain\Company\Actions\UpdateOverviewAction;
 use App\Domain\Company\Actions\UpdateGeneralAction;
 use App\Domain\Company\Actions\UpdateManufacturingAction;
 use App\Domain\Company\Actions\UpdateContactsAction;
 use App\Domain\Company\Actions\UpdateAddressAction;

use App\Services\Company\ActiveContextService;

use App\Models\ExportMarket;
use App\Models\ManufacturingCapability;
use App\Models\CompanyUser;
use App\Models\Supplier;
use App\Models\BusinessType;

class SupplierCompanyController extends Controller
{

public function __construct(
        private ActiveContextService $context,
        private MediaService $mediaService,
        private UpdateOverviewAction $updateOverviewAction,
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
    
$company = $this->context->supplierProfile();

    abort_unless($company, 404);

    $this->authorize('view', $company);

    $company->load([
        'exportMarkets.translation',
        'businessTypes.translation',
        'country',
        'media',
        'profile',
        'contacts' => fn ($query) => $query->orderBy('sort_order'),
    ]);

    $catalogMedia = $company->catalogImageMedia()->first();

    $is_personal = $this->context->isPersonal();
    return view(
        'dashboard.supplier.company-profile.show',
        compact('company', 'catalogMedia', 'is_personal')
    );
}


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

    $this->authorize('uploadCertificate', $supplier);

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

        $this->authorize('deleteCertificate', $supplier);

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

     $this->authorize('uploadFactoryPhotos', $supplier);

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

    $this->authorize('deleteFactoryPhoto', $supplier);

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

    $this->authorize('uploadCatalogImage', $company);

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
    $company = $this->context->supplier();

    if (!$company) {

        $identity = $this->context->identity();

        $company = \App\Models\Supplier::where(
            'supplierable_type',
            $identity['entity_type']
        )
        ->where(
            'supplierable_id',
            $identity['entity_id']
        )
        ->first();
    }

    if (!$company) {
        return response()->json([
            'message' => 'Supplier not found'
        ], 404);
    }

    $this->authorize('updateLogo', $company);

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

    $company = Supplier::where(
        'supplierable_type',
        get_class($participant)
    )
    ->where(
        'supplierable_id',
        $participant->id
    )
    ->first();

} else {

    $company = $participant;
}


abort_unless($company,404);

$this->authorize('drawer', $company);



    $data = [
        'company' => $company,
        'is_personal' => $this->context->isPersonal(),
    ];

    if (in_array($section, ['overview', 'address'])) {
        $data['countries'] = Country::orderBy('name')->get();
    }


    if ($section === 'overview') {
               
        $targetType = $this->context->isPersonal()
    ? 'supplier_individual'
    : 'supplier_company';

$data['businessTypes'] = BusinessType::with('translation')
    ->where('target_type', $targetType)
    ->orderBy('slug')
    ->get();

    }

    if ($section === 'general') {
        $data['exportMarkets'] = ExportMarket::with('translations')->get();
    }

    if ($section === 'manufacturing') {
        $data['manufacturingCapabilities'] = ManufacturingCapability::all();
    }

    if ($section === 'members' && $this->context->isCompany()) {
        $this->authorize('manageMembers', $company);

        $data['members'] = $company->members()
            ->with('user')
            ->get();
    }

    return view(
        "dashboard.supplier.company-profile.drawers.$section",
        $data
    );
}

public function update(Request $request, string $section)
{
    $company = $this->context->supplier();

    if (!$company) {

        $identity = $this->context->identity();

        $company = \App\Models\Supplier::where(
            'supplierable_type',
            $identity['entity_type']
        )
        ->where(
            'supplierable_id',
            $identity['entity_id']
        )
        ->first();
    }

    abort_unless($company, 404);

    $this->authorize('updateProfile', $company);
    
    return match ($section) {

        'overview' => ($this->updateOverviewAction)(
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