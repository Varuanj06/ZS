<?php header('Content-Type: application/json'); ?>
<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/message.php"); ?>
<?php require_once("../classes/message_conversation.php"); ?>
<?php require_once("../classes/order.php"); ?>
<?php require_once("../classes/order_detail.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("functions.php"); ?>

<?php 

	$message_conversation 	= new message_conversation();
	$order 					= new order();
	$order_detail 			= new order_detail();
	$product 				= new product();

/* ====================================================================== *
        GLOBAL VARIABLES
 * ====================================================================== */		

	$id_fb_user 			= $user['id'];
	$id_conversation 		= $_POST['id_conversation'];
	$current_msg 			= $_POST['message'];
	$error 					= false;

/* ====================================================================== *
        BEGIN TRANSACTION
 * ====================================================================== */	

	$conn->beginTransaction();

/* ====================================================================== *
        SAVE MESSAGE FROM THE CUSTOMER
 * ====================================================================== */	

    $message 		= new message();   
    $id_message 	= $message->max_id_message($id_fb_user); 

	$message->set_id_fb_user($id_fb_user);
	$message->set_id_message($id_message);
	$message->set_message($current_msg);
	$message->set_from('user');
	$message->set_id_message_conversation($id_conversation);

	if(!$message->insert()){ $error = true; }

	$message->map($id_fb_user, $id_message);
	$customer_msg_date = $message->get_date();	

/* ====================================================================== *
        GET NEXT STEP ACCORDING TO THE RESPONSE OF THE CUSTOMER
 * ====================================================================== */	

	$message_conversation->map($id_fb_user, $id_conversation);

	if($message_conversation->get_status() == 'closed'){ $error = true; }

	$old_status = $message_conversation->get_status();

	/* update conversation next step */
	$old_step  	= $message_conversation->get_step();
	$new_step 	= get_next_step($old_step, $current_msg);

	if($new_step=='ERROR'){ $error = true; }
	if(!$message_conversation->update_step($id_fb_user, $id_conversation, $new_step)){ $error = true; };

	/* update conversation category */

	$category = get_category($old_step, $new_step);
	if(!$message_conversation->update_category($id_fb_user, $id_conversation, $category)){ $error = true; }

	/* update conversation status */
	
	$new_status = get_status($old_step, $new_step);
	if(!$message_conversation->update_status($id_fb_user, $id_conversation, $new_status)){ $error = true; }

/* ====================================================================== *
        SAVE MESSAGE FROM THE SYSTEM ACCORDING TO THE NEXT STEP
 * ====================================================================== */	

    $admin_msg 		= '';
    $admin_msg_date = '';

    if($old_step != $new_step){

    	$admin_msg = get_admin_msg($new_step);

    }

    if($old_status == 'pending on customer' && $new_status == 'open' && $admin_msg==''){
    	$admin_msg = 'Our customer care agent will study your concern and will get back to you soon';
    }

    if($admin_msg != ''){    

	    $message 		= new message();   
	    $id_message 	= $message->max_id_message($id_fb_user);     

		$message->set_id_fb_user($id_fb_user);
		$message->set_id_message($id_message);
		$message->set_message($admin_msg);
		$message->set_from('smart_response');
		$message->set_id_message_conversation($id_conversation);

		if(!$message->insert()){ $error = true; }

		$message->map($id_fb_user, $id_message);	
		$admin_msg_date = $message->get_date();	

	}

/* ====================================================================== *
        CHANGE "READ" IF THE CUSTOMER REPLIES AN OPEN ORDER
 * ====================================================================== */	

 	if($new_status == 'open'){
 		$message_conversation->update_read_by_admin($id_fb_user, $id_conversation, 'no');
 	}
	
/* ====================================================================== *
        CHECK ERRORS
 * ====================================================================== */	

	if($error){
	  $conn->rollBack();
	}else{
	  $conn->commit();
	}

/* ====================================================================== *
        OUTPUT
 * ====================================================================== */	

 	if($new_status == 'closed'){
 		$new_step = 'closed';
 	}

	echo json_encode(array(
		'error' 			=> $error?'yes':'no',
		'customer_msg_date' => $customer_msg_date,
		'admin_msg' 		=> $admin_msg,
		'admin_msg_date' 	=> $admin_msg_date,
		'new_step' 			=> $new_step,
	));





