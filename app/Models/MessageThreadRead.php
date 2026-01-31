<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MessageThreadRead extends Pivot
{
    public $table = 'message_thread_read';
    public $timestamps = null;
    protected $fillable = ['thread_id', 'user_id'];

    function user()
    {
        return $this->belongsTo(User::class);
    }

    function thread()
    {
        return $this->belongsTo(MessageThread::class);
    }
}
