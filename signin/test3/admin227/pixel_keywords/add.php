<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/pixel_keyword.php"); ?>

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

		$pixel_keyword = new pixel_keyword();

		$error  = "";
		if( isset($_POST['action']) ){

			$pixel_keyword->set_pixel_keyword(trim($_POST['pixel_keyword']));
			$pixel_keyword->set_image($_POST['image']);
			$pixel_keyword->set_genders(format_string($_POST['genders']));
			$pixel_keyword->set_ages(format_string($_POST['ages']));
			$pixel_keyword->set_status($_POST['status']);
			$pixel_keyword->set_expiry_date($_POST['expiry_date']);

			if( $pixel_keyword->insert() ){
				$error = '<div class="alert alert-success"><strong>Awesome</strong>, You successfully added a new pixel keyword.</div>';
			}else{
				$error = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}
			
		}
		
	?>

	<div class="section">
		<div class="content">

			
			<h2>Add a new pixel keyword</h2>
			<a href="./" class="btn btn-default btn-sm">Go back</a>

			<hr>

			<?php echo $error; ?>
			
			<form action="" method="post" name="form">
				<input type="hidden" name="action" />
				
				<p>
					<label for="pixel_keyword">Pixel keyword</label>
					<input class="custom-input" name="pixel_keyword" id="pixel_keyword" maxlength="600" placeholder="Write a pixel keyword" />
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
					</select>
				</p>

				<p>
					<label for="expiry_date">Expiry date</label>
					<input class="custom-input" name="expiry_date" id="expiry_date" maxlength="600" placeholder="click here and choose a date!" />
				</p>

				<p>
					<a href="javascript:save();" class="btn btn-default btn-large btn-green">Save</a>
				</p>

			</form>


		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../pixel_keywords"]').parents('li').addClass('active');</script>

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