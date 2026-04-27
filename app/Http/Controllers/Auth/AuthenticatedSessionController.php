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
    /**
     * Show login page
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();

        /**
         * 1. ADMIN → direct
         */
        if ($user->role === 'admin') {
            return redirect()->route('admin.home');
        }

        /**
         * 2. BUYER → PERSONAL MODE (seed only)
         */
        if ($user->role === 'buyer') {

            session([
                'active_company_type' => 'personal',
                'active_company_id' => null,
                'active_company_role' => 'buyer',
            ]);

            return redirect()->route('buyer.home');
        }

        /**
         * 3. COMPANY USERS (supplier / manufacturer / logistics)
         */
        $memberships = CompanyUser::where('user_id', $user->id)->get();

        /**
         * 3.1 no companies → onboarding
         */
        if ($memberships->isEmpty()) {
            return redirect()->route('onboarding.company.select');
        }

        /**
         * 3.2 single company → auto select
         */
        if ($memberships->count() === 1) {

            $m = $memberships->first();

            session([
                'active_company_id' => $m->company_id,
                'active_company_type' => $m->company_type,
                'active_company_role' => $m->role,
            ]);

            return redirect()->route('dashboard.home');
        }

        /**
         * 3.3 multiple companies → user chooses context
         */
        return redirect()->route('company.switcher');
    }

    /**
     * Logout
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}