<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductMaterial extends Model
{
    protected $fillable = [
        'product_id',
        'material_id',
        
    ];

    

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    
}

