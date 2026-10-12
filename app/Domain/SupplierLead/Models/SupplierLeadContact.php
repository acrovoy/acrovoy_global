<?php

namespace App\Domain\SupplierLead\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierLeadContact extends Model
{
    use SoftDeletes;

    protected $table = 'supplier_lead_contacts';

    protected $fillable = [
        'supplier_lead_id',
        'first_name',
        'last_name',
        'job_title',
        'department',
        'email',
        'phone',
        'whatsapp',
        'telegram',
        'linkedin_url',
        'is_primary',
        'is_decision_maker',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'supplier_lead_id' => 'integer',
            'is_primary' => 'boolean',
            'is_decision_maker' => 'boolean',
            'is_active' => 'boolean',
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

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopePrimary(Builder $query): Builder
    {
        return $query->where('is_primary', true);
    }

    public function scopeDecisionMakers(Builder $query): Builder
    {
        return $query->where('is_decision_maker', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute(): string
    {
        return trim(
            $this->first_name . ' ' . ($this->last_name ?? '')
        );
    }
}