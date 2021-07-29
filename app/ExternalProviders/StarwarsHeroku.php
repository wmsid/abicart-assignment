<?php
namespace App\ExternalProviders;

class StarwarsHeroku {

    private $endPoint;
    
    function __construct(){
        $this->endPoint="https://star-wars-api.herokuapp.com/films";
    }

    public function verifyIdentifierWithProvider($identifier){
        $fetch=$this->getData($identifier);
        if($fetch){
            return true;
        }else{
            return false;
        }
    }

    public function getData($identifier){
        try{
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $this->endPoint."/{$identifier}",['verify' => false]);
            $content = $response->getBody();
            if($response->getStatusCode()=="200"){
                return $response;
            }
        }catch(\Exception $e){
            return false;
        }
    }

    public function getMovieDetails($identifier,$provider_id){
        $fetch=$this->getData($identifier);
        if($fetch){
            $data=json_decode($fetch->getBody())->fields;
            $arr["provider"]=$provider_id;
            $arr["provider_identifier"]=$identifier;
            $arr["title"]=$data->title;
            $arr["release_date"]=$data->release_date;
            return $arr;
        }else{
            return false;
        }
    }
}