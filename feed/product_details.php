<?php $force_session = true; ?>
<?php require_once("../fb_validator.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/espresso_products.php"); ?>
<?php require_once("../classes/attribute.php"); ?>
<?php require_once("../classes/product_lang.php"); ?>
<?php require_once("../classes/product_stock.php"); ?>
<?php require_once("../classes/functions.php"); ?>
<?php require_once("../classes/functions_espresso.php"); ?>
<?php require_once("../classes/image.php"); ?>
<?php require_once("../classes/fb_user_product_view.php"); ?>
<?php require_once("../classes/order.php"); ?>
<?php require_once("../classes/order_detail.php"); ?>
<?php require_once("../classes/fb_user_product_save.php"); ?>
<?php require_once("../classes/keyword_globalinfo.php"); ?>
<?php require_once("../classes/keyword_discount_code.php"); ?>
<?php require_once("../classes/product_globalinfo.php"); ?>
<?php require_once("../classes/fb_user_personal_info.php"); ?>

<!doctype html>
<html lang="en">
<head>

	<?php 

		$id_espresso_product 	= isset($_GET['id_espresso_product']) ? $_GET['id_espresso_product'] : '';
		$id_product 			= isset($_GET['id_product']) ? $_GET['id_product'] : '';

	/* ====================================================================== *
        	CLASSES
 	 * ====================================================================== */	

		$attribute 				= new attribute();
		$product_lang 			= new product_lang();
		$product 				= $id_espresso_product=='' ? new product() : new espresso_products(); 
		$product_stock 			= new product_stock;
		$image 					= new image();
		$order 					= new order();
		$fb_user_product_view 	= new fb_user_product_view();
		$fb_user_product_save 	= new fb_user_product_save();
		$order_detail 			= new order_detail();
		$keyword_discount_code 	= new keyword_discount_code();

	/* ====================================================================== *
        	MAP THE PRODUCT
 	 * ====================================================================== */		

        $current_id_product 	= $id_espresso_product=='' ? $id_product : $id_espresso_product;

		$product->map( $current_id_product );
		$product_lang->map($product->get_id_product_prestashop());

		$current_id_product_prestashop 	= $product->get_id_product_prestashop();

		if (strpos($_SERVER['HTTP_HOST'], 'miracas.in') !== false) {
			echo "<script>location.href='product_details_global.php?id_product=".$_GET['id_product']."';</script>";			
		}

		echo "<title>".$product->get_name()."</title>";

	/* ====================================================================== *
			CURRENT ID_ORDER
	 * ====================================================================== */	

		$current_id_order   = "";
		$id_fb_user 		= isset($user['id'])?$user['id']:'';

		if($id_fb_user != '' && $order->get_id_order_by_fb_user($id_fb_user)){
			$current_id_order = $order->get_id_order_by_fb_user($id_fb_user);
		}  

	/* ====================================================================== *
        	SAVE THE VIEWS OF THIS PRODUCT PER USER
 	 * ====================================================================== */	

        if($id_espresso_product==''){
	        $view_id_fb_user = -1;	
	    	if($active_session === true){
	    		$view_id_fb_user = $user['id'];
	    	}

			$fb_user_product_view->set_id_fb_user($view_id_fb_user);
			$fb_user_product_view->set_id_product($product->get_id_product());

			if($fb_user_product_view->exists()){
				$fb_user_product_view->map($fb_user_product_view->get_id_fb_user(), $fb_user_product_view->get_id_product());
				$fb_user_product_view->set_qty( $fb_user_product_view->get_qty()+1 );
				$fb_user_product_view->update();
			}else{
				$fb_user_product_view->set_qty(1);
				$fb_user_product_view->insert();
			}
		}
		
	/* ====================================================================== *
        	GET USERS WHO HAD JOINED THE DEAL (EITHER CREATED A DISCOUNT CODE OR USED A DISCOUNT CODE)
 	 * ====================================================================== */		

        $id_keyword 					= $id_espresso_product=='' ? str_replace("/", "", $product->get_keywords()) : $product->get_id_keyword();
		$active_keyword_discount_codes 	= $keyword_discount_code->get_list_by_keyword($id_keyword, "order by 1 desc");
		$users_who_joined 				= array();

		foreach ($active_keyword_discount_codes as $row) {

			/* CREATOR */
			
			$order_creator = new order();
			$order_creator->map($row->get_id_order());

			$creator_tmp 				= array();
			$creator_tmp['id_fb_user'] 	= $order_creator->get_id_fb_user();
			$creator_tmp['code'] 		= $row->get_code();
			$creator_tmp['date'] 		= $order_creator->get_date_done();
			$creator_tmp['id_order'] 	= $order_creator->get_id_order();
			$creator_tmp['type'] 		= 'creator';

			$users_who_joined[] 		= $creator_tmp;

			/* FOLLOWERS */

			$followers 					= $order->get_users_that_used_code($row->get_code());
			foreach ($followers as $row_inner) {
				
				$follower_tmp 				= array();
				$follower_tmp['id_fb_user']	= $row_inner->get_id_fb_user();
				$follower_tmp['code'] 		= $row->get_code();
				$follower_tmp['date'] 		= $row_inner->get_date_done();
				$follower_tmp['id_order'] 	= $row_inner->get_id_order();
				$follower_tmp['type'] 		= 'follower';

				$users_who_joined[] 		= $follower_tmp;

			}

		}

		usort($users_who_joined, function($first, $second){
	        return strtotime($first['date']) < strtotime($second['date']);
	    });

	?>

  	<?php require_once("../head.php"); ?>

  	<!-- Media Boxes CSS files -->
	<link rel="stylesheet" href="../includes/plugins/Media Boxes/plugin/components/Magnific Popup/magnific-popup.css"> <!-- only if you use Magnific Popup -->
	<link rel="stylesheet" type="text/css" href="../includes/plugins/Media Boxes/plugin/css/mediaBoxes.css">

	<!-- Popup files -->
	<link rel="stylesheet" type="text/css" href="../includes/plugins/popup/popup.css">
	<script src="../includes/plugins/popup/popup.js"></script>

	<?php 
		$last_chars 			= substr($product->get_image_link(), strrpos($product->get_image_link(), '/') + 1);
		$id_image 				= str_replace(".jpg", "", $last_chars); // get the 75 from this: http://url.com/9-75/75.jpg
		$id_product_prestashop 	= $product->get_id_product_prestashop();
		$img_url 				= "http://miracas.miracaslifestyle.netdna-cdn.com/$id_product_prestashop-$id_image-thickbox/$id_image.jpg";

		$price 					= $id_espresso_product=='' ? get_the_price($product->get_id_product()) : get_the_price_espresso($product->get_id_product());
		$discount 				= $id_espresso_product=='' ? get_the_discount($product->get_id_product(), $price) : get_the_discount_espresso($product->get_id_product(), $price);
		$keyword_discount 		= $id_espresso_product=='' ? get_the_keyword_discount($id_product, $price) : 0;
		$colors 				= $attribute->get_colors_of_product($id_product_prestashop);
		$sizes 					= $attribute->get_sizes_of_product($id_product_prestashop);
		$list_stock 			= $id_espresso_product=='' ? $product_stock->get_list_stock($product->get_id_product(), '') : array();

		//$colors 				= array('black', 'red', 'blue', 'white');

		// TOTAL STOCK AND WHAT COLORS/SIZES ARE AVAILABLE
		$colors_with_stock 		= [];
		$sizes_with_stock 		= [];
		foreach ($list_stock as $current_stock) {
			if($current_stock->get_stock()>0){
				if (!in_array($current_stock->get_color(), $colors_with_stock)) {
				    $colors_with_stock[] = $current_stock->get_color();
				}

				if (!in_array($current_stock->get_size(), $sizes_with_stock)) {
				    $sizes_with_stock[] = $current_stock->get_size();
				}
			}
		}
	?>

	<style>
		.green_text{
			color: #1c9c21;
		}	
		.yellow_text{
			color: #dc9811;
		}
		.product_container{
			background: #fff;	
			position: relative;

			-webkit-box-shadow: 0px 2px 4px 0px rgba(0,0,0,.5);
			-moz-box-shadow: 0px 2px 4px 0px rgba(0,0,0,.5);
			-ms-box-shadow: 0px 2px 4px 0px rgba(0,0,0,.5);
			-o-box-shadow: 0px 2px 4px 0px rgba(0,0,0,.5);
			box-shadow: 0px 2px 4px 0px rgba(0,0,0,.5);

		}
		.product_container::after {
		    content: "";
		    clear: both;
		    display: table;
		}
		.product_imgs,
		.product_info{
			width: 50%;
			float: left;
		}
		@media only screen and (max-width: 960px) {
			.product_imgs,
			.product_info{
				width: 100%;
			}
		}
		.product_info{
			padding: 20px;
			text-align: center;
		}
		.product_name{
			font-size: 20px;
			color: #000000;
		}
		.product_price{
			font-size: 20px;
			line-height: 20px;
			color: #000; 
		}
		.product_price_original{
			display: inline-block;
		    font-size: 16px;
		    color: rgba(0,0,0,.49);
		    position: relative;
		}
		.product_price_original:before {
		    position: absolute;
		    content: "";
		    left: 0;
		    top: 50%;
		    right: 0;
		    border-top: 1px solid;
		    color: #CC2D2D;
		    border-color: inherit;
		}
		.product_title{
			font-size: 16px;
			color: #000;
			margin-bottom: 10px;
		}	
		.product_size{
			display: inline-block;
			margin: 0 3px;
			font-size: 14px;
			color: rgba(0,0,0,.51);
			border-radius: 50%;
			cursor: pointer;
			padding: 5px;
			    border: 1px solid transparent;
		}
		.product_size span{
			border: 1px solid rgba(0,0,0,.51);
			border-radius: 50%;
			display: block;
			height: 40px;
			width: 40px;
			line-height: 40px;
			text-align: center;
		}
		.product_size.selected{
			border: 1px solid #00BCD4;
		}
		.product_color{
			display: inline-block;
			margin: 0 3px;
			height: 40px;
			width: 40px;
			line-height: 40px;
			text-align: center;
			border-radius: 50%;
			padding: 5px;
			cursor: pointer;
			border: 1px solid transparent;
		}
		.product_color>span{
			width: 100%;
			height: 100%;
			border-radius: 50%;
			display: block;
			border: 1px solid transparent;
		}
		.product_color.selected{
			border: 1px solid #00BCD4;
		}
		.btn.add_to_cart,
		.btn.add_to_cart_without_login{
			border: 1px solid #A52A19;
			background: #A52A19 !important;
			border-radius: 3px !important;
			font-size: 16px;
			color: #fff;
			padding: 6px 10px !important;
			margin: 0 5px;

			-webkit-box-shadow: none !important;
			-moz-box-shadow: none !important;
			-ms-box-shadow: none !important;
			-o-box-shadow: none !important;
			box-shadow: none !important;

		}
		.save_id_product{
			border: 1px solid #A52A19;
			background: #fff !important;
			border-radius: 3px !important;
			font-size: 16px;
			color: rgba(0,0,0,.5) !important;
			padding: 6px 10px !important;
			font-weight: 300;
			margin: 0 5px;

			-webkit-box-shadow: none !important;
			-moz-box-shadow: none !important;
			-ms-box-shadow: none !important;
			-o-box-shadow: none !important;
			box-shadow: none !important;
		}
		.save_id_product i{
			color: #A52A19 !important;
		}
		.product_caption{
			margin-top: 10px;
			font-weight: 300;
			font-size: 12px;
			color: rgba(0,0,0,.59);
		}
		.product_detail_bottom{
			background: #ffeff6;
			font-weight: 300;
			font-size: 12px;
			color: #000;
			padding: 20px;
			position: absolute;
			bottom: 0;
			right: 0;
		}
		.product_detail_bottom img{
			width: 100%;
		}
		.enter_code{
			background: #A52A19;
			color: #fff;
			font-size: 14px;
		}
		.input_insert_code{
			width: 100% !important;
			height: 45px !important;
			display: inline-block !important;
			font-size: 24px !important;
		    padding: 0 !important;
		    text-align: center !important;
		    color: #494949 !important;
		    line-height: 1.29412 !important;
			font-weight: 400 !important;
			letter-spacing: -.021em !important;
		}
		.input_insert_phone,
		.input_insert_code_popup{
			border: 1px solid #A52A19 !important;
			border-color: #A52A19 !important;
			width: 100% !important;
			height: 45px !important;
			display: inline-block !important;
			font-size: 24px !important;
		    padding: 0 !important;
		    text-align: center !important;
		    color: #494949 !important;
		    line-height: 1.29412 !important;
			font-weight: 400 !important;
			letter-spacing: -.021em !important;
		}
		.row_compress{
			margin-right: -2px;
			margin-left: -2px;
		}
		.row_compress>div{
			padding-right: 2px;
			padding-left: 2px;
		}
		.fa.keyword_code_check{
			display: none;
		}
	</style>

	<?php if($keyword_discount>0){ ?>
		<style>
			@media only screen and (max-width: 500px) {
				.enter_code{
					position: fixed;
					bottom: 0;
					z-index: 99;
					width: 100% !important;
				}
				.product_info{
					padding-bottom: 0 !important;
				}
				.slideout-panel{
					will-change: unset !important;
				}
			}
		</style>
	<?php } ?>		

