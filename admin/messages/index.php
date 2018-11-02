<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/message.php"); ?>
<?php require_once("../../classes/message_conversation.php"); ?>
<?php require_once("../../classes/order.php"); ?>
<?php require_once("../../classes/address.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
	<link rel="stylesheet" href="../../includes/css/chat.css">
</head>
<body>

	<?php require_once("../navbar.php"); ?>

	<?php 

	/* ====================================================================== *
	    	CLASSES
	 * ====================================================================== */		

		$message 				= new message();
		$message_conversation 	= new message_conversation();
		$order 					= new order();
		$address 				= new address();

		$msg 			= "";

	/* ====================================================================== *
	    	SEND MESSAGE TO EVERYBODY
	 * ====================================================================== */			

		if(isset($_POST['action']) && $_POST['action'] == '1'){
			$users_with_orders 		= $order->get_all_users_with_orders("");

			$conn->beginTransaction();
			$error 			= false;
			
			$mobile_numbers = "";
			foreach ($users_with_orders as $row){

				/* #### SEND MESSAGE TO FB USER #### */
				
				$message->set_id_fb_user($row->get_id_fb_user());
				$message->set_id_message($message->max_id_message($row->get_id_fb_user()));
				$message->set_message($_POST['global_msg']);
				$message->set_from('admin');
				$message->set_id_message_conversation("-1");

				if(!$message->insert()){
					$error = true;
				}

				/* #### GET THE ADDRESS LAST USED BY THE FB USER #### */

				$addresses 			= $address->get_list($row->get_id_fb_user(), " order by date_update desc ");
				$current_address 	= "";
				foreach ($addresses as $row){ 
					$current_address = $row->get_id_address();
					break;
				}
				$address->map($current_address, $row->get_id_fb_user());

				$mobile_numbers .= $address->get_mobile_number().',';
			}

			$message->send_SMS(rtrim($mobile_numbers, ","), $_POST['global_msg']);

			if($error){
			  $conn->rollBack();
			  $msg = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}else{
			  $conn->commit();
			  $msg = '<div class="alert alert-success"><strong>Success!</strong> message delivered.</div>';
			}

		}

	/* ====================================================================== *
	    	FILTER BY CATEGORY
	 * ====================================================================== */			

		$default_category = "";
		$sql_category = "";
		if(isset($_GET['category'])){
			$default_category 	= $_GET['category'];
			$sql_category 		= " and category = '$default_category' ";
		}

	/* ====================================================================== *
	    	GET DATA
	 * ====================================================================== */			

		$open_conversations 	= $message_conversation->get_open_conversations(" $sql_category and read_by_admin = 'no' order by (select date from message where id_message_conversation = message_conversation.id_message_conversation order by date desc limit 1) desc ");

	/* ====================================================================== *
	    	MARK CONVERSATION AS READ BY ADMIN
	 * ====================================================================== */			

		if(isset($_POST['action']) && $_POST['action'] == '2'){ // Mark as read

			$conn->beginTransaction(); //transaccion
			$error = false;

			foreach ($open_conversations as $row) { 
				if(isset( $_POST["checkbox_".$row->get_id_fb_user()."_".$row->get_id_message_conversation()] )){
					if(!$message_conversation->update_read_by_admin($row->get_id_fb_user(), $row->get_id_message_conversation(), 'yes')){
						$error = true;
					}
				}
			}

			if($error == false){
				$msg = '<div class="alert alert-success"><strong>Awesome</strong>, Conversations successfully marked as read.</div>';
				$conn->commit();

				$open_conversations 	= $message_conversation->get_open_conversations(" $sql_category and read_by_admin = 'no' order by (select date from message where id_message_conversation = message_conversation.id_message_conversation order by date desc limit 1) desc ");
			}else{
				$msg = '<div class="alert alert-danger"><strong>Oops</strong>, there was an error.</div>';
				$conn->rollback();
			}
		}
	?>

	<style>
		.nav-tabs{
			font-size: 11px;
		}
		.nav-tabs>li>a{
			padding: 10px 10px;
		}
	</style>

	<div class="section">
		<div class="content">

			<h2>
				Unread conversations
			</h2>		

			<?php echo $msg; ?>
			
			<form action="" method="post" name="form">

				<input type="hidden" name="action">
				<p><textarea name="global_msg" id="global_msg" class="form-control" placeholder="Write some message here"></textarea></p>
				<p><a href="javascript:send_msg_to_all();" class="btn btn-primary">Send message to everybody</a></p>
				

				<br>
				<ul class="nav nav-tabs">
				  <li role="presentation" <?php if($default_category==""){echo 'class="active"';} ?>><a href="./">All</a></li>
				  <li role="presentation" <?php if($default_category=="ENQUIRY"){echo 'class="active"';} ?>><a href="./?category=ENQUIRY">Enquiry</a></li>
				  <li role="presentation" <?php if($default_category=="NEW CUSTOMER"){echo 'class="active"';} ?>><a href="./?category=NEW CUSTOMER">New Customer</a></li>
				  <li role="presentation" <?php if($default_category=="OPEN ORDER"){echo 'class="active"';} ?>><a href="./?category=OPEN ORDER">Open Order</a></li>
				  <!--<li role="presentation" <?php if($default_category=="ORDER MODIFICATION"){echo 'class="active"';} ?>><a href="./?category=ORDER MODIFICATION">Order Modification</a></li>-->
				  <li role="presentation" <?php if($default_category=="REFUSED ISSUE"){echo 'class="active"';} ?>><a href="./?category=REFUSED ISSUE">Refused Issue</a></li>
				  <li role="presentation" <?php if($default_category=="TRACKING NUMBER TO BE GIVEN"){echo 'class="active"';} ?>><a href="./?category=TRACKING NUMBER TO BE GIVEN">Traciking number to be given</a></li>
				  <li role="presentation" <?php if($default_category=="ISSUE WITH TRACKING"){echo 'class="active"';} ?>><a href="./?category=ISSUE WITH TRACKING">Issue with Tracking</a></li>
				  <li role="presentation" <?php if($default_category=="PARTIAL ORDERS"){echo 'class="active"';} ?>><a href="./?category=PARTIAL ORDERS">Partial Orders</a></li>
				  <li role="presentation" <?php if($default_category=="PROCESSING ORDERS"){echo 'class="active"';} ?>><a href="./?category=PROCESSING ORDERS">Processing Orders</a></li>
				  <li role="presentation" <?php if($default_category=="PAYMENT ISSUES"){echo 'class="active"';} ?>><a href="./?category=PAYMENT ISSUES">Payment Issues</a></li>
				</ul>
				<br>

				<table class="table table-condensed table-bordered">
					<thead>
						<tr>
							<th><input type="checkbox" class="check_all"></th>
							<th>FB User</th>
							<th>Category</th>
							<th>Orders</th>
							<th>Last Message</th>
							<th>Date</th>
						</tr>
					</thead>
					<?php 
						foreach ($open_conversations as $row) { 
							$message = new message();
							$message->map_last_by_conversation($row->get_id_fb_user(), $row->get_id_message_conversation());

							$unread = $message->get_unread_messages_by_conversation($row->get_id_fb_user(), 'admin', $row->get_id_message_conversation());
					?>
						<tr>
							<td><input type="checkbox" class="check_box" name="checkbox_<?php echo $row->get_id_fb_user(); ?>_<?php echo $row->get_id_message_conversation(); ?>"></td>
							<td><?php echo $row->get_id_fb_user(); ?></td>
							<td><?php echo $row->get_category(); ?></td>
							<td>
								<?php 
									$orders 			= $order->get_list_per_user($row->get_id_fb_user(), " order by date_done desc  ");
									foreach ($orders as $order_row) {
								?>
										<?php echo $order_row->get_id_order(); ?>,
								<?php 
									}
								?>
							</td>
							<td>
								<a href="message.php?id_fb_user=<?php echo $row->get_id_fb_user(); ?>&id_conversation=<?php echo $row->get_id_message_conversation(); ?>">
									<?php echo $message->get_message(); ?>
								</a>
							</td>
							<td><?php echo $message->get_date(); ?></td>
						</tr>
					<?php } ?>
						<tr>
							<td><a href="javascript:mark_as_read();" class="btn btn-sm">Mark as Read</a></td>
							<td colspan="20">&nbsp;</td>
						</tr>
				</table>

			</form>

		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../messages"]').parents('li').addClass('active');</script>
	<script>
		function send_msg_to_all(){
			if(document.form.global_msg.value != "" && confirm("Are you sure? this message will be send to everybody!")){
				document.form.action.value = "1";
				document.form.submit();
			}else{
				alert("Please write some message!");
			}
		}
	</script>

	<script>
		function mark_as_read(){
			if(confirm("Are you sure?")){
				document.form.action.value = "2";
				document.form.submit();
			}
		}

		$('.check_all').on('click', function(){
			$(this).closest('table').find('.check_box').prop('checked', $(this).prop('checked'));
		});
	</script>

</body>
</html>