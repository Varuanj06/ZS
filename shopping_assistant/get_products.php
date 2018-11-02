<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/order_detail.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/shopping_assistant_conversation.php"); ?>
<?php require_once("../classes/product_lang.php"); ?>
<?php require_once("../classes/fb_user_profile.php"); ?>
<?php require_once("../classes/fb_user_product_view.php"); ?>
<?php require_once("../classes/functions.php"); ?>
<?php require_once("../classes/fb_user_product_save.php"); ?>

<?php 

/* ====================================================================== *
		VARIABLES
 * ====================================================================== */			
	
	$id_fb_user 							= $user['id'];
	$id_shopping_assistant_conversation 	= $_POST['id_shopping_assistant_conversation'];
	$gender 								= $_POST['gender'];
	$age 									= $_POST['age'];
	$extra_param 							= $_POST['extra_param'];
	$profile 								= $_POST['profile'];

/* ====================================================================== *
		CLASSES
 * ====================================================================== */			

	$shopping_assistant_conversation 		= new shopping_assistant_conversation();
	$fb_user_profile 						= new fb_user_profile();
	$fb_user_product_view 					= new fb_user_product_view();
	$product 								= new product();
	$product_lang 							= new product_lang();
	$fb_user_product_save 					= new fb_user_product_save();

