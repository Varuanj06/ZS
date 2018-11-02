<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/espresso_keywords.php"); ?>
<?php require_once("../../classes/espresso_products.php"); ?>
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

 		$espresso_keywords 		= new espresso_keywords();
		$espresso_products 		= new espresso_products();
		$product_lang 			= new product_lang();
		$image 					= new image();

 	/* ====================================================================== *
        	INSERT PRODUCT
 	* ====================================================================== */	

 		$espresso_keywords->map($_SESSION['id_keyword']);

		$error  = "";
		if( isset($_POST['action']) ){

			$conn->beginTransaction();
			$error 			= false;

			$id_product_prestashop 	= $_POST['id_product_prestashop'];
			$id_image 				= $image->get_images($id_product_prestashop)[0];
			
			$espresso_keywords->map($_SESSION['id_keyword']);
			$espresso_products->set_id_keyword($_SESSION['id_keyword']);
			$espresso_products->set_genders($espresso_keywords->get_genders());
			$espresso_products->set_ages($espresso_keywords->get_ages());

			$espresso_products->set_id_product($espresso_products->max_id_product());
			$espresso_products->set_id_product_prestashop($id_product_prestashop);
			$espresso_products->set_name($product_lang->get_product_name($id_product_prestashop));
			$espresso_products->set_link( "http://miracas.com/$id_product_prestashop-.html" );
			$espresso_products->set_image_link( "http://miracas.miracaslifestyle.netdna-cdn.com/$id_product_prestashop-$id_image/$id_image.jpg" );
			$espresso_products->set_like_count("0");
			$espresso_products->set_share_count("0");
			$espresso_products->set_discount($_POST['discount']);
			$espresso_products->set_discount_type($_POST['discount_type']);

			if(!$espresso_products->insert()){
				$error = true;
			}

			if($error){
			  	$conn->rollBack();
			 	$error = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}else{
			  	$conn->commit();
			  	$error = '<div class="alert alert-success"><strong>Awesome</strong>, You successfully added a new product.</div>';
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
	<script>jQuery('a[href="../espresso_keywords"]').parents('li').addClass('active');</script>

<!--
	/* ====================================================================== *
    	SAVE
	* ====================================================================== */								
 -->

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