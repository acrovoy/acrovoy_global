<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecificationTranslation extends Model
{
    protected $fillable = [
        'specification_id',
        'locale',
        'key',
        'value',
    ];

    public function specification()
    {
        return $this->belongsTo(Specification::class);
    }
}
