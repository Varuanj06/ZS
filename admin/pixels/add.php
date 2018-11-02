<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/pixel.php"); ?>
<?php require_once("../../classes/pixel_keyword.php"); ?>
<?php require_once("../../classes/keyword.php"); ?>

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

		$error  	= "";
		$pixel 		= new pixel();
		if( isset($_POST['action']) ){

			$new_keywords = "";
			foreach ($_POST['keywords'] as $row){
				$new_keywords .= $row."/";
			}

			$new_pixel_keywords = "";
			foreach ($_POST['pixel_keywords'] as $row){
				$new_pixel_keywords .= $row."/";
			}

			$pixel->set_id_vendor($_SESSION['id_vendor']);
			$pixel->set_name($_POST['name']);
			$pixel->set_image_link($_POST['image_link']);
			$pixel->set_keywords(format_string($new_keywords));
			$pixel->set_pixel_keywords(format_string($new_pixel_keywords));
			$pixel->set_vendor_link($_POST['vendor_link']);
			$pixel->set_price($_POST['price']);
			$pixel->set_discount($_POST['discount']);
			$pixel->set_discount_type($_POST['discount_type']);
			$pixel->set_type('pixel');

			if( $pixel->insert() ){
				$error = '<div class="alert alert-success"><strong>Awesome</strong>, You successfully added a new pixel.</div>';
			}else{
				$error = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}
			
		}
		
		$keyword 					= new keyword();
		$list_keywords 				= $keyword->get_list(" ORDER BY 1 ");

		$pixel_keyword 				= new pixel_keyword();
		$list_pixel_keywords 		= $pixel_keyword->get_list(" ORDER BY 1 ");
	?>

	<div class="section">
		<div class="content">

			
			<h2>Add a new product</h2>
			<a href="./pixels.php" class="btn btn-default btn-sm">Go back</a>

			<hr>

			<?php echo $error; ?>
			
			<form action="" method="post" name="form">
				<input type="hidden" name="action" />

				<p>
					<label for="name">Name*</label>
					<input class="custom-input" name="name" id="name" maxlength="200" placeholder="Write the pixel name" value="" />
				</p>

				<p>
					<label for="name">Image Link*</label>
					<input class="custom-input" name="image_link" id="image_link" maxlength="600" placeholder="The image link goes here" value="" />
				</p>

				<p>
					<label for="keywords">Keywords*</label>					
					<select name="keywords[]" id="keywords" multiple="multiple" class="form-control">
						<?php foreach ($list_keywords as $row) {  ?>
					  		<option value="<?php echo $row->get_keyword(); ?>" <?php if( strpos($pixel->get_keywords(), '/'.$row->get_keyword().'/') !== false ){ echo "selected"; } ?>><?php echo $row->get_keyword(); ?></option>
					  	<?php } ?>
					</select>					
				</p>

				<p>
					<label for="pixel_keywords">Pixel Keywords</label>					
					<select name="pixel_keywords[]" id="pixel_keywords" multiple="multiple" class="form-control">
						<?php foreach ($list_pixel_keywords as $row) {  ?>
					  		<option value="<?php echo $row->get_pixel_keyword(); ?>" <?php if( strpos($pixel->get_pixel_keywords(), '/'.$row->get_pixel_keyword().'/') !== false ){ echo "selected"; } ?>><?php echo $row->get_pixel_keyword(); ?></option>
					  	<?php } ?>
					</select>					
				</p>

				<p>
					<label for="vendor_link">Vendor Link</label>
					<input class="custom-input" name="vendor_link" id="vendor_link" maxlength="600" placeholder="The vendor link goes here" />
				</p>

				<p>
					<label for="name">Price*</label>
					<input class="custom-input" name="price" id="price" maxlength="60" placeholder="Specify the price" value="0" />
				</p>

				<p>
					<label for="discount_type">Discount Type*</label>
					<select name="discount_type" id="discount_type" class="form-control btn-block">
						<option value="amount">amount</option>
						<option value="percentage">percentage</option>
					</select>
				</p>

				<p>
					<label for="discount">Discount*</label>
					<input class="custom-input" name="discount" id="discount" maxlength="60" placeholder="Write here the discount" value="0" />
				</p>

				<p>
					<a href="javascript:save();" class="btn btn-default btn-large btn-green">Save</a>
				</p>

			</form>


		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../pixels"]').parents('li').addClass('active');</script>

	<link href="../includes/plugins/select2/css/select2.min.css" rel="stylesheet" />
	<script src="../includes/plugins/select2/js/select2.min.js"></script>
	<script>
	  	$('#keywords, #pixel_keywords').select2();
	</script>

	<script>
		function save(){

			if(
				document.form.name.value != "" && 
				document.form.image_link.value != "" && 
				document.form.keywords.value != "" && 
				document.form.price.value != "" && 
				document.form.discount.value != ""
			){
				document.form.action.value = "1";
				document.form.submit();
			}else{
				alert('All fields marked with * are required.');
			}
		}	
	</script>

</body>
</html>