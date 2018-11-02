<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/purchase_order_row.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../../admin/head.php"); ?>

	<link rel="stylesheet" href="../../includes/css/checkbox.css">
</head>
<body>

	<?php 

		if(!isset($_GET['url'])){
			exit();
		}
		$url 		= $_GET['url'];

		$purchase_order_row 	= new purchase_order_row();
		$final_result 	= $purchase_order_row->get_all_from_url($url, " order by id_product_lang, id_row ");


		// ===== Modify purchase order =====>
		$msj = "";
		if( isset($_POST['action']) && $_POST['action'] == '1' ){

			$conn->beginTransaction();
			$error = false;

			foreach ($final_result as $row) {

				$out_of_stock = "";
				$discontinued = "";

				if( isset($_POST[$row->get_id_row()."=>out_of_stock"]) ){
					$out_of_stock = 'yes';
				}

				if( isset($_POST[$row->get_id_row()."=>discontinued"]) ){
					$discontinued = 'yes';
				}
				
				$row->set_comment( $_POST[$row->get_id_row()."=>comment"] );
				$row->set_comment_agent( $_POST[$row->get_id_row()."=>comment_agent"] );
				$row->set_comment_qc( $_POST[$row->get_id_row()."=>comment_qc"] );
				$row->set_price( $_POST[$row->get_id_row()."=>price"] );
				$row->set_out_of_stock( $out_of_stock );
				$row->set_discontinued( $discontinued );

				if( $row->update() === false ){
					$error = true; break;
				}

			}

			if($error === false){
				$conn->commit();
				$msj = '<div class="alert alert-success"><strong>Awesome</strong>, You successfully saved your changes.</div>';
			}else{
				$conn->rollback();
				$msj = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}
		}
	?>

	<div class="section">
		<div class="content">

			<h2>Purchase Order</h2>
			
			<?php echo $msj; ?>

			<hr>

			<form action="" method="post" name="form">
				<input type="hidden" name="action">

				<table class="table table-condensed table-bordered table-striped">
					<thead>
						<tr>
							<th style="width:55px;">Qty</th>
							<th>Price</th>
							<th>Total Price</th>
							<th>Thumbnail</th>
							<th>Link</th>
							<th>Item</th>
							<th>Asian Color</th>
							<th>Asian Size</th>
							<th>Comment</th>
							<th>Comment Agent</th>
							<th>Comment QC</th>
							<th class="text-center">Out of Stock</th>
							<th class="text-center">Discontinued</th>
						</tr>
					</thead>
					<tbody>
						<?php $total 			= 0; ?>
						<?php $all_total_price 	= 0; ?>
						<?php foreach ($final_result as $row) { ?>
							<?php $total += $row->get_qty(); ?>
							<tr>
								<td><?php echo $row->get_qty(); ?></td>
								<?php 
									$price 				= $row->get_price();
									$total_price		= $price * $row->get_qty();
									$all_total_price 	+= $total_price;
								?>
								<td>
									<input name="<?php echo $row->get_id_row()."=>price"; ?>" class="form-control input-sm" type="text" value="<?php echo $price; ?>" />
								</td>
								<td><?php echo $total_price; ?></td>
								<td><img height="60px" src="<?php echo $row->get_image_url(); ?>" alt=""></td>
								<td>
									<a target="_blank" href="<?php echo $row->get_product_link(); ?>">
										Product Link
									</a>
								</td>
								<td><?php echo $row->get_item(); ?></td>
								<td><?php echo $row->get_asian_color(); ?></td>
								<td><?php echo $row->get_asian_size(); ?></td>
								<td>
									<textarea name="<?php echo $row->get_id_row()."=>comment"; ?>" class="form-control" type="text"><?php echo $row->get_comment(); ?></textarea>
								</td>
								<td>
									<textarea name="<?php echo $row->get_id_row()."=>comment_agent"; ?>" class="form-control" type="text"><?php echo $row->get_comment_agent(); ?></textarea>
								</td>
								<td>
									<textarea name="<?php echo $row->get_id_row()."=>comment_qc"; ?>" class="form-control" type="text"><?php echo $row->get_comment_qc(); ?></textarea>
								</td>
								<td class="text-center">
									<input type="checkbox" id="<?php echo $row->get_id_row()."=>out_of_stock"; ?>" name="<?php echo $row->get_id_row()."=>out_of_stock"; ?>" <?php echo $row->get_out_of_stock()=='yes'?'checked':''; ?>>
									<label for="<?php echo $row->get_id_row()."=>out_of_stock"; ?>"></label>
								</td>
								<td class="text-center">
									<input type="checkbox" id="<?php echo $row->get_id_row()."=>discontinued"; ?>" name="<?php echo $row->get_id_row()."=>discontinued"; ?>" <?php echo $row->get_discontinued()=='yes'?'checked':''; ?>>
									<label for="<?php echo $row->get_id_row()."=>discontinued"; ?>"></label>
								</td>
							</tr>
						<?php } ?>
					</tbody>
					<tr>
						<th><?php echo $total; ?></th>
						<th></th>
						<th><?php echo $all_total_price; ?></th>
						<th colspan="10"></th>
					</tr>
				</table>

			</form>

			<p>
				<a href="javascript:save();" class="pull-right btn btn-default btn-large btn-green">Save</a>
			</p>
			
	</div>

	<?php require_once("../../admin/bottom.php"); ?>
	
	<script>
		function save(){
			document.form.action.value = "1";
			document.form.submit();
		}
	</script>

</body>
</html>