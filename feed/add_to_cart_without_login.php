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
	
	$espresso_product 		= $_GET['espresso_product'];
	$color 					= urldecode($_GET['color']);
	$size 					= $_GET['size'];
	$qty 					= $_GET['qty'];
	$id_product 			= $_GET['id_product'];
	$id_product_prestashop 	= $_GET['id_product_prestashop'];
	$with_keyword_discount 	= isset($_GET['with_keyword_discount']) ? $_GET['with_keyword_discount'] : 'no';
	$keyword_code 			= isset($_GET['keyword_code']) ? $_GET['keyword_code'] : '';

	//CHECK KEYWORD CODE
	$invalid_code = false;
	if($keyword_code != ''){
		$invalid_code 	= true;
		$product 		= new product();
		$product->map($id_product);
		$product_keyword = str_replace("/", "", $product->get_keywords());

		if($product->get_keyword_discount($product_keyword) != ''){
			$keyword_discount = new keyword_discount();
			$keyword_discount->map_active_by_keyword($product_keyword);

			$keyword_discount_code = new keyword_discount_code();
			if($keyword_discount_code->exists_with_id_keyword_discount($keyword_code, $keyword_discount->get_id_keyword_discount())){
				$invalid_code = false;
			}
		}
	}

	if($invalid_code){
		echo "INVALID_CODE";
	}else{
	
		unset($_SESSION['pixels_without_login']); // IN CASE HE WANTED TO GO TO THE PIXEL PAGE BUT CHANGED HIS MIND AND CLOSES THE FB LOGIN POPUP
		unset($_SESSION['shopping_assistant_without_login']); // IN CASE HE WANTED TO GO TO THE ASSISTANT SHOPPING PAGE BUT CHANGED HIS MIND AND CLOSES THE FB LOGIN POPUP
		unset($_SESSION['join_the_deal_withouth_login']); // IN CASE HE WANTED TO GO TO JOIN A DEAL IN THE PRODUCT_DETAILS PAGE BUT CHANGED HIS MIND AND CLOSES THE FB LOGIN POPUP
		
		$_SESSION['add_to_cart_without_login'] 				= 'yes';
		$_SESSION['without_login_color'] 					= $color;
		$_SESSION['without_login_size'] 					= $size;
		$_SESSION['without_login_qty'] 						= $qty;
		$_SESSION['without_login_id_product'] 				= $id_product;
		$_SESSION['without_login_id_product_prestashop'] 	= $id_product_prestashop;
		$_SESSION['without_login_espresso_product'] 		= $espresso_product;
		$_SESSION['without_login_with_keyword_discount'] 	= $with_keyword_discount;
		$_SESSION['without_login_keyword_discount_code'] 	= $keyword_code;

		echo "success";

	}