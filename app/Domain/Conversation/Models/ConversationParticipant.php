<?php

namespace App\Domain\Conversation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ConversationParticipant extends Model
{
    protected $fillable = [
        'conversation_id',

        'actor_type',
        'actor_id',

        'context_type',
        'context_id',

        'role',
        'platform_role',

        'last_read_at',
    ];

    protected $casts = [
        'last_read_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function actor(): MorphTo
    {
        return $this->morphTo();
    }

    public function context(): MorphTo
    {
        return $this->morphTo();
    }

    public function unreadCount(): int
{
    return $this->conversation
        ->messages()
        ->when($this->last_read_at, function ($query) {
            $query->where('created_at', '>', $this->last_read_at);
        })
        ->where(function ($query) {
            $query
                ->where('sender_type', '!=', $this->context_type)
                ->orWhere('sender_id', '!=', $this->context_id);
        })
        ->count();
}


}