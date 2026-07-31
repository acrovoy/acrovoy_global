<?php

namespace App\Domain\Contact\Actions;

use App\Domain\Contact\Models\Contact;

class DeleteContactAction
{
    /**
     * Delete the specified contact.
     *
     * @param Contact $contact
     * @return bool
     */
    public function handle(Contact $contact): bool
    {
        return (bool) $contact->delete();
    }
}