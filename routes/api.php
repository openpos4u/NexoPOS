<?php

use App\Events\BeforeStartApiRouteEvent;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Middleware\checkTenantMiddleware;
use Illuminate\Support\Facades\Route;

use App\Http\Middleware\ClearRequestCacheMiddleware;
use App\Http\Middleware\InstalledStateMiddleware;
use Illuminate\Routing\Middleware\SubstituteBindings;
use App\Http\Controllers\TenantController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

$domain = pathinfo( env( 'APP_URL' ) );

/**
 * If something has to happen
 * before the web routes are saved
 * this will be performmed here.
 */
BeforeStartApiRouteEvent::dispatch();

/**
 * By default, wildcard is disabled
 * on the system. In order to enable it, the user
 * will have to follow these instructions https://my.nexopos.com/en/documentation/wildcards
 */
if ( env( 'NS_WILDCARD_ENABLED' ) ) {
    /**
     * The defined route should only be applicable
     * to the main domain.
     */
    $domainString = ( $domain[ 'filename' ] ?: 'localhost' ) . ( isset( $domain[ 'extension' ] ) ? '.' . $domain[ 'extension' ] : '' );

    Route::domain( $domainString )->group( function() {
        include dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'api-base.php';
    });
} else {
    Route::middleware([
        InstalledStateMiddleware::class,
        SubstituteBindings::class,
        ClearRequestCacheMiddleware::class,
    ])->group( function() {
        Route::middleware([
            'auth:sanctum',
        ])->group( function() {
            Route::post('/create-tenant',[TenantController::class,'create']);
            Route::get('/list-tenant',[TenantController::class,'list']);
        });
    });

    Route::middleware([
        'api',
        'universal',
        InitializeTenancyByDomain::class,
    ])->group(function () {
        include dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'api-base.php';
    });
}
