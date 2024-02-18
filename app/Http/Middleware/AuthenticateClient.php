<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Client;

class AuthenticateClient
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $client = Client::where('api_key', $request->header('x-api-key'))
        ->where('api_secret', $request->header('x-api-secret'))
        ->first();

        if (!$client) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->attributes->add(['client_id' => $client->id]);

        return $next($request);
    }
}
