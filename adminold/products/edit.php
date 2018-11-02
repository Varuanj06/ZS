<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/product.php"); ?>
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

		$id_product = $_GET['id_product'];
		$product = new product();
		$product->map($id_product);

		// if the nodeId doesn't exists then go back
		if( !isset($id_product) || !$product->exists() ){
			echo "<script>location.href='./';</script>";
		}

		$error  = "";
		if( isset($_POST['action']) ){
			$product 		= new product();
			$product_lang 	= new product_lang();
			$image 			= new image();

			$product->map($id_product);

			$id_product_prestashop 	= $_POST['id_product_prestashop'];
			$id_image 				= $image->get_images($id_product_prestashop)[0];
			$keywords 				= $_POST['keywords'];
			$genders 				= $_POST['genders'];
			$ages 					= $_POST['ages'];

			$product->set_id_product($id_product);
			$product->set_id_product_prestashop($id_product_prestashop);
			$product->set_name($product_lang->get_product_name($id_product_prestashop));
			$product->set_link( "http://miracas.com/$id_product_prestashop-.html" );
			$product->set_image_link( "http://miracas.com/$id_product_prestashop-$id_image/$id_image.jpg" );
			$product->set_keywords(format_string($keywords));
			$product->set_genders(format_string($genders));
			$product->set_ages(format_string($ages));
			$product->set_discount($_POST['discount']);
			$product->set_discount_type($_POST['discount_type']);

			if( $product->exists() && $product->update() ){
				$error = '<div class="alert alert-success"><strong>Awesome</strong>, You successfully added a new product.</div>';
			}else{
				$error = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
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

				<p>
					<label for="name">Product</label>
					<?php $product_lang = new product_lang(); ?>
					<select name="id_product_prestashop" id="id_product_prestashop" class="chosen-select btn-block">
						<option value="<?php echo $product->get_id_product_prestashop(); ?>"><?php echo $product_lang->get_product_name( $product->get_id_product_prestashop() ); ?></option>
					</select>
				</p>

				<p>
					<label for="keywords">Keywords</label>
					<textarea class="custom-input" name="keywords" id="keywords" maxlength="5000" placeholder="write keywords here, the keywords must have slashes between them (i.e. /cold/big/large/)"><?php echo $product->get_keywords(); ?></textarea>
				</p>
				
				<p>
					<label for="genders">Genders</label>
					<textarea class="custom-input" name="genders" id="genders" maxlength="300" placeholder="write genders here, the genders must have slashes between them (i.e. /male/female/)"><?php echo $product->get_genders(); ?></textarea>
				</p>

				<p>
					<label for="ages">Ages</label>
					<textarea class="custom-input" name="ages" id="ages" maxlength="600" placeholder="write ages here, the ages must have slashes between them (i.e. /15/16/17/18/19/)"><?php echo $product->get_ages(); ?></textarea>
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

				<p>
					<a href="javascript:save();" class="btn btn-default btn-large btn-green">Save</a>
				</p>

			</form>


		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../products"]').parents('li').addClass('active');</script>

	<script>
		function save(){

			$('.alert').hide();

			if(document.form.name.value != ""){
				document.form.action.value = "1";
				document.form.submit();
			}else{
				$('<div class="alert alert-danger">All fields are required.</div>').fadeIn().prependTo(form);
			}
		}	
	</script>


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