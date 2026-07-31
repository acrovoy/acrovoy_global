<?php

namespace App\Http\Controllers;

use App\Domain\Contact\Models\Contact;
use App\Domain\Contact\Services\ContactService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ContactService $service
    ) {
    }

    /**
     * Store a newly created contact.
     */
    public function store(Request $request): JsonResponse
{
    $data = $request->validate([
        'contactable_type' => ['required'],
        'contactable_id'   => ['required'],
        'type'             => ['required'],
        'value'            => ['required'],
        'label'            => ['nullable'],
        'is_primary'       => ['nullable'],
        'is_public'        => ['nullable'],
        'show_in_profile'  => ['nullable'],
    ]);

    $contact = $this->service->create([
        ...$data,
        'is_primary'      => $request->boolean('is_primary'),
        'is_public'       => $request->boolean('is_public'),
        'show_in_profile' => $request->boolean('show_in_profile'),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Contact created successfully.',
        'data'    => $contact,
    ]);
}

    /**
     * Update the specified contact.
     */
    public function update(Request $request, Contact $contact): JsonResponse
{
    $data = $request->validate([
        'type'            => ['required'],
        'value'           => ['required'],
        'label'           => ['nullable'],
        'is_primary'      => ['nullable'],
        'is_public'       => ['nullable'],
        'show_in_profile' => ['nullable'],
    ]);

    $contact = $this->service->update($contact, [
        ...$data,
        'is_primary'      => $request->boolean('is_primary'),
        'is_public'       => $request->boolean('is_public'),
        'show_in_profile' => $request->boolean('show_in_profile'),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Contact updated successfully.',
        'data'    => $contact,
    ]);
}

    /**
     * Remove the specified contact.
     */
    public function destroy(Contact $contact)
{
    $contact->delete();

    return response()->json([
        'message' => 'Contact deleted successfully.',
    ]);
}
}