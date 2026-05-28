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

            return match (ActiveContext::type()) {
                Supplier::class => view('dashboard.supplier.home'),
                \App\Models\Buyer::class => view('dashboard.buyer.home'),
                default => abort(403),
            };
        }

        /**
         * PERSONAL MODE
         */
        if (ActiveContext::isPersonal()) {


            $view = match (ActiveContext::role()) {
                'supplier' => 'dashboard.supplier.home',
                'buyer' => 'dashboard.buyer.home',
                default => 'dashboard.buyer.home',
            };

            return view($view);
        }

        /**
         * FALLBACK
         */
        abort(403);
    }
}
