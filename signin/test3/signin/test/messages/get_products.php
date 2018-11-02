<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/order_detail.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/message_conversation.php"); ?>

<?php 
	
	$id_fb_user 			= $user['id'];
	$id_conversation 		= $_POST['id_conversation'];

	$message_conversation 	= new message_conversation();
	$message_conversation->map($id_fb_user, $id_conversation);

	if($message_conversation->get_id_order() != "" && $message_conversation->get_id_order() != "0"){

?>

		<table>
			<thead>
				<tr>
					<th style="width:70%;" colspan="2">Description</th>
					<th>Color</th>
					<th>Size</th>
					<th>Quantity</th>
				</tr>
			</thead>
			<tbody>
			<?php
				$order_detail 	= new order_detail();
				$details 		= $order_detail->get_list($message_conversation->get_id_order(), " order by id_product ");
				foreach ($details as $row_detail) {

					$product	= new product();
					$product->map($row_detail->get_id_product());
			?>
			        <tr>
			        	<td style="width:1%"><img style="width:40px !important;"  src="<?php echo $product->get_image_link(); ?>" alt=""></td>
			        	<td style="width:80%;">
			        		
			        			<a href="javascript:send_msg('<?php echo str_replace("#", "", $product->get_name()); ?> #<?php echo $row_detail->get_id_order_detail(); ?>');" class="btn btn-green btn-sm">
			        				<?php echo $product->get_name(); ?>
			        			</a>

			        		
			        	</td>
			        	<td>
			        		<?php if($row_detail->get_color() != ""){ ?>
			        			<span class="media-box-color"><span style="background:<?php echo $row_detail->get_color(); ?>;"></span></span>
			        		<?php } ?>
			        	</td>
			        	<td><?php echo $row_detail->get_size(); ?></td>
			        	<td><?php echo $row_detail->get_qty(); ?></td>
			        </tr>
			<?php				
				}
			?>
			</tbody>
		</table>	

<?php

	}

 ?>
