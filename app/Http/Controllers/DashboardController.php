<?php

namespace App\Http\Controllers;

use App\Facades\ActiveContext;
use App\Models\Supplier;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();

        /**
         * PLATFORM ADMIN
         */
        if ($user->role === 'admin') {
            return view('dashboard.admin.home');
        }

        /**
         * COMPANY MODE
         */
        if (ActiveContext::isCompany()) {

            if (ActiveContext::type() === Supplier::class) {

                return view('dashboard.supplier.home', [
                    'company' => ActiveContext::company(),
                    'role' => ActiveContext::role(),
                ]);

            }

        }

        /**
         * PERSONAL MODE
         */
        if (ActiveContext::isPersonal()) {

            return view('dashboard.buyer.home');

        }

        /**
         * FALLBACK
         */
        abort(403);
    }
}