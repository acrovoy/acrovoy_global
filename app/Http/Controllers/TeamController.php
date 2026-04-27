<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CompanyUser;
use App\Models\CompanyInvite;
use App\Services\Company\ActiveCompany;
use Illuminate\Support\Str;

use App\Services\Company\ActiveContextService;

class TeamController extends Controller
{

    public function index()
    {
        $companyId = ActiveCompany::id();

        $membersCount = CompanyUser::where('company_id', $companyId)->count();

        $pendingInvitesCount = CompanyInvite::where('company_id', $companyId)
            ->where('status', 'pending')
            ->count();

        return view(
            'dashboard.supplier.company.team.index',
            compact('membersCount', 'pendingInvitesCount')
        );
    }


    public function members(ActiveContextService $context)
    {
        $companyId = $context->id();
        $companyType = $context->type();

        $members = CompanyUser::with('user')
            ->where('company_id', $companyId)
            ->where('company_type', $companyType)
            ->latest()
            ->get();


          
        return view(
            'dashboard.supplier.company.team.members',
            compact('members')
        );
    }


   public function invite()
{
    $companyId = ActiveCompany::id();
    $companyType = ActiveCompany::type();

    $invites = CompanyInvite::where('company_id', ActiveCompany::id())
    ->where('company_type', ActiveCompany::type())
    ->latest()
    ->get();

    return view(
        'dashboard.supplier.company.team.invite',
        compact('invites')
    );
}


    public function sendInvite(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'role' => ['required', 'in:admin,member,viewer'],
        ]);

        CompanyInvite::create([
            'company_id' => ActiveCompany::id(),
            'email' => $request->email,
            'role' => $request->role,
            'status' => 'pending',
            'token' => Str::uuid(),
            'expires_at' => now()->addDays(7),
        ]);

        return back()->with('success', 'Invitation sent successfully.');
    }


    public function roles()
    {
        $roles = [

            [
                'name' => 'owner',
                'permissions' => [
                    'Full company access',
                    'Manage team members',
                    'Manage products',
                    'Manage orders',
                    'Manage billing',
                ]
            ],

            [
                'name' => 'admin',
                'permissions' => [
                    'Manage team members',
                    'Manage products',
                    'Manage orders',
                ]
            ],

            [
                'name' => 'member',
                'permissions' => [
                    'Manage products',
                    'View orders',
                ]
            ],

            [
                'name' => 'viewer',
                'permissions' => [
                    'View company data only',
                ]
            ],

        ];

        return view(
            'dashboard.supplier.company.team.roles',
            compact('roles')
        );
    }

}