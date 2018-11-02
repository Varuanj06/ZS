<?php require_once("../fb_validator.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/shopping_assistant_conversation.php"); ?>
<?php require_once("../classes/shopping_assistant.php"); ?>
<?php require_once("../classes/order.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
	<link rel="stylesheet" href="../includes/css/chat.css">
</head>
<body>
	
	<?php require_once("../menu.php"); ?>
	<?php require_once("../sidebar.php"); ?>
	<div id="menu-page-wraper">

	<div class="page-wrap"><div class="page-wrap-inner">
	
	<?php require_once("../message.php"); ?>

	<script>$('.nav-right a[href="../orders"]').addClass('selected');</script>

	<?php 
		$id_fb_user 						= $user['id'];

		$shopping_assistant_conversation 	= new shopping_assistant_conversation();
		$shopping_assistant 				= new shopping_assistant();

		/* ====================================================================== *
	        	OUTPUT THE CONVERSATIONS
	 	 * ====================================================================== */

		$output 			= array();
		$sql_order_by 		= " order by (select date from shopping_assistant where id_shopping_assistant_conversation = shopping_assistant_conversation.id_shopping_assistant_conversation order by date desc limit 1) desc ";
		$sql_condition 		= " and step >= '3' "; // conversations closed only if its on the last step
		$all_conversations 	= $shopping_assistant_conversation->get_conversations_by_user($id_fb_user, " $sql_condition $sql_order_by ");

		foreach ($all_conversations as $row){

			$shopping_assistant = new shopping_assistant();
			$shopping_assistant->map_last_by_conversation($id_fb_user, $row->get_id_shopping_assistant_conversation());

			$obj 					= array();	
			$obj['id_conversation'] = $row->get_id_shopping_assistant_conversation();
			$obj['date_complete'] 	= $shopping_assistant->get_date();
			$obj['message'] 		= $shopping_assistant->get_message();
			$obj['status'] 			= $row->get_status();

			$output[] = $obj;

		}

		function order_by_date($a, $b) {
		   	$t1 = strtotime($a["date_complete"]);
		    $t2 = strtotime($b["date_complete"]);

		    return ($t2 - $t1);
		}

		usort($output, "order_by_date");

	?>

	<div>
	<div class="alert alert-info text-center">
		<h4 style="margin:0;">Some messages goes here for the chat app!</h4>
	</div>
	</div>

	<div class="tabs-container">

		<div class="conversation" style="background:none;">
			<ul class="nav nav-tabs" style="display:block;">
		  		<li><a href="../messages">Support Conversations</a></li>
		  		<li class="active"><a href="../shopping_assistant/conversations.php">Fashion Conversations</a></li>
			</ul>
		</div>

		<div class="conversation">
			<?php foreach($output as $row){ ?>

				<a href="../shopping_assistant/?id_conversation=<?php echo $row['id_conversation']; ?>&from_history=1">
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
								<i class="fa fa-chevron-right"></i>		
						    </div>
						</div>
						
					</div>	
				</a>

			<?php } ?>	
		</div>

	</div>

	</div></div>

	<?php require_once("../footer.php"); ?>

	</div>	
</body>
</html>