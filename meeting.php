<?php
require_once 'config.php';

// $loc_token = $db->get_access_token(); 


class ZoomApi extends DB{
	public static $loc_token; 
	//public static $db; 

	 function __construct(){
		$db = new DB();
		self::$loc_token = $db->get_access_token(); 
	}

	/*
	* Refresh Token
	*/
	public static function refreshToken() {
		$db = new DB();
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

	    //echo "<pre>";
	    //print_r($someArray); exit();

	    $token = $someArray['access_token'];
	    $refresh_token = $someArray['refresh_token'];
	    $db->update_access_token($token, $refresh_token);

	    return 1;
	    //exit;
	}

	/*
	* Get User Profile
	*/
	public static function getProfile() {
		$curl_post_url = "https://api.zoom.us/v2/users/me";
		$postdata	= array();
		$ch			= curl_init();

		curl_setopt_array($ch, 
			array(CURLOPT_URL => $curl_post_url,
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
					"Authorization: Bearer ".self::$loc_token
				),
			)
		);
		$result	= curl_exec($ch);
		$err	= curl_error($ch);
		curl_close($ch);

		$someArray = json_decode($result, true);

		//echo __FUNCTION__;
		//echo __CLASS__.'::'.__FUNCTION__; exit;
		if(isset($someArray['code']) && $someArray['code'] == 124) {
			self::refreshToken();
			call_user_func(__CLASS__.'::'.__FUNCTION__);
		} else {
			echo "<pre>";
			print_r($someArray);
		}
		
		exit();
	}

	/*
	* Create Meeting
	*/
	public static function createMeeting() {
		$curl_post_url = "https://api.zoom.us/v2/users/me/meetings";
	    $array = array(
	        "topic" => "Anula Function",
	        "type" => 2,
	        "start_time" => "2020-08-05T20:30:00",
	        "duration" => "30", // 30 mins
	        "password" => "123456",
	        "timezone" => 'Asia/Kolkata'
	    );
	    $postdata = json_encode($array); //exit;

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
	    curl_setopt ($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer ".self::$loc_token,'Content-Type: application/json'));
	    curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata);
	    curl_setopt ($ch, CURLOPT_TIMEOUT, 0);
	    $result = curl_exec($ch);
	    //echo $result; 
	    curl_close($ch);
	    $someArray = json_decode($result, true);
		
		if(isset($someArray['code']) && $someArray['code'] == 124) {
			self::refreshToken();
			call_user_func(__CLASS__.'::'.__FUNCTION__);
		} else {
			echo "<pre>";
			print_r($someArray);
		}
		
		exit();
	}

	/*
	* Get Meeting List
	*/
	public static function getMeetingList() {
		$userEmail = "suryajhealthcare@gmail.com";
		$curl_post_url = "https://api.zoom.us/v2/users/".$userEmail."/meetings";
		$postdata	= array();
		$ch			= curl_init();

		curl_setopt_array($ch, 
			array(CURLOPT_URL => $curl_post_url,
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
					"Authorization: Bearer ".self::$loc_token
				),
			)
		);
		$result	= curl_exec($ch);
		$err	= curl_error($ch);
		curl_close($ch);

		$someArray = json_decode($result, true);

		if(isset($someArray['code']) && $someArray['code'] == 124) {
			self::refreshToken();
			call_user_func(__CLASS__.'::'.__FUNCTION__);
		} else {
			echo "<pre>";
			print_r($someArray);
		}
		
		exit();
	}

	/*
	* Update Meeting
	*/
	public static function updateMeeting($array = array(), $loc_meeting_id) {
		$curl_post_url = "https://api.zoom.us/v2/meetings/$loc_meeting_id";

		if(count($array) == 0) {
			$array = array(
		        "topic" => "Anula Function",
		        "type" => 2,
		        "start_time" => "2020-08-05T20:30:00",
		        "duration" => "30", // 30 mins
		        "password" => "123456",
		        "timezone" => 'Asia/Kolkata'
	    	);
		}
	    
	    $postdata = json_encode($array); //exit;

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
	    curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
	    curl_setopt ($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer ".self::$loc_token,'Content-Type: application/json'));
	    curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata);
	    curl_setopt ($ch, CURLOPT_TIMEOUT, 0);
	    $result = curl_exec($ch);
	    //echo $result; 
	    curl_close($ch);
	    $someArray = json_decode($result, true);
		
		if(isset($someArray['code']) && $someArray['code'] == 124) {
			self::refreshToken();
			call_user_func(__CLASS__.'::'.__FUNCTION__);
		} else {
			//echo "<pre>";
			//print_r($someArray);
			return $someArray;
		}
		
		//exit();
	}

	/*
	* Delete Meeting List
	*/
	public static function deleteMeeting($loc_meeting_id) {
		$userEmail = "suryajhealthcare@gmail.com";
		$curl_post_url = "https://api.zoom.us/v2/meetings/$loc_meeting_id";
		$postdata	= array();
		$ch			= curl_init();

		curl_setopt_array($ch, 
			array(CURLOPT_URL => $curl_post_url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => 1,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "DELETE",
				CURLOPT_SSL_VERIFYHOST=> 0,  
				CURLOPT_SSL_VERIFYPEER=> 0,
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer ".self::$loc_token
				),
			)
		);
		$result	= curl_exec($ch);
		$err	= curl_error($ch);
		curl_close($ch);

		$someArray = json_decode($result, true);

		if(isset($someArray['code']) && $someArray['code'] == 124) {
			self::refreshToken();
			call_user_func(__CLASS__.'::'.__FUNCTION__);
		} else {
			echo "<pre>";
			print_r($someArray);
		}
		
		exit();
	}
}

new ZoomApi();
if (isset($_GET['callbackaction']) && $_GET['callbackaction'] == "refreshToken") { ZoomApi::refreshToken(); } 
if (isset($_GET['callbackaction']) && $_GET['callbackaction'] == "getProfile") { ZoomApi::getProfile(); } 
if (isset($_GET['callbackaction']) && $_GET['callbackaction'] == "createMeeting") { ZoomApi::createMeeting(); } 
if (isset($_GET['callbackaction']) && $_GET['callbackaction'] == "getMeetingList") { ZoomApi::getMeetingList(); }

$updatearray = array(
		        "topic" => "Wellness Activity UPDATED",
		        "type" => 2,
		        "start_time" => "2020-08-05T20:30:00",
		        "duration" => "30", // 30 mins
		        "password" => "123456",
		        "timezone" => 'Asia/Kolkata'
	    	);

if (isset($_GET['callbackaction']) && $_GET['callbackaction'] == "updateMeeting") { ZoomApi::updateMeeting($updatearray,"86215597499"); } 
if (isset($_GET['callbackaction']) && $_GET['callbackaction'] == "deleteMeeting") { ZoomApi::deleteMeeting("123456"); } 