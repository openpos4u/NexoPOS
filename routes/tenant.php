<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Middleware\checkTenantMiddleware;
use App\Events\BeforeStartWebRouteEvent;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    InitializeTenancyByDomain::$onFail = function ($exception, $request, $next) {
        return redirect(env('APP_URL'));
    };

    include dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'web-base.php';


});
