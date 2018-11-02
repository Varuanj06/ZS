<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/pixel.php"); ?>
<?php require_once("../classes/pixel_count.php"); ?>
<?php require_once("../classes/fb_user_details.php"); ?>
<?php require_once("../classes/vendor.php"); ?>

<?php 
	
	$insert 			= $_GET['insert'];
	$id_pixel 			= $_GET['id_pixel'];
	$id_fb_user 		= $user['id'];

	$pixel 				= new pixel();
	$pixel_count 		= new pixel_count();
	$fb_user_details 	= new fb_user_details();
	$vendor 			= new vendor();

	$pixel->map($id_pixel);

	$error = false;
	$conn->beginTransaction();

	$no_details_found = false;
	$fb_user_details->set_id_fb_user($id_fb_user);
	if(!$fb_user_details->exists()){
		$no_details_found = true;
	}

	$pixel_count->set_id_pixel($id_pixel);
	$pixel_count->set_id_fb_user($id_fb_user);


	if($insert == 'yes'){// insert
		if($pixel_count->exists() == false){
			if($pixel_count->insert()){
				
				if($pixel->get_type()=='product'){

					echo "pixel_is_product";

				}else if($pixel->get_type()=='pixel'){

					echo "pixel_count_increase";

				}

			}else{
				$error = true;
			}
		}else{
			$error = true;
		}
	}else{// delete
		if($pixel_count->exists() == true){
			if(!$pixel_count->delete()){
				$error = true;
			}
		}else{
			$error = true;
		}
	}

	$pixel->update_pixel_count($id_pixel);

	if($no_details_found){
		$conn->rollBack();
      	echo "NO_DETAILS";
	}else if($error){
      $conn->rollBack();
      echo "ERROR";
	}else{
      $conn->commit();
	}