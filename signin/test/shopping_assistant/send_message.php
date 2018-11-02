<?php header('Content-Type: application/json'); ?>
<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/shopping_assistant.php"); ?>
<?php require_once("../classes/shopping_assistant_conversation.php"); ?>
<?php require_once("../classes/fb_user_profile.php"); ?>
<?php require_once("functions.php"); ?>

<?php 

	$shopping_assistant 				= new shopping_assistant();   
	$shopping_assistant_conversation 	= new shopping_assistant_conversation();
	$fb_user_profile                  	= new fb_user_profile();

/* ====================================================================== *
        GLOBAL VARIABLES
 * ====================================================================== */		

	$id_fb_user 							= $user['id'];
	$id_shopping_assistant_conversation 	= $_POST['id_shopping_assistant_conversation'];
	$current_msg 							= $_POST['message'];
	$error 									= false;

	$shopping_assistant_conversation->map($id_fb_user, $id_shopping_assistant_conversation);
	if($shopping_assistant_conversation->get_status() == 'closed'){ $error = true; }

/* ====================================================================== *
        BEGIN TRANSACTION
 * ====================================================================== */	

	$conn->beginTransaction();

/* ====================================================================== *
        SAVE MESSAGE FROM THE CUSTOMER
 * ====================================================================== */	

    $id_shopping_assistant 	= $shopping_assistant->max_id_shopping_assistant($id_fb_user); 

	$shopping_assistant->set_id_fb_user($id_fb_user);
	$shopping_assistant->set_id_shopping_assistant($id_shopping_assistant);
	$shopping_assistant->set_message($current_msg);
	$shopping_assistant->set_from('user');
	$shopping_assistant->set_id_shopping_assistant_conversation($id_shopping_assistant_conversation);
	$shopping_assistant->set_step($shopping_assistant_conversation->get_step());

	if(!$shopping_assistant->insert()){ $error = true; }

	$shopping_assistant->map($id_fb_user, $id_shopping_assistant);
	$customer_msg_date = $shopping_assistant->get_date();	

/* ====================================================================== *
        GET NEXT STEP ACCORDING TO THE RESPONSE OF THE CUSTOMER
 * ====================================================================== */	

	/* update conversation next step */
	$old_step  	= $shopping_assistant_conversation->get_step();
	$new_step 	= get_next_step($old_step, $current_msg);

	if($new_step=='ERROR'){ $error = true; }
	if(!$shopping_assistant_conversation->update_step($id_fb_user, $id_shopping_assistant_conversation, $new_step)){ $error = true; };

	/* update conversation status */	
	$new_status = get_status($old_step, $new_step);
	if(!$shopping_assistant_conversation->update_status($id_fb_user, $id_shopping_assistant_conversation, $new_status)){ $error = true; }

/* ====================================================================== *
        SAVE MESSAGE FROM THE SYSTEM ACCORDING TO THE NEXT STEP
 * ====================================================================== */	

    $admin_msg 		= '';
    $admin_msg_date = '';

    if($old_step != $new_step){

    	$admin_msg = get_admin_msg($new_step);

    }

    if($admin_msg != ''){    

	    $shopping_assistant 		= new shopping_assistant();   
	    $id_shopping_assistant 		= $shopping_assistant->max_id_shopping_assistant($id_fb_user);     

		$shopping_assistant->set_id_fb_user($id_fb_user);
		$shopping_assistant->set_id_shopping_assistant($id_shopping_assistant);
		$shopping_assistant->set_message($admin_msg);
		$shopping_assistant->set_from('smart_response');
		$shopping_assistant->set_id_shopping_assistant_conversation($id_shopping_assistant_conversation);
		$shopping_assistant->set_step($new_step);

		if(!$shopping_assistant->insert()){ $error = true; }

		$shopping_assistant->map($id_fb_user, $id_shopping_assistant);	
		$admin_msg_date = $shopping_assistant->get_date();	

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
		'admin_msg' 		=> $admin_msg,
		'new_step' 			=> $new_step,
	));





