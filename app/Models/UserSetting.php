<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    protected $fillable = [
        'user_id',
        'key',
        'value',
    ];

    /**
     * Optional casting.
     */
    protected $casts = [
        'value' => 'string',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Get setting value.
     */
    public static function getValue(
        int $userId,
        string $key,
        mixed $default = null
    ): mixed {
        return static::query()
            ->where('user_id', $userId)
            ->where('key', $key)
            ->value('value') ?? $default;
    }

    /**
     * Set setting value.
     */
    public static function setValue(
        int $userId,
        string $key,
        mixed $value
    ): self {
        return static::updateOrCreate(
            [
                'user_id' => $userId,
                'key' => $key,
            ],
            [
                'value' => is_array($value) || is_object($value)
                    ? json_encode($value)
                    : $value,
            ]
        );
    }

    /**
     * Remove setting.
     */
    public static function remove(
        int $userId,
        string $key
    ): void {
        static::query()
            ->where('user_id', $userId)
            ->where('key', $key)
            ->delete();
    }

    /**
     * Check if setting exists.
     */
    public static function has(
        int $userId,
        string $key
    ): bool {
        return static::query()
            ->where('user_id', $userId)
            ->where('key', $key)
            ->exists();
    }
}