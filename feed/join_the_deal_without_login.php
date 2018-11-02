<?php $force_session = true; ?>
<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/keyword_discount_code.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/keyword_discount.php"); ?>
<?php require_once("../classes/keyword_discount_code.php"); ?>

<?php 
	
	
	$id_product 			= $_GET['id_product'];
	
	unset($_SESSION['pixels_without_login']); // IN CASE HE WANTED TO GO TO THE PIXEL PAGE BUT CHANGED HIS MIND AND CLOSES THE FB LOGIN POPUP
	unset($_SESSION['shopping_assistant_without_login']); // IN CASE HE WANTED TO GO TO THE ASSISTANT SHOPPING PAGE BUT CHANGED HIS MIND AND CLOSES THE FB LOGIN POPUP
	unset($_SESSION['add_to_cart_without_login']); // IN CASE HE WANTED TO ADD A PRODUCTO TO THE CART BUT CHANGED HIS MIND AND CLOSES THE FB LOGIN POPUP

	$_SESSION['join_the_deal_withouth_login'] 			= 'yes';
	$_SESSION['without_login_id_product'] 				= $id_product;

	echo "success";
