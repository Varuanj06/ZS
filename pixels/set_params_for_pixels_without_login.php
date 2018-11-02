<?php $force_session = true; ?>
<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>

<?php 

	unset($_SESSION['add_to_cart_without_login']); // IN CASE HE WANTED TO ADD A PRODUCTO TO THE CART BUT CHANGED HIS MIND AND CLOSES THE FB LOGIN POPUP
	unset($_SESSION['shopping_assistant_without_login']); // IN CASE HE WANTED TO GO TO THE ASSISTANT SHOPPING PAGE BUT CHANGED HIS MIND AND CLOSES THE FB LOGIN POPUP
	unset($_SESSION['join_the_deal_withouth_login']); // IN CASE HE WANTED TO GO TO JOIN A DEAL IN THE PRODUCT_DETAILS PAGE BUT CHANGED HIS MIND AND CLOSES THE FB LOGIN POPUP
	
	$_SESSION['pixels_without_login'] 				= 'yes';
	$_SESSION['pixels_without_login_q'] 			= $_GET['q'];
	$_SESSION['pixels_pixel_keyword'] 				= '';

	echo "success";