<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageThread extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'product_id', 'buyer_id', 'manufacturer_id', 'role_view', 'unread'];

    public function isRead(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->userReads()->where('user_id', auth()->user()?->id)->exists(),
        );
    }

    public function userReads()
    {
        return $this->belongsToMany(User::class, 'message_thread_read');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'thread_id')->orderBy('created_at');
    }

    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'messages_thread_product',
            'thread_id',
            'product_id',
        );
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(Supplier::class, 'manufacturer_id');
    }
}
