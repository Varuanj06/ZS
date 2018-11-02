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
			
		

	?>

  	<?php require_once("../head.php"); ?>

  	<!-- Include select2 plugin -->
  	<link rel="stylesheet" href="../includes/plugins/select2/css/select2.css">
  	<script src="../includes/plugins/select2/js/select2.min.js"></script>

	<!-- Include typeahead plugin -->
	<script src="../includes/plugins/typeahead/typeahead.jquery.min.js"></script>
  	
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

</head>
<body>

	<?php require_once("../menu.php"); ?>
	<?php require_once("../sidebar.php"); ?>
	<div id="menu-page-wraper">

	<div class="page-wrap"><div class="page-wrap-inner">

		<?php require_once("../message.php"); ?>

		<script>$('.nav-right a[href="../feed"]').addClass('selected');</script>

		<div class="tabs-container">
			
			<!-- Tab panes -->
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="search-results">

					<a href='http://url.com/38-jackets-and-blazers'>
						<img class="banner" src='http://beyondroads.in/wp-content/uploads/2015/06/pench-banner-img.jpg'alt="" />
					</a>

					<div id="grid">

					<?php 

						$last_chars 			= substr($product->get_image_link(), strrpos($product->get_image_link(), '/') + 1);
						$id_image 				= str_replace(".jpg", "", $last_chars); // get the 75 from this: http://url.com/9-75/75.jpg
						$id_product_prestashop 	= $product->get_id_product_prestashop();
						$img_url 				= "http://miracas.miracaslifestyle.netdna-cdn.com/$id_product_prestashop-$id_image-thickbox/$id_image.jpg";

						$price 					= get_the_price($id_product_prestashop);
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
										========= ADD TO CART =========
				                	-->

				                	<div style="position:relative;">
									
									<?php if($active_session === true){ ?>
										<?php if($disable_things !== true){ ?>
			                				<select class="qty form-control" style="display:none;">
			                					<option value="1">1</option>
			                				</select>
			                				<a class="add_to_cart btn btn-sm btn-green" href="#">Add to Cart</a>
			                				<a class="btn btn-sm btn-green" href="../cart"><span class="fa fa-shopping-cart"></span></a>
			                				<div class="combination_not_available" style="color:#d44950;position:absolute;bottom:-33px">
		                						The selected product combination is not available. Please select a different color or size.
		                					</div>
			                			<?php } ?>							
		                			<?php }else{ ?>	
				                		<a class="add_to_cart_without_login btn btn-sm btn-green" href="#">Add to Cart</a>
				                		<div class="combination_not_available" style="color:#d44950;position:absolute;bottom:-33px">
	                						The selected product combination is not available. Please select a different color or size.
	                					</div>
			                		<?php } ?>

			                		</div>
			                	
				                	<hr>

				                	<!--
										========= VIEW COUNT AND SAVE FOR LATTER BUTTON =========
				                	-->
			                		
			                		<div class="product_bottom">

			                			<div class="row">
			                				<div class="col-xs-6">
			                					<?php if($active_session === true){ ?>
					                				<?php 
					                					$fb_user_product_save->set_id_fb_user($user['id']);
					                					$fb_user_product_save->set_id_product($product->get_id_product());
					                				?>
						                			<a  style="font-size:9px;background:#c09853;" class="btn btn-primary btn-green btn-sm save_id_product" data-id-product="<?php echo $product->get_id_product(); ?>">
								                		<?php if ($fb_user_product_save->exists()){ echo "<i class='fa fa-check'></i>&nbsp; SAVED"; }else{ echo "<i class='fa fa-plus'></i>&nbsp; SAVE FOR LATER"; } ?>
								                	</a>
								                <?php } ?>
			                				</div>	
			                				<div class="col-xs-6 text-right">
			                					<span class="count">
						                			<i class="fa fa-heart heart-selected"></i>
						                			<?php echo $fb_user_product_view->get_id_product_view_count($product->get_id_product()); ?>
						                		</span>
			                				</div>
			                			</div>

				                	</div>
				                	
				                </div>
				            </div>
				        </div>

				        <div class="media-box" data-columns="2">
				        	<div class="media-box-content">
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
					
					<style>
						body{
							/*min-height: 1000px;*/
						}
					</style>
					<script>
						$('html, body').animate({
					        //scrollTop: $(".suggestions-feed").offset().top
					    }, 500);
					</script>

				</div>
			</div>

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

	 	 	$('body').on('click', '.save_id_product', function(){
	 	 		var $this = $(this);

	 	 		if($this.hasClass('saving'))return;

	 	 		$this.html('SAVING...').addClass('saving');

	 	 		// SAVE VIA AJAX

	 	 		$('.msg_loading').show();

				$.post('save_products.php', 
					{ 
						id_product 						: $this.attr('data-id-product')
					}, 
					function(r){

						if($.trim(r) == 'delete'){
			 	 			$this.html('<i class="fa fa-plus"></i>&nbsp; SAVE FOR LATER');	
			 	 		}else if($.trim(r) == 'insert'){
							$this.html('<i class="fa fa-check"></i>&nbsp; SAVED');	
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

