<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Domain\RFQ\Models\Rfq;

use App\Domain\RFQ\Enums\RfqStatus;

use Illuminate\Http\Request;

use App\Domain\RFQ\DTO\CreateRfqData;
use App\Domain\RFQ\DTO\UpdateRfqData;

use App\Domain\RFQ\Actions\Buyer\CreateRfqAction;
use App\Domain\RFQ\Actions\Buyer\CreateCustomizationRfqAction;
use App\Domain\RFQ\Actions\Buyer\UpdateRfqAction;
use App\Domain\RFQ\Actions\Buyer\ListBuyerRfqsAction;
use App\Domain\RFQ\Actions\CopyProductAttributesToRfqAction;

use App\Http\Requests\Rfq\CreateRfqRequest;
use App\Http\Requests\Rfq\UpdateRfqRequest;

use Illuminate\Support\Facades\Auth;

use App\Services\Company\ActiveContextService;

use App\Models\Product;
use App\Models\Buyer;
use App\Domain\Project\Models\Project;

class BuyerRfqController extends Controller
{
    public function __construct(
        private ActiveContextService $context,
        private CreateRfqAction $createRfqAction,
        private UpdateRfqAction $updateRfqAction,
        private CreateCustomizationRfqAction $createCustomizationRfqAction,
        private ListBuyerRfqsAction $listBuyerRfqsAction,
        private CopyProductAttributesToRfqAction $copyProductAttributesToRfqAction,
    ) {}

    /**
     * RFQ LIST
     */
    public function index()
{
    $buyer = $this->context->buyerProfile();

    abort_unless($buyer, 403);
    
    $result = $this->listBuyerRfqsAction->execute($buyer);

    return view('rfq.buyer.index', [
        'rfqs' => $result['active'],
        'closedRfqs' => $result['closed'],
    ]);
}

    /**
     * CREATE PAGE
     */
    public function create()
    {
        return view('rfq.buyer.create');
    }

    /**
     * STORE RFQ
     */
    public function store(CreateRfqRequest $request)
{
    $buyer = $this->context->buyerProfile();

    abort_unless($buyer, 403);

    $dto = CreateRfqData::fromArray(
        $request->validated()
    );

    $rfq = $this->createRfqAction->execute(
        $dto,
        $buyer->getKey(),
        $buyer::class,
        auth()->id()
    );

    return redirect()
        ->route('rfqs.workspace', $rfq)
        ->with('success', 'RFQ created successfully');
}



    public function storeCustomization(CreateRfqRequest $request)
{
    $product = Product::findOrFail($request->product_id);

    /**
     * RESOLVE BUYER OWNER
     */
    $buyer = $this->context->buyerProfile();

    abort_unless($buyer, 403);

    $buyerType = Buyer::class;
    $buyerId   = $buyer->id;

    

    /**
     * DTO
     */
    $dto = CreateRfqData::fromArray(
        $request->validated()
    );

    /**
     * CREATE RFQ
     */
    $rfq = $this->createCustomizationRfqAction->execute(
        $dto,
        $buyerId,
        $buyerType,
        auth()->id(),
        $product->Supplier::class,
        $product->supplier_id,
        $product->id,
        $request->project_id,
    );

    /**
     * COPY PRODUCT ATTRIBUTES
     */
    $this->copyProductAttributesToRfqAction->execute(
        $product,
        $rfq
    );

    if ($request->project_id) {
        return redirect()
            ->route('buyer.projects.show', $request->project_id)
            ->with('success', 'Product added to project successfully');
    }

    return redirect()
        ->route('rfqs.workspace', $rfq)
        ->with('success', 'RFQ created successfully');
}


    /**
     * EDIT PAGE
     */

    public function edit(Rfq $rfq)
{
    $this->authorizeAccess($rfq);

    return view('rfq.buyer.edit', compact('rfq'));
}

    

    /**
     * UPDATE RFQ
     */
    public function update(
    UpdateRfqRequest $request,
    Rfq $rfq
) {
    $this->authorizeAccess($rfq);

    if ($rfq->status->isPublished()) {
        return back()->with(
            'error',
            'Published RFQ cannot be edited.'
        );
    }

    $dto = UpdateRfqData::fromArray(
        $request->validated()
    );

    $this->updateRfqAction->execute(
        $rfq,
        $dto
    );

    return redirect()
        ->route('buyer.rfqs.workspace', $rfq)
        ->with('success', 'RFQ updated successfully');
}

    /**
     * ACCESS CONTROL
     */
    private function authorizeAccess(Rfq $rfq): void
{
    $buyer = $this->context->buyerProfile();

    abort_unless($buyer, 403);

    abort_if(
        $rfq->buyer_type !== $buyer::class ||
        $rfq->buyer_id !== $buyer->getKey(),
        403
    );
}

    public function updateField(Request $request, Rfq $rfq)
    {
        $field = $request->input('field');

        match ($field) {
            'title' => $rfq->update([
                'title' => $request->title
            ]),

            'description' => $rfq->update([
                'description' => $request->description
            ]),

            'deadline' => $rfq->update([
                'closed_at' => $request->closed_at
            ]),

            default => null,
        };

        return back();
    }
}
