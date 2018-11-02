<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/message.php"); ?>

<?php 
	
	$id_fb_user 	= $user['id'];
	$code 			= $_GET['keyword_code'];
	$phone 			= $_GET['phone'];

	$message 		= new message();

	$msg 			= "The share id is $code";

	$message->send_SMS_with_phone($phone, $msg);

	echo "success";