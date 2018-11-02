<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/keyword.php"); ?>
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

		$error  = "";
		if( isset($_POST['action']) ){
			$keyword 		= new keyword();
			$product 		= new product();
			$product_lang 	= new product_lang();
			$image 			= new image();

			$id_product_prestashop 	= $_POST['id_product_prestashop'];
			$id_image 				= $image->get_images($id_product_prestashop)[0];
			
			$keyword->map($_SESSION['current_keyword']);
			$keywords 				= "/".$_SESSION['current_keyword']."/";
			$genders 				= $keyword->get_genders();
			$ages 					= $keyword->get_ages();


			$product->set_id_product_prestashop($id_product_prestashop);
			$product->set_name($product_lang->get_product_name($id_product_prestashop));
			$product->set_link( "http://miracas.com/$id_product_prestashop-.html" );
			$product->set_image_link( "http://miracas.com/$id_product_prestashop-$id_image/$id_image.jpg" );
			
			$product->set_keywords($keywords);
			$product->set_genders($genders);
			$product->set_ages($ages);

			$product->set_like_count("0");
			$product->set_share_count("0");
			$product->set_discount($_POST['discount']);
			$product->set_discount_type($_POST['discount_type']);

			if( $product->insert() ){
				$error = '<div class="alert alert-success"><strong>Awesome</strong>, You successfully added a new product.</div>';
			}else{
				$error = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}
			
		}
		
	?>

	<div class="section">
		<div class="content">

			
			<h2>Add a new product</h2>
			<a href="./" class="btn btn-default btn-sm">Go back</a>

			<hr>

			<?php echo $error; ?>
			
			<form action="" method="post" name="form">
				<input type="hidden" name="action" />

				<p>
					<label for="name">Product</label>
					<select name="id_product_prestashop" id="id_product_prestashop" class="chosen-select btn-block"></select>
				</p>

				<p>
					<label for="discount_type">Discount Type</label>
					<select name="discount_type" id="discount_type" class="form-control btn-block">
						<option value="amount">amount</option>
						<option value="percentage">percentage</option>
					</select>
				</p>

				<p>
					<label for="discount">Discount</label>
					<input class="custom-input" name="discount" id="discount" maxlength="60" placeholder="Write here the discount" value="0" />
				</p>

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