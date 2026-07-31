<?php

namespace App\Domain\Contact\Models;

use App\Domain\Contact\Enums\ContactType;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Contact extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'contactable_type',
        'contactable_id',
        'created_by',

        'type',
        'value',
        'label',

        'is_primary',
        'is_public',
        'show_in_profile',

        'verified_at',

        'sort_order',

        'meta',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'type' => ContactType::class,

        'is_primary' => 'boolean',
        'is_public'  => 'boolean',
        'show_in_profile'  => 'boolean',

        'verified_at' => 'datetime',

        'sort_order' => 'integer',

        'meta' => 'array',
    ];

    protected $appends = [
    'url',
    'display_value',
    'type_label',
    'icon',
    'is_verified',
];



    /**
     * Get the owner model (User, Company, Branch, etc.).
     */
    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who created the contact.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Determine whether the contact has been verified.
     */
    public function getIsVerifiedAttribute(): bool
    {
        return ! is_null($this->verified_at);
    }

    /**
     * Get the generated URL for the contact.
     *
     * Examples:
     * - tel:+380501234567
     * - mailto:info@company.com
     * - https://t.me/acrovoy
     * - https://wa.me/380501234567
     */
    public function getUrlAttribute(): ?string
    {
        return $this->type?->url($this->value);
    }

    /**
     * Get the formatted value for display.
     *
     * Examples:
     * - @acrovoy
     * - +380501234567
     * - https://company.com
     */
    public function getDisplayValueAttribute(): string
    {
        return $this->type?->format($this->value) ?? $this->value;
    }

    /**
     * Get the human-readable contact type label.
     *
     * Examples:
     * - Phone
     * - Email
     * - Telegram
     * - WhatsApp
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->type?->label() ?? '';
    }

    /**
     * Get the contact icon identifier.
     *
     * Examples:
     * - phone
     * - email
     * - telegram
     * - whatsapp
     */
    public function getIconAttribute(): string
    {
        return $this->type?->icon() ?? 'default';
    }
}