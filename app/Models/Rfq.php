<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rfq extends Model
{
    protected $fillable = [
        'buyer_id',
        'title',
        'description',
        'category_id',
        'quantity',
        'deadline',
        'status',
        'attachment_path',
    ];

    public function offers()
    {
        return $this->hasMany(RfqOffer::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Категория RFQ
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function getUnreadStatusCountAttribute()
{
    $user = auth()->user();

    // Если это не supplier — 0
    if (!$user || $user->role !== 'manufacturer' || !$user->supplier) {
        return 0;
    }

    $supplierId = $user->supplier->id;

    return $this->offers
        ->where('supplier_id', $supplierId)
        ->whereIn('status', ['accepted', 'rejected'])
        ->whereNull('supplier_viewed_at')
        ->count();
}
}
