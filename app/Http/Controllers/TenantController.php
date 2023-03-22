<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Domain;
use App\Exceptions\NotAllowedException;
use App\Http\Requests\TenantValidationRequest;
class TenantController extends Controller
{

    public function create(Request $request)
    {
        if(Tenant::where('id',$request->id)->orwhere('session_domain',$request->session_domain)->orwhere('app_url',$request->app_url)->exists())
        {
            return response()->json(['result'=>"Failed",
                                    "session_domain"=>"Already Exists or Required",
                                    "app_url" => "Already Exists or Required",
                                    "id" => "Already Exists or Required",
                                ], 500);
        }
        try{
            $tenant1 = Tenant::create(['id' => $request->id,'session_domain'=>$request->session_domain,'app_url'=>$request->app_url]);
            $tenant1->domains()->create(['domain' => $request->session_domain]);
        }catch(Exception $e){
            // throw new NotAllowedException( __( 'Something Unusual Occur' ) );
            return response()->json(['result'=>"Failed"], 500);
        }
        return response()->json(['result'=>$tenant1], 200);
    }

    public function list(Request $request)
    {


        $data = Tenant::all();
        return response()->json($data, 200);
    }

}
