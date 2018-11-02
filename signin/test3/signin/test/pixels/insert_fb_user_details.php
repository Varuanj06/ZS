<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>=
<?php require_once("../classes/fb_user_details.php"); ?>

<?php 
	
	$email 				= str_replace(' ', '', $_GET['email']);
	$mobile_number 		= $_GET['mobile_number'];
	$id_fb_user 		= $user['id'];

	$fb_user_details 	= new fb_user_details();

	$fb_user_details->set_id_fb_user($id_fb_user);
	$fb_user_details->set_email($email);
	$fb_user_details->set_mobile_number($mobile_number);
	
	if($fb_user_details->exists() == false){
		if(!$fb_user_details->insert()){
			$error = true;
		}
	}else{
		$error = true;
	}

	if($error){
      echo "ERROR";
	}