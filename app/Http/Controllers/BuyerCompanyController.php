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
 use App\Domain\Company\Actions\UpdateBuyerGeneralAction;
 use App\Domain\Company\Actions\UpdateManufacturingAction;
 use App\Domain\Company\Actions\UpdateContactsAction;
 use App\Domain\Company\Actions\UpdateBuyerAddressAction;

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
        private UpdateBuyerGeneralAction $updateBuyerGeneralAction,
        private UpdateManufacturingAction $updateManufacturingAction,
        private UpdateContactsAction $updateContactsAction,
        private UpdateBuyerAddressAction $updateBuyerAddressAction
        

 
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

$this->authorize('view', $company);


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
     * Upload certificate
     */
    public function uploadCertificate(Request $request)
{

$buyer = $this->context->buyer();

    abort_unless($buyer, 404);

    $this->authorize('update', $buyer);


    $request->validate([
        'certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
    ]);

    // Decode metadata JSON from frontend
    $metadata = [];

    if ($request->filled('metadata')) {
        $metadata = json_decode($request->input('metadata'), true) ?? [];
    }

    $file = $request->file('certificate');

    $dto = new UploadMediaDTO(
        file: $file,
        model: $buyer,
        collection: 'buyer_certificates',
        mediaRole: 'certificate',
        private: false,
        originalFileName: $file->getClientOriginalName(),
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

  /**
     * Delete certificate
     */
    
public function deleteCertificate($id)
{
    try {

        $buyer = $this->context->buyer();

        abort_unless($buyer, 404);

        $this->authorize('update', $buyer);

        $media = $buyer->media()
            ->where('collection', 'buyer_certificates')
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
    $buyer = $this->context->buyer();

    abort_unless($buyer, 404);

    $this->authorize('update', $buyer);

    $request->validate([
        'photos.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
    ]);

    try {

        foreach ($request->file('photos') ?? [] as $file) {

            $dto = new UploadMediaDTO(
                file: $file,
                model: $buyer,
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
    $buyer = $this->context->buyer();

    abort_unless($buyer, 404);

    $this->authorize('update', $buyer);

    $media = $buyer->media()
        ->where('collection', 'factory_photos')
        ->where('id', $id)
        ->firstOrFail();

    DeleteMediaJob::dispatch($media->uuid);

    return response()->json([
        'success' => true,
    ]);
}



public function updateLogo(Request $request)
{
    $company = $this->context->buyer();

    abort_unless($company, 404);

    $this->authorize('update', $company);

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

    abort_unless($company, 404);

    $this->authorize('update', $company);

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
         $this->authorize('manageMembers', $company);
         
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

    
    if (!$company) {

        $identity = $this->context->identity();

      

        $company = \App\Models\Buyer::where(
            'buyerable_type',
            $identity['entity_type']
        )->where(
            'buyerable_id',
            $identity['entity_id']
        )->first();

        
    }

    abort_unless($company, 404);

   $this->authorize('update', $company);

    return match ($section) {

        'overview' => ($this->updateBuyerOverviewAction)(
            $request,
            $company
        ),

        'general' => ($this->updateBuyerGeneralAction)(
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

        'address' => ($this->updateBuyerAddressAction)(
            $request,
            $company
        ),

        default => abort(404),
    };
}

}