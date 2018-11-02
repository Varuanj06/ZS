<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/keyword.php"); ?>
<?php require_once("../../classes/keyword_globalinfo.php"); ?>
<?php require_once("../../classes/product.php"); ?>
<?php require_once("../../classes/pixel.php"); ?>

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

		$keyword 			= new keyword();
		$keyword_globalinfo = new keyword_globalinfo();
		$product 			= new product();
		$pixel 				= new pixel();

		$keyword->map($_GET['keyword']);
		$keyword_globalinfo->map($_GET['keyword']);

		$error  = "";
		if( isset($_POST['action']) ){

			$conn->beginTransaction();
			$error 			= false;
			
			$keyword->set_keyword(trim($_POST['keyword']));
			$keyword->set_image($_POST['image']);
			$keyword->set_genders(format_string($_POST['genders']));
			$keyword->set_ages(format_string($_POST['ages']));
			$keyword->set_status($_POST['status']);
			$keyword->set_profiles(format_string($_POST['profiles']));
			$keyword->set_description($_POST['description']);
			$keyword->set_discount($_POST['discount']);
			$keyword->set_discount_type($_POST['discount_type']);
			$keyword->set_popular($_POST['popular']);
			$keyword->set_global($_POST['global']);

			if(!$product->update_keyword_data($keyword->get_keyword(), $keyword->get_genders(), $keyword->get_ages(), $_GET['keyword'])){
				$error = true;
			}

			if(!$pixel->update_keyword($keyword->get_keyword(), $_GET['keyword'])){
				$error = true;
			}

			if( !($keyword->exists($_GET['keyword']) && $keyword->update($_GET['keyword'])) ){
				$error = true;
			}

			if(!$product->update_global(trim($_POST['keyword']), $_POST['global'])){
				$error = true;
			}

			if($_POST['global'] == 'yes'){
				$keyword_globalinfo->set_keyword(trim($_POST['keyword']));
				$keyword_globalinfo->set_international_shipping_cost($_POST['international_shipping_cost']);
				$keyword_globalinfo->set_domestic_shipping_cost($_POST['domestic_shipping_cost']);
				$keyword_globalinfo->set_vendor_commission_percentage($_POST['vendor_commission_percentage']);
				$keyword_globalinfo->set_shipping_days($_POST['shipping_days']);
				$keyword_globalinfo->set_custom_duty_percentage($_POST['custom_duty_percentage']);
				$keyword_globalinfo->set_expiry_date($_POST['expiry_date']);

				if($keyword_globalinfo->exists(trim($_POST['keyword']))){
					if(!$keyword_globalinfo->update($_GET['keyword'])){
						$error = true;
					}	
				}else{
					if(!$keyword_globalinfo->insert()){
						$error = true;
					}
				}

			}else{
				$keyword_globalinfo->set_keyword(trim($_POST['keyword']));
				if(!$keyword_globalinfo->delete()){
					$error = true;
				}
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

				<div class="row">
					<div class="col-sm-7">

						<p>
							<label for="keyword">Keyword</label>
							<input value="<?php echo $_GET['keyword']; ?>" class="custom-input" name="keyword" id="keyword" maxlength="600" placeholder="Write a keyword" />
						</p>

						<p>
							<label for="image">Image url</label>
							<input value="<?php echo $keyword->get_image(); ?>" class="custom-input" name="image" id="image" maxlength="600" placeholder="Write the image URL" />
						</p>

						<p>
							<label for="genders">Genders</label>
							<textarea class="custom-input" name="genders" id="genders" maxlength="300" placeholder="write genders here, the genders must have slashes between them (i.e. /male/female/)"><?php echo $keyword->get_genders(); ?></textarea>
						</p>

						<p>
							<label for="ages">Ages</label>
							<textarea class="custom-input" name="ages" id="ages" maxlength="600" placeholder="write ages here, the ages must have slashes between them (i.e. /15/16/17/18/19/)"><?php echo $keyword->get_ages(); ?></textarea>
						</p>

						<p>
							<label for="status">Status</label>
							<select name="status" id="status" class="form-control">
								<option value="active" <?php if($keyword->get_status() == 'active'){echo "selected";} ?>>Active</option>
								<option value="inactive" <?php if($keyword->get_status() == 'inactive'){echo "selected";} ?>>Inactive</option>
							</select>
						</p>

						<p>
							<label for="popular">Popular</label>
							<select name="popular" id="popular" class="form-control">
								<option value="yes" <?php if($keyword->get_popular() == 'yes'){echo "selected";} ?>>Yes</option>
								<option value="no" <?php if($keyword->get_popular() == 'no'){echo "selected";} ?>>No</option>
							</select>
						</p>

						<p>
							<label for="profiles">Profiles</label>
							<textarea class="custom-input" name="profiles" id="profiles" maxlength="2000" placeholder="write profiles here, the profiles must have slashes between them (i.e. /mx4k/fa3k/)"><?php echo $keyword->get_profiles(); ?></textarea>
						</p>

						<p>
							<label for="description">Description</label>
							<textarea class="custom-input" name="description" id="description" maxlength="2000" placeholder="write a description here"><?php echo $keyword->get_description(); ?></textarea>
						</p>

						<p>
							<label for="discount_type">Discount Type</label>
							<select name="discount_type" id="discount_type" class="form-control btn-block">
								<option <?php if($keyword->get_discount_type()=="amount"){echo "selected";} ?> value="amount">amount</option>
								<option <?php if($keyword->get_discount_type()=="percentage"){echo "selected";} ?> value="percentage">percentage</option>
							</select>
						</p>

						<p>
							<label for="discount">Discount</label>
							<input class="custom-input" name="discount" id="discount" maxlength="60" placeholder="Write here the discount" value="<?php echo $keyword->get_discount()==''?'0':$keyword->get_discount(); ?>" />
						</p>

					</div>
					<div class="col-sm-5" style="border: 1px solid #d2d2d2; padding: 15px; margin-bottom: 15px;">
						
						<p>
							<label for="global">Global?</label>
							<select name="global" id="global" class="form-control btn-block" onchange="toggle_globalinfo()">
								<option value="no" <?php if($keyword->get_global() == 'no'){echo 'selected';} ?>>No</option>
								<option value="yes" <?php if($keyword->get_global() == 'yes'){echo 'selected';} ?>>Yes</option>
							</select>
						</p>

						<div class="globalinfo">

							<p>
								<label for="international_shipping_cost">International Shipping Cost</label>
								<input class="custom-input" name="international_shipping_cost" id="international_shipping_cost" maxlength="20" placeholder="Write something" value="<?php echo $keyword_globalinfo->get_international_shipping_cost(); ?>" />
							</p>

							<p>
								<label for="domestic_shipping_cost">Domestic Shipping Cost</label>
								<input class="custom-input" name="domestic_shipping_cost" id="domestic_shipping_cost" maxlength="20" placeholder="Write something" value="<?php echo $keyword_globalinfo->get_domestic_shipping_cost(); ?>" />
							</p>

							<p>
								<label for="vendor_commission_percentage">Vendor Comission Percentage</label>
								<input class="custom-input" name="vendor_commission_percentage" id="vendor_commission_percentage" maxlength="20" placeholder="Write something" value="<?php echo $keyword_globalinfo->get_vendor_commission_percentage(); ?>" />
							</p>

							<p>
								<label for="shipping_days">Shipping Days</label>
								<input class="custom-input" name="shipping_days" id="shipping_days" maxlength="20" placeholder="Write something" value="<?php echo $keyword_globalinfo->get_shipping_days(); ?>" />
							</p>

							<p>
								<label for="custom_duty_percentage">Custom Duty Percentage</label>
								<input class="custom-input" name="custom_duty_percentage" id="custom_duty_percentage" maxlength="20" placeholder="Write something" value="<?php echo $keyword_globalinfo->get_custom_duty_percentage(); ?>" />
							</p>

							<p style="margin:0;">
								<label for="expiry_date">Expiry date</label>
								<input value="<?php echo $keyword_globalinfo->get_expiry_date() == '0000-00-00' ? '' : $keyword_globalinfo->get_expiry_date() ; ?>" class="custom-input" name="expiry_date" id="expiry_date" maxlength="600" placeholder="click here and choose a date!" />
							</p>

						</div>

					</div>
				</div>

				<p>
					<a href="javascript:save();" class="btn btn-default btn-large btn-green">Save</a>
				</p>

			</form>


		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../keywords"]').parents('li').addClass('active');</script>

	<script>
		function save(){

			if(document.form.global.value == 'yes' &&
				(
					document.form.international_shipping_cost.value == "" ||
					document.form.domestic_shipping_cost.value == "" ||
					document.form.vendor_commission_percentage.value == "" ||
					document.form.shipping_days.value == "" ||
					document.form.custom_duty_percentage.value == "" ||
					document.form.expiry_date.value == ""
				)
			){
				alert('All global fields are required');
				return;
			}

			if(document.form.name.value != ""){
				document.form.action.value = "1";
				document.form.submit();
			}else{
				alert('All fields are required.');
			}
		}	

		function toggle_globalinfo(){
			if($('#global').val() == 'no'){
				$('.globalinfo').hide()
			}else{
				$('.globalinfo').show()
			}
		}
		toggle_globalinfo();
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