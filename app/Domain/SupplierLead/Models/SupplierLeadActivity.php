<?php

namespace App\Domain\SupplierLead\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierLeadActivity extends Model
{
    use SoftDeletes;

    protected $table = 'supplier_lead_activities';

    protected $fillable = [
        'supplier_lead_id',
        'user_id',
        'type',
        'subject',
        'description',
        'old_status',
        'new_status',
        'follow_up_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'supplier_lead_id' => 'integer',
            'user_id' => 'integer',
            'follow_up_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function supplierLead(): BelongsTo
    {
        return $this->belongsTo(
            SupplierLead::class,
            'supplier_lead_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeOfType(
        Builder $query,
        string $type
    ): Builder {
        return $query->where('type', $type);
    }

    public function scopeFollowUps(Builder $query): Builder
    {
        return $query
            ->whereNotNull('follow_up_at')
            ->whereNull('completed_at');
    }

    public function scopeUpcomingFollowUps(Builder $query): Builder
    {
        return $query
            ->followUps()
            ->where('follow_up_at', '>=', now())
            ->orderBy('follow_up_at');
    }

    public function scopeOverdueFollowUps(Builder $query): Builder
    {
        return $query
            ->followUps()
            ->where('follow_up_at', '<', now())
            ->orderBy('follow_up_at');
    }

    public function scopeForLead(
        Builder $query,
        int $supplierLeadId
    ): Builder {
        return $query->where('supplier_lead_id', $supplierLeadId);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isFollowUp(): bool
    {
        return $this->follow_up_at !== null;
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function isOverdue(): bool
    {
        return $this->isFollowUp()
            && ! $this->isCompleted()
            && $this->follow_up_at->isPast();
    }
}