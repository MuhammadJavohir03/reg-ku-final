<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CheckRole
{
    
    public function handle(Request $request, Closure $next, string $role): Response
    {

        if (!Auth::check() || Auth::user()->role !== $role) {
            return redirect('/')->with('error', "Sizda bu sahifa uchun ruxsat yo'q.");
        }

        return $next($request);
    }
}
