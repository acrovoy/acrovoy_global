<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\CompanyUser;

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

            // сохраняем последний выбранный режим
            $user->setSetting('platform_mode', 'admin');

            // создаем активный контекст администратора
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
        | GLOBAL PERSONAL MODE (DEFAULT STATE)
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

            $membership = CompanyUser::find($lastCompanyUserId);

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
        | 2. LOAD MEMBERSHIPS
        |--------------------------------------------------------------------------
        */
        $memberships = CompanyUser::where('user_id', $user->id)->get();

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
        | 4. SINGLE COMPANY → AUTO LOGIN
        |--------------------------------------------------------------------------
        */
        if ($memberships->count() === 1) {

            $m = $memberships->first();

            session([
                'active_mode' => 'company',
                'active_company_type' => $m->company_type,
                'active_company_id' => $m->company_id,
                'active_company_role' => $m->role,
                'active_personal_mode' => $personalMode,
            ]);

            $user->setSetting('last_company_user_id', $m->id);

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