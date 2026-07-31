<?php

namespace App\Domain\Contact\Traits;

use App\Domain\Contact\Models\Contact;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasContacts
{
    /**
     * Get all contacts for this model.
     */
    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable')
            ->orderBy('sort_order')
            ->orderByDesc('is_primary');
    }

    public function primaryContact()
{
    return $this->morphOne(Contact::class, 'contactable')
        ->where('is_primary', true);
}

}