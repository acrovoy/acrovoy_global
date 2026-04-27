<?php


namespace App\Http\Controllers\BUyer;

use App\Http\Controllers\Controller;
use App\Domain\RFQ\Models\Rfq;
use App\Domain\Negotiation\Models\NegotiationEvent;

class RfqAuditController extends Controller
{
    public function index(Rfq $rfq)
    {
        $events = NegotiationEvent::query()
            ->where('rfq_id', $rfq->id)
            ->latest()
            ->get();

        return view('rfq.audit.index', compact('rfq', 'events'));
    }
}