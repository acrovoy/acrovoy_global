<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeValueTranslation extends Model
{
    protected $fillable = [
        'product_attribute_value_id',
        'locale',
        'value',
    ];

    public function value()
    {
        return $this->belongsTo(
            ProductAttributeValue::class,
            'product_attribute_value_id'
        );
    }

public function valueRelation() // можно назвать valueRelation, а не value
{
    return $this->belongsTo(ProductAttributeValue::class, 'product_attribute_value_id');
}


}
