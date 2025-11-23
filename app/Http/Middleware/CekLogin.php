<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CekLogin
{
    public function handle(Request $request, Closure $next, $role = null)
    {
        // ❗ Cek login dulu
        if (!session()->has('username')) {
            return redirect()->route('login')->with('error', 'Silakan login dulu!');
        }

        // ❗ Jika route membutuhkan role tertentu
        if ($role !== null) {

            // Kalau role session tidak sama → TOLAK
            if (session('role') !== $role) {
                return redirect()->route('home')
                    ->with('error', 'Anda tidak punya akses ke halaman ini!');
            }
        }

        return $next($request);
    }
}
