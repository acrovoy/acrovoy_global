<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\CompanyUser;
use App\Models\CompanyInvite;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */
        if ($user->role === 'admin') {
            $user->setSetting('platform_mode', 'admin');

            session([
                'active_mode' => 'admin',
                'active_company_type' => null,
                'active_company_id' => null,
                'active_company_role' => 'admin',
                'active_personal_mode' => 'admin',
            ]);

            return redirect()->route('admin.home');
        }

        /*
        |--------------------------------------------------------------------------
        | TEAM INVITATION
        |--------------------------------------------------------------------------
        | Если вход выполнен по приглашению, сначала продолжаем приглашение.
        */
        $inviteToken = $request->session()->get('company_invite_token');

        if ($inviteToken) {
            $invite = CompanyInvite::where('token', $inviteToken)->first();

            if (
                $invite &&
                $invite->isPending() &&
                strtolower($invite->email) === strtolower($user->email)
            ) {
                if (!$user->hasVerifiedEmail()) {
                    return redirect()->route('verification.notice');
                }

                return redirect()->route('team.invitation.continue', [
                    'token' => $invite->token,
                ]);
            }

            // Не продолжаем приглашение для другого email
            // или недействительного приглашения.
            $request->session()->forget([
                'company_invite_token',
                'company_invite_url',
            ]);

            if ($invite && strtolower($invite->email) !== strtolower($user->email)) {
                return redirect()->route('dashboard.home')
                    ->with('error', 'This invitation was sent to a different email address.');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | EMAIL VERIFICATION
        |--------------------------------------------------------------------------
        */
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        /*
        |--------------------------------------------------------------------------
        | GLOBAL PERSONAL MODE
        |--------------------------------------------------------------------------
        */
        $personalMode = $user->setting('platform_mode', 'buyer');

        /*
        |--------------------------------------------------------------------------
        | 1. RESTORE LAST COMPANY CONTEXT
        |--------------------------------------------------------------------------
        */
        $lastCompanyUserId = $user->setting('last_company_user_id');

        if ($lastCompanyUserId) {
            $membership = CompanyUser::where('id', $lastCompanyUserId)
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if ($membership) {
                session([
                    'active_mode' => 'company',
                    'active_company_type' => $membership->company_type,
                    'active_company_id' => $membership->company_id,
                    'active_company_role' => $membership->role,
                    'active_personal_mode' => $personalMode,
                ]);

                return redirect()->route('dashboard.home');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 2. LOAD ACTIVE MEMBERSHIPS
        |--------------------------------------------------------------------------
        */
        $memberships = CompanyUser::where('user_id', $user->id)
            ->where('status', 'active')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 3. NO COMPANIES → PERSONAL MODE
        |--------------------------------------------------------------------------
        */
        if ($memberships->isEmpty()) {
            session([
                'active_mode' => 'personal',
                'active_company_type' => null,
                'active_company_id' => null,
                'active_company_role' => null,
                'active_personal_mode' => $personalMode,
            ]);

            return redirect()->route(
                $personalMode === 'supplier'
                    ? 'dashboard.home'
                    : 'buyer.home'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 4. SINGLE COMPANY → AUTO SELECT
        |--------------------------------------------------------------------------
        */
        if ($memberships->count() === 1) {
            $membership = $memberships->first();

            session([
                'active_mode' => 'company',
                'active_company_type' => $membership->company_type,
                'active_company_id' => $membership->company_id,
                'active_company_role' => $membership->role,
                'active_personal_mode' => $personalMode,
            ]);

            $user->setSetting('last_company_user_id', $membership->id);

            return redirect()->route('dashboard.home');
        }

        /*
        |--------------------------------------------------------------------------
        | 5. MULTIPLE COMPANIES → SWITCHER
        |--------------------------------------------------------------------------
        */
        session([
            'active_mode' => 'personal',
            'active_company_type' => null,
            'active_company_id' => null,
            'active_company_role' => null,
            'active_personal_mode' => $personalMode,
        ]);

        return redirect()->route('company.switcher');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
