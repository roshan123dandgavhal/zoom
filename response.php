<?php
require_once 'config.php';
  
try {


    $curl_post_url = "https://zoom.us/oauth/token";
    $postdata = array(
        'grant_type' => 'authorization_code',
        'code' => $_GET['code'],
        "redirect_uri" => REDIRECT_URI
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
    //echo "<pre>";
    //print_r($someArray);

    //$token = json_decode($result->getBody()->getContents(), true);
    //exit;
    $token = $someArray['access_token'];
    $refresh_token = $someArray['refresh_token'];

 
    $db = new DB();
 
    if($db->is_table_empty()) {
        $db->update_access_token($token, $refresh_token);
        echo "Access token inserted successfully.";
    }
} catch(Exception $e) {
    echo $e->getMessage();
}