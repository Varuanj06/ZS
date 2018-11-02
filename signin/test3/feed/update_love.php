<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/love_count.php"); ?>

<?php 

	$love 			= $_GET['love'];
	$count_value 	= $_GET['count_value'];
	$id_product 	= $_GET['id_product'];
	$id_fb_user 	= $_GET['id_fb_user'];

	$product 		= new product();
	$love_count 	= new love_count();

	$error = false;
	$conn->beginTransaction();





	$product->set_id_product($id_product);
	$product->set_love_count($count_value);
	$product->update_love_count();

	$love_count->set_id_product($id_product);
	$love_count->set_id_fb_user($id_fb_user);


	if($love == 'true'){// insert
		if($love_count->exists() == false){
			if(!$love_count->insert()){
				$error = true;
			}
		}else{
			$error = true;
		}
	}else{// delete
		if($love_count->exists() == true){
			if(!$love_count->delete()){
				$error = true;
			}
		}else{
			$error = true;
		}
	}





	if($error){
      $conn->rollBack();
      echo "ERROR";
	}else{
      $conn->commit();
	}