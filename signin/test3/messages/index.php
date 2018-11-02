<?php require_once("../fb_validator.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/message_conversation.php"); ?>
<?php require_once("../classes/message.php"); ?>
<?php require_once("../classes/order.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
	<link rel="stylesheet" href="../includes/css/chat.css">
</head>
<body>

<?php if (strpos($_SERVER['HTTP_HOST'], 'miracas.in') !== false) { ?>
	<?php require_once("../menu_global.php"); ?> 
	<?php require_once("../sidebar_global.php"); ?> 
<?php }else{ ?>
	<?php require_once("../menu.php"); ?> 
	<?php require_once("../sidebar.php"); ?> 
<?php } ?>

<div id="menu-page-wraper">

	<div class="page-wrap"><div class="page-wrap-inner">
	
	<?php require_once("../message.php"); ?>

	<script>$('.nav-right a[href="../orders"]').addClass('selected');</script>

	<?php 
		$id_fb_user 					= $user['id'];

		$message_conversation 			= new message_conversation();
		$message 						= new message();
		$order 							= new order();

		$message->update_read_all($id_fb_user, "-1", 'admin'); // read all the direct messages that are shown along the conversations

		/* ====================================================================== *
	        	START A NEW CONVERSATION
	 	 * ====================================================================== */

		$msg = "";
		if(isset($_POST['action']) && $_POST['action']=='1'){

			$conn->beginTransaction();
			$error 			= false;

			$id_conversation 		= $message_conversation->max_id_message_conversation($id_fb_user);

			$message_conversation->set_id_fb_user($id_fb_user);
			$message_conversation->set_id_message_conversation($id_conversation);

			if( !$message_conversation->insert() ){ $error = true; }

		/* [START] INIT SMART MESSAGES */
			
			$message 		= new message();
			$user_orders 	= $order->get_list_per_user($id_fb_user, "");
			$new_message 	= "";
			$step 			= "";

			if(count($user_orders) > 0){
				
				$user_orders_last_3_months = $order->get_list_per_user($id_fb_user, " and date_done >= now()-interval 3 month ");

				if(count($user_orders_last_3_months) > 0){
					$new_message 	= "Do you wish to talk about an order placed in the last 3 months?";
					$step 			= "1";
				}else{
					$new_message 	= "Please let us know your concern";
					$step 			= "3";
				}

			}else{

				if($order->get_created_order_with_courier_by_fb_user($id_fb_user)){
					$new_message 	= "Hello, we have noticed that you have added some products to your cart. Did you by any chance try to make a payment yet the order is not confirmed?";
					$step 			= "26";
				}else{
					$new_message 	= "Greetings!! We see that you are a new customer, please drop a message to us in the text field below.";
					$step 			= "2";
				}

			}

			$message->set_id_fb_user($id_fb_user);
			$message->set_id_message($message->max_id_message($id_fb_user));
			$message->set_message($new_message);
			$message->set_from('smart_response');
			$message->set_id_message_conversation($id_conversation);

			if( !$message->insert() ){ $error = true; };
			if( !$message_conversation->update_step($id_fb_user, $id_conversation, $step) ){ $error = true; };

		/* [END] INIT SMART MESSAGES */

			if($error){
			  $conn->rollBack();
			  $msg = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}else{
			  $conn->commit();
			  echo "<script>location.href='message.php?id_conversation=$id_conversation';</script>";
			}

		}

		/* ====================================================================== *
	        	GET CONVERSATIONS AND DIRECT MSGS
	 	 * ====================================================================== */

		$all_conversations 	= $message_conversation->get_conversations_by_user($id_fb_user, " order by (select date from message where id_message_conversation = message_conversation.id_message_conversation order by date desc limit 1) desc ");
		$direct_msgs 		= $message->get_messages_by_user($id_fb_user, "-1", " order by date ");

		/* ====================================================================== *
	        	PUT TOGETHER THE CONVERSATIONS AND THE DIRECT MSGS
	 	 * ====================================================================== */

		$output 			= array();

		foreach ($all_conversations as $row){

			$message = new message();
			$message->map_last_by_conversation($id_fb_user, $row->get_id_message_conversation());

			$date 	= explode(" ", $message->get_date());
			$unread = $message->get_unread_messages_by_conversation($id_fb_user, 'admin', $row->get_id_message_conversation());

			$obj 					= array();	
			$obj['id_conversation'] = $row->get_id_message_conversation();
			$obj['date'] 			= $date[0];
			$obj['date_complete'] 	= $message->get_date();
			$obj['unread'] 			= $unread;
			$obj['message'] 		= $message->get_message();
			$obj['status'] 			= $row->get_status();

			$output[] = $obj;

		}

		foreach ($direct_msgs as $row){

			$date 	= explode(" ", $row->get_date());

			$obj 					= array();	
			$obj['id_conversation'] = $row->get_id_message_conversation();
			$obj['date'] 			= $date[0];
			$obj['date_complete'] 	= $row->get_date();
			$obj['unread'] 			= '';
			$obj['message'] 		= $row->get_message();
			$obj['status'] 			= '';

			$output[] = $obj;
		}

		function order_by_date($a, $b) {
		   	$t1 = strtotime($a["date_complete"]);
		    $t2 = strtotime($b["date_complete"]);

		    return ($t2 - $t1);
		}

		usort($output,"order_by_date");

	?>

	<div>
	<div class="alert alert-info text-center">
		<h4 style="margin:0;">Some messages goes here for the chat app!</h4>
	</div>
	</div>

	<div class="tabs-container">

		<div class="conversation" style="background:none;">
			<ul class="nav nav-tabs" style="display:block;">
		  		<li class="active"><a href="../messages">Support Conversations</a></li>
		  		<li><a href="../shopping_assistant/conversations.php">Fashion Conversations</a></li>
			</ul>
		</div>
	
		<form action="" method="post" name="form">
			<input type="hidden" name="action">

			<?php echo $msg; ?>

			<div class="conversation">
				<a href="javascript:start_conversation();" class="btn btn-green btn-block btn-md" style="padding: 13px 13px !important;">
					<i class="fa fa-plus"></i>&nbsp; Start a New Conversation
				</a>
			</div>
		</form>

		<div class="conversation">
			<?php foreach($output as $row){ ?>

				<?php if( $row['id_conversation'] > -1 ){ ?>
					<a href="message.php?id_conversation=<?php echo $row['id_conversation']; ?>">
						<div class="conversation-item">

							<div class="display-table">
							    <div class="display-cell">
							        
									<?php echo $row['message']; ?>

									<span class="conversation-date">
										<?php if($row['status'] == 'pending on customer'){ ?>
											<span class="label label-info">PENDING ON CUSTOMER</span>
										<?php }if($row['status'] == 'created'){ ?>
											<span class="label label-primary">CREATED</span>
										<?php }else if($row['status'] == 'open'){ ?>
											<span class="label label-success">OPEN</span>
										<?php }else if($row['status'] == 'closed'){ ?>
											<span class="label label-default">CLOSED</span>
										<?php } ?>
										|
										<?php echo $row['date_complete']; ?> 
									</span>		

							    </div>
							    <div class="display-cell text-right" style="width:50px;">
							        
									<span class="badge"><?php echo $row['unread']; ?></span>
									<i class="fa fa-chevron-right"></i>		

							    </div>
							</div>
							
						</div>	
					</a>
				<?php }else{ ?>
					<div class="alert alert-info">
						<?php echo $row['message']; ?>
						<span class="conversation-date"><?php echo $row['date_complete']; ?></span>
					</div>
				<?php } ?>

			<?php } ?>	
		</div>

	</div>

	</div></div>

	<?php require_once("../footer.php"); ?>
	
	<script>
		function start_conversation(){
			//if(confirm("Are you sure?")){
				document.form.action.value = '1';
				document.form.submit();
			//}
		}
	</script>
	
	</div>
</body>
</html>