<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/shopping_assistant_conversation.php"); ?>

<?php 
	
	$id_fb_user 							= $user['id'];
	$id_shopping_assistant_conversation 	= $_POST['id_shopping_assistant_conversation'];
	$id_products 							= $_POST['id_products'];

	$shopping_assistant_conversation 		= new shopping_assistant_conversation();
	
	$shopping_assistant_conversation->update_id_products($id_fb_user, $id_shopping_assistant_conversation, $id_products);