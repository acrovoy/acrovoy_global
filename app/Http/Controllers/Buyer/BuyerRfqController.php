<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Domain\RFQ\Models\Rfq;

use App\Domain\RFQ\DTO\CreateRfqData;
use App\Domain\RFQ\DTO\UpdateRfqData;

use App\Domain\RFQ\Actions\Buyer\CreateRfqAction;
use App\Domain\RFQ\Actions\Buyer\UpdateRfqAction;
use App\Domain\RFQ\Actions\Buyer\ListBuyerRfqsAction;

use App\Http\Requests\Rfq\CreateRfqRequest;
use App\Http\Requests\Rfq\UpdateRfqRequest;

use Illuminate\Support\Facades\Auth;

use App\Services\Company\ActiveContextService;

class BuyerRfqController extends Controller
{
    public function __construct(
    private CreateRfqAction $createRfqAction,
    private UpdateRfqAction $updateRfqAction,
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
   

    $rfqs = $this->listBuyerRfqsAction->execute($context);
     
    return view('rfq.buyer.index', compact('rfqs'));
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

    $buyerType = $context->isPersonal() ? 'App\Models\User' : 'company';

    $buyer = $context->isPersonal()
        ? auth()->user()
        : $context->company();

    abort_if(!$buyer, 403);

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
        $buyer,
        $buyerType,
        auth()->id()
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
}