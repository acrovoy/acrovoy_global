<?php

namespace App\Domain\Contact\Services;

use App\Domain\Contact\Actions\CreateContactAction;
use App\Domain\Contact\Actions\DeleteContactAction;
use App\Domain\Contact\Actions\UpdateContactAction;
use App\Domain\Contact\Models\Contact;
use Illuminate\Support\Facades\Auth;

class ContactService
{
    public function __construct(
        protected CreateContactAction $createContactAction,
        protected UpdateContactAction $updateContactAction,
        protected DeleteContactAction $deleteContactAction,
    ) {
    }

    /**
     * Create a new contact.
     */
    public function create(array $data): Contact
    {
        $data = $this->prepareData($data);

        if (($data['is_primary'] ?? false) === true) {
            $this->clearPrimaryContact(
                $data['contactable_type'],
                $data['contactable_id']
            );
        }

        if (($data['show_in_profile'] ?? false) === true) {
    $this->clearProfileContact(
        $data['contactable_type'],
        $data['contactable_id']
    );
}


        return $this->createContactAction->handle($data);
    }

    /**
     * Update the specified contact.
     */
    public function update(Contact $contact, array $data): Contact
    {
        $data = $this->prepareData($data);

        if (($data['is_primary'] ?? false) === true) {
            $this->clearPrimaryContact(
                $contact->contactable_type,
                $contact->contactable_id,
                $contact->id
            );
        }

        if (($data['show_in_profile'] ?? false) === true) {
    $this->clearProfileContact(
        $contact->contactable_type,
        $contact->contactable_id,
        $contact->id
    );
}

        return $this->updateContactAction->handle($contact, $data);
    }

    /**
     * Delete the specified contact.
     */
    public function delete(Contact $contact): bool
    {
        return $this->deleteContactAction->handle($contact);
    }

    /**
     * Prepare contact data before saving.
     */
    protected function prepareData(array $data): array
    {
        if (isset($data['value'])) {
            $data['value'] = trim($data['value']);
        }

        return $data;
    }

    /**
     * Remove the primary flag from other contacts.
     */
    protected function clearPrimaryContact(
        string $contactableType,
        int $contactableId,
        ?int $exceptId = null
    ): void {
        Contact::query()
            ->where('contactable_type', $contactableType)
            ->where('contactable_id', $contactableId)
            ->when(
                $exceptId,
                fn ($query) => $query->where('id', '!=', $exceptId)
            )
            ->update([
                'is_primary' => false,
            ]);
    }

    /**
 * Remove profile flag from other contacts.
 */
protected function clearProfileContact(
    string $contactableType,
    int $contactableId,
    ?int $exceptId = null
): void {
    Contact::query()
        ->where('contactable_type', $contactableType)
        ->where('contactable_id', $contactableId)
        ->when(
            $exceptId,
            fn ($query) => $query->where('id', '!=', $exceptId)
        )
        ->update([
            'show_in_profile' => false,
        ]);
}
}