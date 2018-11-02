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

		$message 			= new message();
		$pixel 				= new pixel();
		$pixel_count 		= new pixel_count();
		$fb_user_details 	= new fb_user_details();

		$all_pixels 	= $pixel->get_pixels_by_vendor($_SESSION['id_vendor'], " order by pixel_count desc ");

		$error  = "";
		if( isset($_POST['action']) && $_POST['action'] == '1' ){//erase one
			$id_pixel = $_POST['id_pixel'];

			$pixel = new pixel();
			$pixel->set_id_pixel($id_pixel);

			if( $pixel->exists() ){
				if($pixel->delete()){
					//all good
				}else{
					$error = '<div class="alert alert-danger"><strong>Oops</strong>, there was an error.</div>';
				}
			}

			$all_pixels 	= $pixel->get_pixels_by_vendor($_SESSION['id_vendor'], " order by pixel_count desc ");
		}else if( isset($_POST['action']) && $_POST['action'] == '2' ){//convert pixel to product
			
			$conn->beginTransaction(); //transaccion
			$error = false;

			foreach ($all_pixels as $row){
				if(isset( $_POST["convert_to_product-".$row->get_id_pixel()] )){

					$current_msg = str_replace("{{product_link}}", $_POST["product_link-".$row->get_id_pixel()], $_POST['message']);

					/* CONVERT PIXEL INTO PRODUCT */
					if(!$pixel->update_type($row->get_id_pixel(), 'product', $_POST["product_link-".$row->get_id_pixel()], $current_msg)){
						$error = true;
					}

					/* GET USERS THAT BOOKED THE PIXEL */
					$mobile_numbers 	= "";
					$fb_users 			= $pixel_count->get_users_by_pixel($row->get_id_pixel(), "");
					foreach ($fb_users as $row_inner) {
						$fb_user_details->map($row_inner->get_id_fb_user());

						$mobile_numbers .= $fb_user_details->get_mobile_number().',';

						/* SEND EMAIL */
						$mail 				= new Mail();
						$mail->send_mail($fb_user_details->get_email(), $pixel_mail_subject, $current_msg, $current_msg);
					}

					/* SEND SMS */
					if(count($fb_users)>0){
						$message->send_SMS(rtrim($mobile_numbers, ","), $current_msg);
					}

				}
			}

			if($error == false){
				$error = '<div class="alert alert-success"><strong>Awesome</strong>, You successfully converted pixels into products.</div>';
				$conn->commit();
				//$conn->rollback();
			}else{
				$error = '<div class="alert alert-danger"><strong>Oops</strong>, there was an error.</div>';
				$conn->rollback();
			}

			$all_pixels 	= $pixel->get_pixels_by_vendor($_SESSION['id_vendor'], " order by pixel_count desc ");
		}
		
	?>

	<div class="section">
		<div class="content">

			<h2>
				Pixels of <?php echo $vendor->get_name(); ?><br>
				<a href="./" class="btn btn-sm btn-gray">Go back</a>
				<a href="converted_pixels.php" class="btn btn-sm btn-green">Converted pixels</a>
				<a href="add.php" class="btn btn-sm">Add a new pixel</a>
			</h2>

			<?php echo $error; ?>

			<hr>
			
			<form action="" method="post" name="form">
				<input type="hidden" name="action" />
				<input type="hidden" name="id_pixel" />

				<table class="table table-condensed table-bordered table-striped">
					<thead>
						<tr>
							<th>#</th>
							<th>
								<input type="checkbox" class="check_all">
							</th>
							<th>Product Link</th>
							<th>Action</th>
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
										<input class="convert_to_product" type="checkbox" name="convert_to_product-<?php echo $row->get_id_pixel(); ?>">
									</td>
									<td>
										<input class="form-control" type="text" name="product_link-<?php echo $row->get_id_pixel(); ?>">
									</td>
									<td>
										<a href="javascript:erase('<?php echo $row->get_id_pixel(); ?>');" class="btn btn-red btn-sm"><i class="glyphicon glyphicon-trash"></i></a>
										<a href="edit.php?id_pixel=<?php echo $row->get_id_pixel(); ?>" class="btn btn-green btn-sm"><i class="glyphicon glyphicon-pencil"></i> &nbsp;Edit</a>
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
							<td colspan="2"><a data-toggle="modal" data-target="#myModal" class="btn btn-green btn-sm">Convert</a></td>
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
				        <h4 class="modal-title" id="myModalLabel">Message to customers</h4>
				      </div>
				      <div class="modal-body">
				        <textarea class="form-control" name="message" id="message" cols="30" rows="10"><?php echo $pixel_msg; ?></textarea>
				      </div>
				      <div class="modal-footer">
				        <button type="button" class="btn btn-gray" data-dismiss="modal">Close</button>
				        <a class="btn btn-green" href="javascript:convert_all();">Send and convert pixels</a>
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
		function erase(id_pixel){
			if( confirm("Are you sure?") ){
				document.form.action.value = "1";
				document.form.id_pixel.value = id_pixel;
				document.form.submit();
			}
		}

		function convert_all(){
			if($('#message').val()==''){
				alert('You must specify a message');
				return;
			}

			if( confirm("Are you sure?") ){
				document.form.action.value="2";
				document.form.submit();
			}
		}

		$('.check_all').on('click', function(){
			var checkBoxes = $('.convert_to_product');
			checkBoxes.prop("checked", $(this).prop("checked"));
		});
	</script>
	
</body>
</html>