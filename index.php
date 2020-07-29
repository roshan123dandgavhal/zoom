<?php
require_once 'config.php';

$db = new DB();
if($db->is_table_empty()) {
	$url = "https://zoom.us/oauth/authorize?response_type=code&client_id=".CLIENT_ID."&redirect_uri=".REDIRECT_URI;
?>
<a href="<?php echo $url; ?>">Login with Zoom</a>
<?php	 
} else { ?>
	<a href="meeting.php?callbackaction=getProfile" target="_blank">Get My Profile</a><br/>
	<a href="meeting.php?callbackaction=createMeeting" target="_blank">Create Meeting</a></br/>
	<a href="meeting.php?callbackaction=getMeetingList" target="_blank">Get List Of Meeting</a></br>
<?php }
?>