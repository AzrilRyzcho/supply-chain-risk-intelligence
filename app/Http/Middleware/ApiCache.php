<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiCache
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya tambahkan cache control pada method GET
        if ($request->isMethod('GET')) {
            $response->headers->set('Cache-Control', 'max-age=60, private');
        }

        return $response;
    }
}
