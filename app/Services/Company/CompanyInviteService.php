<?php

namespace App\Services\Company;

use App\Models\CompanyInvite;
use Illuminate\Support\Str;

class CompanyInviteService
{
    public static function invite($company, string $email, string $role, $user)
    {
        return CompanyInvite::create([
            'company_id' => $company->id,
            'company_type' => get_class($company),
            'email' => $email,
            'role' => $role,
            'token' => Str::random(40),
            'invited_by' => $user->id,
            'expires_at' => now()->addDays(7),
        ]);
    }
}