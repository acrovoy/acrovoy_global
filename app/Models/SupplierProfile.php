<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierProfile extends Model
{
    protected $fillable = [
        'supplier_id',
        'about_us_description',
        'manufacturing_description',
        'factory_area',
        'production_lines',
        'monthly_capacity',
        'lead_time_days',
        'annual_export_revenue',
        'registration_capital',
        'founded_year',
        'total_employees',
        'moq'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function manufacturingCapabilities()
{
    return $this->belongsToMany(
        \App\Models\ManufacturingCapability::class,
        'supplier_profile_manufacturing_capability', // имя pivot таблицы
        'supplier_profile_id',                       // внешний ключ текущей модели
        'manufacturing_capability_id'                // внешний ключ связанной модели
    );
}

}
