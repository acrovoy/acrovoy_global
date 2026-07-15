<?php

namespace App\Domain\Conversation\Models;

use App\Domain\Conversation\Enums\MessageType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Message extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',

        'conversation_id',

        'sender_type',
        'sender_id',
         'created_by',

        'message_type',

        'message',

        'payload',

        'reply_to_message_id',

        'edited_at',
    ];

    protected $casts = [
        'message_type' => MessageType::class,

        'payload' => 'array',

        'edited_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Message $message) {

            if (empty($message->uuid)) {
                $message->uuid = (string) Str::uuid();
            }

        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): MorphTo
    {
        return $this->morphTo();
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reply_to_message_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Message::class, 'reply_to_message_id');
    }

    public function creator()
{
    return $this->belongsTo(
        \App\Models\User::class,
        'created_by'
    );
}

   
}