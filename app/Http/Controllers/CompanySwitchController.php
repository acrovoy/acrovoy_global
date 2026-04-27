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

    // 👇 PERSONAL MODE
    if ($type === 'personal') {

        session()->forget([
            'active_company_type',
            'active_company_id',
            'active_company_role',
        ]);

        return redirect()->route('buyer.home');
    }

    $user = auth()->user();

    $membership = CompanyUser::where('user_id', $user->id)
        ->where('company_type', $type)
        ->where('company_id', $id)
        ->first();

    if (!$membership) {
        abort(403);
    }

    session([
        'active_company_type' => $type,
        'active_company_id' => $id,
        'active_company_role' => $membership->role,
    ]);

    return redirect()->route('dashboard.home');
}

public function index()
{
    $user = auth()->user();

    $companies = CompanyUser::where('user_id', $user->id)
        ->with('company')
        ->get();

    return view('dashboard.company.switcher', compact('companies'));
}

}