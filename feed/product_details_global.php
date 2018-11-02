<?php $force_session = true; ?>
<?php require_once("../fb_validator.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/attribute.php"); ?>
<?php require_once("../classes/product_lang.php"); ?>
<?php require_once("../classes/product_stock.php"); ?>
<?php require_once("../classes/functions.php"); ?>
<?php require_once("../classes/image.php"); ?>
<?php require_once("../classes/fb_user_product_view.php"); ?>
<?php require_once("../classes/fb_user_product_save.php"); ?>
<?php require_once("../classes/keyword_globalinfo.php"); ?>
<?php require_once("../classes/product_globalinfo.php"); ?>
<?php require_once("../classes/order_detail.php"); ?>
<?php require_once("../classes/order.php"); ?>

<!doctype html>
<html lang="en">
<head>

	<?php 

	/* ====================================================================== *
        	CLASSES
 	 * ====================================================================== */	

		$attribute 				= new attribute();
		$product_lang 			= new product_lang();
		$product 				= new product();
		$product_stock 			= new product_stock;
		$image 					= new image();
		$fb_user_product_view 	= new fb_user_product_view();
		$fb_user_product_save 	= new fb_user_product_save();
		$order_detail 			= new order_detail();
		$order 					= new order();

	/* ====================================================================== *
        	MAP THE PRODUCT
 	 * ====================================================================== */		

		$product->map($_GET['id_product']);
		$product_lang->map($product->get_id_product_prestashop());

		echo "<title>".$product->get_name()."</title>";

	/* ====================================================================== *
        	SAVE THE VIEWS OF THIS PRODUCT PER USER
 	 * ====================================================================== */	

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

	/* ====================================================================== *
			CURRENT ID_ORDER
	 * ====================================================================== */	

		$id_fb_user 		= isset($user['id'])?$user['id']:'';
		$current_id_order   = "";

		if($id_fb_user != '' && $order->get_id_order_by_fb_user($id_fb_user)){
			$current_id_order = $order->get_id_order_by_fb_user($id_fb_user);
		}  
		
	?>

  	<?php require_once("../head.php"); ?>
  	
  	<!-- Media Boxes CSS files -->
  	<link rel="stylesheet" href="../includes/plugins/Media Boxes/plugin/components/Font Awesome/css/font-awesome.min.css"> <!-- only if you use Font Awesome -->
	<link rel="stylesheet" href="../includes/plugins/Media Boxes/plugin/components/Magnific Popup/magnific-popup.css"> <!-- only if you use Magnific Popup -->
	<link rel="stylesheet" type="text/css" href="../includes/plugins/Media Boxes/plugin/css/mediaBoxes.css">

	<style>
		.trigger-lightbox{
			cursor: pointer;
		}
		.media-boxes-no-more-entries{
			display: none;
		}
		.media-box-little-grid{
			width: 100%;
			overflow: hidden;
		}
		.media-box-little-grid>div{
			width: 33.33% !important;
			float: left !important;
			background-color: black;
			background-image: url('../includes/plugins/Media Boxes/plugin/css/icons/loading-image.gif');
			background-position: center center;
    		background-repeat: no-repeat;
		}
		.placeholder>div{
			width: 100%;
			height: 100%;
			background-size:100% auto;
		}
		.tabs-container {
    		max-width: 970;
    		margin: auto;
		}
		.banner{
			max-width: 100%;
			width: 100%;
			margin-bottom: 10px;
			margin-top: -10px;
		}
	</style>

	<!-- countdown plugin -->
	<link rel="stylesheet" href="../includes/plugins/countdown/jquery.countdown.css">
  	<script src="../includes/plugins/countdown/jquery.plugin.min.js"></script>
	<script src="../includes/plugins/countdown/jquery.countdown.min.js"></script>