</head>
<body>

	<?php require_once("../menu.php"); ?>
	<?php require_once("../sidebar.php"); ?>



	<div id="menu-page-wraper">

	<div class="page-wrap"><div class="page-wrap-inner">

		<?php require_once("../message.php"); ?>

		<script>$('.nav-right a[href="../feed"]').addClass('selected');</script>

		<div class="tabs-container">
			
			<div class="product_container">
				<div class="product_imgs" data-slideout-ignore>

					<link rel="stylesheet" href="../includes/plugins/Media Boxes/plugin/components/Font Awesome/css/font-awesome.min.css"> <!-- only if you use Font Awesome -->
					<link rel="stylesheet" type="text/css" href="../includes/plugins/slick/slick.css">
  					<link rel="stylesheet" type="text/css" href="../includes/plugins/slick/slick-theme.css">

  					<style>
  						.slick-initialized .slick-slide {
  							outline: none;
  						}
  						.slick-slide div{
  							display: block !important;
  						}
  						.slick-next,
  						.slick-prev{
  							font: normal normal normal 14px/1 FontAwesome;
  						}
  						.slick-next:before{
							content: "\f0a9";
							font-family: inherit;
  						}
  						.slick-prev:before{
							content: "\f0a8";
							font-family: inherit;
  						}
  						.open_magnificpopup{
  							cursor: pointer;
  						}
  					</style>

					<section class="slider">
						<?php
			            	$id_images 	= $image->get_images($id_product_prestashop);
							foreach ($id_images as $id_image) {
								$image_url = "http://miracas.miracaslifestyle.netdna-cdn.com/$id_product_prestashop-$id_image/$id_image.jpg";
								$popup_url = "http://miracas.miracaslifestyle.netdna-cdn.com/$id_product_prestashop-$id_image/$id_image.jpg"; 
								//$image_url = 'http://miracas.miracaslifestyle.netdna-cdn.com/1085-10406-thickbox/10406.jpg';
						?>
								<div>
							      	<!--<img src="http://miracas.miracaslifestyle.netdna-cdn.com/30756-458939/458939.jpg" style="width: auto;">-->
							      	<img 
							      		src="<?php echo $image_url; ?>" 
							      		style="width: auto;" 
							      		class="open_magnificpopup" 
							      		data-mfp-src="<?php echo $popup_url; ?>" 
							      	>
							    </div>
						<?php 
							}
		            	?>
					</section>
						
					<script src="../includes/plugins/slick/slick.js" type="text/javascript" charset="utf-8"></script>
					<script>
						function resize_imgs(){
							//var width 		= $('.product_info').outerWidth();
							//var new_width 	= (75 * width) / 100; // show 75% of the width
							//$('.product_imgs').find('img').width(new_width);

							var new_height 		= (viewport().height-52) * 90 / 100;
							$('.product_imgs').find('img').height(new_height);

							// adjust trustpay div 
							$('.product_info').css('padding-bottom', $('.product_detail_bottom').outerHeight(true));
							$('.product_detail_bottom').width($('.product_info').width())
						}

						$('document').ready(function(){
							resize_imgs();
						});

						$(window).resize(function(){
							resize_imgs();
						});

						$(".slider").slick({
					        dots: false,
					        infinite: true,
					        variableWidth: true
					    });

					    function viewport() {
				            var e = window, a = 'inner';
				            if (!('innerWidth' in window )) {
				                a = 'client';
				                e = document.documentElement || document.body;
				            }
				            return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
				        }
					</script>

				</div>
				<div class="product_info">

					<!--
						========= THE NAME =========
                	-->

					<div class="product_name"><?php echo $product->get_name(); ?></div>
					
					<br>

					<!--
						========= SOME INFO =========
                	-->

                	<!--<div class="green_text">You will recieve this product by <?php echo date('F d', strtotime(date("Y-m-d"). ' + 14 days')); ?> if you order today.</div>-->

                	<?php 
                		$user_gender 	= isset($user['gender'])?$user['gender']:'';
                		$price_total 	= $price-$discount;
                		$lower_limit 	= $user_gender=='female' ? $cod_lowerlimit_woman : $cod_lowerlimit_man;
						$upper_limit 	= $user_gender=='female' ? $cod_upperlimit_woman : $cod_upperlimit_man;

                		if( $price_total <= $upper_limit && $price_total >= $lower_limit ){ 
                	?>
                		<div class="yellow_text">Cash on Delivery is available for this product at 6000 + pinodes.</div>
                	<?php 
                		} 
                	?>

                	<br>

                	<!--
						========= THE PRICE =========
                	-->

					<?php if($keyword_discount<=0){ ?>

						<div class="product_price">
							&#8377;<?php echo number_format($price-$discount, 2); ?>

							<?php if($discount>0){ ?>
								<br>
								<div class="product_price_original">
		        					&#8377;<?php echo number_format($price, 2); ?>
		        				</div>
		        			<?php } ?>
						</div>	
						
	        			<br><br>

        			<?php } ?>

        			<!--
						========= THE STOCK =========
                	-->

        			<?php foreach ($list_stock as $current_stock){ ?>
						<div 
							class="stock" 
							data-color="<?php echo $current_stock->get_color(); ?>" 
							data-size="<?php echo $current_stock->get_size(); ?>" 
							data-stock="<?php echo $current_stock->get_stock(); ?>"
						></div>
                	<?php } ?>
					
					<!--
						========= THE SIZE =========
                	-->
			
					<?php if(count($sizes)>0){ ?>
						<div class="product_title">SELECT SIZE</div>
						
						<div>
	                		<?php 
	                			foreach ($sizes as $size){ 

	                				$public_size = $size;
	                				if($size == 'Extra Small') 	$public_size = 'XS';
	                				if($size == 'Small') 		$public_size = 'S';
	                				if($size == 'Medium') 		$public_size = 'M';
	                				if($size == 'Large') 		$public_size = 'L';
	                				if($size == 'Extra Large') 	$public_size = 'XL';
	                		?>
	                			<?php if(count($list_stock) && !in_array($size, $sizes_with_stock)){continue;} ?>
	                			<div class="product_size" data-size="<?php echo $size; ?>">
	                				<span><?php echo $public_size; ?></span>
	                			</div>
	                		<?php 
	                			} 
	                		?>
                		</div>

                		<br><br>
            		<?php } ?>


            		<!--
						========= THE COLORS =========
                	-->

                	<?php if(count($colors)>0){ ?>
                		<div class="product_title">COLORS</div>

						<div>
	            			<?php foreach ($colors as $color) {?>
	        					<?php if(count($list_stock) && !in_array($color, $colors_with_stock)){continue;} ?>
	                			<div class="product_color" data-color="<?php echo $color; ?>">
	                				<span 
	                					style="
	                						background:<?php echo $color; ?>;
	                						<?php if($color=='white' || $color=='#fff' || $color=='#ffffff'){ echo " border: 1px solid #dedddd !important; "; } ?>
	                					"
	                				></span>
	                			</div>
	                		<?php } ?>
                		</div>

                		<br><br>
            		<?php } ?>

            		<!--
						========= PRICE & ADD TO CART =========
                	-->
							
					<div style="max-width: 500px;margin: auto;">
						
						<?php if($keyword_discount>0){ ?>

							<div class="grid">
	            				<div class="column-50p" style="padding-right: 5px;">
	            					
	            					<a class="<?php echo $active_session ? "add_to_cart" : "add_to_cart_without_login"; ?> original_price" href="#">
		            					<div class="price_container">
											<div class="price_description">
												Buy At <br>
												Original Price
											</div>
											<div class="price_amount">
												<div class="vcenter"><div class="vcenter">
													&#8377;<?php echo number_format($price-$discount, 2); ?>
												</div></div>
											</div>	
										</div>
									</a>

									<div style="height: 10px;"></div>
									<span>&nbsp;</span>

	            				</div>
	            				<div class="column-50p" style="padding-left: 5px;">
								
									<a class="<?php echo $active_session ? "add_to_cart" : "add_to_cart_without_login"; ?> with_keyword_discount" href="#">
										<div class="price_container">
											<div class="price_description">
												Buy In <br>
												Groups
											</div>
											<div class="price_amount">
												<div class="vcenter"><div class="vcenter">
													<span style="text-decoration: line-through;">&#8377;<?php echo number_format($price-$discount, 2); ?></span>
													<br>
													&#8377;<?php echo number_format($price-$keyword_discount, 2); ?>
												</div></div>
											</div>	
										</div>
									</a>

									<div style="height: 10px;"></div>
									<a href="javascript:$('#how_it_works_modal').modal('show').appendTo('body');">How it works?</a>

	            				</div>
	            			</div>

	            		<?php }else{ ?>

							<?php if($id_espresso_product=='' && $active_session === true){ ?>
		        				<?php 
		        					$fb_user_product_save->set_id_fb_user($user['id']);
		        					$fb_user_product_save->set_id_product($product->get_id_product());
		        				?>
		            			<a 
		            				class="btn btn-sm btn-green save_id_product" 
		            				data-id-product="<?php echo $product->get_id_product(); ?>"
		            				data-saved="<i class='fa fa-check'></i>&nbsp; Saved" 
				                	data-notsaved="<i class='fa fa-bookmark'></i>&nbsp; Save for later"
		            			>
			                		<?php if ($fb_user_product_save->exists()){ echo "<i class='fa fa-check'></i>&nbsp; Saved"; }else{ echo "<i class='fa fa-bookmark'></i>&nbsp; Save for later"; } ?>
			                	</a>
			                <?php } ?>
							
							<span style="position: relative;">
			    				<a class="<?php echo $active_session ? "add_to_cart" : "add_to_cart_without_login"; ?> btn btn-sm " href="#">
			    					<span class="fa fa-shopping-cart"></span> &nbsp; Add to Cart
			    				</a>
			    			</span>

	            		<?php } ?>

	    				<div class="combination_not_available">
							The selected product combination is not available. Please select a different color or size.
						</div>

						<?php $product_in_cart = $order_detail->exists_product_prestashop_in_order($current_id_order, $id_product_prestashop); ?>

						<div class="alert alert-success product_in_cart" style="margin-bottom: 0;margin-top: 10px;padding: 5px;font-size: 11px; <?php if(!$product_in_cart){ echo "display:none;";  } ?>">
							The product has been added to your cart
						</div>

					</div>

					<!--
						========= SOME EXTRA INFORMATION =========
                	-->

                	<br>

                	<div class="green_text"><a href="../cart" >Open My Shopping Cart</a></div>

					<div class="product_caption">
						PREMIUM INTERNATIONAL CATALOG | ASSURED QUALITY
					</div>

					<?php if($id_espresso_product==''){ ?>
						<br>
						<div class="green_text">You and <?php echo $fb_user_product_view->get_id_product_view_count($product->get_id_product()); ?> others have shown interest in this product.</div>
						<br>
					<?php } ?>

					<!--
						========= USERS WHO JOINED THE DEAL =========
                	-->

					<?php if($keyword_discount>0){ ?>
						<div>
							<?php 
								$cont 		= 0;
								$more_deals = false;
								foreach ($users_who_joined as $row) { 
									if($row['id_fb_user'] == $id_fb_user) continue;
									
									$cont++;
									if($cont>2) {
										$more_deals = true;
										break;
									}

									$fb_profile_img = "http://graph.facebook.com/".$row['id_fb_user']."/picture?width=80&height=80";

									$fb_user_personal_info  = new fb_user_personal_info();
									$fb_user_personal_info->map($row['id_fb_user']);
							?>
									<div class="row text-left">
										<div class="col-sm-7">
											
											<img src="<?php echo $fb_profile_img; ?>" alt="" style="border-radius: 100%; margin-right: 20px;" class="pull-left" height="45px">
											<strong><?php echo $fb_user_personal_info->get_name()." ".$fb_user_personal_info->get_last_name(); ?></strong>
											<br>
											Joined the deal on <?php echo date('M d,y', strtotime($row['date'])); ?>

										</div>
										<div class="col-sm-5">

											<div class="price_container enter_phone_number" data-code="<?php echo $row['code']; ?>" data-img="<?php echo $fb_profile_img; ?>">
												<div class="price_description">
													Join The <br>
													Deal
												</div>
												<div class="price_amount">
													<div class="vcenter"><div class="vcenter">
														<span style="text-decoration: line-through;">&#8377;<?php echo number_format($price-$discount, 2); ?></span>
														<br>
														&#8377;<?php echo number_format($price-$keyword_discount, 2); ?>
													</div></div>
												</div>	
											</div>

										</div>
									</div>
									<hr style="margin: 10px 0;">
							<?php
								}
							?>
							
							<?php if($more_deals){ ?>
								<a href="javascript:$('#users_who_joined').modal('show').appendTo('body');">Show More Deals</a>
								<br><br>
							<?php } ?>

						</div>
					<?php } ?>
					
					<!--
						========= ENTER DISCOUNT CODE OR RETURN POLICY TEXT =========
                	-->
					
					<?php if($keyword_discount>0){ ?>
						<div class="product_detail_bottom enter_code text-left">
							<div class="row">
								<div class="col-sm-6">
									Already have a share id from a friend
									<br>
									Get the discount right away 

									<span class="fa fa-check pull-right keyword_code_check" style="font-size: 25px;margin-top: -10px;"></span>
								</div>
								<div class="col-sm-6">
									
									<div class="row row_compress">
										<div class="col-xs-2">
											<input type="text" class="input_insert_code" maxlength="1" />		
										</div>
										<div class="col-xs-2">
											<input type="text" class="input_insert_code" maxlength="1" />		
										</div>
										<div class="col-xs-2">
											<input type="text" class="input_insert_code" maxlength="1" />		
										</div>
										<div class="col-xs-2">
											<input type="text" class="input_insert_code" maxlength="1" />		
										</div>
										<div class="col-xs-2">
											<input type="text" class="input_insert_code" maxlength="1" />		
										</div>
										<div class="col-xs-2">
											<input type="text" class="input_insert_code" maxlength="1" />		
										</div>
									</div>

								</div>
							</div>
						</div>
					<?php }else{ ?>
						<div class="product_detail_bottom text-left">
							<div class="row">
								<div class="col-sm-4">
									<img src="http://miracas.com/ZS/assets/trustPay_logo.png" alt="">
								</div>
								<div class="col-sm-8">
									15 Days Return Policy. No Questions Asked. <br>
									Reverse Pickup available in over 5000+ pincodes. <br>
									100% Payment protection for your order. You can invoke this if you have paid for the item but didn't receive it within 30 days.
								</div>
							</div>
						</div>
					<?php } ?>							

				</div>
			</div>

		<!-- 
		/* ====================================================================== *
	      		PRODUCT INFORMATION
	 	 * ====================================================================== */ 
	 	-->	  

	 		<br>

	 		<style>
	 			.product_description{
	 				overflow-x: scroll;
    				max-width: 100%;
	 				background: #fff;
	 				padding: 20px;

	 				-webkit-box-shadow: 0px 2px 4px 0px rgba(0,0,0,.5);
					-moz-box-shadow: 0px 2px 4px 0px rgba(0,0,0,.5);
					-ms-box-shadow: 0px 2px 4px 0px rgba(0,0,0,.5);
					-o-box-shadow: 0px 2px 4px 0px rgba(0,0,0,.5);
					box-shadow: 0px 2px 4px 0px rgba(0,0,0,.5);
	 			}
	 			.section_title{
	 				border-bottom: 1px solid rgba(151, 151, 151, .5);
				    display: inline-block;
				    width: 450px;
				    max-width: 100% !important;
				    text-align: center;
				    margin-bottom: 20px;
				    font-size: 18px;
				    color: rgba(0,0,0,.47);
				    padding: 5px 0;
	 			}
	 		</style>
			
			<div class="product_description" data-slideout-ignore>
				<div class="text-center">
					<div class="section_title">PRODUCT INFORMATION</div>	
				</div>

				<?php echo $product_lang->get_description(); ?>
			</div>				

		<!-- 
		/* ====================================================================== *
	      		COLLECTIONS
	 	 * ====================================================================== */ 
	 	-->	  

	 	<?php if($id_espresso_product==''){ ?>

			<br><br><br>

			<style>

			/* KEYWORD CONTAINER */

				.keyword_with_collection_border{
					border: 1px gray;
					margin: 0 -5px;
					margin-bottom: 10px;
					padding: 0px;
					cursor: pointer;
				}

				@media only screen and (max-width: 768px) {
					.keyword_with_collection_border{
						overflow-y: hidden;
						overflow-x: scroll;
					}
					.keyword_with_collection{
						width: 270%;
					}
				}

				.keyword_with_collection_border::after {
				    content: "";
				    clear: both;
				    display: table;
				}

				.keyword_with_collection{
					/*width: 100%;*/
					height: auto;
				}

				.keyword_with_collection-card{
					height: 100%;
					width: 100%;
					padding: 10px;
		    		background: #f3e4ca;
				}

				.keyword_with_collection-card-text{
					background-color: rgba(171, 119, 42, 1);
					color: white;
				}

				.keyword_with_collection-card-text>span{
					display: table;
					width: 100%;
					height: 100%;
				}
				.keyword_with_collection-card-text>span>span{
					display: table-cell;
					vertical-align: middle;
				}

			/* COLUMNS */

				.keyword_with_collection-column{
					width: 33.33%;
					float: left;
					position: relative;
					padding: 0 5px;
				}

			/* COLLECTION */

				.collection_title{
					position: absolute;
					top: 50%;
					left: 50%;

					-webkit-transform   : translateX(-50%) translateY(-50%);
		      		-moz-transform      : translateX(-50%) translateY(-50%);
				    -ms-transform       : translateX(-50%) translateY(-50%);
				    transform           : translateX(-50%) translateY(-50%);

					width: 100px;
					height: 100px;
					text-align: center;
					border-radius: 50%;
					background: #f3e4ca;
					display: table;
					font-size: 15px;
				}
				.collection_title_content{
					display: table-cell;
		    		vertical-align: middle;
				}
				.collection_img_grid{
					margin: 0 -5px;
					margin-bottom: -10px;
					overflow: hidden;
				}
				.collection_img{
					padding: 0 5px;
					margin-bottom: 10px;
					float: left;
					width: 50%;
					/* background-color: #f3e4ca; */
					background-image: url('../includes/plugins/Media Boxes/plugin/css/icons/loading-image.gif');
					background-position: center center;
					background-repeat: no-repeat;
				}
				.collection_img>div{
					width: 100%;
					height: 100%;
					/* background-size:100% auto; */
					background-size: cover;
					background-position: center center;
					background-repeat: no-repeat;
				}
				@media only screen and (max-width: 1000px) {
					.collection_title{
						width: 80px;
						height: 80px;
						font-size: 13px;
					}
				}
				@media only screen and (max-width: 768px) {
					.collection_title{
						width: 70px;
						height: 70px;
						font-size: 12px;
					}
				}
				@media only screen and (max-width: 500px) {
					.collection_title{
						width: 60px;
						height: 60px;
						font-size: 11px;
					}
				}
			</style>

			<?php 

				// VARS

				$product_keyword 	= str_replace("/", "", $product->get_keywords());
				$user_birthday 		= isset($user['birthday'])?$user['birthday']:'';
				$user_gender 		= isset($user['gender'])?$user['gender']:'';

				// GET AGE

				$user_age 			= '';
				if( $user_birthday != '' ){
					$from 			= new DateTime($user_birthday);
					$to   			= new DateTime('today');
					$user_age  		= $from->diff($to)->y;
				}

				// GENDER AND AGE WITH SLASHES

				$user_gender_slash 	= $user_gender != '' ? '/'.$user_gender.'/' : '';
				$user_age_slash 	= $user_age != '' ? '/'.$user_age.'/' :  '';

			?>

			<div class="text-center">
				<div class="section_title">SIMILAR COLLECTIONS</div>	
			</div>

			<div data-slideout-ignore class="keyword_with_collection_border" onclick="location.href='./?q=<?php echo $product_keyword; ?><?php echo str_replace("?", "&", $extra_param); ?>&collections=yes';">
				<div class="keyword_with_collection">

					<?php 
						/* ====================================================================== *
						    	COLLECTIONS
						 * ====================================================================== */	

						$products 	= $product->get_search($product_keyword, $user_gender_slash, $user_age_slash, " order by like_count desc ");
					   	$output 	= array(); 	

					    foreach ($products as $row){
					   		$product_date 	= $product_lang->get_date_add($row->get_id_product_prestashop());
							//if($product_date == '') $product_date = '2017-06-06'; // this line is for testing purposes only
					   		if($product_date <> ''){
					   			$product_date_format 	= strtotime( date("F Y", strtotime($product_date)) );

					   			if(!array_key_exists($product_date_format, $output)){
					   				$output[$product_date_format]   	= array();	
					   			}

					   			$output[$product_date_format][] 	=	array( 'id_product' => $row->get_id_product() );
					   		}
						}	

						krsort($output);
					?>
					
					<?php foreach ($output as $key => $value){ ?>
						<div class="keyword_with_collection-column">
							<div class="keyword_with_collection-card">
								<div class="collection_title">
									<div class="collection_title_content">
										<strong><?php echo date("F", $key); ?></strong>
										<br>
										<?php echo date("Y", $key); ?>
									</div>
								</div>

								<div class="collection_img_grid">
									<?php 
										for ($i=0; $i < 4; $i++) { 
										
											if(isset($value[$i])){
												$product_collections = new product();
												$product_collections->map($value[$i]['id_product']);

												$id_product_prestashop 	= $product_collections->get_id_product_prestashop();
												$img_link_last_chars 	= substr($product_collections->get_image_link(), strrpos($product_collections->get_image_link(), '/') + 1);
												$id_image 				= str_replace(".jpg", "", $img_link_last_chars); // get the 75 from this: http://url.com/9-75/75.jpg
												$img_url 				= "http://miracas.miracaslifestyle.netdna-cdn.com/$id_product_prestashop-$id_image-thickbox/$id_image.jpg";

												echo "<div class='collection_img'><div style=\"background-image:url('$img_url');\"></div></div>";

											}else{
												echo "<div class='collection_img'><div style='background:gray;'></div></div>";	
											}
										
										} 
									?>
								</div>
							</div>
						</div>
					<?php } ?>

				</div>
			</div>

			<!-- #### RESIZE IMAGES #### -->

			<script>
				function resize_keyword_with_collections(){

					/* MAKE THE HEIGHT OF THE IMAGES OF THE COLLECTIONS MATCH THEIR WIDTH */

			    	var collection_img 		= $('.keyword_with_collection').find('.collection_img').eq(0);
			    	var collection_img_w	= collection_img.outerWidth(true);

			    	var css 				= 	" .keyword_with_collection .collection_img{ "+ 
				    						  	" 	height: "+collection_img_w+"px !important; "+ 
				    						  	" } ";

			    	var $stylesheet 		= $('<style type="text/css" media="screen" />').html(css);
					$('body').append($stylesheet);

					/* MAKE THE FIRST COLUMN MATCH THE HEIGHT OF THE OTHER COLUMNS */

					var collection_column 	= $('.keyword_with_collection').find('.keyword_with_collection-column').eq(0);
					var collection_column_h	= collection_column.outerHeight(true);

					var css 				= 	" .keyword_with_collection .keyword_with_collection-column-first{ "+ 
				    						  	" 	height: "+collection_column_h+"px !important; "+ 
				    						  	" } ";

			    	var $stylesheet 		= $('<style type="text/css" media="screen" />').html(css);
					$('body').append($stylesheet);
			    }

			    setTimeout(function(){
			    	// this is also executed after the Media Boxes finish loading, because sometimes it doesn't load on time so it doesn't resize correctly
			    	resize_keyword_with_collections();
			    }, 1);

			    $(window).resize(function(){
			    	setTimeout(function(){
			    		resize_keyword_with_collections();
			    	}, 1);
			    });
			</script>

			<!-- #### SHOW HIDE COLUMNS ACCORDING TO SCREEN SIZE #### -->

			<script>
				function viewport() {
		            var e = window, a = 'inner';
		            if (!('innerWidth' in window )) {
		                a = 'client';
		                e = document.documentElement || document.body;
		            }
		            return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
		        }

				function hide_show_columns(){
					if(viewport().width <= 500){
						hide_show_column(2);
					}else if(viewport().width > 500 && viewport().width <= 768){
						hide_show_column(2);
					}else if(viewport().width > 768 && viewport().width <= 3000){
						hide_show_column(2);
					}else{
						hide_show_column(2);
					}
				}

				function hide_show_column(max){
					$(".keyword_with_collection").each(function(){
						$(this).find(".keyword_with_collection-column").each(function(index){
							if(index <= max){
								$(this).show();
							}else{
								$(this).hide();
							}
						});
					}); 
				}

				hide_show_columns();

				$(window).resize(function(){
					hide_show_columns();
				});
			</script>

		<?php } ?>

		</div> <!-- End tabs-container -->

	<!-- 
	/* ====================================================================== *
      		ENTER PHONE NUMBER TO REQUEST CODE
 	 * ====================================================================== */ 
 	-->	 

		<!-- Popup -->

		<div id="popup-enter_phone_number" style="display: none;">
			<button type="button" class="fa-window-close close">
	          	<span aria-hidden="true">&times;</span>
	        </button>

			<div class="text-center">
				<h4 style="color: #3ac73a;">Request the discount code to join the deal</h4>

				<br>
				
				<div>
					<img class="img_of_user_to_follow" src="" alt="" style="border-radius: 100%; margin-right: 20px; vertical-align: top;" height="65px">
					<div class="question_mark">?</div>
				</div>

				<br>

				<div>Enter Your Mobile Number</div>

				<div class="row row_compress">
					<div class="col-xs-1">
						<input type="text" class="input_insert_phone" maxlength="1" />		
					</div>
					<div class="col-xs-1">
						<input type="text" class="input_insert_phone" maxlength="1" />		
					</div>
					<div class="col-xs-1">
						<input type="text" class="input_insert_phone" maxlength="1" />		
					</div>
					<div class="col-xs-1">
						<input type="text" class="input_insert_phone" maxlength="1" />		
					</div>
					<div class="col-xs-1">
						<input type="text" class="input_insert_phone" maxlength="1" />		
					</div>
					<div class="col-xs-1">
						<input type="text" class="input_insert_phone" maxlength="1" />		
					</div>
					<div class="col-xs-1">
						<input type="text" class="input_insert_phone" maxlength="1" />		
					</div>
					<div class="col-xs-1">
						<input type="text" class="input_insert_phone" maxlength="1" />		
					</div>
					<div class="col-xs-1">
						<input type="text" class="input_insert_phone" maxlength="1" />		
					</div>
					<div class="col-xs-1">
						<input type="text" class="input_insert_phone" maxlength="1" />		
					</div>
				</div>

				<br><br>

				<a href="javascript:send_keyword_code();" class="btn btn-green btn-sm request_discount_code" style="background-color: #A52A19;">
					Request Discount Code
				</a>
			</div>
		</div>

		<style>
			.popup-modal{
				background: none;
			}
			.popup-content{
				border-radius: 12px !important;
			}
			.question_mark{
				width: 65px;
				height: 65px;
				display: inline-block;
				border: 1px dashed #a7a7a7;
				color: #a7a7a7;
				border-radius: 50%;
				font-size: 30px;
				text-align: center;
				line-height: 65px;
			}
			.popup-modal .close{
			    top: -15px;
			    right: -15px;
			    color: #828282;
			    border: 0;
			    font-weight: normal;
			    text-shadow: none;
			    font-size: 21px;
			    background: #e0e0e0;
			}
			.enter_phone_number{
				cursor: pointer;
			}
			.popup-modal{
				width: 500px !important;
				max-width: 100% !important;
				height: auto !important; 
				overflow: unset !important;
				bottom: unset;
    			top: 50%;
    			transform: translateY(-50%);
			}
			#popup-enter_phone_number .col-xs-1 {
				width: 10% !important;
			}

			@media only screen and (max-width: 450px) {
				.popup-modal{
					height: 220px !important; 
				}
				.popup-content{
					padding: 30px 10px;
				}
				.popup-modal .close{
				    top: -15px;
				    right: -0px;
				}
			}
		</style>

		<!-- OPEN PHONE NUMBER POPUP --> 		

		<script>
			$('#popup-enter_phone_number').appendTo('body');

			$('body').on('click', '.enter_phone_number', function(){
				if("<?php echo $active_session ? 'active' : 'noactive' ?>" == 'noactive'){
					var idProduct 			= '<?php echo $current_id_product; ?>';

					$.get('join_the_deal_without_login.php?id_product='+idProduct, function(r){		 
		    			if($.trim(r) == "success"){
		    				$('#myModal').modal('show').appendTo('body');
		    			}else{
		    				alert("Oops! something went wrong, refresh the page and try again.");
		    			}
		    		});

				}else{
					if( something_missing() ){
						alert(' Please choose size / color');
					}else{
						var code 	= $(this).attr('data-code');
						var img 	= $(this).attr('data-img');

						$('#popup-enter_phone_number').find('.img_of_user_to_follow').attr('src', img);
						$('#popup-enter_phone_number').attr('data-code', code).popup();
						$('.input_insert_phone').val('');
						$('.input_insert_phone').first().focus();
					}
				}
			});
		</script>

		<!-- INSERT PHONE NUMBER -->	  		

		<script>

			// GO TO THE NEXT INPUT

			$('body').on('keyup', '.input_insert_phone', function(e){
				var $this = $(this);

				$this.val( $this.val().toUpperCase() );

				if($this.val().length > 0){ // next
					$this.parent('.col-xs-1').next().find('.input_insert_phone').focus();
				}
			});

			// WHEN DELETE GO TO PREVIOUS INPUT

			$('body').on('keydown', '.input_insert_phone', function(e){
				var $this = $(this);

				if($this.val().length == 0 && (e.keyCode==8 || e.keyCode==46)){
					$this.parent('.col-xs-1').prev().find('.input_insert_phone').focus();	
				}
			});


			// PASTE CODE

			$('body').on("paste", '.input_insert_phone', function(e) {
			    var pastedData = e.originalEvent.clipboardData.getData('text');

			    if(pastedData.length == 10){
			    	var pastedData_values = pastedData.split("");

				    $('.input_insert_phone').each(function(index){
				    	$(this).val(pastedData_values[index]);
				    });
			    }

			});

			// SEND CODE

			var requesting_discount_code = false;
			function send_keyword_code(){
				if(requesting_discount_code) return;

				requesting_discount_code = true;
				$('.request_discount_code').html('<i class="fa fa-refresh fa-spin fa-fw"></i> Requesting...');
				$('.resend_discount_code').html('<i class="fa fa-refresh fa-spin fa-fw"></i> Sending...');

				var all_completed 	= true;
				var phone 			= '';
				$('.input_insert_phone').each(function(){
					if($(this).val() == ''){
						all_completed = false;
					}else{
						phone += $(this).val();
					}
				});

				if(all_completed){

					var code = $('#popup-enter_phone_number').attr('data-code');
				
					$.get('send_keyword_code.php?phone='+phone+'&keyword_code='+code, function(r){	
		    			if($.trim(r) == "success"){
		    				//alert("Code Sent!");

		    				$('#popup-enter_phone_number').popup('close_withouth_bg');
		    				//$('.input_insert_phone').val('');
							//$('.input_insert_phone').first().focus();

							requesting_discount_code = false;
							$('.request_discount_code').html('Request Discount Code');
							$('.resend_discount_code').html('Resend');

							setTimeout(function(){
								popup_insert_code( $('#popup-enter_phone_number').find('.img_of_user_to_follow').attr('src') );
							}, 0);
		    			}
			    	});
					
				}else{
					alert('Your mobile number is not complete');
					requesting_discount_code = false;
					$('.request_discount_code').html('Request Discount Code');
					$('.resend_discount_code').html('Resend');
				}
			}

		</script>

	<!-- 
	/* ====================================================================== *
      		POPUP ENTER THE CODE
 	 * ====================================================================== */ 
 	-->	 

		<!-- Popup -->

		<div id="popup-enter_keyword_code" style="display: none;">
			<button type="button" class="fa-window-close close">
	          	<span aria-hidden="true">&times;</span>
	        </button>

			<div class="text-center">
				<h4 style="color: #3ac73a;" class="enter_discount_code_popup_msg">Enter the discount code to get the discount</h4>

				<br>
				
				<div>
					<img class="img_of_user_to_follow" src="" alt="" style="border-radius: 100%; margin-right: 20px; vertical-align: top;" height="65px">
					<div class="question_mark">?</div>
				</div>
				
				<br>

				<div class="row row_compress">
					<div class="col-xs-2">
						<input type="text" class="input_insert_code_popup" maxlength="1" />		
					</div>
					<div class="col-xs-2">
						<input type="text" class="input_insert_code_popup" maxlength="1" />		
					</div>
					<div class="col-xs-2">
						<input type="text" class="input_insert_code_popup" maxlength="1" />		
					</div>
					<div class="col-xs-2">
						<input type="text" class="input_insert_code_popup" maxlength="1" />		
					</div>
					<div class="col-xs-2">
						<input type="text" class="input_insert_code_popup" maxlength="1" />		
					</div>
					<div class="col-xs-2">
						<input type="text" class="input_insert_code_popup" maxlength="1" />		
					</div>
				</div>

				<br>

				Did not get the discount code? <a href="javascript:send_keyword_code();" style="color: #3ac73a; text-decoration: none !important;" class="resend_discount_code">Resend</a>

			</div>
		</div>

		<!-- OPEN INSERT CODE POPUP --> 		

		<script>
			$('#popup-enter_keyword_code').appendTo('body');

			function popup_insert_code(img_src){
				$('#popup-enter_keyword_code').find('.img_of_user_to_follow').attr('src', img_src);
				$('#popup-enter_keyword_code').popup();
			}
		</script>

		<!-- INSERT PHONE NUMBER -->	  		

		<script>
			
			// GO TO THE NEXT INPUT

			$('.input_insert_code_popup').on('keyup', function(e){
				var $this = $(this);

				$this.val( $this.val().toUpperCase() );

				if($this.val().length > 0){ // next
					$this.parent('.col-xs-2').next().find('.input_insert_code_popup').focus();
				}

				insert_code_popup_complete();
			});

			// WHEN DELETE GO TO PREVIOUS INPUT

			$('.input_insert_code_popup').on('keydown', function(e){
				var $this = $(this);

				if($this.val().length == 0 && (e.keyCode==8 || e.keyCode==46)){
					$this.parent('.col-xs-2').prev().find('.input_insert_code_popup').focus();	
				}
			});

			// ON COMPLETE 

			var keyword_code = '';
			function insert_code_popup_complete(){
				var all_completed 	= true;
				var code 			= '';
				$('.input_insert_code_popup').each(function(){
					if($(this).val() == ''){
						all_completed = false;
					}else{
						code += $(this).val();
					}
				});

				if(all_completed){
					keyword_code = code;

					$('.enter_discount_code_popup_msg').html('<i class="fa fa-refresh fa-spin fa-fw"></i> Entering...')

					$('.with_keyword_discount').trigger('click');
					$('.input_insert_code_popup').val('');
					$('.input_insert_code_popup').first().focus();
				}else{
					keyword_code = '';
				}
			}

			// PASTE CODE

			$('.input_insert_code_popup').on("paste", function(e) {
			    var pastedData = e.originalEvent.clipboardData.getData('text');

			    if(pastedData.length == 6){
			    	var pastedData_values = pastedData.split("");

				    $('.input_insert_code_popup').each(function(index){
				    	$(this).val(pastedData_values[index]);
				    });
			    }

			});

		</script>

	<!-- 
	/* ====================================================================== *
      		USERS WHO JOINED THE DEAL
 	 * ====================================================================== */ 
 	-->	  

		<!-- Modal -->

		<div class="modal fade" id="users_who_joined" role="dialog" aria-labelledby="myModalLabel">
		  	<div class="modal-dialog" role="document">
		    	<div class="modal-content">
		      		<div class="modal-body">

		      			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				          <span aria-hidden="true">&times;</span>
				        </button>

				        <h5>Users who joined the deal</h5>
				        <hr>
		      			
						<?php 
							$cont = 0;
							foreach ($users_who_joined as $row) { 
								if($row['id_fb_user'] == $id_fb_user) continue;

								$cont++;
								if($cont>25) break;

								$fb_profile_img = "http://graph.facebook.com/".$row['id_fb_user']."/picture?width=80&height=80";

								$fb_user_personal_info  = new fb_user_personal_info();
								$fb_user_personal_info->map($row['id_fb_user']);
						?>
								<div class="row text-left">
									<div class="col-sm-7">
										
										<img src="<?php echo $fb_profile_img; ?>" alt="" style="border-radius: 100%; margin-right: 20px;" class="pull-left" height="45px">
										<strong><?php echo $fb_user_personal_info->get_name()." ".$fb_user_personal_info->get_last_name(); ?></strong>
										<br>
										Joined the deal on <?php echo date('M d,y', strtotime($row['date'])); ?>

									</div>
									<div class="col-sm-5">

										<div class="price_container enter_phone_number" data-code="<?php echo $row['code']; ?>">
											<div class="price_description">
												Join The <br>
												Deal
											</div>
											<div class="price_amount">
												<div class="vcenter"><div class="vcenter">
													<span style="text-decoration: line-through;">&#8377;<?php echo number_format($price-$discount, 2); ?></span>
													<br>
													&#8377;<?php echo number_format($price-$keyword_discount, 2); ?>
												</div></div>
											</div>	
										</div>

									</div>
								</div>
								<hr style="margin: 10px 0;">
						<?php
							}
						?>

		      		</div>
		    	</div>
		  	</div>
		</div>	

	<!-- 
	/* ====================================================================== *
      		PRODUCT WITH DISCOUNT ADDED
 	 * ====================================================================== */ 
 	-->	  

 		<style>
 			#popup-product_with_discount_added hr{
 				margin: 10px 0 !important;
 			}
 		</style>

		<!-- popup -->

		<div id="popup-product_with_discount_added" style="display: none;">
			<button type="button" class="fa-window-close close">
	          	<span aria-hidden="true">&times;</span>
	        </button>

			<div class="alert alert-success text-center">
				Group discount has been successfully applied
				<br>
				and product has been added to cart 
			</div>

			<div class="row">
				<div class="col-xs-4">
					<img src="<?php echo $product->get_image_link(); ?>" style="width: 100%; border-radius: 4px;">
				</div>
				<div class="col-xs-8">
					<h4><?php echo $product->get_name(); ?></h4>
					<hr>
					<div style="color: #d33d0f;">
						<span style="text-decoration: line-through;">&#8377;<?php echo number_format($price-$discount, 2); ?></span>
						&#8377;<?php echo number_format($price-$keyword_discount, 2); ?>								
					</div>
					<br>
					<a href="../cart" class="btn btn-green btn-sm">My Shopping Cart</a>
				</div>
			</div>

		</div>	

		<script>
			$('#popup-product_with_discount_added').appendTo('body');
		</script>

	<!-- 
	/* ====================================================================== *
      		HOW IT WORKS MODAL
 	 * ====================================================================== */ 
 	-->	  

		<!-- Modal -->

		<div class="modal fade" id="how_it_works_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  	<div class="modal-dialog" role="document">
		    	<div class="modal-content">
		      		<div class="modal-body">
						
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				          <span aria-hidden="true">&times;</span>
				        </button>

		      			<h2 class="green_text" style="margin: 0 0 30px 0;">How it works?</h2>
						
						<p class="yellow_text">
						A friend has shared you a Unique Six Letter Discount Code ?
						</p>
						
						<p>
						Just enter the six letter code and avail the discount instantly
						</p>
						<p class="yellow_text">
						Want to join a stranger and buy in a pubilc deal ?
						</p>
						
						<p>
						Join any of the pubilc user deals, listed on the page by clicking on "Join The Deal". You will be paired with the selected user and will be able to get discount instantly.
						</p>
						<p class="yellow_text">
						Want to create a new deal for you and your friends ?						</p>
		      			<p>
		        			<span class="fa fa-check-circle"></span>&nbsp; Buy the product by clicking on <strong  class="yellow_text"> Buy in Groups</strong> button . Once the order is placed, you will receive a unique share id and a collection link to hundreds of products with same group discount.
		        		</p>
		        		<p>
							<span class="fa fa-check-circle"></span>&nbsp; Share the unique ID and Collection Link with your friend. Your friend needs to enter the unique 6 digit code in the specified area on the product page.
						</p>
						<p>
						Your friend can purchases from any of the hundreds of products given in the collection link. When your friend completes the purchase from the link given, both of your orders will be processed.
						</p>
						
						<br>
						<p>
							
							<br>
							<br>
							<iframe width="400" height="250" src="https://www.youtube.com/embed/pxbl4pAx_5g" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
						</p>
		      		</div>
		    	</div>
		  	</div>
		</div>

	<!-- 
	/* ====================================================================== *
      		FACEBOOK LOGIN
 	 * ====================================================================== */ 
 	-->	  
			
		<style>
			body{
				margin: 0 !important;
			}
			.modal {
			  text-align: center;
			}
			.love-button {
			    margin-top: -8px;
			}

			@media screen and (min-width: 768px) { 
			  .modal:before {
			    display: inline-block;
			    vertical-align: middle;
			    content: " ";
			    height: 100%;
			  }
			}

			.modal-dialog {
			  display: inline-block;
			  text-align: left;
			  vertical-align: middle;
			}
			.table-description-container{
				width: 100%;
				max-width: 100%;
				min-width: 100%;
				overflow-x: scroll;
			}
		</style>

		<!-- Modal -->

		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  	<div class="modal-dialog" role="document">
		    	<div class="modal-content">
		      		<div class="modal-body">
		        		<a href="<?php echo $loginUrl; ?>" class="btn btn-blue btn-block">
			      			<i class="fa fa-facebook-official"></i> &nbsp;Sign in with Facebook
			    		</a>
		      		</div>
		    	</div>
		  	</div>
		</div>

	<!-- 
	/* ====================================================================== *
      		LUCKY SIZES STUFF
 	 * ====================================================================== */ 
 	-->	

		<script>
			function check_stock(item){
				var color 				= $('.product_color.selected').data('color');
		    	var size 				= $('.product_size.selected').data('size');
		    	var output 				= true;	

		    	$('.combination_not_available').stop().hide();

				if($('.product_color')[0] == undefined){
		    		color = 'Empty';
		    	}
		    	if($('.product_size')[0] == undefined){
		    		size = 'Empty';
		    	}

		    	if(size == undefined || size == null || color == undefined || color == null){
		    		// something is missing
		    	}else{
		    		var stock = $('.stock');
		    		if(stock[0] != undefined){
		    			var stock_val = stock.filter('[data-color="'+color+'"][data-size="'+size+'"]').attr('data-stock');
		    			if(stock_val>0){
		    				//all good
		    			}else{
		    				$('.combination_not_available').fadeIn(400);
		    				output = false;
		    			}
		    		}
		    	}			

		    	return output;
			}
		</script>

	<!-- 
	/* ====================================================================== *
      		INSERT CODE
 	 * ====================================================================== */ 
 	-->	  		

		<script>

			// GO TO THE NEXT INPUT

			$('.input_insert_code').on('keyup', function(e){
				var $this = $(this);

				$this.val( $this.val().toUpperCase() );

				if($this.val().length > 0){ // next
					$this.parent('.col-xs-2').next().find('.input_insert_code').focus();
				}

				insert_code_complete();
			});

			// WHEN DELETE GO TO PREVIOUS INPUT

			$('.input_insert_code').on('keydown', function(e){
				var $this = $(this);

				if($this.val().length == 0 && (e.keyCode==8 || e.keyCode==46)){
					$this.parent('.col-xs-2').prev().find('.input_insert_code').focus();	
				}
			});

			// ON COMPLETE 

			var keyword_code = '';
			function insert_code_complete(){
				var all_completed 	= true;
				var code 			= '';
				$('.input_insert_code').each(function(){
					if($(this).val() == ''){
						all_completed = false;
					}else{
						code += $(this).val();
					}
				});

				if(all_completed){
					keyword_code = code;

					$('.with_keyword_discount').trigger('click');
					$('.input_insert_code').val('');
					$('.input_insert_code').first().focus();
				}else{
					keyword_code = '';
				}
			}

			// PASTE CODE

			$('.input_insert_code').on("paste", function(e) {
			    var pastedData = e.originalEvent.clipboardData.getData('text');

			    if(pastedData.length == 6){
			    	var pastedData_values = pastedData.split("");

				    $('.input_insert_code').each(function(index){
				    	$(this).val(pastedData_values[index]);
				    });
			    }

			});

		</script>

	<!-- 
	/* ====================================================================== *
      		ADD TO CART
 	 * ====================================================================== */ 
 	-->	  

		<script>

		    $('body').on('click', '.product_size', function(){
		    	var $this = $(this);

		    	$this.siblings('.product_size').removeClass('selected');
		    	$this.addClass('selected');

		    	check_stock($this);
		    });

		    $('body').on('click', '.product_color', function(){
		    	var $this = $(this);

		    	$this.siblings('.product_color').removeClass('selected');
		    	$this.addClass('selected');

		    	check_stock($this);
		    });

		    function something_missing(){
		    	var color 				= $('.product_color.selected').data('color');
		    	var size 				= $('.product_size.selected').data('size');
		    	var something_missing 	= false;

		    	if($('.product_color')[0] != undefined){
		    		if(color == undefined || color == null){
		    			something_missing = true;
		    		}
		    	}
		    	if($('.product_size')[0] != undefined){
		    		if(size == undefined || size == null){
		    			something_missing = true;
		    		}
		    	}

		    	return something_missing;
		    }

		    $('body').on('click', '.add_to_cart', function(e){
		    	e.preventDefault();
		    	var $this 				= $(this);
		    	var color 				= $('.product_color.selected').data('color');
		    	var size 				= $('.product_size.selected').data('size');
		    	var qty 				= 1;
		    	var idProductPrestashop = '<?php echo $current_id_product_prestashop; ?>';
		    	var idProduct 			= '<?php echo $current_id_product; ?>';

		    	if(!check_stock($this)){
		    		return;
		    	}

		    	if($this.hasClass('adding')){
		    		return;
		    	}

		    	$this.siblings('.fa-check').remove();

		    	if( something_missing() ){
		    		alert('Please make sure to select the color and / or size')
		    	}else{
		    		//adding to cart...
		    		$this.addClass('adding');

		    		if(size == undefined || size == null){
		    			size = '';
		    		}
		    		if(color == undefined || color == null){
		    			color = '';
		    		}

		    		if($this.hasClass('original_price') || $this.hasClass('with_keyword_discount')){
	    				$this.find('.price_description').html("<i class='fa fa-circle-o-notch fa-spin'></i> <br>loading...")
	    			}else{
	    				$this.html("<i class='fa fa-circle-o-notch fa-spin'></i> &nbsp;loading...");
	    			}

	    			var current_keyword_code = keyword_code;

		    		$.get('add_to_cart.php?color='+encodeURIComponent(color)+'&size='+size+'&qty='+qty+'&id_product_prestashop='+idProductPrestashop+'&id_product='+idProduct+'&espresso_product=<?php echo $id_espresso_product==''?'no':'yes'; ?>&with_keyword_discount='+($this.hasClass('with_keyword_discount')?'yes':'no')+'&keyword_code='+current_keyword_code, function(r){
		    		
		    			if($this.hasClass('original_price')){
		    				$this.find('.price_description').html('Buy At<br>Original Price')
		    			}else if($this.hasClass('with_keyword_discount')){
		    				$this.find('.price_description').html('Buy In<br>Groups')
		    			}else{
		    				$this.html("Add to Cart");	
		    			}	
		    		
		    			$this.removeClass('adding');
		    			$('.enter_discount_code_popup_msg').html('Enter the discount code to get the discount')

		    			if($.trim(r) == "INVALID_CODE"){
		    				alert("The code you entered is not valid!");
		    				keyword_code = '';
		    			}else if(current_keyword_code!=''){

							$('#popup-product_with_discount_added').popup();
							$('#popup-enter_keyword_code').popup('close_withouth_bg');

		    				keyword_code = '';
		    			}

		    			if($.trim(r) == "ERROR"){
		    				alert("Oops! something went wrong, refresh the page and try again.");
		    			}

		    			if($.trim(r) != "ERROR" && $.trim(r) != "INVALID_CODE"){
		    				$('.product_in_cart').hide().slideDown();
		    			}
		    		});
		    		
		    	}
		    });

		</script>

	<!-- 
	/* ====================================================================== *
      		ADD TO CART WITHOUT LOGIN
 	 * ====================================================================== */ 
 	-->	  

		<script>

			$('body').on('click', '.add_to_cart_without_login', function(e){
		    	e.preventDefault();
		    	var $this 				= $(this);
		    	var color 				= $('.product_color.selected').data('color');
		    	var size 				= $('.product_size.selected').data('size');
		    	var qty 				= 1;
		    	var idProductPrestashop = '<?php echo $current_id_product_prestashop; ?>';
		    	var idProduct 			= '<?php echo $current_id_product; ?>';

		    	if(!check_stock($this)){
		    		return;
		    	}

		    	if($this.hasClass('adding')){
		    		return;
		    	}

		    	$this.siblings('.fa-check').remove();

		    	if( something_missing() ){
		    		alert('Please make sure to select the color and / or size')
		    	}else{
		    		//adding to cart...
		    		$this.addClass('adding');

		    		if(size == undefined || size == null){
		    			size = '';
		    		}
		    		if(color == undefined || color == null){
		    			color = '';
		    		}

		    		if($this.hasClass('original_price') || $this.hasClass('with_keyword_discount')){
	    				$this.find('.price_description').html("<i class='fa fa-circle-o-notch fa-spin'></i> <br>loading...")
	    			}else{
	    				$this.html("<i class='fa fa-circle-o-notch fa-spin'></i> &nbsp;loading...");
	    			}
		    		
		    		$.get('add_to_cart_without_login.php?color='+encodeURIComponent(color)+'&size='+size+'&qty='+qty+'&id_product_prestashop='+idProductPrestashop+'&id_product='+idProduct+'&espresso_product=<?php echo $id_espresso_product==''?'no':'yes'; ?>&with_keyword_discount='+($this.hasClass('with_keyword_discount')?'yes':'no')+'&keyword_code='+keyword_code, function(r){		 

		    			if(keyword_code!=''){
							keyword_code = '';
						}   			
		    			
		    			if($this.hasClass('original_price')){
		    				$this.find('.price_description').html('Buy At<br>Original Price')
		    			}else if($this.hasClass('with_keyword_discount')){
		    				$this.find('.price_description').html('Buy In<br>Groups')
		    			}else{
		    				$this.html("Add to Cart");	
		    			}
		    			
		    			$this.removeClass('adding');

		    			if($.trim(r) == "INVALID_CODE"){
		    				alert("The code you entered is not valid!");
		    			}

		    			if($.trim(r) == "success"){
		    				$('#myModal').modal('show').appendTo('body');
		    			}else{
		    				alert("Oops! something went wrong, refresh the page and try again.");
		    			}
		    		});
		    		
		    	}
		    });

		</script>	

	<!-- 
	/* ====================================================================== *
      		SAVE SELECTED PRODUCTS
 	 * ====================================================================== */ 
 	-->	  

		<script>

	 	 	$('body').on('click', '.save_id_product', function(){
	 	 		var $this = $(this);

	 	 		if($this.hasClass('saving'))return;

	 	 		$this.html('Saving...').addClass('saving');

	 	 		// SAVE VIA AJAX

	 	 		$('.msg_loading').show();

				$.post('save_products.php', 
					{ 
						id_product : $this.attr('data-id-product')
					}, 
					function(r){

						if($.trim(r) == 'delete'){
			 	 			$this.html($this.attr('data-notsaved'));	
			 	 		}else if($.trim(r) == 'insert'){
							$this.html($this.attr('data-saved'));	
			 	 		}

			 	 		$this.removeClass('saving');

					});
	 	 	});

		</script>
	
	</div></div>

	<!-- Magnific Popup -->
	<script src="../includes/plugins/Media Boxes/plugin/components/Magnific Popup/jquery.magnific-popup.min.js"></script> <!-- only if you use Magnific Popup -->
	<script>
		$('.product_container').magnificPopup({
            delegate: '.open_magnificpopup',
            type: 'image',
            removalDelay : 200,
            closeOnContentClick : false,
            mainClass : 'my-mfp-slide-bottom',
            fixedContentPos: false,
            gallery:{
                enabled:true
            },
            closeMarkup : '<button title="%title%" class="mfp-close"></button>',
            titleSrc: 'title',
            callbacks : {
                beforeOpen: function() {
                    this.container.data('scrollTop', parseInt($(window).scrollTop()));
                },
                open: function(){
                    $('html, body').scrollTop( this.container.data('scrollTop') );
                },
            },
        });
	</script>

	<?php require_once("../footer.php"); ?>
	
	</div>
</body>

</html>

