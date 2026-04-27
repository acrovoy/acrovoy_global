<?php

namespace App\Services\Company;

use App\Models\CompanyUser;
use Illuminate\Support\Facades\Auth;

class ActiveCompany
{
    /**
     * SESSION KEYS
     */
    private const KEY_ID    = 'active_company_id';
    private const KEY_TYPE  = 'active_company_type';
    private const KEY_ROLE  = 'active_company_role';

    /*
    |--------------------------------------------------------------------------
    | BASIC GETTERS
    |--------------------------------------------------------------------------
    */

    public static function id()
    {
        return session(self::KEY_ID);
    }

    public static function type()
    {
        return session(self::KEY_TYPE);
    }

    public static function role()
    {
        return session(self::KEY_ROLE);
    }

    /*
    |--------------------------------------------------------------------------
    | CHECKS
    |--------------------------------------------------------------------------
    */

    public static function isSet(): bool
    {
        return self::id() !== null && self::type() !== null;
    }

    public static function hasActive(): bool
    {
        return self::isSet() && self::model() !== null;
    }

    /*
    |--------------------------------------------------------------------------
    | CORE RESOLVERS
    |--------------------------------------------------------------------------
    */

    /**
     * Return CompanyUser membership record
     */
    public static function membership(): ?CompanyUser
    {
        if (!self::isSet()) {
            return null;
        }

        return CompanyUser::where('user_id', Auth::id())
            ->where('company_id', self::id())
            ->where('company_type', self::type())
            ->first();
    }

    /**
     * Return active company model (Supplier / Logistic / etc.)
     */
    public static function model()
    {
        if (!self::isSet()) {
            return self::fallback();
        }

        $membership = self::membership();

        if (!$membership) {
            return self::fallback();
        }

        $class = self::type();

        if (!class_exists($class)) {
            return self::fallback();
        }

        return $class::find(self::id());
    }

    /*
    |--------------------------------------------------------------------------
    | FALLBACK LOGIC
    |--------------------------------------------------------------------------
    */

    /**
     * Safe fallback if session is invalid
     */
    public static function fallback()
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        $first = CompanyUser::where('user_id', $user->id)->first();

        if (!$first) {
            return null;
        }

        // auto-repair session
        session([
            self::KEY_ID   => $first->company_id,
            self::KEY_TYPE => $first->company_type,
            self::KEY_ROLE => $first->role,
        ]);

        return $first->company;
    }

    /*
    |--------------------------------------------------------------------------
    | SET ACTIVE COMPANY
    |--------------------------------------------------------------------------
    */

    public static function set(CompanyUser $membership): void
    {
        session([
            self::KEY_ID   => $membership->company_id,
            self::KEY_TYPE => $membership->company_type,
            self::KEY_ROLE => $membership->role,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SECURITY CHECK
    |--------------------------------------------------------------------------
    */

    public static function ensureAccess(): void
    {
        $membership = self::membership();

        if (!$membership) {
            abort(403, 'You do not have access to this company context.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SHORTCUT HELPERS
    |--------------------------------------------------------------------------
    */

    public static function company()
    {
        return self::model();
    }

    public static function user()
    {
        return Auth::user();
    }

    public static function isOwner(): bool
    {
        return self::role() === 'owner';
    }

    public static function isAdmin(): bool
    {
        return in_array(self::role(), ['owner', 'admin']);
    }
}