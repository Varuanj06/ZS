<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/message_conversation.php"); ?>

<?php 

	$message_conversation 	= new message_conversation();

	$id_fb_user 		= $user['id'];

	$message_conversation->map($id_fb_user, $_GET['id_conversation']);
	
	if($message_conversation->get_status() == 'closed'){
		echo 'closed';
	}else{
		echo $message_conversation->get_step();
	}