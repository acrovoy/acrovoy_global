<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyInvite extends Model
{
    protected $fillable = [
        'company_id',
        'company_type',
        'email',
        'role',
        'token',
        'accepted_at',
        'expires_at',
        'invited_by',
    ];

    public function company()
    {
        return $this->morphTo(null, 'company_type', 'company_id');
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && now()->gt($this->expires_at);
    }

    public function isPending(): bool
    {
        return !$this->accepted_at && !$this->isExpired();
    }
}