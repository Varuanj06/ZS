<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/product.php"); ?>
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

		if(isset($_GET['current_keyword'])){
			$_SESSION['current_keyword'] = $_GET['current_keyword'];
		}
		if(!isset($_SESSION['current_keyword'])){
			echo "<script>location.href='../keywords';</script>";
			exit();
		}

		$product 		= new product();
		$all_products 	= $product->get_list_by_keyword($_SESSION['current_keyword'], "");

		$error  = "";
		if( isset($_POST['action']) && $_POST['action'] == '1' ){//erase one
			$id_product = $_POST['id_product'];

			$product = new product();
			$product->set_id_product($id_product);

			if( $product->exists() ){
				if($product->delete()){
					//all good
				}else{
					$error = '<div class="alert alert-danger"><strong>Oops</strong>, there was an error.</div>';
				}
			}

			$all_products 	= $product->get_list_by_keyword($_SESSION['current_keyword'], "");
		}

		else if( isset($_POST['action']) && $_POST['action'] == '2' ){//erase all
			foreach ($all_products as $row){
				if(isset( $_POST["delete-".$row->get_id_product()] )){
					$id_product = $row->get_id_product();

					$product = new product();
					$product->set_id_product($id_product);

					if( $product->exists() ){
						if($product->delete()){
							//all good
						}else{
							$error = '<div class="alert alert-danger"><strong>Oops</strong>, there was an error.</div>';
						}
					}
				}
			}

			$all_products 	= $product->get_list_by_keyword($_SESSION['current_keyword'], "");
		}

		$product_lang = new product_lang();
		
	?>

	<div class="section">
		<div class="content">

			<h2>
				Products under <strong><?php echo $_SESSION['current_keyword']; ?></strong><br>
				<a href="../keywords" class="btn btn-sm btn-gray">Go back</a>
				<a href="add.php" class="btn btn-sm">Add a new product</a>
			</h2>

			<?php echo $error; ?>

			<hr>
			
			<form action="" method="post" name="form">
				<input type="hidden" name="action" />
				<input type="hidden" name="id_product" />

				<table class="table table-condensed table-bordered table-striped" id="table">
					<thead>
						<tr>
							<th>#</th>
							<th>
								<input type="checkbox" class="check_all">
							</th>
							<th>Action</th>
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
									<td>
										<input class="delete_product" type="checkbox" name="delete-<?php echo $row->get_id_product(); ?>">
									</td>
									<td>
										<a href="javascript:erase('<?php echo $row->get_id_product(); ?>');" class="btn btn-red btn-sm"><i class="glyphicon glyphicon-trash"></i></a>
										<a href="edit.php?id_product=<?php echo $row->get_id_product(); ?>" class="btn btn-green btn-sm"><i class="glyphicon glyphicon-pencil"></i> &nbsp;Edit</a>
									</td>
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
					<thead>
						<tr>
							<td>&nbsp;</td>
							<td><a href="javascript:delete_all();" class="btn btn-red btn-sm">Delete!</a></td>
							<td colspan="11">&nbsp;</td>
						</tr>
					</thead>
				</table>

			</form>

		</div>
	</div>

	<script>
		function erase(id_product){
			if( confirm("Are you sure?") ){
				document.form.action.value = "1";
				document.form.id_product.value = id_product;
				document.form.submit();
			}
		}

		function delete_all(){
			if( confirm("Are you sure?") ){
				document.form.action.value="2";
				document.form.submit();
			}
		}
	</script>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../keywords"]').parents('li').addClass('active');</script>

	<link rel="stylesheet" type="text/css" href="../includes/plugins/dataTable/media/css/jquery.dataTables.css">
	<script type="text/javascript" language="javascript" src="../includes/plugins/dataTable/media/js/jquery.dataTables.js"></script>

	<script>
		$(document).ready(function(){
			$('#table').DataTable();
		});
	</script>

	<script>
		$('.check_all').on('click', function(){
			var checkBoxes = $('.delete_product');
			checkBoxes.prop("checked", $(this).prop("checked"));
		});
	</script>
	
</body>
</html>