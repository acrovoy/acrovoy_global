<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManufacturingCapabilityTranslation extends Model
{
    protected $fillable = [
        'manufacturing_capability_id',
        'locale',
        'name'
    ];

    public function capability()
    {
        return $this->belongsTo(ManufacturingCapability::class);
    }
}
