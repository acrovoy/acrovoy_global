<?php

namespace App\Domain\Product\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialTranslation extends Model
{
    protected $fillable = ['material_id', 'locale', 'name'];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}

