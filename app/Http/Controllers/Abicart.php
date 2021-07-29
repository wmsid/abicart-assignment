<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use App\Models\Provider;
use App\ExternalProviders\StarwarsHeroku;
use Illuminate\Support\Facades\DB;


class Abicart extends Controller
{
    //
    public function subscribe(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'providerAlias' => 'required',
            'providerIdentifier' => 'required'
        ]);

        if ($validator->fails()) {
            $output["status"]=0;
            $output["message"]="Request not validated. Refer documentation";
        }else{
            $providerRow= Provider::where('alias',$request->providerAlias)->first();
            
            //Check if already not subscribed
            $current=Subscription::where('user_id',"=",$request->user_id)
            ->where('provider_id',"=",$providerRow->id)
            ->where('provider_identifier',"=",$request->providerIdentifier)
            ->where('status',"=",'1')
            ->first();

            if($current){
                $output["status"]="0";
                $output["message"]="Already Subscribed";
                return $output;
            }

            $class='App\\ExternalProviders\\'.$providerRow->className;
            $provider=new $class;

            //Check correct Identifer
            if($provider->verifyIdentifierWithProvider($request->providerIdentifier)){

                //Insert or Update
                $sub=Subscription::firstOrNew([
                    'user_id' => $request->user_id,
                    'provider_id' => $providerRow->id,
                    'provider_identifier' => $request->providerIdentifier,
                ]);

                $sub->status=1;
                $sub->save();

                //Maintain History
                SubscriptionHistory::create([
                    'user_id' => $request->user_id,
                    'provider_id' => $providerRow->id,
                    'provider_identifier' => $request->providerIdentifier,
                    'status_to' => '1',
                    'expire_at' => date('Y-m-d H:i:s'),
                    'action_at' => date('Y-m-d H:i:s', strtotime("+30 days"))
                ]);

                $output["status"]=1;
                $output["expiry"]=date('Y-m-d H:i:s', strtotime("+30 days"));
                $output["message"]="Subscription Success";
            }else{
                $output["status"]=0;
                $output["message"]="Cannot validate the movie with Provider. Please try again.";
                return $output;
            }
        }
        return $output;
    }

    public function unsubscribe(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'providerAlias' => 'required',
            'providerIdentifier' => 'required'
        ]);

        if ($validator->fails()) {
            $output["status"]=0;
            $output["message"]="Request not validated. Refer documentation";
        }else{
            
            $providerRow= Provider::where('alias',$request->providerAlias)->first();

            //Check if already not unsubscribed
            $current=Subscription::where('user_id',"=",$request->user_id)
                ->where('provider_id',"=",$providerRow->id)
                ->where('provider_identifier',"=",$request->providerIdentifier)
                ->first();

            if($current === null || $current->status==0){
                $output["status"]="0";
                $output["message"]="Already was not subscribed";
                return $output;
            }
            
            Subscription::where('user_id',"=",$request->user_id)
            ->where('provider_id',"=",$providerRow->id)
            ->where('provider_identifier',"=",$request->providerIdentifier)
            ->update(['status' => 0]);

            SubscriptionHistory::create([
                'user_id' => $request->user_id,
                'provider_id' => $providerRow->id,
                'provider_identifier' => $request->providerIdentifier,
                'status_to' => '0',
                'expire_at' => date('Y-m-d H:i:s'),
                'action_at' => date('Y-m-d H:i:s', strtotime("+30 days"))
            ]);

            $output["status"]=1;
            $output["message"]="Un Subscribe Success";            
        }
        return $output;
    }

    public function viewActiveSubscriptions(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            $output["status"]=0;
            $output["message"]="Request not validated. Refer documentation";
        }else{
            $validSubs=Subscription::where('user_id','=',$request->user_id)->where('status','=','1')->cursor();
            $movies=array();
            foreach($validSubs as $validSub){
                $class='App\\ExternalProviders\\'.$validSub->provider->className;
                $provider=new $class;
                array_push($movies,$provider->getMovieDetails($validSub->provider_identifier,$validSub->provider_id));
            }
            $output["status"]=1;
            $output["movies"]=$movies;	
        }
        return $output;
    }
}
