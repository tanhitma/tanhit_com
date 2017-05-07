<?php
use Haridarshan\Instagram\Instagram;
use Haridarshan\Instagram\InstagramRequest;
use Haridarshan\Instagram\InstagramResponse;
use Haridarshan\Instagram\Exceptions\InstagramOAuthException;
use Haridarshan\Instagram\Exceptions\InstagramResponseException;
use Haridarshan\Instagram\Exceptions\InstagramServerException;

class InstagramParser {
    private $api = 'https://api.instagram.com/v1';
    private $client_id = 'f641e06fccec412fa9847bb362d33812';
    private $client_secret = '9e93873be8834c24a4353a49a0039c3e';
    private $callback = 'https://tanhit.com/my-account';
    private $token = '3245430378.f641e06.ee1f38064b00429982186c1233a4f730';

    public function __construct() {
        //$this->token = '3245430378.f641e06.cd94db8843f5463eb8320843c2b86ff7';
        $this->instagram = new Instagram([
            "ClientId"     => $this->client_id,
            "ClientSecret" => $this->client_secret,
            "Callback"     => $this->callback
        ]);
    }

    public function auth(){
        $scope = [
            "basic",
            "likes",
            "public_content",
            "follower_list",
            "comments",
            "relationships"
        ];

        // To get the Instagram Login Url
        $insta_url = $this->instagram->getLoginUrl(["scope" => $scope]);
        echo "<a href='{$insta_url}'>Login with Instagram</a>";

        try {
            if(isset($_GET['code'])){
                $oauth = $this->instagram->oauth($_GET['code']);
                // To get User's Access Token
                $insta_access_token = $oauth->getAccessToken();
                // To get User's Info Token
                $user_info = $oauth->getUserInfo();
                echo 'Token: ' . $insta_access_token . PHP_EOL;
                print_r($user_info);
            }
        } catch (InstagramOAuthException $e) {
            echo $e->getMessage();
        } catch (Exception $e){
            echo $e->getMessage();
        }
    }

    public function getUser(){
        $data = $this->makeRequest('/users/self');
        return $data;
    }

    public function getRecent(){
        $data = $this->makeRequest('/users/self/media/recent');
        return $data;
    }

    public function getComments($id){
        try{
            $remote = json_decode(file_get_contents("https://www.instagram.com/p/{$id}/?__a=1"));
            return $remote->graphql->shortcode_media->edge_media_to_comment->edges;
        } catch (Exception $e){
            echo $e->getMessage();
        }
        return false;
    }

    private function makeRequest($endpoint){
        try {
            $client = $this->instagram->getHttpClient(); //new GuzzleHttp\Client();
            $res = $client->request('GET', "{$this->api}{$endpoint}", ['query' => ['access_token' => $this->token]]);

            if($res->getStatusCode() == 200){
                return (\GuzzleHttp\json_decode($res->getBody()))->data;
            } else {
                echo "Bad response code - {$res->getStatusCode()}";
                return false;
            }
        } catch (InstagramResponseException $e) {
            echo $e->getMessage();
        } catch (InstagramServerException $e) {
            echo $e->getMessage();
        } catch (Exception $e){
            echo $e->getMessage();
        }
        return false;
    }
}