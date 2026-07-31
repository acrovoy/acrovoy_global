<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Domain\Contact\Traits\HasContacts;

class Buyer extends Model
{
    use HasFactory, HasContacts;


    public function businessTypes()
{
    return $this->morphToMany(
        BusinessType::class,
        'business_typeable'
    );
}

}
