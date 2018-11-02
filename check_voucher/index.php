<?php require_once("../fb_validator.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/order.php"); ?>
<?php require_once("../classes/order_detail.php"); ?>
<?php require_once("../classes/order_address.php"); ?>
<?php require_once("../classes/address.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/product_lang.php"); ?>
<?php require_once("../classes/voucher.php"); ?>
<?php require_once("../classes/order_voucher.php"); ?>
<?php require_once("../classes/functions.php"); ?>

<!doctype html>
<html lang="en">
<head>

  	<?php require_once("../head.php"); ?>

	<?php
		$id_fb_user 		= $user['id'];
		$order 				= new order();
		$order_detail 		= new order_detail();
		$order_address 		= new order_address();
		$address 			= new address();
		$product_lang 		= new product_lang();
		$voucher 			= new voucher();
		$order_voucher 		= new order_voucher();

		$current_id_order   = "";
		$details 			= array();
		if($order->get_id_order_by_fb_user($id_fb_user)){
			$current_id_order = $order->get_id_order_by_fb_user($id_fb_user);
			$details = $order_detail->get_list($current_id_order, " order by id_product ");
			$order->map($current_id_order);
		}

		$addresses 			= $address->get_list($id_fb_user, " order by date_update desc ");
		$current_address 	= "";
		foreach ($addresses as $row){ 
			$current_address = $row->get_id_address();
			break;
		}
		$address = new address();
		$address->map($current_address, $id_fb_user);

		// ######### CHECK FOR FEW STUFF #########
		if($current_id_order == "" || $current_address == "" || count($details) == 0){
			echo "<script>location.href='../select_address';</script>";
			exit();
		}

		// ######### CALCULATE TOTAL AMMOUNT #########
		$total_ammount = 0;
		foreach ($details as $row) {
			$qty 			= $row->get_qty();
			$price 			= get_the_price($row->get_id_product());
			$discount 		= get_the_discount($row->get_id_product(), $price);
			$price_final 	= ((float)$price-(float)$discount)*(float)$qty;

			$total_ammount += $price_final;
		}

		// ######### GET VOUCHERS AVAILABLE FOR USER #########
		$vouchers_available 	= $voucher->get_all_for_user($address->get_email(), $current_id_order, "order by till_date");

		if(count($vouchers_available) <= 0){
			if(isset($_GET['from']) && $_GET['from'] == 'pay'){
				echo "<script>location.href='../select_address';</script>";
			}else{
				echo "<script>location.href='../pay';</script>";
			}
			exit();
		}

		$msj = "";
		if(isset($_POST['action']) && $_POST['action'] == '1'){

			$error = false;
			$conn->beginTransaction();

			if(!$order_voucher->delete_all_from_order($current_id_order)){
				$error = true;
			}

			foreach ($vouchers_available as $row){
				if( isset($_POST['voucher_'.$row->get_id_voucher()]) ){

					$order_voucher->set_id_order($current_id_order);
					$order_voucher->set_id_voucher($row->get_id_voucher());
					$order_voucher->set_code($row->get_code());
					$order_voucher->set_email($address->get_email());
					$order_voucher->set_till_date($row->get_till_date());
					$order_voucher->set_value_kind($row->get_value_kind());
					$order_voucher->set_value($row->get_value());

					if(!$order_voucher->insert()){
						$error = true;
						break;
					}

				}
			}

			// Create a new voucher if the discount is more than the total 

			if(!$voucher->delete_automatic_voucher($current_id_order)){
				$error = true;
			}

			$vouchers_discount 	= $order_voucher->get_vouchers_discount_real($current_id_order, $total_ammount);
			$difference 		= $total_ammount - $vouchers_discount;
			if($difference < 0){
				$voucher = new voucher();

				$voucher->set_id_voucher($voucher->max_id_voucher());
				$voucher->set_code(md5($voucher->get_id_voucher()));
				$voucher->set_emails("/".$address->get_email()."/");
				$voucher->set_till_date(date('Y-m-d', strtotime('+1 year')));
				$voucher->set_value_kind('amount');
				$voucher->set_value(abs($difference));
				$voucher->set_made_from_id_order($current_id_order);
				$voucher->set_description("Leftover Voucher");
				$voucher->set_min_cart_value("0");
				$voucher->set_visibility("Y");
				
				if( !$voucher->insert() ){
					$error = true;
				}
			}

			if($error){
			  $conn->rollBack();
			  $msj = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}else{
			  $conn->commit();
			  echo "<script>location.href='../pay';</script>";
			  exit();
			}
		}else if(isset($_POST['action']) && $_POST['action'] == '2'){

			$error = false;
			$conn->beginTransaction();

			// delete voucher

			$order_voucher->set_id_order($current_id_order);
			$order_voucher->set_id_voucher($_POST['id_voucher']);
			if(!$order_voucher->delete()){
				$error = true;
			}

			// Create a new voucher if the discount is more than the total 

			if(!$voucher->delete_automatic_voucher($current_id_order)){
				$error = true;
			}

			$vouchers_discount 	= $order_voucher->get_vouchers_discount_real($current_id_order, $total_ammount);
			$difference 		= $total_ammount - $vouchers_discount;
			if($difference < 0){
				$voucher = new voucher();

				$voucher->set_id_voucher($voucher->max_id_voucher());
				$voucher->set_code(md5($voucher->get_id_voucher()));
				$voucher->set_emails("/".$address->get_email()."/");
				$voucher->set_till_date(date('Y-m-d', strtotime('+1 year')));
				$voucher->set_value_kind('amount');
				$voucher->set_value(abs($difference));
				$voucher->set_made_from_id_order($current_id_order);
				$voucher->set_description("Leftover Voucher");
				$voucher->set_min_cart_value("0");
				$voucher->set_visibility("Y");
				
				if( !$voucher->insert() ){
					$error = true;
				}
			}

			if($error){
			  $conn->rollBack();
			  $msj = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}else{
			  $conn->commit();
			}

		}
	?>
	
	<style>
		input[type="text"]{
			border: 1px solid #ccc;
			height:24px;
			padding: 6px 8px;
			display: block;
		}
		.alert-new-voucher{
			margin-top: 20px;
		}
		.vouchers_table td{
			padding-right: 10px;
		}
		.label {
    		display: block;
    		line-height: normal;
    		white-space: normal;
		}
	</style>
