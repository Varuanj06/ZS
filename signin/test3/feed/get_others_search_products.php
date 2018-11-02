<?php require_once("../fb_validator.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/search_history_detail.php"); ?>

<?php 

	$idsearch = $_GET['idsearch'];

	$search_history_detail 		= new search_history_detail();

	$details 		= $search_history_detail->get_list($idsearch, "");
	foreach ($details as $detail) {
		$product_link 			= $detail->get_link();

		$last_chars 			= substr($detail->get_image_link(), strrpos($detail->get_image_link(), '/') + 1);
		$id_image 				= str_replace(".jpg", "", $last_chars); // get the 75 from this: http://miracas.com/9-75/75.jpg
		$id_product_prestashop 	= $detail->get_id_product_prestashop();
		$image_url 				= "http://miracas.com/".$id_product_prestashop."-".$id_image."-medium/".$id_product_prestashop."_.jpg";
		$image_url 				= $detail->get_image_link();

		echo "<a target='_blank' href='$product_link'><img src='$image_url' /></a>";
	}

