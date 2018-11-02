<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/pixel.php"); ?>
<?php require_once("../../classes/pixel_count.php"); ?>
<?php require_once("../../classes/vendor.php"); ?>
<?php require_once("../../classes/message.php"); ?>
<?php require_once("../../classes/fb_user_details.php"); ?>
<?php require_once("../../includes/plugins/PHPMailer_class.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
</head>
<body>

	<?php require_once("../navbar.php"); ?>

	<?php 

		$id_vendor = isset($_SESSION['id_vendor'])?$_SESSION['id_vendor']:"";
		$vendor = new vendor();
		$vendor->set_id_vendor($id_vendor);
		if( $vendor->exists() == false ){
			echo "<script>location.href='./';</script>";	
			exit();
		}
		$vendor->map($id_vendor);

		$pixel 				= new pixel();
		$pixel_count 		= new pixel_count();
		$fb_user_details 	= new fb_user_details();
		$message 			= new message();

		$all_pixels 	= $pixel->get_converted_pixels_by_vendor($_SESSION['id_vendor'], " order by pixel_count desc ");
		$msg 			= "";

		if( isset($_POST['action']) && $_POST['action'] == '1' ){//send reminder
			
			foreach ($all_pixels as $row){
				if(isset( $_POST["send_reminder-".$row->get_id_pixel()] )){

					$closest_expiry_date = $pixel->get_closest_expiry_date_from_pixel_keyword($row->get_id_pixel());

					if($closest_expiry_date!=false){

						$from_date 	= date_create();
						$till_date 	= date_create("$closest_expiry_date 00:00:00");
						
						$diff 		= date_diff($from_date, $till_date);
						$years 		= $diff->y;
						$months  	= $diff->m;
						$days 		= $diff->d;
						$hours 		= $diff->h;
						$minutes 	= $diff->i;
						$seconds 	= $diff->s;

						$expiry_msg = ($months>0?"$months months, ":"") . "$days days, $hours hours and $minutes minutes";

						$current_msg = $_POST['message'];
						$current_msg = str_replace("{{product_link}}", $row->get_product_link(), $current_msg);
						$current_msg = str_replace("{{pixel_keyword_expiry}}", $expiry_msg, $current_msg);

						/* GET USERS THAT BOOKED THE PIXEL */
						$mobile_numbers 	= "";
						$fb_users 			= $pixel_count->get_users_by_pixel($row->get_id_pixel(), "");
						foreach ($fb_users as $row_inner) {
							$fb_user_details->map($row_inner->get_id_fb_user());

							$mobile_numbers .= $fb_user_details->get_mobile_number().',';

							/* SEND EMAIL */
							$mail 				= new Mail();
							$mail->send_mail($fb_user_details->get_email(), $pixel_reminder_subject, $current_msg, $current_msg);
						}

						/* SEND SMS */
						if(count($fb_users)>0){
							$message->send_SMS(rtrim($mobile_numbers, ","), $current_msg);
						}

					}

				}
			}

			$msg 	= "<div class='alert alert-success'>Request Sent</div>";

		}
	?>

	<div class="section">
		<div class="content">

			<h2>
				Converted Pixels of <?php echo $vendor->get_name(); ?><br>
				<a href="./pixels.php" class="btn btn-sm btn-gray">Go back</a>
			</h2>
		
			<?php echo $msg; ?>

			<hr>

			<form action="" method="post" name="form">
				<input type="hidden" name="action" />
			
				<table class="table table-condensed table-bordered table-striped">
					<thead>
						<tr>
							<th>#</th>
							<th>
								<input type="checkbox" class="check_all">
							</th>
							<th>Product Link</th>
							<th>Count</th>
							<th>Name</th>	
							<th>Image</th>
							<th>Keywords</th>
							<th>Pixel Keywords</th>
							<th>Vendor link</th>
							<th>Price</th>							
							<th>Discount</th>
							<th>Discount Type</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$count = 0;
							foreach ($all_pixels as $row){
								$count++;
						?>
								<tr>
									<td><?php echo $count; ?></td>
									<td>
										<input class="send_reminder" type="checkbox" name="send_reminder-<?php echo $row->get_id_pixel(); ?>">
									</td>
									<td> 
										<a target="_blank" href="<?php echo $row->get_product_link(); ?>">Link</a> 
									</td>
									<td><?php echo $row->get_pixel_count(); ?></td>
									<td><?php echo $row->get_name(); ?></td>
									<td><img src="<?php echo $row->get_image_link(); ?>" height="60px" alt=""></td>
									<td><?php echo $row->get_keywords(); ?></td>
									<td><?php echo $row->get_pixel_keywords(); ?></td>
									<td>
										<a href="<?php echo $row->get_vendor_link(); ?>" target="_blank">Link</a>
									</td>
									<td class="text-right"><?php echo number_format($row->get_price(), 2); ?></td>
									<td class="text-right"><?php echo number_format($row->get_discount(), 2); ?></td>
									<td><?php echo $row->get_discount_type(); ?></td>
								</tr>
						<?php
							}
						?>
					</tbody>
					<thead>
						<tr>
							<td>&nbsp;</td>
							<td colspan="2"><a data-toggle="modal" data-target="#myModal" class="btn btn-green btn-sm">Send a Reminder</a></td>
							<td colspan="11">&nbsp;</td>
						</tr>
					</thead>
				</table>

				<!-- Modal -->
				<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				  <div class="modal-dialog" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				        <h4 class="modal-title" id="myModalLabel">Send Reminder</h4>
				      </div>
				      <div class="modal-body">
				        <textarea class="form-control" name="message" id="message" cols="30" rows="10"><?php echo $pixel_reminder_msg; ?></textarea>
				      </div>
				      <div class="modal-footer">
				        <button type="button" class="btn btn-gray" data-dismiss="modal">Close</button>
				        <a class="btn btn-green" href="javascript:send_reminder();">Send Reminder</a>
				      </div>
				    </div>
				  </div>
				</div>
	
			</form>

		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../pixels"]').parents('li').addClass('active');</script>

	<script>
		function send_reminder(){
			if($('#message').val()==''){
				alert('You must specify a message');
				return;
			}

			if( confirm("Are you sure?") ){
				document.form.action.value="1";
				document.form.submit();
			}
		}

		$('.check_all').on('click', function(){
			var checkBoxes = $('.send_reminder');
			checkBoxes.prop("checked", $(this).prop("checked"));
		});
	</script>
	
</body>
</html>