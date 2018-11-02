<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/vendor.php"); ?>
<?php require_once("../../classes/purchase_order.php"); ?>
<?php require_once("../../classes/purchase_order_row.php"); ?>
<?php require_once("../../classes/order_detail.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
</head>
<body>

	<?php require_once("../navbar.php"); ?>

	<?php 

		$id_vendor = isset($_SESSION['id_vendor'])?$_SESSION['id_vendor']:"";
		$vendor = new vendor();
		$vendor->set_id_vendor($id_vendor);
		if( $vendor->exists() == false ){
			echo "<script>location.href='../vendors';</script>";	
			exit();
		}
		$vendor->map($id_vendor);

		$msj  = "";
		if( isset($_POST['action']) && $_POST['action'] == '1' ){ // ERASE

			$conn->beginTransaction();

			$id_purchase_order 	= $_POST['id_purchase_order'];
			$error 		= false;

			/* Erase form PO */
			$purchase_order = new purchase_order();
			$purchase_order->set_id_purchase_order($id_purchase_order);

			if($purchase_order->delete()){
				//all good
			}else{
				$error = true;
			}

			/* Set status POmade back to empty */
			$purchase_order_row 	= new purchase_order_row();
			$final_result 			= $purchase_order_row->get_all_from_id_purchase_order($id_purchase_order, " order by id_product_lang, id_row ");
			foreach ($final_result as $row) {
				$id_order_details       = explode("-", $row->get_id_order_details());
				foreach ($id_order_details as $row) {
				    if($row=='')continue;

				    $pieces           = explode("@@", $row);
				    $id_order         = $pieces[0];
				    $id_order_detail  = $pieces[1];

				    $order_detail     = new order_detail();
				    $order_detail->set_POmade("");
				    $order_detail->set_id_order($id_order);
				    $order_detail->set_id_order_detail($id_order_detail);
				    if($order_detail->update_POmade() === false){
				          $error = true;
				    }
				    
				}
			}

			/* Erase form PO row */
			$purchase_order_row = new purchase_order_row();
			if($purchase_order_row->delete_by_id_purchase_order($id_purchase_order)){
				//all good
			}else{
				$error = true;
			}

			if($error === true){
				$msj = '<div class="alert alert-danger"><strong>Oops</strong>, there was an error.</div>';
				$conn->rollBack();
			}else{
				$conn->commit();
			}
		}else if( isset($_POST['action']) && $_POST['action'] == '2' ){ // MODIFY

			$conn->beginTransaction();
			$error = false;

			$purchase_order 	= new purchase_order();
			$orders   			= $purchase_order->get_all_from_vendor($id_vendor, " order by date desc ");

			foreach ($orders as $row) {
				
				$row->set_name( $_POST[$row->get_id_purchase_order()."=>name"] );

				if( $row->update() === false ){
					$error = true; break;
				}

			}

			if($error === false){
				$conn->commit();
				$msj = '<div class="alert alert-success"><strong>Awesome</strong>, You successfully updated the purchase order.</div>';
			}else{
				$conn->rollback();
				$msj = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}

		}else if( isset($_POST['action']) && $_POST['action'] == '3' ){ // CLOSE PO

			$conn->beginTransaction();
			$error = false;

			$id_purchase_order 	= $_POST['id_purchase_order'];
			
			/* Set status POmade back to empty */
			$purchase_order_row 	= new purchase_order_row();
			$final_result 			= $purchase_order_row->get_all_from_id_purchase_order($id_purchase_order, " order by id_product_lang, id_row ");
			foreach ($final_result as $row) {
				$id_order_details       = explode("-", $row->get_id_order_details());
				foreach ($id_order_details as $row) {
				    if($row=='')continue;

				    $pieces           = explode("@@", $row);
				    $id_order         = $pieces[0];
				    $id_order_detail  = $pieces[1];

				    $order_detail     = new order_detail();
				    $order_detail->set_POmade("");
				    $order_detail->set_id_order($id_order);
				    $order_detail->set_id_order_detail($id_order_detail);
				    if($order_detail->update_POmade() === false){
				          $error = true;
				    }
				    
				}
			}

			if($error === false){
				$conn->commit();
				$msj = '<div class="alert alert-success"><strong>Awesome</strong>, You successfully close the PO.</div>';
			}else{
				$conn->rollback();
				$msj = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}

		}

		function check_POclosed($purchase_order_row, $order_detail, $id_purchase_order){
			$POclosed 				= false;
			$final_result 			= $purchase_order_row->get_all_from_id_purchase_order($id_purchase_order, " order by id_product_lang, id_row ");
			foreach ($final_result as $row) {
				$id_order_details       = explode("-", $row->get_id_order_details());
				foreach ($id_order_details as $row) {
				    if($row=='')continue;

				    $pieces           = explode("@@", $row);
				    $id_order         = $pieces[0];
				    $id_order_detail  = $pieces[1];

				    $order_detail->map($id_order, $id_order_detail);
				    if($order_detail->get_POmade() == ""){
				    	$POclosed = true;
				    	break;
				    }
				}
			}

			return $POclosed;
		}

		$purchase_order 	= new purchase_order();
		$orders    = $purchase_order->get_all_from_vendor($id_vendor, " order by date desc ");

	?>

	<div class="section">
		<div class="content">

			<h2>Purchase Orders for <?php echo $vendor->get_name(); ?></h2>
			
			<p>
				<a href="purchase_order.php" class="btn btn-default btn-gray btn-sm">Go back</a>				
			</p>	

			<?php echo $msj; ?>

			<hr>
			
			<form action="" method="post" name="form">
				<input type="hidden" name="action" />
				<input type="hidden" name="id_purchase_order" />

				<table class="table table-condensed table-bordered table-striped">
					<thead>
						<tr>
							<th>Action</th>
							<th>Name</th>
							<th>Date</th>
							<th>Link for vendor</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($orders as $row) { ?>
							<?php 
								$POclosed = check_POclosed(new purchase_order_row(), new order_detail(), $row->get_id_purchase_order());
							?>
							<tr>
								<td>
									<?php if($POclosed === true){ ?>
										<span class="label label-default">PO closed</span>
									<?php }else{ ?>
										<a href="javascript:close_PO('<?php echo $row->get_id_purchase_order(); ?>');" class="btn btn-sm">Close PO</a>
									<?php } ?>
									<a href="javascript:erase('<?php echo $row->get_id_purchase_order(); ?>');" class="btn btn-red btn-sm"><i class="glyphicon glyphicon-trash"></i></a>
								</td>
								<td>
									<input name="<?php echo $row->get_id_purchase_order()."=>name"; ?>" class="form-control input-sm" type="text" value="<?php echo $row->get_name(); ?>" />
								</td>
								<td><a href="edit_purchase_order.php?id_purchase_order=<?php echo $row->get_id_purchase_order(); ?>"><?php echo $row->get_date(); ?></a></td>
								<td>
									<a target="_blank" href="../../public/PO/?url=<?php echo $row->get_url(); ?>"><?php echo $row->get_url(); ?></a>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</form>

			<p>
				<a href="javascript:save();" class="btn btn-default btn-large btn-green">Save</a>
			</p>
			
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../purchase_order"]').parents('li').addClass('active');</script>
	
	<script>
		function erase(id_purchase_order){
			if( confirm("Are you sure?") ){
				document.form.action.value = "1";
				document.form.id_purchase_order.value = id_purchase_order;
				document.form.submit();
			}
		}	

		function save(){
			document.form.action.value = "2";
			document.form.submit();
		}

		function close_PO(id_purchase_order){
			if( confirm("Are you sure?") ){
				document.form.action.value = "3";
				document.form.id_purchase_order.value = id_purchase_order;
				document.form.submit();
			}
		}
	</script>

</body>
</html>