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

		$espresso_keywords 	= new espresso_keywords();
		$espresso_keywords->map($_GET['id_keyword']);

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

			if( !($espresso_keywords->exists($_GET['id_keyword']) && $espresso_keywords->update()) ){
				$error = true;
			}

			if($error){
			  	$conn->rollBack();
			  	$error = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}else{
			  	$conn->commit();
			  	$error = '<div class="alert alert-success"><strong>Awesome</strong>, You successfully updated the keyword.</div>';
				echo "<script>location.href='./'</script>";
				exit();
			}
		}
		
	?>

	<div class="section">
		<div class="content">

			
			<h2>Edit keyword</h2>
			<a href="./" class="btn btn-default btn-sm">Go back</a>

			<hr>

			<?php echo $error; ?>
			
			<form action="" method="post" name="form">
				<input type="hidden" name="action" />

				<p>
					<label for="keyword">Keyword</label>
					<input value="<?php echo $espresso_keywords->get_keyword(); ?>" class="custom-input" name="keyword" id="keyword" maxlength="600" placeholder="Write a keyword" />
				</p>

				<p>
					<label for="image">Image url</label>
					<input value="<?php echo $espresso_keywords->get_image(); ?>" class="custom-input" name="image" id="image" maxlength="600" placeholder="Write the image URL" />
				</p>

				<p>
					<label for="genders">Genders</label>
					<textarea class="custom-input" name="genders" id="genders" maxlength="300" placeholder="write genders here, the genders must have slashes between them (i.e. /male/female/)"><?php echo $espresso_keywords->get_genders(); ?></textarea>
				</p>

				<p>
					<label for="ages">Ages</label>
					<textarea class="custom-input" name="ages" id="ages" maxlength="600" placeholder="write ages here, the ages must have slashes between them (i.e. /15/16/17/18/19/)"><?php echo $espresso_keywords->get_ages(); ?></textarea>
				</p>

				<p>
					<label for="status">Status</label>
					<select name="status" id="status" class="form-control">
						<option value="active" <?php if($espresso_keywords->get_status() == 'active'){echo "selected";} ?>>Active</option>
						<option value="inactive" <?php if($espresso_keywords->get_status() == 'inactive'){echo "selected";} ?>>Inactive</option>
						<option value="BREWED" <?php if($espresso_keywords->get_status() == 'BREWED'){echo "selected";} ?>>BREWED</option>
						<option value="daily" <?php if($espresso_keywords->get_status() == 'daily'){echo "selected";} ?>>daily</option>

					</select>
				</p>

				<p>
					<label for="popular">Popular</label>
					<select name="popular" id="popular" class="form-control">
						<option value="yes" <?php if($espresso_keywords->get_popular() == 'yes'){echo "selected";} ?>>Yes</option>
						<option value="no" <?php if($espresso_keywords->get_popular() == 'no'){echo "selected";} ?>>No</option>
					</select>
				</p>

				<p>
					<label for="description">Description</label>
					<textarea class="custom-input" name="description" id="description" maxlength="2000" placeholder="write a description here"><?php echo $espresso_keywords->get_description(); ?></textarea>
				</p>

				<p>
					<label for="discount">Discount</label>
					<input class="custom-input" name="discount" id="discount" maxlength="60" placeholder="Write here the discount" value="<?php echo $espresso_keywords->get_discount()==''?'0':$espresso_keywords->get_discount(); ?>" />
				</p>

				<p>
					<label for="discount_type">Discount Type</label>
					<select name="discount_type" id="discount_type" class="form-control btn-block">
						<option <?php if($espresso_keywords->get_discount_type()=="amount"){echo "selected";} ?> value="amount">amount</option>
						<option <?php if($espresso_keywords->get_discount_type()=="percentage"){echo "selected";} ?> value="percentage">percentage</option>
					</select>
				</p>

				<p>
					<label for="booking_threshold">Booking Threshold</label>
					<input class="custom-input" name="booking_threshold" id="booking_threshold" maxlength="60" placeholder="Write here the booking threshold" value="<?php echo $espresso_keywords->get_booking_threshold(); ?>" />
				</p>

				<p>
					<label for="brand_link">Brand Link</label>
					<input class="custom-input" name="brand_link" id="brand_link" maxlength="60" placeholder="Write here the brand link" value="<?php echo $espresso_keywords->get_brand_link(); ?>" />
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