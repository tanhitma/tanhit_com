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
    private $callback = 'https://test-dotrox.tanhit.com/my-account';
    private $token = '';

    public function __construct() {
        $this->token = '3245430378.f641e06.cd94db8843f5463eb8320843c2b86ff7';
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
            return $remote;
        } catch (Exception $e){
            echo $e->getMessage();
        }
        return false;
    }

    private function makeRequest($endpoint){
        try {
            /*$r = (new InstagramRequest($this->instagram, $endpoint, ["access_token" => $this->token]))->getResponse();
            if($r->getMetaData()->code == 200){
                return $r->getData();
            } else {
                echo "Bad response code - {$r->getMetaData()->code}";
                return false;
            }*/
            $client = $this->instagram->getHttpClient(); //new GuzzleHttp\Client();
            $res = $client->request('GET', "{$this->api}{$endpoint}", ['query' => ['access_token' => $this->token]]);
            //$res = $client->get("{$this->api}{$endpoint}?access_token={$this->token}");
            //echo $res->getStatusCode();
            //print_r($res->getHeader('content-type'));

            //echo $res->getBody();
            //print_r((\GuzzleHttp\json_decode($res->getBody()))->data);
            if($res->getStatusCode() == 200){
                return (\GuzzleHttp\json_decode($res->getBody()))->data;
                //return \GuzzleHttp\json_decode($this->request($endpoint));
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

    private function request($path = '', $type = 'GET',  $data = [], $ctype = ''){
        if(!$ctype){
            $post = http_build_query($data);
        } else {
            $post = $data;
        }
        $url = "https://api.instagram.com/v1{$path}?access_token={$this->token}";

        if($type == 'POST'){
            $headers = [
                'Content-Type: ' . ($ctype ? $ctype : 'application/x-www-form-urlencoded'),
                'Connection: Keep-Alive',
                'Content-Length: ' . strlen($post)
            ];
        } else {
            $headers = [];
        }

        //$this->log($url);
        //$this->log($path);
        //$this->log($post);

        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_CUSTOMREQUEST, $type);
        //curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($c, CURLOPT_HTTPHEADER, $headers);
        //curl_setopt($c, CURLOPT_COOKIE, $this->cookies);
        //curl_setopt($c, CURLOPT_HEADER, 1);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLINFO_HEADER_OUT, 1);
        //curl_setopt($c, CURLOPT_VERBOSE, 1);

        //curl_setopt($c, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($c);
        echo $response;
        $body = mb_substr($response, curl_getinfo($c, CURLINFO_HEADER_SIZE));
        //echo PHP_EOL . '**********' . PHP_EOL;
        //print_r($body);
        //print_r(curl_errno($c));
        //print_r(curl_getinfo($c));
        curl_close($c);

        //$this->log('Resp: ');
        //$this->log($response);

        /*preg_match('/^Set-Cookie:\s*([^;]*)/im', $response, $cookies);
        //$this->log($cookies);
        if($cookies){
            $this->cookies = $cookies[1];
        }*/

        return (string)$response;

        /*preg_match('/Set-Cookie:(?<cookie>\s{0,}.*)$/im', $response, $cookies);
        $this->log($cookies);*/

        //$this->log(json_decode($response, true));
        //print_r(json_decode($response, true));
    }

    /*************************************/
    function scrape_instagram( $username ) {

        $username = strtolower( $username );
        $username = str_replace( '@', '', $username );

        if ( false === ( $instagram = get_transient( 'instagram-a5-'.sanitize_title_with_dashes( $username ) ) ) ) {

            //$remote = wp_remote_get( 'http://instagram.com/'.trim( $username ) );
            $remote = wp_remote_get( 'https://instagram.com/p/BR5k2LjhXfV/?__a=1' );

            if ( is_wp_error( $remote ) )
                return new WP_Error( 'site_down', esc_html__( 'Unable to communicate with Instagram.', 'wp-instagram-widget' ) );

            if ( 200 != wp_remote_retrieve_response_code( $remote ) )
                return new WP_Error( 'invalid_response', esc_html__( 'Instagram did not return a 200.', 'wp-instagram-widget' ) );

            $shards = explode( 'window._sharedData = ', $remote['body'] );
            $insta_json = explode( ';</script>', $shards[1] );
            $insta_array = json_decode( $insta_json[0], TRUE );

            if ( ! $insta_array )
                return new WP_Error( 'bad_json', esc_html__( 'Instagram has returned invalid data.', 'wp-instagram-widget' ) );

            if ( isset( $insta_array['entry_data']['ProfilePage'][0]['user']['media']['nodes'] ) ) {
                $images = $insta_array['entry_data']['ProfilePage'][0]['user']['media']['nodes'];
            } else {
                return new WP_Error( 'bad_json_2', esc_html__( 'Instagram has returned invalid data.', 'wp-instagram-widget' ) );
            }

            if ( ! is_array( $images ) )
                return new WP_Error( 'bad_array', esc_html__( 'Instagram has returned invalid data.', 'wp-instagram-widget' ) );

            $instagram = array();

            foreach ( $images as $image ) {

                $image['thumbnail_src'] = preg_replace( '/^https?\:/i', '', $image['thumbnail_src'] );
                $image['display_src'] = preg_replace( '/^https?\:/i', '', $image['display_src'] );

                // handle both types of CDN url
                if ( ( strpos( $image['thumbnail_src'], 's640x640' ) !== false ) ) {
                    $image['thumbnail'] = str_replace( 's640x640', 's160x160', $image['thumbnail_src'] );
                    $image['small'] = str_replace( 's640x640', 's320x320', $image['thumbnail_src'] );
                } else {
                    $urlparts = wp_parse_url( $image['thumbnail_src'] );
                    $pathparts = explode( '/', $urlparts['path'] );
                    array_splice( $pathparts, 3, 0, array( 's160x160' ) );
                    $image['thumbnail'] = '//' . $urlparts['host'] . implode( '/', $pathparts );
                    $pathparts[3] = 's320x320';
                    $image['small'] = '//' . $urlparts['host'] . implode( '/', $pathparts );
                }

                $image['large'] = $image['thumbnail_src'];

                if ( $image['is_video'] == true ) {
                    $type = 'video';
                } else {
                    $type = 'image';
                }

                $caption = __( 'Instagram Image', 'wp-instagram-widget' );
                if ( ! empty( $image['caption'] ) ) {
                    $caption = $image['caption'];
                }

                $instagram[] = array(
                    'description'   => $caption,
                    'link'		  	=> trailingslashit( '//instagram.com/p/' . $image['code'] ),
                    'time'		  	=> $image['date'],
                    'comments'	  	=> $image['comments'],//['count'],
                    'likes'		 	=> $image['likes']['count'],
                    'thumbnail'	 	=> $image['thumbnail'],
                    'small'			=> $image['small'],
                    'large'			=> $image['large'],
                    'original'		=> $image['display_src'],
                    'type'		  	=> $type
                );
            }

            // do not set an empty transient - should help catch private or empty accounts
            /*if ( ! empty( $instagram ) ) {
                $instagram = base64_encode( serialize( $instagram ) );
                set_transient( 'instagram-a5-'.sanitize_title_with_dashes( $username ), $instagram, apply_filters( 'null_instagram_cache_time', HOUR_IN_SECONDS*2 ) );
            }*/
        }

        if ( ! empty( $instagram ) ) {
            return $instagram;
        } else {
            return new WP_Error( 'no_images', esc_html__( 'Instagram did not return any images.', 'wp-instagram-widget' ) );
        }
    }

}