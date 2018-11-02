<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/keyword.php"); ?>
<?php require_once("../../classes/product_lang.php"); ?>
<?php require_once("../../classes/product.php"); ?>
<?php require_once("../../classes/functions.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
</head>
<body>

	<?php require_once("../navbar.php"); ?>


	<?php 

		if(!isset($_GET['to_keyword'])){
			echo "<script>location.href='./';</script>";
			exit();
		}
		
		$product 		= new product();
		$keyword 		= new keyword();	
		$list_keyw 		= $keyword->get_list(" ORDER BY 1 ");

		$to_keyword	 	= $_GET['to_keyword'];
		$from_keyword 	= isset($_POST['from_keyword'])?$_POST['from_keyword']:"";
	?>

	<div class="section">
		<div class="content">

			
			<h2>Import products to <strong><?php echo $to_keyword; ?></strong></h2>
			<a href="./" class="btn btn-default btn-sm">Go back</a>

			<hr>
			
			<form action="" method="post" name="form">
				<input type="hidden" name="action">

				<p>
					<label for="from_keyword">From</label>
					<select name="from_keyword" id="from_keyword" class="form-control" style="max-width:200px;" onchange="document.form.submit();">
						<?php foreach ($list_keyw as $row) { ?>
							<?php 
								if($row->get_keyword() == $to_keyword){ continue; } 

								if($from_keyword == ''){
									$from_keyword = $row->get_keyword();
								}
							?>
							<option <?php if($from_keyword==$row->get_keyword()){echo "selected";} ?> value="<?php echo $row->get_keyword(); ?>">
								<?php echo $row->get_keyword(); ?>
							</option>
						<?php } ?>
					</select>
				</p>
				
				<?php 
					$all_products 	= $product->get_list_by_keyword($from_keyword, "");

					if(isset($_POST['action']) && $_POST['action'] == '1'){

						$conn->beginTransaction(); //transaccion
						$error = false;

						foreach ($all_products as $row){
							$id_product = $row->get_id_product();
							if(isset($_POST["product_$id_product"]) && $_POST["product_$id_product"] != ''){

								$keyword 		= new keyword();
								$product 		= new product();
								
								$keyword->map($to_keyword);
								$keywords 				= "/".$to_keyword."/";
								$genders 				= $keyword->get_genders();
								$ages 					= $keyword->get_ages();


								$product->set_id_product_prestashop($row->get_id_product_prestashop());
								$product->set_name($row->get_name());
								$product->set_link($row->get_link());
								$product->set_image_link($row->get_image_link());
								
								$product->set_keywords($keywords);
								$product->set_genders($genders);
								$product->set_ages($ages);

								$product->set_like_count("0");
								$product->set_share_count("0");
								$product->set_discount($row->get_discount());
								$product->set_discount_type($row->get_discount_type());

								if( !$product->insert() ){
									$error = true; 
									break;
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
									<td class="text-right"><?php echo number_format(get_the_price($row->get_id_product_prestashop()), 2); ?></td>
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