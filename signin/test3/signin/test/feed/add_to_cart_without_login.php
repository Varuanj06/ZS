<?php $force_session = true; ?>
<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>

<?php 

	$color 					= urldecode($_GET['color']);
	$size 					= $_GET['size'];
	$qty 					= $_GET['qty'];
	$id_product 			= $_GET['id_product'];
	$id_product_prestashop 	= $_GET['id_product_prestashop'];
	
	unset($_SESSION['pixels_without_login']); // IN CASE HE WANTED TO GO TO THE PIXEL PAGE BUT CHANGED HIS MIND AND CLOSES THE FB LOGIN POPUP
	unset($_SESSION['shopping_assistant_without_login']); // IN CASE HE WANTED TO GO TO THE ASSISTANT SHOPPING PAGE BUT CHANGED HIS MIND AND CLOSES THE FB LOGIN POPUP
	
	$_SESSION['add_to_cart_without_login'] 				= 'yes';
	$_SESSION['without_login_color'] 					= $color;
	$_SESSION['without_login_size'] 					= $size;
	$_SESSION['without_login_qty'] 						= $qty;
	$_SESSION['without_login_id_product'] 				= $id_product;
	$_SESSION['without_login_id_product_prestashop'] 	= $id_product_prestashop;

	echo "success";