<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PDO;
use Curl\Curl;

Class MainController extends Controller {

    private $clientId= "83c7fac2a0c64eeab920b8bb3f0ec9e2";
    private $clientSecret = "1bee9939614847f2b83df700648ad0d3";
    private $redirectUri = "http://localhost:8000/api/callback";
    private $stateKey = 'spotify_auth_state';

    function showHome(Request $request){

        $username = $request->session()->get('username');
        return view("home", ["username"=>$username, "error"=>""]);
    }

    function logout(){

    }
    function spotifyAuthenticate(Request $request){

        $state = $this->generateRandomString(16);
        $request->session()->set($this->stateKey, $state);

        $scope = 'user-read-private user-read-email';

        $queryString = http_build_query([
                "response_type" => 'code',
                "client_id" => $this->clientId,
                "scope" => $scope,
                "redirect_uri" => $this->redirectUri,
                "state" => $state
            ]);

        return redirect("https://accounts.spotify.com/authorize?" . $queryString);

    }

    function spotifyCallback(Request $request){

        // Get request info
        $code = $request->input("code", null);
        $state = $request->input("state", null);

        // Verify state between session and request
        $storedState = $request->session()->get($this->stateKey);
        if ($state == null || $state != $storedState) {
            return view("home", ["error"=>"state_mismatch"]);
        } 
            
        
        try {

            // Get access token

            $clientInfo = base64_encode($this->clientId . ':' . $this->clientSecret);
            $headers = [
                "Authorization" => "Basic " . $clientInfo,
                "Content-Type" =>       "application/json"
            ];
            $formFields = [
                "code"=>$code,
                "redirect_uri" => $this->redirectUri,
                "grant_type" => 'authorization_code'
                ];

            $curl = new Curl();
            $curl->setHeader("Authorization", "Basic " . $clientInfo);
            $curl->setHeader("Content-Type", "application/x-www-form-urlencoded");
            $curl->post("https://accounts.spotify.com/api/token", array(
                "code"=>$code,
                "redirect_uri" => $this->redirectUri,
                "grant_type" => 'authorization_code'));

            
            if ($curl->error) {
                echo $curl->error_code;
            }
            else {

                $json = json_decode($curl->response);
                $access_token = $json->access_token;
                $refresh_token = $json->refresh_token;

                $curl = new Curl();
                $curl->setHeader("Authorization", "Bearer " . $access_token);
                $curl->setHeader("Content-Type", "text/json");
                $curl->get("https://api.spotify.com/v1/me");
                
                $json = json_decode($curl->response);

                // User is authenticated
                $request->session()->set('accessToken', $access_token);
                $request->session()->set('username', $json->id);
                $request->session()->set('userInfo', $json);

                return redirect('/');
                
            }

        } catch (Exception $ex) {
            echo $ex;
        }

    }

    function spotifyPlaylist(Request $request){

    }

    function spotifyCategories(Request $request, Response $response){

        $url = "https://api.spotify.com/v1/browse/categories";

        $access_token = $request->session()->get('accessToken');

        $curl = new Curl();
        $curl->setHeader("Authorization", "Bearer " . $access_token);
        $curl->setHeader("Content-Type", "text/json");
        $curl->get($url);
        
        $json = json_decode($curl->response);

        return response()->json($json);

    }

    function generateRandomString($length){
        $text = "";
        $possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        for ($i = 0; $i < $length; $i++) {
            $text .=  $possible[rand(0, strlen($possible) - 1)];
        }
        return $text;
    }

}