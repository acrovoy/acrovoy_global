<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Facades\ActiveContext;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
       

        return $next($request);
    }
}