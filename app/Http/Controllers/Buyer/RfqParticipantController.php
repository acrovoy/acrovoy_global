<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\RFQ\Models\Rfq;
use App\Domain\RFQ\Models\RfqParticipant;
use App\Domain\RFQ\Services\RfqParticipantService;
use App\Models\Supplier;

class RfqParticipantController extends Controller
{
    /**
     * PARTICIPANTS PAGE
     */
    public function index(Rfq $rfq)
    {
        return view('rfq.participants.index', compact('rfq'));
    }

    /**
     * INVITE PARTICIPANTS (SINGLE ENTRY POINT)
     */
    public function store(
    Request $request,
    Rfq $rfq,
    RfqParticipantService $service
) {
    $this->authorize('update', $rfq);

    if ($request->filled('participant_type') && $request->filled('participant_id')) {

        $participant = $request->participant_type::findOrFail($request->participant_id);

        $service->invite($rfq, $participant);
    }

    if ($request->filled('category_id')) {

        $service->inviteCategory($rfq, (int) $request->category_id);
    }

    if ($request->filled('email')) {

        $service->inviteByEmail($rfq, $request->email);
    }

    return back()->with('success', 'Invitations sent');
}


    /**
     * REMOVE PARTICIPANT
     */
    public function remove(
        Rfq $rfq,
        RfqParticipant $participant,
        RfqParticipantService $service
    ) {
        $this->authorize('update', $rfq);

        abort_unless($participant->rfq_id === $rfq->id, 403);

        $service->remove($participant);

        return back()->with('success', 'Participant removed');
    }
}