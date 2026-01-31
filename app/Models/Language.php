<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    /**
     * The attributes that are mass assignable.
     * These fields can be filled when creating or updating a Language.
     */
    protected $fillable = [
        'code',        // Language code, e.g., "en", "ru"
        'name',        // Name in English, e.g., "English"
        'native_name', // Name in the native language, e.g., "Русский"
        'locale',      // Locale code, e.g., "en-US", "ru-RU"
        'direction',   // Text direction: "ltr" (left-to-right) or "rtl" (right-to-left)
        'priority',    // Display priority: "core", "high", "medium", "low"
        'sort_order',  // Sorting order in lists
        'is_active',   // Whether the language is active (true/false)
        'is_default',  // Whether this language is the default for the platform
        'notes',       // Optional notes for admin/manager
    ];

    /**
     * The attributes that should be cast to native types.
     * Ensures correct type handling for booleans and integers.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Default values for attributes.
     * These are applied when creating a new Language if no value is provided.
     */
    protected $attributes = [
        'is_active' => true,
        'direction' => 'ltr',
        'priority' => 'medium',
        'sort_order' => 100,
        'is_default' => false,
    ];
}
