<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        error_log('Q-MATE REQUEST START');

        error_log(
            'Q-MATE REQUEST: ' .
            $request->method() .
            ' ' .
            $request->path()
        );

        try {
            if ($request->hasSession() && session()->has('locale')) {
                app()->setLocale(session('locale'));
            }

            error_log('Q-MATE REQUEST BEFORE NEXT');

            $response = $next($request);

            error_log('Q-MATE REQUEST AFTER NEXT');

            return $response;

        } catch (\Throwable $e) {
            error_log(
                'Q-MATE MIDDLEWARE ERROR: ' .
                get_class($e) .
                ' | ' .
                $e->getMessage() .
                ' | FILE: ' .
                $e->getFile() .
                ' | LINE: ' .
                $e->getLine()
            );

            throw $e;
        }
    }
}