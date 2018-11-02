<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/message.php"); ?>
<?php require_once("../../classes/message_conversation.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
	<link rel="stylesheet" href="../../includes/css/chat.css">
	<link rel="stylesheet" href="../../includes/plugins/font-awesome/css/font-awesome.min.css">
	<style>
		.new_message {
			margin-top: 20px;
		    display: -webkit-flex !important;
		    display: -moz-flex !important;
		    display: -ms-flex !important;
		    display: -o-flex !important;
		    display: flex !important;
		}
	</style>
</head>
<body>

	<?php require_once("../navbar.php"); ?>

	<?php 
		if(!isset($_GET['id_fb_user'])){
			echo "<script>location.href='../messages';</script>";
			exit();
		}

		$id_fb_user 				= $_GET['id_fb_user'];
		$message 					= new message();
		$message_conversation 		= new message_conversation();

		$message_conversation->map($id_fb_user, $_GET['id_conversation']);

		$all_messages 		= $message->get_messages_by_user($id_fb_user, $_GET['id_conversation'], " order by date ");
	?>

	<div class="section">
		<div class="content">

			<form action="" class="form_send_msg">
			    <div class="chat">
					
					<?php foreach($all_messages as $row){ ?>
						<div class="<?php echo ( $row->get_from() == 'admin' || $row->get_from() == 'system' || $row->get_from() == 'smart_response' ) ? 'self' : 'other'; ?>">
							<div class="msg">
								<?php echo $row->get_message(); ?>
								<div class="msg-date"><?php echo $row->get_date(); ?></div>
							</div>
						</div>	
					<?php } ?>
					
					<?php if($message_conversation->get_status() == 'closed' || $message_conversation->get_status() == 'open'){ ?>
						<div class="new_message" style="min-height: 0;">
							<input type="text" class="form-control message" placeholder="Write a message here" />
							<a href="javascript:send_message('closed');" class="btn btn-green">Send & Close</a>
							<a href="javascript:send_message('pending on customer');" class="btn btn-gray">Send & Keep Open</a>
						</div>
					<?php }else{ ?>
						<div class="alert alert-info" style="margin:0;margin-top:20px;">
							You can't reply to a &nbsp;<strong>Pending On Customer</strong>&nbsp; conversation
						</div>
					<?php } ?>
				</div>
			</form>			

		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../messages"]').parents('li').addClass('active');</script>

	<script>
		function scroll_to_div(){
			$(window).scrollTop($('.chat').offset().top + $('.chat').outerHeight(true) - $(window).height());
		}
		scroll_to_div();

		$('.form_send_msg').on('submit', function(e){
		      e.preventDefault();
		      send_message('closed');
		 });

		function send_message(status){
			var msg_input	= $('.message');
			var msg 		= msg_input.val();

			if(msg != ''){
				msg_input.val('').focus();
				var new_msg 	= $(' 	<div class="self">'+
											'<div class="msg">'+
												msg+
												'<div class="msg-date"><i class="fa fa-circle-o-notch fa-spin"></i></div>'+
											'</div>'+
										'</div>');

				new_msg.insertBefore($('.new_message'));
				scroll_to_div();
				$.get('send_message.php?id_fb_user=<?php echo $id_fb_user; ?>&message='+msg+'&id_conversation=<?php echo $_GET["id_conversation"]; ?>&status='+status, function(r){
					if(r.indexOf('ERROR') > -1){
						r = '<i class="fa fa-times"></i> Error!';	
					}

					new_msg.find('.msg-date').html($.trim(r));
					scroll_to_div();
				});
			}else{
				alert('Make sure to write some message!');
			}

			return false;
		}
	</script>

	<script>
		//$('html, body').animate({ scrollTop: $(document).height() }, 1000);
		$('.new_message input').focus();
	</script>

</body>
</html>