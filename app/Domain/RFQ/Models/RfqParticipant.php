<?php

namespace App\Domain\RFQ\Models;

use App\Models\Supplier;
use App\Domain\RFQ\Enums\RfqParticipantStatus;
use Illuminate\Database\Eloquent\Model;

class RfqParticipant extends Model
{
    protected $fillable = [
        'rfq_id',
        'status',
        'invited_at',
        'responded_at',
        'participant_type',
        'participant_id',
    ];

    protected $casts = [
        'status' => RfqParticipantStatus::class,
        'invited_at' => 'datetime',
        'responded_at' => 'datetime',
    ];


    public function rfq()
    {
        return $this->belongsTo(Rfq::class);
    }

    public function participant()
{
    return $this->morphTo();
}
   
    public function statusEnum(): RfqParticipantStatus
{
    return $this->status instanceof RfqParticipantStatus
        ? $this->status
        : RfqParticipantStatus::from($this->status);
}

public function scopeActive($query)
{
    return $query->where('status', '!=', 'removed');
}
}