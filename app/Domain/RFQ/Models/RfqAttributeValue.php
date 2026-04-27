<?php

namespace App\Domain\RFQ\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Attribute;
use App\Models\AttributeOption;

class RfqAttributeValue extends Model
{
    protected $fillable = [

        'rfq_id',
        'attribute_id',

        'value_text',
        'value_number',
        'value_boolean',
        'value_date',

        'attribute_option_id'

    ];


    public function rfq()
    {
        return $this->belongsTo(Rfq::class);
    }


    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }


    public function option()
    {
        return $this->belongsTo(
            AttributeOption::class,
            'attribute_option_id'
        );
    }


    public function options()
    {
        return $this->belongsToMany(

            AttributeOption::class,

            'rfq_attribute_value_options',

            'rfq_attribute_value_id',

            'attribute_option_id'

        );
    }
}