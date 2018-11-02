<?php $force_session = true; ?>
<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/image.php"); ?>

<?php 

	$id_product_prestashop = $_GET['id_product_prestashop'];

	$image 		= new image();

	$id_images 	= $image->get_images($id_product_prestashop);
	foreach ($id_images as $id_image) {
		$image_url = "http://miracas.miracaslifestyle.netdna-cdn.com/$id_product_prestashop-$id_image/$id_image.jpg";

		echo "<span class='mb-open-popup mfp-image' data-popuptrigger='yes' data-mfp-src='$image_url' data-src='$image_url'></span>";
	}

