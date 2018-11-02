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

		$voucher = new voucher();

		$error  = "";
		if( isset($_POST['action']) ){

			$voucher->set_id_voucher($voucher->max_id_voucher());
			$voucher->set_code(md5($voucher->get_id_voucher()));
			$voucher->set_emails($_POST['emails']);
			$voucher->set_till_date($_POST['till_date']);
			$voucher->set_value_kind($_POST['value_kind']);
			$voucher->set_value($_POST['value']);
			$voucher->set_description($_POST['description']);
			$voucher->set_min_cart_value($_POST['min_cart_value']);
			$voucher->set_visibility(isset($_POST['visibility'])?"Y":"N");
			
			if( $voucher->insert() ){
				$error = '<div class="alert alert-success"><strong>Awesome</strong>, You successfully added a new voucher.</div>';
			}else{
				$error = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}
			
		}

		$param = "";
		if(isset($_GET['search'])){
			$param = "?search=".$_GET['search'];
		}
		
	?>

	<div class="section">
		<div class="content">

			
			<h2>Add a new voucher</h2>
			<a href="./<?php echo $param; ?>" class="btn btn-default btn-sm">Go back</a>

			<hr>

			<?php echo $error; ?>
			
			<form action="" method="post" name="form">
				<input type="hidden" name="action" />

				<p>
					<label for="description">Description</label>
					<textarea class="custom-input" name="description" id="description" maxlength="300" placeholder="Some description goes here"></textarea>
				</p>
				
				<p>
					<label for="emails">Emails</label>
					<textarea class="custom-input" name="emails" id="emails" maxlength="600" placeholder="/first@gmail.com/second@gmail.com/third@gmail.com/ OR /all/"></textarea>
				</p>
				
				<p>
					<label for="value">Value</label>
					<input class="custom-input" name="value" id="value" maxlength="600" placeholder="Write the value" />
				</p>

				<p>
					<label for="till_date">Till date</label>
					<?php 
						$futureDate = date('Y-m-d', strtotime('+1 year'));
					?>
					<input class="custom-input" name="till_date" id="till_date" maxlength="600" placeholder="Write the till date" value="<?php echo $futureDate; ?>" />
				</p>

				<p>
					<label for="value_kind">Value kind</label>
					<select name="value_kind" id="value_kind" class="form-control">
						<option value="amount">Amount</option>
						<option value="percentage">Percentage</option>

					</select>
				</p>

				

				<p>
					<label for="min_cart_value">Min cart value</label>
					<input class="custom-input" name="min_cart_value" id="min_cart_value" maxlength="600" placeholder="The minimum of the cart" value="0"/>
				</p>

				<p>
					<label for="visibility">Visibility</label>
					<input class="custom-input" name="visibility" id="visibility" type="checkbox" value="Y" checked />
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

			if(
				document.form.emails.value != "" &&
				document.form.till_date.value != "" &&
				document.form.value.value != ""
			){
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
    		 format: "yyyy-mm-dd"
    	});
    </script>

</body>
</html>