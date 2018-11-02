<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/fb_user_product_save.php"); ?>

<?php 
	
	$id_fb_user 				= $user['id'];
	$id_product 				= $_POST['id_product'];

	$fb_user_product_save 		= new fb_user_product_save();

	$fb_user_product_save->set_id_fb_user($id_fb_user);
	$fb_user_product_save->set_id_product($id_product);

	if($fb_user_product_save->exists()){
		$fb_user_product_save->delete();
		echo "delete";
	}else{
		$fb_user_product_save->insert();
		echo "insert";
	}