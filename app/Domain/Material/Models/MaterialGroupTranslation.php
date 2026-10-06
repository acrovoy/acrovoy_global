<?php

namespace App\Domain\Material\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialGroupTranslation extends Model
{
    use HasFactory;

    protected $table = 'material_group_translations';

    protected $fillable = [
        'material_group_id',
        'locale',
        'name',
        'description',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function materialGroup(): BelongsTo
    {
        return $this->belongsTo(
            MaterialGroup::class,
            'material_group_id'
        );
    }
}