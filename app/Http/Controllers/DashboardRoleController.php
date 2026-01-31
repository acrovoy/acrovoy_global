<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardRoleController extends Controller
{
    public function switch(Request $request)
    {
        $role = $request->get('role');

        if (!in_array($role, ['manufacturer', 'buyer', 'admin'])) {
            abort(404);
        }

        session(['dashboard_role' => $role]);

        return redirect()->back();
    }
}
