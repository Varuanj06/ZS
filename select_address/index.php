<?php require_once("../fb_validator.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/order.php"); ?>
<?php require_once("../classes/order_detail.php"); ?>
<?php require_once("../classes/address.php"); ?>

<!doctype html>
<html lang="en">
<head>

  	<?php require_once("../head.php"); ?>

	<?php
		$id_fb_user 		= $user['id'];
		$order 				= new order();
		$address 			= new address();
		$order_detail 		= new order_detail();
		$error 				= "";

		$current_id_order   = "";
		$details 			= array();
		if($order->get_id_order_by_fb_user($id_fb_user)){
			$current_id_order = $order->get_id_order_by_fb_user($id_fb_user);
			$details = $order_detail->get_list($current_id_order, " order by id_product ");
		}

		if($current_id_order == "" || count($details) == 0){
			echo "<script>location.href='../cart';</script>";
			exit();
		}

		if(isset($_POST['action']) && $_POST['action'] == '1'){
			$error = false;
			$conn->beginTransaction();

			$address 			= new address();
			$id_address 		= "";

			$address->set_id_fb_user($id_fb_user);
			$address->set_name($_POST['name']);
			$address->set_mobile_number($_POST['mobile_number']);
			$address->set_address($_POST['address_text']);
			$address->set_landmark($_POST['landmark']);
			$address->set_city($_POST['city']);
			$address->set_state($_POST['state']);
			$address->set_pin_code($_POST['pin_code']);
			$address->set_email(str_replace(' ', '', $_POST['email']));

			if($_POST['address'] == 'new'){
				$id_address = $address->max_id_address($id_fb_user);
				$address->set_id_address($id_address);

				if(!$address->insert()){
					$error = true;
				}
			}else{
				$id_address = $_POST['address'];
				$address->set_id_address($id_address);
				if(!$address->update()){
					$error = true;
				}
			}

			if($error){
		      $conn->rollBack();
		      $error = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}else{
		      $conn->commit();
		      echo "<script>location.href='../check_voucher';</script>";
			  exit();
			}
		}
	?>

	<style>
		input[type="text"]{
			border: 1px solid #ccc;
			height:24px;
			padding: 6px 8px;
			display: block;
		}
		.row{
			margin-bottom: 10px;
		}
	</style>
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

	<script>$('.nav-right a[href="../cart"]').addClass('selected');</script>

	<div class="tabs-container">

		<h2><i class="fa fa-truck"></i> Ship to </h2>
		<a href="../cart" class="btn btn-sm btn-green">go back</a>	
		<br><br>

		<?php echo $error; ?>
	
		<div class="cart-item">
			<form action="" name="form" method="post">
				<input type="hidden" name="action">	
				<p>
					<?php 
						$addresses 			= $address->get_list($id_fb_user, " order by date_update desc ");
						$current_address	= "new";

						if(isset($_POST['address'])){
							$current_address = $_POST['address'];
						}
					?>
					<select name="address" id="address" class="form-control" onchange="change_address();">
						<option value="new" <?php if($current_address == 'new'){echo "selected";} ?>>New Address</option>
						<?php foreach ($addresses as $row){ if(!isset($_POST['address']) && $current_address == "new"){$current_address = $row->get_id_address();} ?>
							<option value="<?php echo $row->get_id_address(); ?>" <?php if($current_address == $row->get_id_address()){echo "selected";} ?>><?php echo $row->get_name().", ".$row->get_address().", ".$row->get_city().", ".$row->get_state(); ?></option>
						<?php } ?>
					</select>
				</p>

				<br>
				<?php 
					$address = new address();
					if($current_address != 'new'){
						$address->map($current_address, $id_fb_user);
					}
				?>
				
				<div class="row">
					<div class="col-sm-2"><label for="name">Name</label></div>
					<div class="col-sm-6"><input value="<?php echo $address->get_name(); ?>" type="text" name="name" maxlength="300" class="form-control"></div>
				</div>				
				
				<div class="row">
					<div class="col-sm-2"><label for="mobile_number">Mobile Number</label></div>
					<div class="col-sm-6"><input value="<?php echo $address->get_mobile_number(); ?>" type="text" name="mobile_number" maxlength="300" class="form-control"></div>
				</div>
				
				<div class="row">
					<div class="col-sm-2"><label for="address_text">Address</label></div>
					<div class="col-sm-6"><input value="<?php echo $address->get_address(); ?>" type="text" name="address_text" maxlength="400" class="form-control"></div>
				</div>
				
				<div class="row">
					<div class="col-sm-2"><label for="landmark">Landmark</label></div>
					<div class="col-sm-6"><input value="<?php echo $address->get_landmark(); ?>" type="text" name="landmark" maxlength="300" class="form-control"></div>
				</div>
				
				<div class="row">
					<div class="col-sm-2"><label for="city">City</label></div>
					<div class="col-sm-6"><input value="<?php echo $address->get_city(); ?>" type="text" name="city" maxlength="300" class="form-control"></div>
				</div>
				
				<div class="row">
					<div class="col-sm-2"><label for="state">State</label></div>
					<div class="col-sm-6"><input value="<?php echo $address->get_state(); ?>" type="text" name="state" maxlength="300" class="form-control"></div>
				</div>
				
				<div class="row">
					<div class="col-sm-2"><label for="pin_code">Pin code</label></div>
					<div class="col-sm-6"><input value="<?php echo $address->get_pin_code(); ?>" type="text" name="pin_code" maxlength="300" class="form-control"></div>
				</div>

				<div class="row">
					<div class="col-sm-2"><label for="email">Email</label></div>
					<div class="col-sm-6"><input value="<?php echo $address->get_email(); ?>" type="text" name="email" maxlength="300" class="form-control"></div>
				</div>
			</form>

			<br>
			
			<p>
				<a href="javascript:save();" class="btn btn-green btn-default">Ship to this Address</a>
			</p>
		</div>	

	</div> <!-- End tabs-container -->
	
	<script>
		var submit = false

		function save(){
			if(submit == true){
				return;
			}
			if( 
				document.form.name.value != "" && 
				document.form.mobile_number.value != "" && 
				document.form.address_text.value != "" && 
				document.form.landmark.value != "" && 
				document.form.city.value != "" && 
				document.form.state.value != "" &&
				document.form.pin_code.value != "" &&
				document.form.email.value != "" 
			){
				document.form.action.value = "1";
				document.form.submit();
			}else{
				alert('All fileds are required!')
			}
		}

		function change_address(){
			submit = true;
			document.form.submit();
		}
	</script>
	
	</div></div>
	<?php require_once("../footer.php"); ?>
	
	</div>
</body>

</html>