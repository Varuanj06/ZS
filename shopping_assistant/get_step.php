<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/shopping_assistant_conversation.php"); ?>

<?php 

	$shopping_assistant_conversation 	= new shopping_assistant_conversation();

	$id_fb_user 						= $user['id'];

	$shopping_assistant_conversation->map($id_fb_user, $_GET['id_shopping_assistant_conversation']);
	
	if($shopping_assistant_conversation->get_status() == 'closed'){
		echo 'closed';
	}else{
		echo $shopping_assistant_conversation->get_step();
	}