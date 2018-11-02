<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/espresso_keywords.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
</head>
<body>

	<?php require_once("../navbar.php"); ?>


	<?php 

		function format_string($str){

			$str = trim($str);
			if($str[0] != "/"){
				$str = "/".$str;
			}
			if($str[strlen($str)-1] != "/"){
				$str = $str."/";
			}

			$str = str_replace("/ ", "/", $str);

			return $str;
		}

		$espresso_keywords 			= new espresso_keywords();

		$error  = "";
		if( isset($_POST['action']) ){

			$conn->beginTransaction();
			$error 			= false;

			$espresso_keywords->set_keyword(trim($_POST['keyword']));
			$espresso_keywords->set_image($_POST['image']);
			$espresso_keywords->set_genders(format_string($_POST['genders']));
			$espresso_keywords->set_ages(format_string($_POST['ages']));
			$espresso_keywords->set_status($_POST['status']);
			$espresso_keywords->set_description($_POST['description']);
			$espresso_keywords->set_discount($_POST['discount']);
			$espresso_keywords->set_discount_type($_POST['discount_type']);
			$espresso_keywords->set_popular($_POST['popular']);
			$espresso_keywords->set_booking_threshold($_POST['booking_threshold']);
			$espresso_keywords->set_brand_link($_POST['brand_link']);

			if( !$espresso_keywords->insert() ){
				$error = true;
			}

			if($error){
			  $conn->rollBack();
			  $error = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}else{
			  $conn->commit();
			  $error = '<div class="alert alert-success"><strong>Awesome</strong>, You successfully added a new keyword.</div>';
			}
			
		}
		
	?>

	<div class="section">
		<div class="content">

			
			<h2>Add a new keyword</h2>
			<a href="./" class="btn btn-default btn-sm">Go back</a>

			<hr>

			<?php echo $error; ?>
			
			<form action="" method="post" name="form">
				<input type="hidden" name="action" />

				<p>
					<label for="keyword">Keyword</label>
					<input class="custom-input" name="keyword" id="keyword" maxlength="600" placeholder="Write a keyword" />
				</p>

				<p>
					<label for="image">Image url</label>
					<input class="custom-input" name="image" id="image" maxlength="600" placeholder="Write the image URL" />
				</p>

				<p>
					<label for="genders">Genders</label>
					<textarea class="custom-input" name="genders" id="genders" maxlength="300" placeholder="write genders here, the genders must have slashes between them (i.e. /male/female/)"></textarea>
				</p>

				<p>
					<label for="ages">Ages</label>
					<textarea class="custom-input" name="ages" id="ages" maxlength="600" placeholder="write ages here, the ages must have slashes between them (i.e. /15/16/17/18/19/)"></textarea>
				</p>

				<p>
					<label for="status">Status</label>
					<select name="status" id="status" class="form-control">
						<option value="active">Active</option>
						<option value="inactive">Inactive</option>
						<option value="BREWED">BREWED</option>
						<option value="daily">daily</option>

					</select>
				</p>

				<p>
					<label for="popular">Popular</label>
					<select name="popular" id="popular" class="form-control">
						<option value="yes">Yes</option>
						<option value="no">No</option>
					</select>
				</p>

				<p>
					<label for="description">Description</label>
					<textarea class="custom-input" name="description" id="description" maxlength="2000" placeholder="write a description here"></textarea>
				</p>

				<p>
					<label for="discount">Discount</label>
					<input class="custom-input" name="discount" id="discount" maxlength="60" placeholder="Write here the discount" value="0" />
				</p>

				<p>
					<label for="discount_type">Discount Type</label>
					<select name="discount_type" id="discount_type" class="form-control btn-block">
						<option value="amount">amount</option>
						<option value="percentage">percentage</option>
					</select>
				</p>

				<p>
					<label for="booking_threshold">Booking Threshold</label>
					<input class="custom-input" name="booking_threshold" id="booking_threshold" maxlength="60" placeholder="Write here the booking threshold" value="" />
				</p>

				<p>
					<label for="brand_link">Brand Link</label>
					<input class="custom-input" name="brand_link" id="brand_link" maxlength="60" placeholder="Write here the brand link" value="" />
				</p>

				<p>
					<a href="javascript:save();" class="btn btn-default btn-large btn-green">Save</a>
				</p>

			</form>


		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../espresso_keywords"]').parents('li').addClass('active');</script>

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
    	$('#expiry_date').datepicker({
    		 orientation: "bottom right",
    		 format: "yyyy-mm-dd"
    	});
    </script>

</body>
</html>