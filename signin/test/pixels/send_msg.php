<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/pixel.php"); ?>
<?php require_once("../classes/fb_user_details.php"); ?>
<?php require_once("../includes/plugins/PHPMailer_class.php"); ?>
<?php require_once("../classes/message.php"); ?>
<?php require_once("../classes/vendor.php"); ?>

<?php 
	
	$id_pixel 			= $_GET['id_pixel'];
	$type  				= $_GET['type'];
	$id_fb_user 		= $user['id'];

	$pixel 				= new pixel();
	$fb_user_details 	= new fb_user_details();
	$vendor 			= new vendor();

	$pixel->map($id_pixel);

	if($type=='pixel_is_product'){

		/* IF IT IS A CONVERTED PIXEL (A PRODUCT) THEN SEND THE SMS AND EMAIL */
		
		$current_msg = str_replace("{{product_link}}", $pixel->get_product_link(), $pixel->get_message());
		
		$fb_user_details->map($id_fb_user);

		/* SEND EMAIL */
		$mail 				= new Mail();
		$mail->send_mail($fb_user_details->get_email(), $pixel_mail_subject, $current_msg, $current_msg);

		/* SEND SMS */
		$message 			= new message();
		$message->send_SMS($fb_user_details->get_mobile_number(), $current_msg);

	}else if($type=='pixel_count_increase'){

		// Notified emails of a pixel count increase
		foreach ($on_pixel_increase_emails as $row) {
			$vendor->map($pixel->get_id_vendor());

			$current_subject 	= $on_pixel_increase_subject;
			$current_subject 	= str_replace("{{vendor_name}}", $vendor->get_name(), $current_subject);

			$current_msg 		= $on_pixel_increase_msg;
			$current_msg 		= str_replace("{{pixel_name}}", $pixel->get_name(), $current_msg);
			$current_msg 		= str_replace("{{vendor_name}}", $vendor->get_name(), $current_msg);

			$mail 				= new Mail();
			$mail->send_mail($row, $current_subject, $current_msg, $current_msg);
		}

	}