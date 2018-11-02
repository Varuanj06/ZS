<?php require_once("../fb_validator.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/message.php"); ?>
<?php require_once("../classes/message_conversation.php"); ?>
<?php require_once("../classes/order.php"); ?>
<?php require_once("../classes/order_detail.php"); ?>
<?php require_once("../classes/product.php"); ?>

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
		$id_fb_user 			= $user['id'];

	/* ====================================================================== *
        	CLASSES
 	 * ====================================================================== */	

		$message 				= new message();
		$message_conversation 	= new message_conversation();
		$order 					= new order();
		$order_detail 			= new order_detail();

	/* ====================================================================== *
        	FUNCTIONS WHEN ENTERING THE PAGE
 	 * ====================================================================== */		

		$message_conversation->map($id_fb_user, $_GET['id_conversation']);
		$message->update_read_all($id_fb_user, $_GET['id_conversation'], 'admin');

	/* ====================================================================== *
        	GET THE MESSAGES
 	 * ====================================================================== */			

		$all_messages 		= $message->get_messages_by_user($id_fb_user, $_GET['id_conversation'], " order by date ");

		
	?>

	<div class="tabs-container">

		<a href="./" class="btn btn-primary btn-green btn-sm" style="/*position:fixed;top: 60px;left: 10px;*/margin:15px 0 0 0px;">Go Back</a>

	    <div class="chat">

			<div>
			<div class="alert alert-info text-center">
				<h5 style="margin:0;">For any of your quesries about products or orders, you can drop a message here and we will reply back as soon as possible. Please note that this is not a real time chat ,but as soon as there is a reply , you wil be intimated.</h4>
			</div>
			</div>

			<?php foreach($all_messages as $row){ ?>
				<div class="<?php echo ( $row->get_from() == 'admin' || $row->get_from() == 'system' || $row->get_from() == 'smart_response' ) ? 'other' : 'self'; ?>">
					<div class="msg">
						<?php echo $row->get_message(); ?>
						<div class="msg-date"><?php echo $row->get_date(); ?></div>
					</div>
				</div>	
			<?php } ?>

			<div class="new_message">

			<!--
			/* ====================================================================== *
		        	LOADING
		 	 * ====================================================================== */	
		 	-->

		 		<div class="msg_loading"></div>	

			<!--
			/* ====================================================================== *
		        	CLOSED CONVERSATION
		 	 * ====================================================================== */	
		 	-->

		 		<div class="msg_input step_closed"> <!-- step_19 step_24 step_25 step_6 step_7  -->
		 			<div class="alert alert-info text-center" style="margin:0;"><strong>This conversation is closed</strong></div>
				</div>

			<!--
			/* ====================================================================== *
		        	YES OR NO
		 	 * ====================================================================== */	
		 	-->

				<div class="msg_input step_1 step_11 step_15 step_16 step_26 step_28 step_30">
					<div class="col-container">
						<div class="col-50"><a href="javascript:send_msg('YES');" class="btn btn-green btn-block">YES</a></div>
						<div class="col-50"><a href="javascript:send_msg('NO');" class="btn btn-green btn-block">NO</a></div>
					</div>
				</div>

			<!--
			/* ====================================================================== *
		        	WRITE SOME MESSAGE
		 	 * ====================================================================== */	
		 	-->
				
				<div class="msg_input no-padding step_2 step_3 step_12 step_13 step_14 step_18 step_21 step_22 step_23 step_27 step_29 step_19 step_24 step_25 step_6 step_7 step_31">
					<div class="col-container col-without-margin">
						<div class="col-80"><input type="text" class="form-control message" placeholder="Write a message here" /></div>
						<div class="col-20"><a href="javascript:send_text_msg();" class="btn btn-green btn-block">Send</a></div>
					</div>
				</div>
				

			<!--
			/* ====================================================================== *
		        	SELECT_ORDER
		 	 * ====================================================================== */	
		 	-->	
				
				<?php 
					$user_orders_last_3_months = $order->get_list_per_user($id_fb_user, " and date_done >= now()-interval 3 month order by date_done desc ");
				?>

				<div class="msg_input step_4">
					<?php foreach ($user_orders_last_3_months as $row) { ?>
						<div class="select_order" onclick="send_msg('ORDER #<?php echo $row->get_id_order(); ?>');" style="cursor:pointer;">
							
							<a class="btn btn-green btn-block btn-sm">
								ORDER #<?php echo $row->get_id_order(); ?>

								<span><?php echo date('M d, Y', strtotime($row->get_date_done())); ?></span>
							</a>

							<table>
								<thead>
									<tr>
										<th style="width:70%;">Description</th>
										<th>Color</th>
										<th>Size</th>
										<th>Quantity</th>
									</tr>
								</thead>
								<tbody>
								<?php
									$details = $order_detail->get_list($row->get_id_order(), " order by id_product ");
									foreach ($details as $row_detail) {

										$product	= new product();
										$product->map($row_detail->get_id_product());
								?>
								        <tr>
								        	<td class="item_description">
								        		<a target="_blank" href="<?php echo $product->get_link(); ?>">
								        			<img style="width:30px !important;" src="<?php echo $product->get_image_link(); ?>" alt="">
								        		</a>
								        		
								        		<div>
								        			<p class="item_name"><?php echo $product->get_name(); ?></p>
								        		</div>
								        	</td>
								        	<td>
								        		<?php if($row_detail->get_color() != ""){ ?>
								        			<span class="media-box-color"><span style="background:<?php echo $row_detail->get_color(); ?>;"></span></span>
								        		<?php } ?>
								        	</td>
								        	<td><?php echo $row_detail->get_size(); ?></td>
								        	<td><?php echo $row_detail->get_qty(); ?></td>
								        </tr>
								<?php				
									}
								?>
								</tbody>
							</table>	
						</div>
					<?php } ?>	
					<div class="select_order">
						<a href="javascript:send_msg('MY ORDER IS NOT VISIBLE');" class="btn btn-green btn-block" style="margin:0;">My Order Is Not Visible</a>
					</div>
				</div>	

			<!--
			/* ====================================================================== *
		        	CONTINUE
		 	 * ====================================================================== */	
		 	-->

				<div class="msg_input step_10">
					<a href="javascript:send_msg('CONTINUE');" class="btn btn-green btn-block">CONTINUE</a>
				</div>	

			<!--
			/* ====================================================================== *
		        	IT HAS RETURNED PRODUCTS
		 	 * ====================================================================== */	
		 	-->

				<div class="msg_input step_8">
					<div class="col-container">
						<div class="col-50"><a href="javascript:send_msg('MY ORDER');" class="btn btn-green btn-block">MY ORDER</a></div>
						<div class="col-50"><a href="javascript:send_msg('MY RETURNED PRODUCT(S)');" class="btn btn-green btn-block">MY RETURNED PRODUCT(S)</a></div>
					</div>
				</div>	

			<!--
			/* ====================================================================== *
		        	IT HAS BOTH RETURNED PRODUCTS AND RETURN REQUEST RAISED
		 	 * ====================================================================== */	
		 	-->

				<div class="msg_input step_5">
					<div class="col-container">
						<div class="col-33"><a href="javascript:send_msg('MY ORDER');" class="btn btn-green btn-block">MY ORDER</a></div>
						<div class="col-33"><a href="javascript:send_msg('MY RETURNED PRODUCT(S)');" class="btn btn-green btn-block">MY RETURNED PRODUCT(S)</a></div>
						<div class="col-33"><a href="javascript:send_msg('MY RETURN REQUEST RAISED');" class="btn btn-green btn-block">MY RETURN REQUEST RAISED</a></div>
					</div>
				</div>	
				
			<!--
			/* ====================================================================== *
		        	IT HAS RETURN REQUEST RAISED
		 	 * ====================================================================== */	
		 	-->

				<div class="msg_input step_9">
					<div class="col-container">
						<div class="col-50"><a href="javascript:send_msg('MY ORDER');" class="btn btn-green btn-block">MY ORDER</a></div>
						<div class="col-50"><a href="javascript:send_msg('MY RETURN REQUEST RAISED');" class="btn btn-green btn-block">MY RETURN REQUEST RAISED</a></div>
					</div>
				</div>		

			<!--
			/* ====================================================================== *
		        	SELECT THE PRODUCT OF THE ORDER
		 	 * ====================================================================== */	
		 	-->

				<div class="msg_input select_product step_20">
					<div class="select_product_loading"></div>
				</div>		
					
			</div>

		</div>

	</div>

	</div></div>

	<?php require_once("../footer.php"); ?>

	<script>

	/* ====================================================================== *
        	SCROLL TO THE BOTTOM
 	 * ====================================================================== */		

		function scroll_to_div(){
			//$(window).scrollTop($('.chat').offset().top + $('.chat').outerHeight(true) - $(window).height());
			$(window).scrollTop($('.new_message').offset().top + 80 - $(window).height());
		}
		scroll_to_div();

	/* ====================================================================== *
        	GET STEP
 	 * ====================================================================== */	

 	 	function get_step(){

 	 		$('.msg_loading').show();
 	 		
			$.get('get_step.php?id_conversation=<?php echo $_GET["id_conversation"]; ?>', function(r){

				$('.msg_loading').hide();								
				update_msg_input($.trim(r));

			});

		}		

		get_step();

	/* ====================================================================== *
        	GET PRODUCTS
 	 * ====================================================================== */		

 	 	function get_products(){
 	 		if($('.select_product').find('.select_product_loading')[0] !== undefined){

	 	 		$.post('get_products.php', 
					{ 
						id_conversation : "<?php echo $_GET["id_conversation"]; ?>"
					}, 
					function(r){
						
						if($.trim(r) != ''){
							$('.select_product').html($.trim(r));
						}
						scroll_to_div(); 

					});
	 	 		
			}
 	 	}

 	 	get_products();

	/* ====================================================================== *
        	DISPLAY THE CORRECT INPUT TOOL
 	 * ====================================================================== */

 	 	function update_msg_input(step){
 	 				
			$('.msg_input').hide();
			//$('.step_'+step).show().css('display', 'flex').find('input').focus();
			$('.step_'+step).show().find('input').focus();
			scroll_to_div(); 

 	 	}

	/* ====================================================================== *
        	SEND MSG
 	 * ====================================================================== */	

 	 	$(".msg_input .message").on('keyup', function (e) {
		    if (e.keyCode == 13) {
		        send_text_msg();
		    }
		});

 	 	function send_text_msg(){
 	 		send_msg($('.message').val());
 	 		$('.message').val('').focus();
 	 	}		

		function send_msg(msg){

			/* ### CHECK IF MESSAGE IS EMPTY ### */
			
			if(msg == ''){
				alert('Your message is empty!');
				return;
			}

			/* ### PREPARE NEW MESSAGE AND PUT IT IN THE CONVERSATION WITH A LOADING WHEEL ### */

			var new_msg = show_msg('self', msg, '<i class="fa fa-circle-o-notch fa-spin"></i>');
			scroll_to_div();

			/* ### TRY TO SEND THE MSG ### */

			$('.msg_loading').show();

			$.post('send_message.php', 
				{ 
					message 		: msg, 
					id_conversation : "<?php echo $_GET["id_conversation"]; ?>"
				}, 
				function(r){

					new_msg.find('.msg-date').html( r.error=='yes' ? '<i class="fa fa-times"></i> Error!' : r.customer_msg_date );

					if(r.admin_msg != '' && r.error=='no'){
						show_msg('other', r.admin_msg, r.admin_msg_date)
					}

					$('.msg_loading').hide();
					update_msg_input(r.new_step);

					get_products();

				});

		}

		function show_msg(from, msg, date){
			return $(' <div class="'+from+'"><div class="msg">'+msg+' <div class="msg-date">'+date+'</div></div></div>').insertBefore( $('.new_message') );
		}

	</script>
	
	</div>
</body>
</html>
