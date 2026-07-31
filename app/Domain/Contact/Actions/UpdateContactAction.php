<?php

namespace App\Domain\Contact\Actions;

use App\Domain\Contact\Models\Contact;

class UpdateContactAction
{
    /**
     * Update the specified contact.
     *
     * @param Contact $contact
     * @param array $data
     * @return Contact
     */
    public function handle(Contact $contact, array $data): Contact
    {
        $contact->update([
            'type' => $data['type'] ?? $contact->type,
            'value' => $data['value'] ?? $contact->value,

            'label' => $data['label'] ?? $contact->label,

            'is_primary' => !empty($data['is_primary']),
            'is_public' => !empty($data['is_public']),
            'show_in_profile' => !empty($data['show_in_profile']),

            'sort_order' => $data['sort_order'] ?? $contact->sort_order,

            'meta' => $data['meta'] ?? $contact->meta,
        ]);

        return $contact->fresh();
    }
}