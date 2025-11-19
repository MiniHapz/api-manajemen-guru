<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user(); // ambil user dari request

        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized. Admin only.'
            ], 403);
        }

        // Tambahkan sekolah_id ke request supaya controller bisa langsung pakai
        $request->merge([
            'user_sekolah_id' => $user->sekolah_id
        ]);

        return $next($request);
    }
}
