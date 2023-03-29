<?php

namespace App\Http\Middleware;

use App\Events\InstalledStateBeforeCheckedEvent;
use App\Models\Migration;
use App\Services\Options;
use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\SetupController;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Exceptions\NotAllowedException;
use App\Services\Setup;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Illuminate\Support\Str;
class InstalledStateMiddleware
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
        $domain = Str::replaceFirst( 'http://', '', env('APP_URL') );
        $domain = Str::replaceFirst( 'https://', '', $domain );

        if ($request->getHost() != $domain) {
            if(!tenant('id')){
                InitializeTenancyByDomain::$onFail = function ($exception, $request, $next) {
                    throw new NotAllowedException( __( 'Something Unusual Occur' ) );
                    return redirect(env('APP_URL').'/sign-in');
                };
            }
        }

        InstalledStateBeforeCheckedEvent::dispatch( $next, $request );
        if ( ns()->installed()) {
            ns()->update
            ->getMigrations()
            ->each( function( $file ) {
                $migration = Migration::where( 'migration', $file )->first();
                if ( ! $migration instanceof Migration ) {
                    $migration = new Migration;
                    $migration->migration = $file;
                    $migration->batch = 0;
                    $migration->save();
                }
            });
            return $next($request);
        }



        throw new NotAllowedException( __( 'Something Unusual Occur' ) );
    }
}
