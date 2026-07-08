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

use App\Http\Requests\Rfq\CreateRfqRequest;
use App\Http\Requests\Rfq\UpdateRfqRequest;

use Illuminate\Support\Facades\Auth;

use App\Services\Company\ActiveContextService;

use App\Models\Product;

class BuyerRfqController extends Controller
{
    public function __construct(
    private CreateRfqAction $createRfqAction,
    private UpdateRfqAction $updateRfqAction,
    private CreateCustomizationRfqAction $createCustomizationRfqAction,
    private ListBuyerRfqsAction $listBuyerRfqsAction,
) {}

    /**
     * RFQ LIST
     */
    public function index(ActiveContextService $context)
{
    /**
     * CONTEXT OWNER
     */

    

    if ($context->isPersonal()) {

        $buyerType = auth()->user()::class;
        $buyerId   = auth()->id();

    } else {

        $buyerType = $context->type();
        $buyerId   = $context->id();

    }

    /**
     * LOAD RFQs
     */
   

    $result = $this->listBuyerRfqsAction->execute($context);

        
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
    public function store(
    CreateRfqRequest $request,
    ActiveContextService $context
)
{


    /**
     * RESOLVE BUYER OWNER FROM CONTEXT
     */

    $buyerType = $context->type();
    $buyerId = $context->id();

    

    /**
     * DTO
     */

    $dto = CreateRfqData::fromArray(
        $request->validated()
    );

    /**
     * CREATE RFQ
     */

    $rfq = $this->createRfqAction->execute(
        $dto,
        $buyerId,
        $buyerType,
        auth()->id()
    );

    return redirect()
        ->route('rfqs.workspace', $rfq)
        ->with('success', 'RFQ created successfully');
}

public function storeCustomization(
    CreateRfqRequest $request,
    ActiveContextService $context
)
{

    $product = Product::findOrFail($request->product_id);

    /**
     * RESOLVE BUYER OWNER FROM CONTEXT
     */

    $buyerType = $context->type();
    $buyerId = $context->id();

    
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
        $product->supplier_type,
        $product->supplier_id,
        $product->id,
    );

    return redirect()
        ->route('rfqs.workspace', $rfq)
        ->with('success', 'RFQ created successfully');
}


    /**
     * EDIT PAGE
     */
    public function edit(
    Rfq $rfq,
    ActiveContextService $context
)
{
    $this->authorizeAccess($rfq, $context);

    return view('rfq.buyer.edit', compact('rfq'));
}

    /**
     * UPDATE RFQ
     */
    public function update(
    UpdateRfqRequest $request,
    Rfq $rfq,
    ActiveContextService $context
)
{
    $this->authorizeAccess($rfq, $context);


    if ($rfq->status->isPublished()) {

    return back()->with('error', 'Published RFQ cannot be edited.');
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
    private function authorizeAccess(
    Rfq $rfq,
    ActiveContextService $context
): void
{
    /**
     * PERSONAL MODE
     */

    if ($context->isPersonal()) {

        abort_if(
            $rfq->buyer_type !== auth()->user()::class
            || $rfq->buyer_id !== auth()->id(),
            403
        );

        return;
    }

    /**
     * COMPANY MODE
     */

    abort_if(
        $rfq->buyer_type !== $context->type()
        || $rfq->buyer_id !== $context->id(),
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