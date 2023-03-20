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
