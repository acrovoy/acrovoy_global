<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingTemplateTranslation extends Model
{
    protected $fillable = ['shipping_template_id', 'locale', 'title', 'description'];
}
