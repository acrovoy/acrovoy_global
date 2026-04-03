<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeValueOption extends Model
{
    protected $fillable = [
        'product_attribute_value_id',
        'attribute_option_id',
    ];

    public function value()
    {
        return $this->belongsTo(ProductAttributeValue::class, 'product_attribute_value_id');
    }

    public function option()
    {
        return $this->belongsTo(AttributeOption::class, 'attribute_option_id');
    }
}