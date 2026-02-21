<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportMarketTranslation extends Model
{
    protected $fillable = [
        'export_market_id',
        'locale',
        'name'
    ];
}
