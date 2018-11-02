<div class="text-center login-loading" style="margin-top:50px;"><i class="fa fa-refresh fa-spin" style="font-size: 40px;"></i></div>
<?php require_once("../fb_validator.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/shopping_assistant_conversation.php"); ?>

<!-- Include CSS -->
<link rel="stylesheet" href="../includes/plugins/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../includes/plugins/font-awesome/css/font-awesome.css">
<link rel="stylesheet" href="../includes/css/style.css">
<link rel="stylesheet" href="../includes/css/login.css">

<?php 
	$id_fb_user 						= $user['id'];

	$shopping_assistant_conversation 	= new shopping_assistant_conversation();

        if($shopping_assistant_conversation->get_current_conversation($id_fb_user)){
        	$id_shopping_assistant_conversation = $shopping_assistant_conversation->get_current_conversation($id_fb_user);

        	$shopping_assistant_conversation->set_id_fb_user($id_fb_user);
        	$shopping_assistant_conversation->set_id_shopping_assistant_conversation($id_shopping_assistant_conversation);
        	$shopping_assistant_conversation->set_status('closed');

        	$shopping_assistant_conversation->update_status();
        }

        $extra_param = "";
		if(isset($_GET['set_gender']) && $_GET['set_gender'] != ''){ 
			$extra_param = "?set_gender=".$_GET['set_gender'];
		}

        echo "<script>location.href='../feed$extra_param';</script>";
        exit();