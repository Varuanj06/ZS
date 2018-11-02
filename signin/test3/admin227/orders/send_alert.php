<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/message.php"); ?>
<?php require_once("../../classes/message_conversation.php"); ?>
<?php require_once("../../classes/address.php"); ?>

<?php 
	$message 				= new message();
	$message_conversation 	= new message_conversation();
	$address 				= new address();
	
	$id_fb_user 		= $_GET['id_fb_user'];
	$id_message 		= $message->max_id_message($id_fb_user);

	$message->set_id_fb_user($id_fb_user);
	$message->set_id_message($id_message);
	$message->set_message($_GET['message']);
	$message->set_from('admin');
	$message->set_id_message_conversation("-1");

	if($message->insert()){

		/* #### SEND SMS #### */
		$message->send_SMS_by_id_fb_user($id_fb_user, $address);
		/* #### END SEND SMS #### */

	}else{
		echo "ERROR";
	}