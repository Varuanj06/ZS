<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/keyword.php"); ?>
<?php require_once("../../classes/product.php"); ?>
<?php require_once("../../classes/espresso_keywords.php"); ?>
<?php require_once("../../classes/espresso_products.php"); ?>
<?php require_once("../../classes/product_lang.php"); ?>
<?php require_once("../../classes/functions.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
</head>
<body>

	<?php require_once("../navbar.php"); ?>


	<?php 

		if(!isset($_GET['to'])){
			echo "<script>location.href='./';</script>";
			exit();
		}
		
		$product 					= new product();
		$keyword 					= new keyword();	
		$espresso_products 			= new espresso_products();
		$espresso_keywords 			= new espresso_keywords();	
		
		$keyword_list 				= $keyword->get_list(" ORDER BY 1 ");
		$espresso_keywords_list 	= $espresso_keywords->get_list(" ORDER BY 1 ");

		$to	 						= isset($_GET['to']) ? $_GET['to'] : '-@-';
		$to_type 					= explode('@', $to)[0]!='-' ? explode('@', $to)[0] : '';
		$to_keyword 				= explode('@', $to)[1]!='-' ? explode('@', $to)[1] : '';;

		$from 						= isset($_POST['from']) ? $_POST['from'] : '-@-';
		$from_type 					= explode('@', $from)[0]!='-' ? explode('@', $from)[0] : '';
		$from_keyword 				= explode('@', $from)[1]!='-' ? explode('@', $from)[1] : '';;
	?>

	<div class="section">
		<div class="content">

			
			<h2>Import products</h2>
			<a href="./" class="btn btn-default btn-sm">Go back</a>

			<hr>
			
			<form action="" method="post" name="form">
				<input type="hidden" name="action">

				<p>
					<label for="from">From</label>
					<select name="from" id="from" class="form-control" style="max-width:200px;" onchange="document.form.submit();">
						<option value="-@-" selected>SELECT A KEYWORD</option>
						<!-- NORMAL KEYWORDS -->
						<?php foreach ($keyword_list as $row) { ?>
							<?php if($row->get_keyword() == $to_keyword){ continue; } ?>
							<option <?php if($from_keyword==$row->get_keyword()){echo "selected";} ?> value="normal@<?php echo $row->get_keyword(); ?>">
								<?php echo $row->get_keyword(); ?>
							</option>
						<?php } ?>

						<!-- ESPRESSO KEYWORDS -->
						<?php foreach ($espresso_keywords_list as $row) { ?>
							<?php if($row->get_id_keyword() == $to_keyword){ continue; } ?>
							<option <?php if($from_keyword==$row->get_id_keyword()){echo "selected";} ?> value="espresso@<?php echo $row->get_id_keyword(); ?>"><?php echo $row->get_keyword(); ?></option>
						<?php } ?>

					</select>
				</p>
				
				<?php 
					$all_products 	= $from_type=='normal' ? $product->get_list_by_keyword($from_keyword, "") : $espresso_products->get_list_by_keyword($from_keyword, "");

					if(isset($_POST['action']) && $_POST['action'] == '1'){

						$conn->beginTransaction(); //transaccion
						$error = false;

						foreach ($all_products as $row){
							$id_product = $row->get_id_product();
							if(isset($_POST["product_$id_product"]) && $_POST["product_$id_product"] != ''){

								// GET DATA

								$genders 		= '';
								$ages 			= '';
								$global 		= 'no';

								if($to_type=='normal'){
									$keyword 		= new keyword();	
									$keyword->map($to_keyword);

									$genders 				= $keyword->get_genders();
									$ages 					= $keyword->get_ages();
									$global 				= $keyword->get_global();
								}else{
									$espresso_keywords 		= new espresso_keywords();
									$espresso_keywords->map($to_keyword);

									$genders 				= $espresso_keywords->get_genders();
									$ages 					= $espresso_keywords->get_ages();
								}

								// SAVE DATA

								if($to_type=='normal'){
									$product 		= new product();

									$product->set_keywords("/".$to_keyword."/");
									$product->set_genders($genders);
									$product->set_ages($ages);	
									$product->set_id_product($product->max_id_product());
									$product->set_id_product_prestashop($row->get_id_product_prestashop());
									$product->set_name($row->get_name());
									$product->set_link($row->get_link());
									$product->set_image_link($row->get_image_link());
									$product->set_like_count("0");
									$product->set_share_count("0");
									$product->set_discount($row->get_discount());
									$product->set_discount_type($row->get_discount_type());
									$product->set_global($global);

									if( !$product->insert() ){
										$error = true; 
										break;
									}
								}else{
									$espresso_products 		= new espresso_products();
								
									$espresso_products->set_id_keyword($to_keyword);
									$espresso_products->set_genders($genders);
									$espresso_products->set_ages($ages);
									$espresso_products->set_id_product($espresso_products->max_id_product());
									$espresso_products->set_id_product_prestashop($row->get_id_product_prestashop());
									$espresso_products->set_name($row->get_name());
									$espresso_products->set_link($row->get_link());
									$espresso_products->set_image_link($row->get_image_link());
									$espresso_products->set_like_count("0");
									$espresso_products->set_share_count("0");
									$espresso_products->set_discount($row->get_discount());
									$espresso_products->set_discount_type($row->get_discount_type());

									if( !$espresso_products->insert() ){
										$error = true; 
										break;
									}

								}

							}
						}

						if($error == false){
							echo '<div class="alert alert-success"><strong>Awesome</strong>, You successfully imported new products.</div>';
							$conn->commit();
						}else{
							echo '<div class="alert alert-danger"><strong>Oops</strong>, there was an error.</div>';
							$conn->rollback();
						}
					}

				?>

				<?php if($from_keyword!='' && $from_type!=''){ ?>
					<table class="table table-condensed table-bordered table-striped" id="table">
						<thead>
							<tr>
								<th>#</th>
								<th><input type="checkbox" class="check_them_all" /></th>
								<th>Id Product</th>
								<th>Name</th>	
								<th>Link</th>
								<th>Image</th>
								<th>Price</th>
								<th>Discount</th>
								<th>Discount Type</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$count = 0;
								foreach ($all_products as $row){
									$count++;
							?>
									<tr>
										<td><?php echo $count; ?></td>
										<td><input type="checkbox" class="product_checkbox" name="product_<?php echo $row->get_id_product(); ?>" /></td>
										<td><?php echo $row->get_id_product_prestashop(); ?></td>
										<td><?php echo $row->get_name(); ?></td>
										<td><a href="<?php echo $row->get_link(); ?>">link</a></td>
										<td><img src="<?php echo $row->get_image_link(); ?>" height="60px" alt=""></td>
										<td class="text-right"><?php echo number_format(get_the_price($row->get_id_product()), 2); ?></td>
										<td class="text-right"><?php echo number_format($row->get_discount(), 2); ?></td>
										<td><?php echo $row->get_discount_type(); ?></td>
									</tr>
							<?php
								}
							?>
						</tbody>
						<tr>
							<td>&nbsp;</td>
							<td><a href="javascript:import_products();" class="btn btn-sm btn-green">Import</a></td>
							<td colspan="7">&nbsp;</td>
						</tr>
					</table>
				<?php } ?>
			</form>

		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../keywords"]').parents('li').addClass('active');</script>
	
	<script>
		function import_products(){
			if($('.product_checkbox:checked').length > 0){
				document.form.action.value = "1";
				document.form.submit();
			}else{
				alert('You need to select some products!');
			}
		}

		$('.check_them_all').on('click', function(){
			var checkBoxes = $('.product_checkbox');
			checkBoxes.prop("checked", $(this).prop("checked"));
		});
	</script>

</body>
</html>