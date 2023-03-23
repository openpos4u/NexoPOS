<?php

namespace App\Http\Middleware;

use App\Exceptions\NotAllowedException;
use Closure;
use App\Services\Options;
use App\Http\Controllers\SetupController;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;

class NotInstalledStateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
    }
}
