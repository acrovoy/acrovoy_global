<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupplierCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'file_path',
        'name',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
