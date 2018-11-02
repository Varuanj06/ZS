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

	/* ====================================================================== *
	    	ERASE
	 * ====================================================================== */			

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

	/* ====================================================================== *
	    	MODIFY
	 * ====================================================================== */			
	 		
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

	/* ====================================================================== *
	    	CLOSE PO
	 * ====================================================================== */			

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

	/* ====================================================================== *
	    	ADD AWB (GROUP)
	 * ====================================================================== */					

		}else if( isset($_POST['action']) && $_POST['action'] == '4' ){ // ADD AWB
			$purchase_order 	= new purchase_order();
			$orders    			= $purchase_order->get_all_from_vendor($id_vendor, " order by date desc ");

			foreach ($orders as $row) {
				if(isset($_POST['checkbox_'.$row->get_id_purchase_order()])){
					$purchase_order->update_awb_details($_POST['awb_number'], $_POST['awb_status'], $row->get_id_purchase_order());
				}
			}

	/* ====================================================================== *
	    	REMOVE AWB DETAILS (UNGROUP)
	 * ====================================================================== */					

		}else if( isset($_POST['action']) && $_POST['action'] == '5' ){ // REMOVE AWB
			$purchase_order 	= new purchase_order();
			
			$purchase_order->edit_awb_details($_POST['awb_number'], '', '');

	/* ====================================================================== *
	    	EDIT AWB DETAILS
	 * ====================================================================== */					

		}else if( isset($_POST['action']) && $_POST['action'] == '6' ){ // EDIT AWB
			$purchase_order 	= new purchase_order();
			
			$purchase_order->edit_awb_details($_POST['old_awb_number'], $_POST['awb_number'], $_POST['awb_status']);
		}

	/* ====================================================================== *
	    	CHECK PO
	 * ====================================================================== */			

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

	/* ====================================================================== *
	    	PO LIST
	 * ====================================================================== */				

		$purchase_order 	= new purchase_order();
		$awb_numbers 		= [];
		$orders    			= $purchase_order->get_all_from_vendor($id_vendor, " order by awb_number desc, date desc");

		// GET ALL DIFFERENT AWB NUMBERS

		foreach ($orders as $row) {
			if( !array_key_exists($row->get_awb_number(), $awb_numbers) ){
				$awb_numbers[$row->get_awb_number()] = $row->get_awb_status();
			}
		}

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

				<?php foreach ($awb_numbers as $current_awb_number => $current_awb_status) { ?>

					<table class="table table-condensed table-bordered">
						<thead>
						<!--
							/* ====================================================================== *
							    	AWB DETAILS
							 * ====================================================================== */			
						-->	

							<?php if($current_awb_number != ''){ ?>

								<tr>
									<td colspan="20" class="alert alert-info">
										<a class="btn btn-sm btn-red pull-right" href="javascript:remove_awb_details('<?php echo $current_awb_number; ?>');">
											Ungroup
										</a>
										<a class="btn btn-sm pull-right" href="javascript:open_edit_awb_details('<?php echo $current_awb_number; ?>', '<?php echo $current_awb_status; ?>');" style="margin-right: 5px;">
											Edit AWB details
										</a>
									</td>
								</tr>

							<?php }else{ ?>

								<tr>
									<td colspan="20" class="alert alert-warning text-center">
										Purchase orders without AWB details
									</td>
								</tr>

							<?php } ?>

						<!--
							/* ====================================================================== *
							    	HEAD
							 * ====================================================================== */			
						-->	

							<tr>
								<th>Action</th>
								<th>Name</th>
								<th>Date</th>
								<th>Link for vendor</th>
								<?php if($current_awb_number == ''){ ?>
									<th>
										<input type="checkbox" class="check_all">
										<a href="javascript:open_awb_details();" class="btn btn-sm pull-right">
											<span class="fa fa-plus"></span>
										</a>
									</th>
								<?php } ?>
								<th>AWB number</th>
								<th>AWB status</th>
							</tr>
						</thead>
						<tbody>

						<!--
								/* ====================================================================== *
								    	PO
								 * ====================================================================== */			
							-->	

							<?php 
								foreach ($orders as $row) {
									$POclosed = check_POclosed(new purchase_order_row(), new order_detail(), $row->get_id_purchase_order());

									if($row->get_awb_number() != $current_awb_number) continue;
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
									<?php if($current_awb_number == ''){ ?>
										<td>
											<?php if($current_awb_number == ''){ ?>
												<input type="checkbox" class="check_box" name="checkbox_<?php echo $row->get_id_purchase_order(); ?>">
											<?php } ?>
										</td>
									<?php } ?>
									<td><?php echo $row->get_awb_number(); ?></td>
									<td><?php echo $row->get_awb_status(); ?></td>
								</tr>

							<?php 
								} 
							?>
						</tbody>
					</table>

					<br>

				<?php } ?>

				<div class="modal fade" id="modal-awb">
				  	<div class="modal-dialog" role="document">
				    	<div class="modal-content">
				      		<div class="modal-header">
				        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				        		<h4 class="modal-title" id="gridSystemModalLabel">AWB details</h4>
			      			</div>
			      			
			      			<div class="modal-body">
			      				<p>
				        			<label for="awb_number">AWB Number</label>
				        			<input name="awb_number" id="awb_number" type="text" class="form-control" maxlength="100" />
				        			<input name="old_awb_number" type="hidden"  /> <!-- only used when editing the awb_number -->
				        		</p>
				        		<p>
				        			<label for="awb_status">Status</label>
				        			<input name="awb_status" id="awb_status" type="text" class="form-control" maxlength="1000" />
				        		</p>
				      		</div>
				      		
				      		<div class="modal-footer">
				        		<button type="button" class="btn btn-gray" data-dismiss="modal">Close</button>
				        		<a id="add_awb" href="javascript:add_awb_details();" class="btn btn-primary">Add AWB</a>
				        		<a id="edit_awb" href="javascript:edit_awb_details();" class="btn btn-primary">Edit AWB</a>
				      		</div>
				    	</div><!-- /.modal-content -->
				  	</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
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

	<script>
		function open_awb_details(){
			$('#add_awb').show();
			$('#edit_awb').hide();
			$('#modal-awb').modal();
		}

		function add_awb_details(){
			if( document.form.awb_number.value != "" && document.form.awb_status.value != "" ){
				document.form.action.value = "4";
				document.form.submit();
			}else{
				alert("all fields are required!");
			}
		}

		function remove_awb_details(awb_number){
			if( confirm("Are you sure?") ){
				document.form.awb_number.value 	= awb_number;
				document.form.action.value 		= "5";
				document.form.submit();
			}
		}

		function open_edit_awb_details(awb_number, awb_status){
			$('#edit_awb').show();
			$('#add_awb').hide();
			document.form.old_awb_number.value = awb_number;
			document.form.awb_number.value = awb_number;
			document.form.awb_status.value = awb_status;
			$('#modal-awb').modal();
		}

		function edit_awb_details(){
			if( document.form.awb_number.value != "" && document.form.awb_status.value != "" ){
				console.log('here');
				document.form.action.value = "6";
				document.form.submit();
			}else{
				alert("all fields are required!");
			}
		}

		$('.check_all').on('click', function(){
			$(this).closest('table').find('.check_box').prop('checked', $(this).prop('checked'));
		});
	</script>

</body>
</html>