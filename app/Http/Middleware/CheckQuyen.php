<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckQuyen
{
    public function handle(Request $request, Closure $next, string $vaiTro)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $userVaiTro = $request->user()->vaiTro->ten_vai_tro ?? null;

        if (!$userVaiTro) {
            return response()->json([
                'message' => 'User has no role assigned',
                'required_role' => $vaiTro
            ], 403);
        }

        if ($userVaiTro !== $vaiTro) {
            return response()->json([
                'message' => 'Forbidden',
                'required_role' => $vaiTro,
                'your_role' => $userVaiTro
            ], 403);
        }

        return $next($request);
    }
}
