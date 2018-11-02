<?php require_once("../fb_validator.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/order.php"); ?>
<?php require_once("../classes/order_detail.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/product_lang.php"); ?>
<?php require_once("../classes/functions.php"); ?>

<!doctype html>
<html lang="en">
<head>

  	<?php require_once("../head.php"); ?>

	<?php
		$id_fb_user 		= $user['id'];
		$order 				= new order();
		$order_detail 		= new order_detail();
		$product_lang 		= new product_lang();

		$current_id_order   = "";
		$details 			= array();
		if($order->get_id_order_by_fb_user($id_fb_user)){
			$current_id_order = $order->get_id_order_by_fb_user($id_fb_user);
			$details = $order_detail->get_list($current_id_order, " order by id_product ");
		}

		if(isset($_POST['action'])){

			$error = false;
			$conn->beginTransaction();

			$order_detail->set_id_order($current_id_order);
			$order_detail->set_id_order_detail($_POST['id_order_detail']);
			if(!$order_detail->delete()){
				$error = true;
			}

			if(count($details)==1){
				$order->set_id_order($current_id_order);
				if(!$order->delete()){
					$error = true;
				}
			}

			if($error){
		      $conn->rollBack();
		      //echo "ERROR";
			}else{
		      $conn->commit();
			}

			$details = $order_detail->get_list($current_id_order, " order by id_product ");
		}
	?>

	<style>
		input[type="text"]{
			border: 1px solid #ccc;
			height:24px;
			width:50px;
			padding: 6px 8px;
		}
	</style>
</head>
<body>
	<?php require_once("../menu.php"); ?>
	<?php require_once("../sidebar.php"); ?>
	<div id="menu-page-wraper">

	<div class="page-wrap"><div class="page-wrap-inner">
	
	<?php require_once("../message.php"); ?>
	
	<script>$('.nav-right a[href="../cart"]').addClass('selected');</script>

	<div class="tabs-container">

		<h2><span class="fa fa-shopping-cart"></span> Shopping Cart</h2>
		<br>

		<form action="" name="form" method="post">
			<input type="hidden" name="action">
			<input type="hidden" name="id_order">
			<input type="hidden" name="id_order_detail">
		</form>

		<?php if($current_id_order != "" && count($details)>0){?>
				<div class="cart-item">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Item Description</th>
								<th>Color</th>
								<th>Size</th>
								<th style="width:200px;">Qty</th>
								<th class="text-right">Price</th>
							</tr>
						</thead>
						<tbody>
						<?php
							foreach ($details as $row) {

								$product	= new product();
								$product->map($row->get_id_product());
						?>
						        <tr>
						        	<td class="item_description">
						        		<!--<a target="_blank" href="<?php echo $product->get_link(); ?>">-->
						        		<a href="../feed/product_details.php?id_product=<?php echo $row->get_id_product(); ?>">
						        			<img style="width:50px !important;" src="<?php echo $product->get_image_link(); ?>" alt="">
						        		</a>
						        		
						        		<div>
						        			<p class="item_name"><?php echo $product->get_name(); ?></p>
						        			<p><a href="javascript:remove('<?php echo $row->get_id_order_detail(); ?>');" class="btn btn-sm btn-red">Remove</a></p>
						        		</div>
						        	</td>
						        	<td>
						        		<?php if($row->get_color() != ""){ ?>
						        			<span class="media-box-color"><span style="background:<?php echo $row->get_color(); ?>;"></span></span>
						        		<?php } ?>
						        	</td>
						        	<td><?php echo $row->get_size(); ?></td>
						        	<td class="qty_tr">
						        		<?php echo $row->get_qty(); ?>
						        		<input class="qty" maxlength="3" class="form-control" type="hidden" value="<?php echo $row->get_qty(); ?>">
										<!--
						        		<input class="qty" maxlength="3" class="form-control" type="text" value="<?php echo $row->get_qty(); ?>">
						        		<a data-idorderdetail="<?php echo $row->get_id_order_detail(); ?>" href="#" class="update_qty btn btn-sm btn-green">Update</a>
						        		-->
						        	</td>
						        	<?php 
					        			$price 			= get_the_price($row->get_id_product_prestashop());
					        			$discount 		= get_the_discount($row->get_id_product(), $price);
										$price_final 	= number_format(((float)$price-(float)$discount)*(float)$row->get_qty(), 2, '.', ',');
					        		?>
						        	<td class="text-right price_tr" data-singleprice="<?php echo ((float)$price-(float)$discount); ?>">
						        		₹<?php echo $price_final; ?>
						        	</td>
						        </tr>
						<?php				
							}
						?>
							<tr>
								<td colspan="4">&nbsp;</td>
								<td class="total text-right"></td>
							</tr>
						</tbody>
					</table>
					<p>
						<a href="../select_address" class="btn btn-green btn-default">Continue</a>
					</p>
				</div>
		<?php
			}else{
				echo '<div class="cart-item">You Shopping Cart is Empty!</div>';
			}
		?>

	</div> <!-- End tabs-container -->

	<script>
		function number_format(number, decimals, dec_point, thousands_sep) {
		    var n = !isFinite(+number) ? 0 : +number, 
		        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
		        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
		        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
		        toFixedFix = function (n, prec) {
		            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
		            var k = Math.pow(10, prec);
		            return Math.round(n * k) / k;
		        },
		        s = (prec ? toFixedFix(n, prec) : Math.round(n)).toString().split('.');
		    if (s[0].length > 3) {
		        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
		    }
		    if ((s[1] || '').length < prec) {
		        s[1] = s[1] || '';
		        s[1] += new Array(prec - s[1].length + 1).join('0');
		    }
		    return s.join(dec);
		}

		function remove(idOrderDetail){
			if(confirm("Are you sure?")){
				document.form.action.value = "1";
				document.form.id_order_detail.value = idOrderDetail;
				document.form.submit();
			}
		}

		function calculate_total(){
			var total = 0;
			$('.table').find('.price_tr').each(function(){
				var $this 			= $(this);
				var singleprice		= parseFloat($this.data('singleprice'));
				var qty 			= parseFloat($this.siblings('.qty_tr').find('input').val());

				total =  Math.round((total + (singleprice*qty))* 1e12)/ 1e12;
			});

			$('.total').html('₹'+number_format(total, 2));
		}
		calculate_total();

		function recalculate_price(update_button, qty){
			var price 		= update_button.parents('.qty_tr').siblings('.price_tr');
			var price_val 	= price.data('singleprice');
			var result 		= Math.round((qty*price_val)* 1e12)/ 1e12;
			result 			= parseFloat(result).toFixed(2);

			price.html("₹"+number_format(result, 2));

			calculate_total();
		}

		$('.update_qty').on('click', function(e){
			e.preventDefault();
			var $this 			= $(this);
			var idOrderDetail 	= $this.data('idorderdetail');
			var qtyInput 		= $this.siblings('.qty');
			var qty 			= qtyInput.val();

			if($this.hasClass('updating')){
				return;
			}

			if (isNaN(qty)) {
			    alert("make sure the qty is a number:"+qty);
			}else if(qty <= 0){
				alert("make sure the qty is > 0 ");
			}else{
				$this.addClass('updating');
				$this.html("<i class='fa fa-circle-o-notch fa-spin'></i> &nbsp;updating...");

				$.get('update_qty.php?id_order_detail='+idOrderDetail+'&qty='+qty, function(r){
					$this.removeClass('updating');
					$this.html('Update');

					recalculate_price($this, qty);
				});
			}
		})
	</script>
	
	</div></div>
	<?php require_once("../footer.php"); ?>
	
	</div>
</body>

</html>