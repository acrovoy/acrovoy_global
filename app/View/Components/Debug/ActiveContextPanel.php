<?php

namespace App\View\Components\Debug;

use Illuminate\View\Component;
use App\Services\Company\ActiveContextService;
use Throwable;

class ActiveContextPanel extends Component
{
    public $context;
    public $user;
    public $product;

    // RFQ / Negotiation optional (future-safe)
    public $rfq = null;
    public $offer = null;
    public $offerVersion = null;

    public $lastAction = null;
    public $lastActionResult = null;

    public function __construct(
        $product = null,
        $rfq = null,
        $offer = null,
        $offerVersion = null
    ) {
        try {
            $this->context = app(ActiveContextService::class);
        } catch (Throwable $e) {
            $this->context = null;
        }

        try {
            $this->user = auth()->user();
        } catch (Throwable $e) {
            $this->user = null;
        }

        $this->product = $product;
        $this->rfq = $rfq;
        $this->offer = $offer;
        $this->offerVersion = $offerVersion;

        // safe defaults
        $this->lastAction = null;
        $this->lastActionResult = null;
    }

    public function render()
    {
        return view('components.debug.active-context-panel');
    }
}