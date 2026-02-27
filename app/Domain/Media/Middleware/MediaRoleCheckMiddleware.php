<?php

namespace App\Domain\Media\Middleware;

use Closure;
use Illuminate\Http\Request;

class MediaRoleCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * B2B marketplace security layer.
     * Allow media operations only for authorized roles.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        /**
         * Example RBAC logic for ACROVOY marketplace.
         *
         * Modify roles according to your platform policy.
         */

        $allowedRoles = [];

        if ($user->hasRole('admin')) {
            return $next($request);
        }

        if ($user->hasRole('supplier')) {
            $allowedRoles = [
                'supplier_gallery',
                'supplier_certificate',
                'supplier_factory_media'
            ];
        }

        $collection = $request->input('collection');

        if ($collection && !in_array($collection, $allowedRoles)) {
            abort(403, 'Media upload not allowed for this role or collection');
        }

        return $next($request);
    }
}