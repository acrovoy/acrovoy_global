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
    return $this->hasMany(RfqAttributeValue::class);
}

public function customAttributes()
{
    return $this->hasMany(RfqCustomAttribute::class);
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


}