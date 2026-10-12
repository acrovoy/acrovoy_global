<?php

namespace App\Domain\SupplierLead\Models;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Country;


use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SupplierLead extends Model
{
    use SoftDeletes;

    protected $table = 'supplier_leads';

    protected $fillable = [
        'company_name',
        'legal_name',
        'slug',
        'linkedin_url',
        'website',
        'email',
        'phone',
        'country_id',
        'city',
        'address',
        'company_type',
        'acquisition_status',
        'priority',
        'source',
        'assigned_to',
        'created_by',
        'supplier_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'assigned_to' => 'integer',
            'created_by' => 'integer',
            'supplier_id' => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(
            SupplierLeadContact::class,
            'supplier_lead_id'
        );
    }

    public function activities(): HasMany
    {
        return $this->hasMany(
            SupplierLeadActivity::class,
            'supplier_lead_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeByStatus(
        Builder $query,
        string $status
    ): Builder {
        return $query->where('acquisition_status', $status);
    }

    public function scopeByPriority(
        Builder $query,
        string $priority
    ): Builder {
        return $query->where('priority', $priority);
    }

    public function scopeAssignedTo(
        Builder $query,
        int $userId
    ): Builder {
        return $query->where('assigned_to', $userId);
    }

    public function scopeUnassigned(Builder $query): Builder
    {
        return $query->whereNull('assigned_to');
    }

    public function scopeLinkedToSupplier(Builder $query): Builder
    {
        return $query->whereNotNull('supplier_id');
    }

    public function scopeNotLinkedToSupplier(Builder $query): Builder
    {
        return $query->whereNull('supplier_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isLinkedToSupplier(): bool
    {
        return $this->supplier_id !== null;
    }

    public function categories(): BelongsToMany
{
    return $this->belongsToMany(
        Category::class,
        'supplier_lead_categories',
        'supplier_lead_id',
        'category_id',
            )->withTimestamps();
}

public function country(): BelongsTo
{
    return $this->belongsTo(Country::class, 'country_id');
}
}