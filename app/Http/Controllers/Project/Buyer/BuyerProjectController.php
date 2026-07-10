<?php

namespace App\Http\Controllers\Project\Buyer;

use App\Http\Controllers\Controller;

use App\Domain\Project\Models\Project;
use App\Models\Product;

use App\Http\Requests\Project\CreateProjectRequest;

use App\Services\Company\ActiveContextService;
use App\Domain\Project\Actions\Buyer\ListBuyerProjectAction;
use App\Domain\Project\Actions\Buyer\CreateProjectAction;
use App\Domain\Project\DTO\CreateProjectData;

class BuyerProjectController extends Controller
{
    public function __construct(
        private ActiveContextService $context,
        private ListBuyerProjectAction $listBuyerProjectAction,
        private CreateProjectAction $createProjectAction,
        
    ) {}
    /**
     * Список проектов покупателя
     */
    public function index()
{


$result = $this->listBuyerProjectAction->execute($this->context);

    

    return view('project.buyer.index', [
        'projects' => $result['active'],
        'closedProjects' => $result['closed'],
    ]);
}

/**
     * CREATE PAGE
     */
    public function create()
    {
        return view('project.buyer.create');
    }

public function store(
    CreateProjectRequest $request,
    ActiveContextService $context
)
{
    $buyerType = $context->type();
    $buyerId = $context->id();

    
    /**
     * DTO
     */

    $dto = CreateProjectData::fromArray(
        $request->validated()
    );

    /**
     * CREATE PROJECT
     */

    $rfq = $this->createProjectAction->execute(
        $dto,
        $buyerId,
        $buyerType,
        auth()->id()
    );

    return redirect()
        ->route('buyer.projects.index')
        ->with('success', 'Project created successfully');
}





   public function show(Project $project)
{
    return view('project.buyer.show', compact('project'));
}
   

}