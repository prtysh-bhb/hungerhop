<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiResponseMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Ensure JSON response for API routes
        if ($request->is('api/*') && ! $response instanceof JsonResponse) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid response format',
                'data' => null,
            ], 500);
        }

        // Add JSON headers
        if ($response instanceof JsonResponse) {
            $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }
}
