<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanyUser;

class CompanySwitchController extends Controller
{
    public function switch(Request $request)
    {
        $request->validate([
            'company_key' => 'required|string',
        ]);

        [$type, $id] = explode('|', $request->company_key);

        $user = auth()->user();



        /*
        |------------------------------------------------------
        | PERSONAL MODE - BUYER
        |------------------------------------------------------
        */
        if ($type === 'personal_buyer') {

            session()->forget([
                
                'active_company_type',
                'active_company_id',
                'active_company_role',
                'active_company_user_id',
            ]);

            session([
                'active_mode' => 'personal',
                'active_company_type' => null,
                'active_company_id' => null,
                'active_company_role' => null,
                'active_personal_mode' => 'buyer',
            ]);

            

            $user->setSetting('platform_mode', 'buyer');

            $user->setSetting('last_company_user_id', null);
            $user->setSetting('last_company_id', null);
            $user->setSetting('last_company_type', null);

            return redirect()->route('buyer.home');
        }

        /*
        |------------------------------------------------------
        | PERSONAL MODE - SUPPLIER
        |------------------------------------------------------
        */
        if ($type === 'personal_supplier') {

            session()->forget([
                'active_company_type',
                'active_company_id',
                'active_company_role',
                'active_company_user_id',
            ]);

            session([
                'active_mode' => 'personal',
                'active_company_type' => null,
                'active_company_id' => null,
                'active_company_role' => null,
                'active_personal_mode' => 'supplier',
            ]);

           

            $user->setSetting('platform_mode', 'supplier');

            $user->setSetting('last_company_user_id', null);
            $user->setSetting('last_company_id', null);
            $user->setSetting('last_company_type', null);

            return redirect()->route('dashboard.home');
        }

        /*
        |------------------------------------------------------
        | RESOLVE MEMBERSHIP (COMPANY MODE)
        |------------------------------------------------------
        */

        $membership = CompanyUser::query()
            ->where('user_id', $user->id)
            ->where('company_type', $type)
            ->where('company_id', $id)
            ->where('status', 'active')
            ->first();

        if (!$membership) {
            abort(403);
        }

        /*
        |------------------------------------------------------
        | ACTIVE CONTEXT (COMPANY MODE)
        |------------------------------------------------------
        */

        session([
            'active_mode' => 'company',
            'active_company_type'  => $membership->company_type,
            'active_company_id' => $membership->company_id,
            'active_company_role' => $membership->role,
            'active_company_user_id' => $membership->id,
            
        ]);

        $request->session()->regenerate();

        /*
        |------------------------------------------------------
        | SAVE LAST CONTEXT
        |------------------------------------------------------
        */

        $user->setSetting('last_company_user_id', $membership->id);
        $user->setSetting('last_company_id', $membership->company_id);
        $user->setSetting('last_company_type', $membership->company_type);

        /*
        |------------------------------------------------------
        | PLATFORM MODE (IMPORTANT FIXED)
        |------------------------------------------------------
        */

        $platformMode = match ($membership->company_type) {
            \App\Models\Supplier::class => 'supplier',
            \App\Models\Buyer::class => 'buyer',
            default => $user->setting('platform_mode', 'buyer'),
        };

        $user->setSetting('platform_mode', $platformMode);

        /*
        |------------------------------------------------------
        | FINAL REDIRECT (FIX)
        |------------------------------------------------------
        */

        $route = $platformMode === 'supplier'
            ? 'dashboard.home'
            : 'buyer.home';

        return redirect()->route($route);
    }

    public function index()
    {
        $user = auth()->user();

        $companies = CompanyUser::query()
            ->where('user_id', $user->id)
            ->with('company')
            ->get();

        return redirect()->back();
    }
}