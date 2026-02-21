<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierTypeTranslation extends Model
{
    protected $fillable = [
        'supplier_type_id',
        'locale',
        'name'
    ];

    public function supplierType()
    {
        return $this->belongsTo(SupplierType::class);
    }
}
