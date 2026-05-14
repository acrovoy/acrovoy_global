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


public function getParticipantNameAttribute(): ?string
{
    $participant = $this->participant;

    if (!$participant) {
        return null;
    }

    // Supplier / Company
    if ($participant instanceof \App\Models\Supplier) {
        return $participant->name ?? $participant->title ?? 'Supplier';
    }

    // fallback если потом появятся другие типы
    if (isset($participant->name)) {
        return $participant->name;
    }

    return class_basename($participant);
}


}