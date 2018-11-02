<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/message.php"); ?>
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
		$message 		= new message();
		$order 			= new order();
		$address 		= new address();

		$unread_msg 	= $message->get_all_unread_messages(" order by date desc ");

		$msg 			= "";
		if(isset($_POST['action']) && $_POST['action'] == '1'){ // Send message to everybody!
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
	?>

	<div class="section">
		<div class="content">

			<h2>
				Send Common Message To All
			</h2>		

			<?php echo $msg; ?>
			
			<form action="" method="post" name="form">
				<input type="hidden" name="action">
				<p><textarea name="global_msg" id="global_msg" class="form-control" placeholder="Write some message here"></textarea></p>
				<p><a href="javascript:send_msg_to_all();" class="btn btn-primary">Send message to everybody</a></p>
			</form>
			<h2>
				Unread messages
			</h2>		

			<table class="table table-condensed table-bordered">
				<thead>
					<tr>
						<th>FB User</th>
						<th>Orders</th>
						<th>Last Message</th>
						<th>Date</th>
					</tr>
				</thead>
				<?php foreach ($unread_msg as $row) { ?>
					<tr>
						<td><?php echo $row->get_id_fb_user(); ?></td>
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
							<a href="../messages?id_fb_user=<?php echo $row->get_id_fb_user(); ?>">
								<?php echo $row->get_message(); ?>
							</a>
						</td>
						<td><?php echo $row->get_date(); ?></td>
					</tr>
				<?php } ?>
			</table>

			<?php 

			?>

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

</body>
</html>