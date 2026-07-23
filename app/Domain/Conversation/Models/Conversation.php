<?php

namespace App\Domain\Conversation\Models;

use App\Domain\Conversation\Enums\ConversationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

use App\Domain\Conversation\Enums\ConversationStatus;


class Conversation extends Model
{
    protected $fillable = [
        'uuid',
        'conversation_type',
        'subject_type',
        'subject_id',
        'title',
        'subtitle',
        'created_by',
        'last_message_id',
        'last_message_at',
        'status',
    ];

    protected $casts = [
        'conversation_type' => ConversationType::class,
        'last_message_at' => 'datetime',
        'status' => ConversationStatus::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (Conversation $conversation) {

            if (empty($conversation->uuid)) {
                $conversation->uuid = (string) Str::uuid();
            }

        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    /**
 * Текущий participant.
 *
 * Используется для определения last_read_at.
 */
public function participant(): HasOne
{
    return $this->hasOne(
        ConversationParticipant::class
    );
}


    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)
            ->orderBy('created_at');
    }

    public function lastMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}