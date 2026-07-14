<?php

namespace App\Http\Controllers\Project\Supplier;

use App\Http\Controllers\Controller;

use App\Services\Company\ActiveContextService;
use App\Domain\Project\Models\Project;
use App\Domain\RFQ\Models\Rfq;
use App\Domain\Negotiation\Actions\CreateRfqOfferAction;
use App\Domain\Negotiation\Resolvers\OfferVersionResolver;

use App\Domain\Project\Actions\Supplier\ListSupplierProjectsAction;

class SupplierProjectController extends Controller
{

public function __construct(
        private ActiveContextService $context,
        private ListSupplierProjectsAction $listSupplierProjectsAction,
        private OfferVersionResolver $offerVersionResolver,
        
    ) {}

    public function index()
{
    $result = $this->listSupplierProjectsAction
        ->execute($this->context);

    return view('project.supplier.index', [
        'projects'       => $result['active'],
        'closedProjects' => $result['closed'],
    ]);
}

public function show(Project $project)
    {

        $project->load([
            'rfqs.product',
            'rfqs.attributeValues',
        ]);

        return view('project.buyer.show', compact('project'));
    }

    public function requirements(Rfq $rfq)
    {

    $buyerSnapshotMap = $rfq->attributeValues
            ->keyBy('attribute_id');

    $project = $rfq->load('project')->project;

    $rfq->loadMissing([
                'attributeValues.attribute.options',
                'attributeValues.options',
                'hiddenAttributes',
            ]);

            $hiddenIds = $rfq->hiddenAttributes->pluck('id')->toArray();

            $rfq->setRelation(
                'attributeValues',
                $rfq->attributeValues->reject(function ($value) use ($hiddenIds) {
                    return in_array($value->attribute_id, $hiddenIds);
                })
            );


            $supplier = $this->context->supplierParticipant();

            if (!$supplier) {
                abort(403);
            }

            /** @var CreateRfqOfferAction $action */
            $action = app(CreateRfqOfferAction::class);

            $offer = $action->execute(
                rfq: $rfq,
                supplier: $supplier,
                context: $this->context
            );


            $offerVersion = $this->offerVersionResolver->resolve(
                $offer,
                request('version')
            );


            $itemsByAttribute = $offerVersion?->items
                ?->whereNotNull('attribute_id')
                ?->keyBy('attribute_id') ?? collect();


            $supplierOfferVersionToCounter = null;

            if ($offerVersion->is_counter == 1) {
                $versionNumberOfCounter = $offerVersion->version_number;
                $supplierOfferVersionToCounter = $offer->versions()
                    ->where('status', '!=', 'draft')
                    ->where('is_counter', 0)
                    ->where('version_number', $versionNumberOfCounter - 1)
                    ->first();
            }

            $currentDraft = $this->offerVersionResolver->currentDraft($offer);

            $canCreateRevision = $this->offerVersionResolver->canCreateRevision($offer, $offerVersion);


            $versions = $offer->versions()
                ->orderByDesc('created_at')
                ->with(['items.options.translations'])
                ->get();




            $isReadonly = $offerVersion
                ? $offerVersion->status !== 'draft'
                : true;


            $isCounter = $offerVersion?->is_counter ?? false;



            return view('project.supplier.requirements', compact('rfq', 'currentDraft', 'canCreateRevision', 'versions','isReadonly', 'isCounter',
            'project', 'offerVersion', 'itemsByAttribute', 'supplierOfferVersionToCounter', 'buyerSnapshotMap'));

    }


}