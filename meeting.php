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

	    if(isset($someArray['access_token'])) {

	    	$token = $someArray['access_token'];
		    $refresh_token = $someArray['refresh_token'];
		    $db->update_access_token($token, $refresh_token);
		    self::$loc_token = $token;

		    return 1;
	    } else {

	    	return 0;
	    }
	    echo "<pre>";
	   print_r($someArray); exit();

	    
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
		$userEmail is Registered email id for zoom account
	*/
	public static function getMeetingList($userEmail) {
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


	/*
	* Get Meeting Participants
		Meeting id Generated
	*/
	public static function getMeetingParticipants($loc_meeting_id) {
		$ch = curl_init();

		curl_setopt_array($ch, array(
		CURLOPT_URL => "https://api.zoom.us/v2/past_meetings/$loc_meeting_id/participants?page_size=30&type=past",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_SSL_VERIFYHOST=> 0,  
		CURLOPT_SSL_VERIFYPEER=> 0,
		CURLOPT_HTTPHEADER => array(
		"Authorization: Bearer ".self::$loc_token
		),
		));

		$result	= curl_exec($ch);
		$err	= curl_error($ch);
		curl_close($ch);
		echo $err;
		$someArray = json_decode($result, true);
		echo "<pre>";
			print_r($someArray);
			exit;

		if(isset($someArray['code']) && $someArray['code'] == 124) {
			self::refreshToken();
			call_user_func(__CLASS__.'::'.__FUNCTION__);
		} else {
			echo "<pre>";
			print_r($someArray);
		}
		
		exit();
	}


	//UUID Generated after End of the meeting
	public static function getMeetingDetails($meetingUUID)
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.zoom.us/v2/past_meetings/$meetingUUID",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_SSL_VERIFYHOST=> 0,  
		  CURLOPT_SSL_VERIFYPEER=> 0,
		  CURLOPT_HTTPHEADER => array(
		    "Authorization: Bearer ".self::$loc_token
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		echo $err;

		$someArray = json_decode($response, true);
		echo "<pre>";
		print_r($someArray);
		exit;

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  echo $response;
		}
	}
/*
		public static function getMeetingParticipantsDetails($loc_meeting_id) {
		$loc_meeting_id = '87963008857';

		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.zoom.us/v2/metrics/meetings/87963008857/participants?page_size=30&type=live",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_SSL_VERIFYHOST=> 0,  
		  CURLOPT_SSL_VERIFYPEER=> 0,
		  CURLOPT_HTTPHEADER => array(
		    "Authorization: Bearer ".self::$loc_token
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  echo $response;
		}
	}*/


}

new ZoomApi();
if (isset($_GET['callbackaction']) && $_GET['callbackaction'] == "refreshToken") { ZoomApi::refreshToken(); } 
if (isset($_GET['callbackaction']) && $_GET['callbackaction'] == "getProfile") { ZoomApi::getProfile(); } 
if (isset($_GET['callbackaction']) && $_GET['callbackaction'] == "createMeeting") { ZoomApi::createMeeting(); } 
if (isset($_GET['callbackaction']) && $_GET['callbackaction'] == "getMeetingList") { ZoomApi::getMeetingList("suryajhealthcare@gmail.com"); }

$updatearray = array(
		        "topic" => "UPDATED Meeting Topic",
		        "type" => 2,
		        "start_time" => "2020-08-05T20:30:00", //updates date time
		        "duration" => "30", // 30 mins
		        "password" => "123456", //updated password
		        "timezone" => 'Asia/Kolkata'
	    	);

if (isset($_GET['callbackaction']) && $_GET['callbackaction'] == "updateMeeting") { ZoomApi::updateMeeting($updatearray,"86215597499"); } 
if (isset($_GET['callbackaction']) && $_GET['callbackaction'] == "deleteMeeting") { ZoomApi::deleteMeeting("123456"); } 
if (isset($_GET['callbackaction']) && $_GET['callbackaction'] == "getMeetingParticipants") { ZoomApi::getMeetingParticipants("87963008857"); }
if (isset($_GET['callbackaction']) && $_GET['callbackaction'] == "getMeetingDetails") { ZoomApi::getMeetingDetails("NJRvUmEcSxqIwb0BGJLyTQ=="); }
// if (isset($_GET['callbackaction']) && $_GET['callbackaction'] == "getMeetingParticipantsDetails") { ZoomApi::getMeetingParticipantsDetails("87963008857"); }
