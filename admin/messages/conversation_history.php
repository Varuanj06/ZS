<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/message.php"); ?>
<?php require_once("../../classes/message_conversation.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
	<link rel="stylesheet" href="../../includes/css/chat.css">
</head>
<body>

	<?php require_once("../navbar.php"); ?>

	<?php 
		$message 				= new message();
		$message_conversation 	= new message_conversation();
		$id_fb_user 			= $_GET['id_fb_user'];

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
			$obj['category'] 		= $row->get_category();

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
			$obj['status'] 			= 'Notification';
			$obj['category'] 		= '';

			$output[] = $obj;
		}

		function order_by_date($a, $b) {
		   	$t1 = strtotime($a["date_complete"]);
		    $t2 = strtotime($b["date_complete"]);

		    return ($t2 - $t1);
		}

		usort($output,"order_by_date");

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
				Conversation History
			</h2>		

			<table class="table table-condensed table-bordered">
				<thead>
					<tr>
						<th>Category</th>
						<th>Last Message</th>
						<th>Status</th>
						<th>Date</th>
					</tr>
				</thead>
				<?php foreach($output as $row){ ?>
					<?php if( $row['id_conversation'] > -1 ){ ?>
						<tr>
							<td><?php echo $row['category']; ?></td>
							<td>
								<a href="message.php?id_fb_user=<?php echo $id_fb_user; ?>&id_conversation=<?php echo $row['id_conversation']; ?>">
									<?php echo $row['message']; ?>
								</a>
							</td>
							<td>
								<?php if($row['status'] == 'pending on customer'){ ?>
									<span class="label label-info">PENDING ON CUSTOMER</span>
								<?php }if($row['status'] == 'created'){ ?>
									<span class="label label-primary">CREATED</span>
								<?php }else if($row['status'] == 'open'){ ?>
									<span class="label label-success">OPEN</span>
								<?php }else if($row['status'] == 'closed'){ ?>
									<span class="label label-default">CLOSED</span>
								<?php } ?>
							</td>
							<td><?php echo $row['date_complete']; ?></td>
						</tr>
					<?php }else{ ?>
						<tr class="alert alert-info">
							<td><?php echo $row['status']; ?></td>
							<td><?php echo $row['category']; ?></td>
							<td><?php echo $row['message']; ?></td>
							<td><?php echo $row['date_complete']; ?></td>
						</tr>
					<?php } ?>
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