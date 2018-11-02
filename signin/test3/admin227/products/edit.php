<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/keyword.php"); ?>
<?php require_once("../../classes/product.php"); ?>
<?php require_once("../../classes/product_globalinfo.php"); ?>
<?php require_once("../../classes/product_stock.php"); ?>
<?php require_once("../../classes/product_lang.php"); ?>
<?php require_once("../../classes/image.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
</head>
<body>

	<?php require_once("../navbar.php"); ?>

	<?php 

	/* ====================================================================== *
        	CLASSES
 	* ====================================================================== */	

 		$keyword 				= new keyword();
 		$product 				= new product();
 		$product_globalinfo 	= new product_globalinfo();
 		$product_stock 			= new product_stock();
		$product_lang 			= new product_lang();
		$image 					= new image();

 	/* ====================================================================== *
        	VARIABLES
 	* ====================================================================== */							

		$id_product = $_GET['id_product'];
		$product->map($id_product);
		$product_globalinfo->map($id_product);

	/* ====================================================================== *
        	IF THE ID_PRODUCT DOESN'T EXISTS THEN GO BACK
 	* ====================================================================== */								

		if( !isset($id_product) || !$product->exists() ){
			echo "<script>location.href='./';</script>";
		}

	/* ====================================================================== *
        	UPDATE PRODUCT
 	* ====================================================================== */								

 		$keyword->map( substr($product->get_keywords(), 1, -1) );

		$error  = "";
		if( isset($_POST['action']) ){

			$conn->beginTransaction();
			$error 			= false;

			$product->map($id_product);

			$id_product_prestashop 	= $_POST['id_product_prestashop'];
			$id_image 				= $image->get_images($id_product_prestashop)[0];

			$product->set_id_product($id_product);
			$product->set_id_product_prestashop($id_product_prestashop);
			$product->set_name($product_lang->get_product_name($id_product_prestashop));
			$product->set_link( "http://miracas.com/$id_product_prestashop-.html" );
			$product->set_image_link( "http://miracas.miracaslifestyle.netdna-cdn.com/$id_product_prestashop-$id_image/$id_image.jpg" );
			$product->set_discount($_POST['discount']);
			$product->set_discount_type($_POST['discount_type']);
			$product->set_global($keyword->get_global());

			if( !($product->exists() && $product->update()) ){
				$error = true;
			}

			if(isset($_POST['color-size'])){

				$product_stock->delete_product($id_product);

				foreach ($_POST['color-size'] as $key => $value) {
					if($value != ''){
						$split 		= explode('@@', $key);
						$color 		= $split[0];
						$size 		= $split[1];

						$product_stock->set_id_product($id_product);
						$product_stock->set_id_product_stock($product_stock->max_id_product_stock($id_product));
						$product_stock->set_color($color);
						$product_stock->set_size($size);
						$product_stock->set_stock($value);

						if(!$product_stock->insert()){
							$error = true;
						}
					}
				}
			}

			if($keyword->get_global() == 'yes'){
				$product_globalinfo->set_id_product($id_product);
				$product_globalinfo->set_vendor_price($_POST['vendor_price']);
				$product_globalinfo->set_vendor_link($_POST['vendor_link']);
				$product_globalinfo->set_status($_POST['status']);

				if($product_globalinfo->exists()){
					if(!$product_globalinfo->update()){
						$error = true;
					}
				}else{
					if(!$product_globalinfo->insert()){
						$error = true;
					}
				}
			}

			if($error){
			  	$conn->rollBack();
			 	$error = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}else{
			  	$conn->commit();
			  	$error = '<div class="alert alert-success"><strong>Awesome</strong>, You successfully updated a new product.</div>';
			}
		}
		
	?>

	<div class="section">
		<div class="content">

			
			<h2>Edit the product</h2>
			<a href="./" class="btn btn-default btn-sm">Go back</a>

			<hr>

			<?php echo $error; ?>
			
			<form action="" method="post" name="form">
				<input type="hidden" name="action" />

				<div class="row">
					<div class="col-sm-7">

						<p>
							<label for="name">Product</label>
							<?php $product_lang = new product_lang(); ?>
							<select name="id_product_prestashop" id="id_product_prestashop" class="chosen-select btn-block" onchange="get_color_size();">
								<option value="<?php echo $product->get_id_product_prestashop(); ?>"><?php echo $product_lang->get_product_name( $product->get_id_product_prestashop() ); ?></option>
							</select>
						</p>

						<p>
							<label for="discount_type">Discount Type</label>
							<select name="discount_type" id="discount_type" class="form-control btn-block">
								<option <?php if($product->get_discount_type()=="amount"){echo "selected";} ?> value="amount">amount</option>
								<option <?php if($product->get_discount_type()=="percentage"){echo "selected";} ?> value="percentage">percentage</option>
							</select>
						</p>

						<p>
							<label for="discount">Discount</label>
							<input class="custom-input" name="discount" id="discount" maxlength="60" placeholder="Write here the discount" value="<?php echo $product->get_discount(); ?>" />
						</p>

						<div class="color-size"></div>

					</div>
					<?php if($keyword->get_global() == 'yes'){ ?>
						<div class="col-sm-5" style="border: 1px solid #d2d2d2; padding: 15px; margin-bottom: 15px;">

							<h4>Global Info</h4>

							<hr>
							
							<p>
								<label for="vendor_price">Vendor Price</label>
								<input class="custom-input" name="vendor_price" id="vendor_price" maxlength="20" placeholder="Write something" value="<?php echo $product_globalinfo->get_vendor_price(); ?>" />
							</p>

							<p>
								<label for="vendor_link">Vendor Link</label>
								<input class="custom-input" name="vendor_link" id="vendor_link" maxlength="200" placeholder="Write something" value="<?php echo $product_globalinfo->get_vendor_link(); ?>" />
							</p>

							<p>
								<label for="status">Status</label>
								<select name="status" id="status" class="form-control">
									<option value="active" <?php if($product_globalinfo->get_status()=='active'){echo 'selected';} ?>>Active</option>
									<option value="inactive" <?php if($product_globalinfo->get_status()=='inactive'){echo 'selected';} ?>>Inactive</option>
								</select>
							</p>

						</div>
					<?php } ?>
				</div>

				<p>
					<a href="javascript:save();" class="btn btn-default btn-large btn-green">Save</a>
				</p>

			</form>


		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../keywords"]').parents('li').addClass('active');</script>

<!--
	/* ====================================================================== *
    	SAVE
	* ====================================================================== */								
 -->

	<script>
		function save(){

			if(document.form.vendor_price != undefined &&
				(
					document.form.vendor_price.value == '' ||
					document.form.vendor_link.value == ''
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
	</script>

<!--
	/* ====================================================================== *
    	GET COLOR-SIZE COMBINATION
	* ====================================================================== */								
 -->	
	
	<script>

		function get_color_size(){

			var id_product_prestashop = $('#id_product_prestashop').val();
			$('.color-size').html('<i class="fa fa-circle-o-notch fa-spin"></i> Getting Colors and Sizes<br><br>');

			$.get('get_color_size.php?id_product=<?php echo $id_product; ?>&id_product_prestashop='+id_product_prestashop, function(r){
				$('.color-size').html($.trim(r));
			});

		}

		get_color_size();

	</script>

<!--
	/* ====================================================================== *
    	PRESTASHOP PRODUCTS
	* ====================================================================== */								
 -->

	<link href="../includes/plugins/select2/css/select2.min.css" rel="stylesheet" />
	<script src="../includes/plugins/select2/js/select2.min.js"></script>
	<script type="text/javascript">
	  $('#id_product_prestashop').select2({
	  	minimumInputLength: 1,
		placeholder: "Select a product",
  		allowClear: true,
	  	ajax: {
		    url: "get_products.php",
		    dataType: 'json',
		    delay: 250,
		    data: function (params) {
		      return {
		        q: params.term, // search term
		        page: params.page
		      };
		    },
		    processResults: function (data, page) {
		      return {
		        results: data
		      };
		    },
		    cache: true
		},
		escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
		templateResult: function(repo) { 
			if (repo.loading) return repo.text;
			return "<div>" + repo.name + "</div>"; 
		},
		templateSelection: function(repo) { 
			return repo.name || repo.text; 
		}
	  });
	</script>

</body>
</html>