</head>
<body>

<?php if (strpos($_SERVER['HTTP_HOST'], 'miracas.in') !== false) { ?>
	<?php require_once("../menu_global.php"); ?> 
	<?php require_once("../sidebar_global.php"); ?> 
<?php }else{ ?>
	<?php require_once("../menu.php"); ?> 
	<?php require_once("../sidebar.php"); ?> 
<?php } ?>

<div id="menu-page-wraper">

	<div class="page-wrap"><div class="page-wrap-inner">

	<?php require_once("../message.php"); ?>
	<script>$('.nav-right a[href="../cart"]').addClass('selected');</script>

	<div class="tabs-container">

		<h2><i class="fa fa-ticket"></i> Voucher</h2>

		<?php echo $msj; ?>

		<a href="../select_address" class="btn btn-sm btn-green">go back</a>	
		<br><br>

		<form action="" method="post" name="form">
			<input type="hidden" name="action">
			<input type="hidden" name="id_voucher">

			<div class="cart-item">
					
				<h3>Available vouchers</h3>

				<table class="vouchers_table">
				
				<div class="alert alert-info text-center">
					<h5 style="margin:0;" >If you are unable to see your voucher here, it is possible that your voucher may have expired. This normally happens if the voucher code starts with the letter "V". To get a new voucher issued, please contact our customer care at 022 3077 0240 
					</h5>
				</div>
				
				<?php foreach ($vouchers_available as $row) { ?>
					
					<?php
						$good = true;
						if($row->get_min_cart_value() != 0 && $total_ammount < $row->get_min_cart_value()){ 
							$good = false;
						}
					?>
					<tr <?php if($row->get_visibility() == 'N'){ echo "style='display:none';"; } ?>>
						<td>
							<?php if(!$good){ ?>
								-<?php if($row->get_value_kind() == 'amount'){echo "₹";} ?><?php echo $row->get_value(); ?><?php if($row->get_value_kind() == 'percentage'){echo "%";} ?>
							<?php }else{ ?>
								<input 
									class="vouchers" 
									type="checkbox" 
									name="voucher_<?php echo $row->get_id_voucher(); ?>" 
									id="voucher_<?php echo $row->get_id_voucher(); ?>"
									data-value_kind="<?php echo $row->get_value_kind(); ?>"
									data-value="<?php echo $row->get_value(); ?>"
									data-code="<?php echo $row->get_code(); ?>"
									style="display:none;"
									<?php if($order_voucher->exists($current_id_order, $row->get_id_voucher())){ ?>
										checked="checked"
									<?php } ?>
								/>
								<?php if($order_voucher->exists($current_id_order, $row->get_id_voucher())){ ?>
									<i class="fa fa-check"></i>
								<?php } ?>
									-<?php if($row->get_value_kind() == 'amount'){echo "₹";} ?><?php echo $row->get_value(); ?><?php if($row->get_value_kind() == 'percentage'){echo "%";} ?>
							<?php } ?>
						</td>
						<td>
							<h5><?php echo $row->get_description(); ?></h5>
						</td>
						<td>
							<?php if(!$good){ ?>
								<div class="label label-warning">The cart value is less for this voucher to apply</div>
							<?php }else{ ?>
								<?php if($order_voucher->exists($current_id_order, $row->get_id_voucher())){ ?>
									<a href="javascript:remove_vocher('<?php echo $row->get_id_voucher(); ?>');" style="font-size:12px;font-weight:bold;">Remove</a>
								<?php }else{ ?>
									<a href="javascript:copypaste('<?php echo $row->get_code(); ?>');" class="voucher_<?php echo $row->get_code(); ?>" style="font-size:12px;font-weight:bold;">
										Add the voucher code
									</a>
								<?php } ?>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>

				</table>

				<h3>Voucher code</h3>

				<p><input type="text" class="form-control purchase_code" placeholder="Write your purchase code here"></p>

				<a class="btn btn-sm btn-green apply_purchase_code">Apply</a>
				
				<hr>

				<table class="table-totals">
					<tr>
						<td style="width: 150px;">Items:</td>
						<td class="text-right total_items" data-total_items="<?php echo $total_ammount; ?>">
							₹<?php echo number_format($total_ammount, 2); ?>
						</td>
					</tr>
					<tr>
						<td>Discount:</td>
						<td class="text-right discount"></td>
					</tr>
					<tr>
						<td>Order total:</td>
						<td class="text-right order_total"></td>
					</tr>
				</table>

				<hr>
				
				<p>
					<a href="javascript:save_vouchers();" class="btn btn-green btn-default">Save vouchers</a>
				</p>
			</div>	
		</form>

	</div> <!-- End tabs-container -->
	

	</div></div>
	<?php require_once("../footer.php"); ?>

	<script>
		$('.apply_purchase_code').on('click', function(e){
			e.preventDefault();
			var code_input 		= $('.purchase_code');
			var code 			= code_input.val();

			//$('.voucher_'+code).hide();
			code_input.val('');

			var checkbox = $('.vouchers[data-code="'+code+'"]');

			if(checkbox.is(':checked')){
				alert('This code is already applied!')
			}else{
				$('.vouchers[data-code="'+code+'"]').prop('checked', true);
			}

			calculate_all();

		});

		function copypaste(code){
			$('.purchase_code').val(code);
		}

		function save_vouchers(){
			document.form.action.value = "1";
			document.form.submit();
		}

		function remove_vocher(id_voucher){
			if(confirm("Are you sure?")){
				document.form.action.value = "2";
				document.form.id_voucher.value = id_voucher;
				document.form.submit();
			}
		}
	</script>

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

		function calculate_all(){
			$('.alert-new-voucher').remove();

			var total_items_val 	= parseFloat($('.total_items').attr('data-total_items'));
			var discount 			= 0;

			$('.vouchers:checked').each(function(){
				var $this = $(this);
				var value = 0;

				if($this.attr('data-value_kind') == 'percentage'){
					value = total_items_val * (parseFloat($this.attr('data-value'))/100);
				}else if($this.attr('data-value_kind') == 'amount'){
					value = parseFloat($this.attr('data-value'));
				}

				discount = Math.round((discount + value)* 1e12)/ 1e12;
			});

			var total = total_items_val-discount;

			if(total < 0){
				var message = $('<div class="alert alert-warning alert-new-voucher">In your next order you will recive a new voucher for ₹'+number_format(Math.abs(total), 2)+'</div>');
				message.insertAfter($('.table-totals'));
				total = 0;
			}

			$('.discount').html('₹'+number_format(discount, 2));
			$('.order_total').html('₹'+number_format(total, 2));
		}

		calculate_all();
	</script>

	</div>

	
</body>

</html>