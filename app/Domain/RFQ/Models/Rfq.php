<?php

namespace App\Domain\RFQ\Models;

use App\Domain\RFQ\Enums\RfqStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domain\RFQ\Models\RfqAttributeValue;
use App\Domain\RFQ\Enums\RfqVisibilityType;

use App\Models\User;
use App\Models\Category;

class Rfq extends Model
{
    use SoftDeletes;
    

    protected $table = 'rfqs';

    protected $fillable = [
        'buyer_id',
        'buyer_type',
        'created_by',
        'title',
        'description',
        'type',
        'status',
        'published_at',
        'closed_at',
        'category_id',
        'visibility_type',
        
    ];

    protected $casts = [
        'status' => RfqStatus::class,
        'published_at' => 'datetime',
        'closed_at' => 'datetime',
        'visibility_type' => RfqVisibilityType::class,
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    
    public function participants()
    {
        return $this->hasMany(RfqParticipant::class);
    }

    public function participant()
{
    return $this->morphTo();
}

    public function offers()
{
    return $this->hasMany(
        \App\Domain\Negotiation\Models\RfqOffer::class
    );
}

public function attributeValues()
{
    return $this->hasMany(RfqAttributeValue::class)
        ->with([
            'attribute.translations',
            'attribute.options.translations',
        ]);
}

/*
|--------------------------------------------------------------------------
| CUSTOM ATTRIBUTE VALUES
|--------------------------------------------------------------------------
*/

public function customAttributeValues()
{
    return $this->attributeValues()
        ->whereHas('attribute', function ($q) {
            $q->where('is_custom', true);
        });
}

/*
|--------------------------------------------------------------------------
| SYSTEM ATTRIBUTE VALUES
|--------------------------------------------------------------------------
*/

public function systemAttributeValues()
{
    return $this->attributeValues()
        ->whereHas('attribute', function ($q) {
            $q->where('is_custom', false);
        });
}


public function isPrivate(): bool
{
    return $this->visibility_type === RfqVisibilityType::PRIVATE;
}

public function isCategory(): bool
{
    return $this->visibility_type === RfqVisibilityType::CATEGORY;
}

public function isPlatform(): bool
{
    return $this->visibility_type === RfqVisibilityType::PLATFORM;
}

public function isOpen(): bool
{
    return $this->visibility_type === RfqVisibilityType::OPEN;
}
    

public function visibilityCategories()
{
    return $this->belongsToMany(
        Category::class,
        'rfq_visibility_categories'
    );
}

/**
 * Get human-readable public RFQ identifier.
 *
 * Example:
 * ID: 12 → RFQ-00012
 *
 * This is used only for UI/display purposes.
 * DO NOT use for database relations or logic.
 *
 * @return string
 */
public function getPublicIdAttribute()
{
    return 'RFQ-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
}


public function hiddenAttributes()
{
    return $this->belongsToMany(
        \App\Models\Attribute::class,
        'rfq_hidden_attributes',
        'rfq_id',
        'attribute_id'
    );
}

}