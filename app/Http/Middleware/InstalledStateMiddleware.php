<?php

namespace App\Http\Middleware;

use App\Events\InstalledStateBeforeCheckedEvent;
use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\SetupController;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use App\Exceptions\NotAllowedException;

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

        if ( ns()->installed() ) {
            
            return $next($request);
        }
        if (isset($_SERVER['HTTP_HOST'])) {
            $hostArray = explode('.', $_SERVER['HTTP_HOST']);
            //if the address is a subdomain and exist the .xxx.env file
            $envFile = sprintf('.%s.env', $hostArray[0]);
            
            if (count($hostArray) >=  2 ) {
                    $client = new \GuzzleHttp\Client();
                    $res = $client->get('https://us-central1-ishipd-prod.cloudfunctions.net/pos-status', ['domain' => $hostArray[0].".ferrypalpos.com"]);
                    // $response = json_decode($res->getBody());
                    
                    if($res->getStatusCode() != 200)
                    {
                        throw new NotAllowedException( __( 'You\'re not allowed to see this page.' ) );
                    }else{
                        if(json_decode($res->getBody())->enabled){
                            
                            $res= $client->get('https://us-central1-ishipd-prod.cloudfunctions.net/pos-config?domain='.$hostArray[0].'.ferrypalpos.com');
                            
                            
                            $setup = new SetupController();
                            $request1 = new \Illuminate\Http\Request([
                                'DB_HOST' => 'localhost',
                                'DB_DATABASE'=> json_decode($res->getBody())->dbname,
                                'DB_USERNAME'=> json_decode($res->getBody())->username,
                                'DB_PASSWORD'=> json_decode($res->getBody())->password,
                                'DB_PREFIX'=> 'ns_',
                                'DB_PORT'=> '3306',
                                'DB_CONNECTION'=> 'mysql',
                                'database_driver' => 'mysql',
                                'hostname' => 'localhost',
                                'database_port' => 3306,
                                'database_name' => json_decode($res->getBody())->dbname,
                                'username' => json_decode($res->getBody())->username,
                                'password' => json_decode($res->getBody())->password,
                                'database_prefix' => 'ns_'
                                
                            ]);
                            
                        
                            $setup->checkDatabase($request1);
                            
                            // dd("hello");
                            return redirect()->route('ns.login');                    
        
                        }
                        else{
                            throw new NotAllowedException( __( 'You\'re not allowed to see this page.' ) );
                        }
                    }  
            }
        }
        
        return redirect()->route( 'ns.do-setup' );
    }
}