</head>
<body>

	<?php require_once("../menu_global.php"); ?>
	<?php require_once("../sidebar_global.php"); ?>
	<div id="menu-page-wraper">

	<div class="page-wrap"><div class="page-wrap-inner">

		<?php require_once("../message.php"); ?>

		<script>$('.nav-right a[href="../feed"]').addClass('selected');</script>

		<div class="tabs-container" style="max-width:800px;margin:0 auto !important;">
			
			<!--
			<a href='http://miracas.com/ZS/static/Refund.php'>
				<img class="banner" src='http://miracas.com/ZS/assets/advertising_custom.jpg'alt="" />
			</a>
			-->
			
			<?php if($product->get_global() == 'yes'){ ?>	
				<?php 
					$keyword_globalinfo = new keyword_globalinfo();
					$keyword_globalinfo->map(substr($product->get_keywords(), 1, -1));
				?>						
				<div class="countdown_keyword_global">
					<div id="countdown_k"></div>
					<script>
						var tillDate = new Date('<?php echo $keyword_globalinfo->get_expiry_date(); ?>');
						tillDate.setHours(0,0,0,0);
						$('#countdown_k').countdown({until: tillDate, format:'DHMS', padZeroes: true, layout: ' <span class="cd_time">{dn} <span class="cd_time_txt">{dl}</span></span> <span class="cd_time">{hn}:{mn}:{snn} <span class="cd_time_txt">{hl}</span></span>' });
					</script>
				</div>
			<?php } ?>


			<div id="grid">

			<?php 

				$last_chars 			= substr($product->get_image_link(), strrpos($product->get_image_link(), '/') + 1);
				$id_image 				= str_replace(".jpg", "", $last_chars); // get the 75 from this: http://url.com/9-75/75.jpg
				$id_product_prestashop 	= $product->get_id_product_prestashop();
				$img_url 				= "http://miracas.miracaslifestyle.netdna-cdn.com/$id_product_prestashop-$id_image-thickbox/$id_image.jpg";

				$price 					= get_the_price($product->get_id_product());
				$discount 				= get_the_discount($product->get_id_product(), $price);
				$colors 				= $attribute->get_colors_of_product($id_product_prestashop);
				$sizes 					= $attribute->get_sizes_of_product($id_product_prestashop);
				$list_stock 			= $product_stock->get_list_stock($product->get_id_product(), '');

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
		        <div class="media-box">
		            <div class="media-box-content">
		                <div class="media-box-little-grid">
			            	<?php
			            		$id_images 	= $image->get_images($id_product_prestashop);
								foreach ($id_images as $id_image) {
									$image_url = "http://miracas.miracaslifestyle.netdna-cdn.com/$id_product_prestashop-$id_image-home/$id_image.jpg";
									$popup_url = "http://miracas.miracaslifestyle.netdna-cdn.com/$id_product_prestashop-$id_image/$id_image.jpg"; 

									//echo "<img class='mb-open-popup mfp-image' data-popuptrigger='yes' data-mfp-src='$popup_url' src='$image_url' />";
									echo "<div class='placeholder mb-open-popup mfp-image' data-popuptrigger='yes' data-mfp-src='$popup_url' data-src='$popup_url'><div style='background-image:url($image_url)'></div></div>";
								}
			            	?>
			            </div>
		            </div>
		        </div>
				
				<div class="media-box">
		            <div class="media-box-content" data-idproductprestashop="<?php echo $id_product_prestashop; ?>" data-idproduct="<?php echo $product->get_id_product(); ?>">
		                <div class="media-box-text" style="margin: 0 !important;">

		                	<!--
								========= THE NAME =========
		                	-->

		                	<p>
		                		<?php echo $product->get_name(); ?>
		                	</p>

		                	<!--
								========= THE PRICE =========
		                	-->

		                	<p>
		                		<span class="media-box-price">
		                			<?php if($discount>0){ ?>
		                			<span style="font-size: 12px;color: rgba(145,145,145,.5);text-decoration: line-through;">₹<?php echo number_format($price, 2); ?></span>
		                			<?php } ?>

		                			₹<?php echo number_format($price-$discount, 2); ?>
		                		</span>

		                		<span class="count pull-right">
		                			<i class="fa fa-heart heart-selected"></i>
		                			<?php echo $fb_user_product_view->get_id_product_view_count($product->get_id_product()); ?>
		                		</span>
		                	</p>

		                	<!--
								========= THE STOCK =========
		                	-->

		                	<?php foreach ($list_stock as $current_stock){ ?>
								<div class="stock" data-color="<?php echo $current_stock->get_color(); ?>" data-size="<?php echo $current_stock->get_size(); ?>" data-stock="<?php echo $current_stock->get_stock(); ?>"></div>
		                	<?php } ?>
							
							<!--
								========= THE COLORS =========
		                	-->

		                	<?php if(count($colors)>0){ ?>
		                		<br>
		                		<div class="row">
		                			<div class="col-sm-2 padding-top">
		                				<strong>Colors:</strong>
		                			</div>
		                			<div class="col-sm-9">
		                				<?php foreach ($colors as $color) {?>
		                					<?php if(count($list_stock) && !in_array($color, $colors_with_stock)){continue;} ?>
				                			<span class="media-box-color" data-color="<?php echo $color; ?>"><span style="background:<?php echo $color; ?>;"></span></span>
				                		<?php } ?>
		                			</div>
		                		</div>
	                		<?php } ?>

							<!--
								========= THE SIZES =========
		                	-->

							<?php if(count($sizes)>0){ ?>
								<br>
		                		<div class="row">
		                			<div class="col-sm-2 padding-top">
		                				<strong>Sizes:</strong>
		                			</div>
		                			<div class="col-sm-9">
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
				                			<span class="media-box-size" data-size="<?php echo $size; ?>"><span><?php echo $public_size; ?></span></span>
				                		<?php 
				                			} 
				                		?>
		                			</div>
	                			</div>
	                		<?php } ?>

		                	<!--
								========= PRODUCT BOTTOM =========
		                	-->

		                	<div class="combination_not_available" style="color:#d44950;position: absolute; margin-top: -10px; font-size: 10px;">
        						The selected product combination is not available.
        					</div>

		                	<br>
	                		
	                		<div class="product_bottom">

	                			<div class="row">
	                				<div class="col-xs-6 text-center">
	                					<?php if($active_session === true){ ?>
			                				<?php 
			                					$fb_user_product_save->set_id_fb_user($user['id']);
			                					$fb_user_product_save->set_id_product($product->get_id_product());
			                				?>
				                			<a href="#" class="save_id_product" data-id-product="<?php echo $product->get_id_product(); ?>" data-saved="<i class='fa fa-check'></i>&nbsp; In Whishlist" data-notsaved="<i class='fa fa-bookmark'></i>&nbsp; Whishlist"> 
						                		<?php if ($fb_user_product_save->exists()){ echo "<i class='fa fa-check'></i>&nbsp; In Whishlist"; }else{ echo "<i class='fa fa-bookmark'></i>&nbsp; Whishlist"; } ?>
						                	</a>
						                <?php } ?>
	                				</div>	
	                				<div class="col-xs-6 text-center">
	                					<?php if($active_session === true){ ?>
			                				
			                				
											<?php 
												$product_in_cart = $order_detail->exists_product_prestashop_in_order($current_id_order, $id_product_prestashop); // if product is not added yet to the cart
											?>
											<?php if(!$product_in_cart){ ?>
												<div class="add_controls">
													<a class="add_to_cart" href="#"><span class="fa fa-shopping-cart"></span>&nbsp; Add to Cart</a>
				                				</div>
											<?php } ?>

											<div class="added_controls" <?php if($product_in_cart){ echo 'style="display:block;"'; } ?>>
			                					<a href="../cart" style="line-height: 14px;padding: 6px 0px 2px;">
			                						<span class="fa fa-search"></span>&nbsp; View Your Cart
			                						<br>
			                						<span style="font-size: 9px;color;color:#3c763d;">Succesfully added to cart</span>
			                					</a>
			                				</div>

			                			<?php }else{ ?>	
					                		<a class="add_to_cart_without_login" href="#"><span class="fa fa-shopping-cart"></span>&nbsp; Add to Cart</a>
				                		<?php } ?>
	                				</div>
	                			</div>

		                	</div>
		                	
		                </div>
		            </div>
		        </div>

		       

		        <?php if($product->get_global() == 'yes'){ ?>

		        	<div class="media-box">
		        		<div class="media-box-content media-box-text" style="margin:0 !important;">
							<?php 
								$keyword_globalinfo = new keyword_globalinfo();
								$keyword_globalinfo->map(substr($product->get_keywords(), 1, -1));

								$product_globalinfo = new product_globalinfo();
								$product_globalinfo->map($product->get_id_product());

								$custom_duty_percentage 		= ( (float) $keyword_globalinfo->get_custom_duty_percentage() ) / 100;
								$international_shipping_cost 	= (float) $keyword_globalinfo->get_international_shipping_cost();
								$domestic_shipping_cost  		= (float) $keyword_globalinfo->get_domestic_shipping_cost();
								$vendor_commission_percentage 	= ( (float) $keyword_globalinfo->get_vendor_commission_percentage() ) / 100;
								$vendor_price 					= (float) $product_globalinfo->get_vendor_price();

								$original_price  				= $vendor_price;
								$original_price 				= $original_price * 10;
								$original_price 				= $original_price + ( round($original_price*$vendor_commission_percentage, 2) );
							?>
							
							<h5 style="margin: 0 0 20px 0;">Product price breakup</h5>

							<div class="product_breakup">
			                	<div class="row">
									<div class="col-xs-8"><i class="fa fa-tag"></i> Original price:</div>
									<div class="col-xs-4">₹<?php echo number_format($original_price, 2); ?></div>
								</div>
								<div class="row">
			                		<div class="col-xs-8"><i class="fa fa-hand-paper-o"></i> Custom duty:</div>
									<div class="col-xs-4">₹<?php echo number_format(round($original_price*$custom_duty_percentage, 2), 2); ?></div>
								</div>
								<div class="row">
									<div class="col-xs-8"><i class="fa fa-plane"></i> International logistics:</div>
									<div class="col-xs-4">₹<?php echo number_format($international_shipping_cost, 2); ?></div>
								</div>
								<div class="row">
									<div class="col-xs-8"><i class="fa fa-truck"></i> Domestic logistics:</div>
									<div class="col-xs-4">₹<?php echo number_format($domestic_shipping_cost, 2); ?></div>
			                	</div>
			                </div>
			            </div>
	                </div>

                <?php } ?>
				
				 <div class="media-box">
		        	<div class="media-box-content media-box-text" style="margin:0 !important;">          		
						
						<h6 style="margin:0;" > 
						<img src="http://miracas.com/ZS/assets/trustPay_logo_in.gif" width="50%"/>
						<br><br>15 Days Return Policy. No Questions Asked.
						<br><br>Reverse Pickup available in over 5000+ pincodes.
						<br><br>100% Payment protection for your order. You can invoke this if you have paid for the item but didn't receive it within 30 days.
						</h6>	
	                	<hr>
                	</div>
		        </div>

		        <div class="media-box" data-columns="2">
		        	<div class="media-box-content">
						<div class="alert alert-info text-center">
							<h5 style="margin:0;" > 
													 Sizes mentioned here are Indian Sizes so that you can choose easily. For eg: if you are used to wearing 'M' size, please choose 'M' </h5>

						</div>
			        	<div class="table-description-container">
							<?php echo $product_lang->get_description(); ?>
						</div>
					</div>
		        </div>
			</div>
			
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
				      <i class="fa fa-facebook-official"></i> &nbsp;
				      Sign in with Facebook
				    </a>
			      </div>
			    </div>
			  </div>
			</div>

			<br>

		</div> <!-- End tabs-container -->

	<!-- 
	/* ====================================================================== *
      		MEDIA BOXES
 	 * ====================================================================== */ 
 	-->	  

		<script src="../includes/plugins/Media Boxes/plugin/components/Isotope/jquery.isotope.min.js"></script>
		<script src="../includes/plugins/Media Boxes/plugin/components/imagesLoaded/jquery.imagesLoaded.min.js"></script>
		<script src="../includes/plugins/Media Boxes/plugin/components/Transit/jquery.transit.min.js"></script>
		<script src="../includes/plugins/Media Boxes/plugin/components/jQuery Easing/jquery.easing.js"></script>
		<script src="../includes/plugins/Media Boxes/plugin/components/Waypoints/waypoints.min.js"></script>
		<script src="../includes/plugins/Media Boxes/plugin/components/Modernizr/modernizr.custom.min.js"></script>
		<script src="../includes/plugins/Media Boxes/plugin/components/Magnific Popup/jquery.magnific-popup.min.js"></script> <!-- only if you use Magnific Popup -->
		<script src="../includes/plugins/Media Boxes/plugin/js/jquery.mediaBoxes.dropdown.js"></script>
		<script src="../includes/plugins/Media Boxes/plugin/js/jquery.mediaBoxes.js"></script>

		<script>

		    var $grid = $('#grid').mediaBoxes({
					    	columns: 2,
					    	horizontalSpaceBetweenBoxes: 15,
				        	verticalSpaceBetweenBoxes: 3,
				        	boxesToLoadStart: 8,
					    	boxesToLoad: 4,
					    	deepLinkingOnPopup: false,
        					deepLinkingOnFilter: false,
					    	resolutions: [
					            {
					                maxWidth: 960,
					                columnWidth: 'auto',
					                columns: 2,
					            },
					            {
					                maxWidth: 600,
					                columnWidth: 'auto',
					                columns: 1,
					            },
					        ],
					    });

		    function resize_little_thumbs(){
		    	var items 	= $('.media-box-little-grid>div');
		    	var width 	= items.outerWidth(true);
		    	items.height(width);
		    	$grid.isotopeMB('layout');
		    }

		    setTimeout(function(){
		    	// this is also executed after the Media Boxes finish loading, because sometimes it doesn't load on time so it doesn't resize correctly
		    	resize_little_thumbs();
		    }, 300);

		    $(window).resize(function(){
		    	setTimeout(function(){
		    		resize_little_thumbs();
		    	}, 300);
		    });

		</script>

	<!-- 
	/* ====================================================================== *
      		LUCKY SIZES STUFF
 	 * ====================================================================== */ 
 	-->	

		<script>
			function check_stock(item){
				var content 			= item.parents('.media-box-content');
		    	var color 				= content.find('.color-selected').data('color');
		    	var size 				= content.find('.size-selected').data('size');
		    	var output 				= true;	

		    	content.find('.combination_not_available').stop().hide();

				if(content.find('.media-box-color')[0] == undefined){
		    		color = 'Empty';
		    	}
		    	if(content.find('.media-box-size')[0] == undefined){
		    		size = 'Empty';
		    	}

		    	if(size == undefined || size == null || color == undefined || color == null){
		    		// something is missing
		    	}else{
		    		var stock = content.find('.stock');
		    		if(stock[0] != undefined){
		    			var stock_val = stock.filter('[data-color="'+color+'"][data-size="'+size+'"]').attr('data-stock');
		    			if(stock_val>0){
		    				//all good
		    			}else{
		    				content.find('.combination_not_available').fadeIn(400);
		    				output = false;
		    			}
		    		}
		    	}			

		    	return output;
			}
		</script>

	<!-- 
	/* ====================================================================== *
      		ADD TO CART
 	 * ====================================================================== */ 
 	-->	  

		<script>

		    $('body').on('click', '.media-box-size', function(){
		    	var $this = $(this);

		    	$this.siblings('.media-box-size').removeClass('size-selected');
		    	$this.addClass('size-selected');

		    	check_stock($this);
		    });

		    $('body').on('click', '.media-box-color', function(){
		    	var $this = $(this);

		    	$this.siblings('.media-box-color').removeClass('color-selected');
		    	$this.addClass('color-selected');

		    	check_stock($this);
		    });

		    $('body').on('click', '.add_to_cart', function(e){
		    	e.preventDefault();
		    	var $this 				= $(this);
		    	var content 			= $this.parents('.media-box-content');
		    	var color 				= content.find('.color-selected').data('color');
		    	var size 				= content.find('.size-selected').data('size');
		    	var qty 				= 1;
		    	var idProductPrestashop = content.data('idproductprestashop');
		    	var idProduct 			= content.data('idproduct');
		    	var something_missing 	= false;

		    	if(!check_stock($this)){
		    		return;
		    	}

		    	if($this.hasClass('adding')){
		    		return;
		    	}

		    	$this.siblings('.fa-check').remove();

		    	if(content.find('.media-box-color')[0] != undefined){
		    		if(color == undefined || color == null){
		    			something_missing = true;
		    		}
		    	}
		    	if(content.find('.media-box-size')[0] != undefined){
		    		if(size == undefined || size == null){
		    			something_missing = true;
		    		}
		    	}

		    	if( something_missing ){
		    		alert('make sure to select all the parameters')
		    	}else{
		    		//adding to cart...
		    		$this.addClass('adding');

		    		if(size == undefined || size == null){
		    			size = '';
		    		}
		    		if(color == undefined || color == null){
		    			color = '';
		    		}

		    		$this.html("<i class='fa fa-circle-o-notch fa-spin'></i> &nbsp;adding...");
		    		$.get('add_to_cart.php?color='+encodeURIComponent(color)+'&size='+size+'&qty='+qty+'&id_product_prestashop='+idProductPrestashop+'&id_product='+idProduct, function(r){
		    			var check_mark = $('<i style="margin-left:7px;font-size: 18px;" class="fa fa-check"></i>').insertAfter($this);

		    			setTimeout(function() {
						    check_mark.fadeOut('fast');
						}, 4000); // <-- time in milliseconds
		    			
		    			$this.html("Add to Cart");
		    			$this.removeClass('adding');

		    			if($.trim(r) == "ERROR"){
		    				alert("Oops! something went wrong, refresh the page and try again.");
		    			}else{
		    				$this.parents('.add_controls').hide().siblings('.added_controls').show();
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
		    	var content 			= $this.parents('.media-box-content');
		    	var color 				= content.find('.color-selected').data('color');
		    	var size 				= content.find('.size-selected').data('size');
		    	var qty 				= 1;
		    	var idProductPrestashop = content.data('idproductprestashop');
		    	var idProduct 			= content.data('idproduct');
		    	var something_missing 	= false;

		    	if(!check_stock($this)){
		    		return;
		    	}

		    	if($this.hasClass('adding')){
		    		return;
		    	}

		    	$this.siblings('.fa-check').remove();

		    	if(content.find('.media-box-color')[0] != undefined){
		    		if(color == undefined || color == null){
		    			something_missing = true;
		    		}
		    	}
		    	if(content.find('.media-box-size')[0] != undefined){
		    		if(size == undefined || size == null){
		    			something_missing = true;
		    		}
		    	}

		    	if( something_missing ){
		    		alert('make sure to select all the parameters')
		    	}else{
		    		//adding to cart...
		    		$this.addClass('adding');

		    		if(size == undefined || size == null){
		    			size = '';
		    		}
		    		if(color == undefined || color == null){
		    			color = '';
		    		}

		    		$this.html("<i class='fa fa-circle-o-notch fa-spin'></i> &nbsp;loading...");
		    		$.get('add_to_cart_without_login.php?color='+encodeURIComponent(color)+'&size='+size+'&qty='+qty+'&id_product_prestashop='+idProductPrestashop+'&id_product='+idProduct, function(r){		    			
		    			$this.html("Add to Cart");
		    			$this.removeClass('adding');

		    			if($.trim(r) == "success"){
		    				$('#myModal').modal('show').appendTo('body');
		    			}else{
		    				alert("Oops! something went wrong, refresh the page and try again.");
		    			}
		    		});
		    		
		    	}
		    });

		    $(document).on( 'scroll', function(){
			    Waypoint.refreshAll();
			});

		</script>	

	<!-- 
	/* ====================================================================== *
      		CUSTOMIZE MAGNIFIC POPUP
 	 * ====================================================================== */ 
 	-->	  

		<script>

		    function loading_mp($this){
		    	$this.removeClass('fa-plus');
		    	$this.addClass('fa-circle-o-notch');
		    	$this.addClass('fa-spin');
		    }

		    function stopLoading($this){
		    	$this.removeClass('fa-circle-o-notch');
		    	$this.removeClass('fa-spin');
		    	$this.addClass('fa-plus');
		    }

		    function startMagnificPopup(mediaBoxContainer){
				mediaBoxContainer.find('.mb-open-popup').first().trigger('click');
		    }

		    $('body').on('click', '.trigger-lightbox', function(){
		    	$(this).find('.open-lightbox').trigger('click');
		    });

		    $('body').on('click', '.open-lightbox', function(e){
		    	e.stopPropagation()
		    	var $this 				= $(this);

		    	loading_mp($this);

		    	var mediaBoxContainer 	= $this.parents('.media-box-container');
		    	var newItems 			= '<span class="mb-open-popup mfp-image" data-popuptrigger="yes" data-mfp-src="http://www.davidbo.dreamhosters.com/plugins/mediaBoxes/example/gallery/img-2.jpg" ></span>';
		    	var idProductPrestashop = $this.data('idproductprestashop');
		    	
		    	if(mediaBoxContainer.find('.mb-open-popup')[0] == undefined){
			    	$.get('get_popup_images.php?id_product_prestashop='+idProductPrestashop, function(newItems){
			    		mediaBoxContainer.append($($.trim(newItems)));
			    		startMagnificPopup(mediaBoxContainer);
			    		stopLoading($this);
			    	});
		    	}else{
		    		startMagnificPopup(mediaBoxContainer);
		    		stopLoading($this);
		    	}
		    });

		</script>

	<!-- 
	/* ====================================================================== *
      		SAVE SELECTED PRODUCTS
 	 * ====================================================================== */ 
 	-->	  

		<script>

	 	 	$('body').on('click', '.save_id_product', function(e){
	 	 		e.preventDefault();
	 	 		var $this = $(this);

	 	 		if($this.hasClass('saving'))return;

	 	 		$this.html('Saving...').addClass('saving');

	 	 		// SAVE VIA AJAX

	 	 		$('.msg_loading').show();

				$.post('save_products.php', 
					{ 
						id_product 						: $this.attr('data-id-product')
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

	<?php require_once("../footer.php"); ?>
	
	</div>
</body>

</html>

