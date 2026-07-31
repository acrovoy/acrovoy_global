<?php

namespace App\Domain\Contact\Actions;

use App\Domain\Contact\Models\Contact;
use Illuminate\Support\Facades\Auth;

class CreateContactAction
{
    /**
     * Create a new contact.
     *
     * @param array $data
     * @return Contact
     */
    public function handle(array $data): Contact
    {
        
        return Contact::create([
            'contactable_type' => $data['contactable_type'],
            'contactable_id'   => $data['contactable_id'],

            'created_by' => $data['created_by'] ?? Auth::id(),

            'type' => $data['type'],
            'value' => $data['value'],

            'label' => $data['label'] ?? null,

            'is_primary' => !empty($data['is_primary']),
            'is_public' => !empty($data['is_public']),
            'show_in_profile' => !empty($data['show_in_profile']),

            'sort_order' => $data['sort_order'] ?? 0,

            'meta' => $data['meta'] ?? null,
        ]);
    }
}