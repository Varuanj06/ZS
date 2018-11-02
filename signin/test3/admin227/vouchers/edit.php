<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/voucher.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
</head>
<body>

	<?php require_once("../navbar.php"); ?>


	<?php 

		$param = "";
		if(isset($_GET['search'])){
			$param = "?search=".$_GET['search'];
		}

		$voucher = new voucher();
		$voucher->map($_GET['id_voucher']);

		$error  = "";
		if( isset($_POST['action']) ){
			
			$voucher->set_emails($_POST['emails']);
			$voucher->set_till_date($_POST['till_date']);
			$voucher->set_value_kind($_POST['value_kind']);
			$voucher->set_value($_POST['value']);
			$voucher->set_description($_POST['description']);
			$voucher->set_min_cart_value($_POST['min_cart_value']);
			$voucher->set_visibility(isset($_POST['visibility'])?"Y":"N");

			if( $voucher->update() ){
				$error = '<div class="alert alert-success"><strong>Awesome</strong>, You successfully updated the voucher.</div>';
				echo "<script>location.href='./$param'</script>";
				exit();
			}else{
				$error = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}
		}
	?>

	<div class="section">
		<div class="content">

			
			<h2>Edit Voucher</h2>
			<a href="./<?php echo $param; ?>" class="btn btn-default btn-sm">Go back</a>

			<hr>

			<?php echo $error; ?>
			
			<form action="" method="post" name="form">
				<input type="hidden" name="action" />

				<p>
					<label for="description">Description</label>
					<textarea class="custom-input" name="description" id="description" maxlength="300" placeholder="Some description goes here"><?php echo $voucher->get_description(); ?></textarea>
				</p>

				<p>
					<label for="emails">Emails</label>
					<textarea class="custom-input" name="emails" id="emails" maxlength="600" placeholder="/first@gmail.com/second@gmail.com/third@gmail.com/ OR /all/"><?php echo $voucher->get_emails(); ?></textarea>
				</p>

				<p>
					<label for="till_date">Till date</label>
					<input class="custom-input" name="till_date" id="till_date" maxlength="600" placeholder="Write the till date" value="<?php echo $voucher->get_till_date(); ?>" />
				</p>

				<p>
					<label for="value_kind">Value kind</label>
					<select name="value_kind" id="value_kind" class="form-control">
						<option value="percentage" <?php if($voucher->get_value_kind() == 'percentage'){echo "selected";} ?>>Percentage</option>
						<option value="amount" <?php if($voucher->get_value_kind() == 'amount'){echo "selected";} ?>>Amount</option>
					</select>
				</p>

				<p>
					<label for="value">Value</label>
					<input class="custom-input" name="value" id="value" maxlength="600" placeholder="Write the value" value="<?php echo $voucher->get_value(); ?>" />
				</p>

				<p>
					<label for="min_cart_value">Min cart value</label>
					<input class="custom-input" name="min_cart_value" id="min_cart_value" maxlength="600" placeholder="The minimum of the cart" value="<?php echo $voucher->get_min_cart_value(); ?>" />
				</p>

				<p>
					<label for="visibility">Visibility</label>
					<input class="custom-input" name="visibility" id="visibility" type="checkbox" <?php if($voucher->get_visibility() == 'Y'){echo "checked";} ?> />
				</p>

				<p>
					<a href="javascript:save();" class="btn btn-default btn-large btn-green">Save</a>
				</p>

			</form>


		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../vouchers"]').parents('li').addClass('active');</script>

	<script>
		function save(){

			if(document.form.name.value != ""){
				document.form.action.value = "1";
				document.form.submit();
			}else{
				alert('All fields are required.');
			}
		}	
	</script>

	<link href="../includes/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css" rel="stylesheet">
    <script src="../includes/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

    <script>
    	$('#till_date').datepicker({
    		 orientation: "bottom right",
    		 format: "yyyy-mm-dd",
    	});
    </script>

</body>
</html>