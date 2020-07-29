<?php
require_once 'config.php';
  

    $db = new DB();
    $loc_token = $db->get_access_token(); 

    $curl_post_url = "https://api.zoom.us/v2/users/me";
    $postdata = array();
    //$postdata = json_encode($array); //exit;

$ch = curl_init();

curl_setopt_array($ch, array(
  CURLOPT_URL => "https://api.zoom.us/v2/users/me",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => 1,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_SSL_VERIFYHOST=> 0,  
  CURLOPT_SSL_VERIFYPEER=> 0,
  CURLOPT_HTTPHEADER => array(
    "Authorization: Bearer ".$loc_token
  ),
));
 //curl_setopt ($ch, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");
$result = curl_exec($ch);
$err = curl_error($ch);

curl_close($ch);

/*if ($err) {
  echo "cURL Error #:" . $err;
} */

 $someArray = json_decode($result, true);
    echo "<pre>";
    print_r($someArray);

    //exit;

if(isset($someArray['code']) && $someArray['code'] == 124) {
    $refresh_token = $db->get_refersh_token();
    $curl_post_url = "https://zoom.us/oauth/token";
    $postdata = array(
        'grant_type' => 'refresh_token',
        'refresh_token' => $refresh_token,
    );
    //$postdata = json_encode($array); //exit;

    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt ($ch, CURLOPT_MAXREDIRS, 3);
    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_VERBOSE, 0);
    curl_setopt ($ch, CURLOPT_HEADER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt ($ch, CURLOPT_POST, true);
    curl_setopt ($ch, CURLOPT_HEADER, false);
    curl_setopt ($ch, CURLOPT_URL, $curl_post_url);
    curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic '.base64_encode(CLIENT_ID.':'.CLIENT_SECRET)));
    curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt ($ch, CURLOPT_TIMEOUT, 0);
    $result = curl_exec($ch);
    //echo $result; 
    curl_close($ch);
    $someArray = json_decode($result, true);

    echo "<pre>";
    print_r($someArray);

    $token = $someArray['access_token'];
    $refresh_token = $someArray['refresh_token'];
    $db->update_access_token($token, $refresh_token);
    exit;
}


    
    