/* ====================================================================== *
		MAP INFO
 * ====================================================================== */			

	$shopping_assistant_conversation->map($id_fb_user, $id_shopping_assistant_conversation);
	$fb_user_profile->map_last_profile($id_fb_user);

	if($shopping_assistant_conversation->get_price_range() != ""){

/* ====================================================================== *
		GET PRODUCTS
 * ====================================================================== */			

		function get_products($profile){

			global $fb_user_product_view;
			global $product;
			global $id_fb_user;
			global $product_lang;
			global $shopping_assistant_conversation;

			$output_products 		= array();

			$products_tmp 	= $fb_user_product_view->get_products_by_profile($profile, $id_fb_user,' order by sum(qty) desc ');
			foreach ($products_tmp as $row) {

				$product->map($row);
				$id_product_prestashop 	= $product->get_id_product_prestashop();
				
				/* ### CHECK PRICE RANGE ### */

				if($product_lang->get_product_active($id_product_prestashop) == '0')continue;

				$price 					= get_the_price($product->get_id_product());
				$discount 				= get_the_discount($product->get_id_product(), $price);
				$total 					= $price-$discount;

				$price_range 			= explode(' - ', $shopping_assistant_conversation->get_price_range());
				$lower_limit 			= $price_range[0];
				$upper_limit 			= $price_range[1];

				
				if($total>=$lower_limit && $total<=$upper_limit){
					$output_products[] = $row;
				}
			}

			return $output_products;
			
		}

/* ====================================================================== *
		INIT PROFILE
 * ====================================================================== */					

		if($profile=='init'){
			$profile = $fb_user_profile->get_profile();
		}

/* ====================================================================== *
		GET THE NEXT PROFILE THAT HAS PRODUCTS
 * ====================================================================== */					

		$products 	= array();

		while(true){
			
			$products = get_products($profile);

			if(count($products)>0){
				break;
			}else{
				if($fb_user_profile->get_next_profile($profile)){
					$profile = $fb_user_profile->get_next_profile($profile); // try with the next profile and see if there are some products
				}else{
					break; // no more profiles to look for, so just let it finish!
				}
			}

		}
?>
		
		<div id="products-grid-<?php echo $profile ?>" data-profile="<?php echo $profile ?>">
			<?php 
				foreach ($products as $row) {

					/* ### PRODUCT INFO ### */

					$product->map($row);

					/* ### SOME VARIABLES ### */

					$last_chars 			= substr($product->get_image_link(), strrpos($product->get_image_link(), '/') + 1);
					$id_image 				= str_replace(".jpg", "", $last_chars); // get the 75 from this: http://url.com/9-75/75.jpg
					$id_product_prestashop 	= $product->get_id_product_prestashop();
					$img_url 				= "http://miracas.miracaslifestyle.netdna-cdn.com/$id_product_prestashop-$id_image-thickbox/$id_image.jpg";

					/* ### PRICE ### */

					$price 					= get_the_price($id_product_prestashop);
					$discount 				= get_the_discount($product->get_id_product(), $price);
					$total 					= $price-$discount;

					/* ### FB USER SAVED #### */

					$fb_user_product_save->set_id_fb_user($id_fb_user);
			        $fb_user_product_save->set_id_product($product->get_id_product());

			?>
					<div class="media-box">
			        	<div class="media-box-img-container">
			            	<div class="item-image" style="background-image:url('<?php echo $img_url; ?>')"></div>
						</div>	
						
						<div class="media-box-info">
							<div class="media-box-info-inner">
			            		<div class="media-box-info-title"><?php echo $product->get_name(); ?></div>
			            		<div class="media-box-info-price">₹<?php echo number_format($total, 2); ?></div>
			            		<a class="btn btn-primary btn-green btn-sm" target="_blank" href="../feed/product_details.php?id_product=<?php echo $product->get_id_product(); ?><?php echo $extra_param; ?>">
			                		<i class="fa fa-shopping-cart"></i>&nbsp; BUY NOW
			                	</a>
			                	<a style="background-color: #DEC395;color: #6D4D1C;" class="btn btn-primary btn-green btn-sm save_id_product" data-id-product="<?php echo $product->get_id_product(); ?>">
			                		<?php if ($fb_user_product_save->exists()){ echo "<i class='fa fa-check'></i>&nbsp; SAVED"; }else{ echo "<i class='fa fa-plus'></i>&nbsp; SAVE FOR LATER"; } ?>
			                	</a>
			                </div>
			            </div>
		        	</div>
			<?php 
				} 
			?>
		</div>

		<?php if($fb_user_profile->get_next_profile($profile)){ ?>
			<div class="text-center">
				<a class="btn btn-primary btn-green btn-md show_me_more" data-next-profile="<?php echo $fb_user_profile->get_next_profile($profile); ?>" data-current-profile="<?php echo $profile; ?>" style="margin-bottom:10px;margin-top:20px;display:none;">Show Me More</a>
			</div>
		<?php }else{ ?>
			<div class="item-text text-center">
				End of suggestions. We will intimate you when we add our new collection that will fit your taste. Continue browsing through our collection, you might find something awesome.
			</div>
		<?php } ?>

		<style>
			.media-box{
				padding: 20px;
				border-bottom: 1px solid #e8e8e8;
				background: #fff;
			}
			.media-box-container{
				background: #fff;
			}
			.media-box-img-container{
				width: 100px;
				height: 100px;
				float: left;

				background-color: black;
				background-image: url('../includes/plugins/Media Boxes/old/css/icons/loading-image.gif');
				background-position: center center;
	    		background-repeat: no-repeat;
			}
			.media-box-img{
				background-size:100% auto;
				width: 100%;
				height: 100%;
			}
			.media-box-info{
				height: 100px;
				display: table-cell;
				vertical-align: middle;
				padding: 0 20px;
			}
			@media only screen and (max-width: 768px) {
				.media-box{
					padding: 10px;
				}
				.media-box-info{
					padding: 0 10px;
				}
				.media-box-info .btn-sm{
					font-size: 10px;
				}
			}
			.media-box-info-inner{
				display: inline-block;
			}
			.media-box-info-title{
				font-size: 13px;
				font-weight: 400;
				margin-bottom: 5px;
			}
			.media-box-info-price{
				margin-bottom: 5px;
			}
			.media-box-container{
				-webkit-box-shadow: none !important;
				-moz-box-shadow: none !important;
				-o-box-shadow: none !important;
				-ms-box-shadow: none !important;
				box-shadow: none !important;
			}
			.media-box-info .btn{
				font-weight: 400;
			}
		</style>
		
		<script>
			var $grid = $('#products-grid-<?php echo str_replace("+", "\\\+", $profile) ?>').mediaBoxes({
				    	columns: 1,
				    	resolutions: [],
				    	horizontalSpaceBetweenBoxes: 0,
			        	verticalSpaceBetweenBoxes: 0,
			        	boxesToLoadStart: 8,
				    	boxesToLoad: 4,
				    	deepLinkingOnPopup: false,
						deepLinkingOnFilter: false,
				    });
		</script>
<?php

	}

 ?>
