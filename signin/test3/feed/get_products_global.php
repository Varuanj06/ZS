<?php $force_session = true; ?>
<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/product_stock.php"); ?>
<?php require_once("../classes/attribute.php"); ?>
<?php require_once("../classes/product_lang.php"); ?>
<?php require_once("../classes/love_count.php"); ?>
<?php require_once("../classes/functions.php"); ?>
<?php require_once("../classes/order.php"); ?>
<?php require_once("../classes/order_detail.php"); ?>
<?php require_once("../classes/pixel.php"); ?>
<?php require_once("../classes/fb_user_product_view.php"); ?>
<?php require_once("../classes/fb_user_product_save.php"); ?>
<?php require_once("../classes/keyword.php"); ?>
<?php require_once("../classes/keyword_globalinfo.php"); ?>
<?php require_once("../classes/product_globalinfo.php"); ?>

<?php 

/* ====================================================================== *
    	CLASSES
 * ====================================================================== */		

    $product 				= new product();
    $product_stock 			= new product_stock();
    $order 					= new order();
    $order_detail 			= new order_detail();
    $attribute 				= new attribute();
	$product_lang 			= new product_lang();
	$pixel 					= new pixel();
	$fb_user_product_view 	= new fb_user_product_view();
	$fb_user_product_save 	= new fb_user_product_save();
	$keyword 				= new keyword();
	$keyword_globalinfo 	= new keyword_globalinfo();
	$product_globalinfo 	= new product_globalinfo();

/* ====================================================================== *
    	USER DETAILS
 * ====================================================================== */	
	
    // FORCE THE GENDER
    
	if(isset($_GET['set_gender'])){
		$user['gender'] = $_GET['set_gender'];
	}	

	// FB DETAILS

	$id_fb_user 		= isset($user['id'])?$user['id']:'';
	$user_birthday 		= isset($user['birthday'])?$user['birthday']:'';
	$user_gender 		= isset($user['gender'])?$user['gender']:'';
	$user_name 			= isset($user['first_name'])?$user['first_name']:'';
	$user_last_name 	= isset($user['last_name'])?$user['last_name']:'';

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

	// EXTRA PARAM

	$extra_param = "";
	if($active_session !== true){ 
		$extra_param = "?set_gender=".str_replace("/", "", $_GET['set_gender']);
	}

/* ====================================================================== *
		CURRENT ID_ORDER
 * ====================================================================== */	

	$current_id_order   = "";

	if($id_fb_user != '' && $order->get_id_order_by_fb_user($id_fb_user)){
		$current_id_order = $order->get_id_order_by_fb_user($id_fb_user);
	}  

/* ====================================================================== *
    	GET PRODUCTS FROM KEYWORD
 * ====================================================================== */	

    $q 				= isset($_GET['q'])?$_GET['q']:'';	
	$products 		= array();
	$products_stock = array();

	$lucky_size 	= isset($_GET['size'])?$_GET['size']:'';
	if($lucky_size != ''){
		$q = "Lucky Size: $lucky_size";
	}

	$id_tag 		= isset($_GET['id_tag'])?$_GET['id_tag']:'';
	if($id_tag != ''){
		$q = "Tag Search";
	}

	$q_fb_user 		= isset($_GET['usr'])?$_GET['usr']:'';
	if($q_fb_user != ''){
		$q = "User Search";
	}

	if( $q != '' ){

		echo "<title>$q</title>";

		/* GET PRODUCTS */

		if($lucky_size != ''){ // only "lucky size products" with stock in the selected size
			$products = $product->get_search_by_lucky_size($lucky_size, $user_gender_slash, $user_age_slash, "");
		}else if($id_tag != ''){ // only products with id_tag
			$products = $product->get_search_by_tag($id_tag, "");
		}else if($q_fb_user != ''){ // only "user saved products"
			$products = $product->get_search_by_fb_user($q_fb_user, "");
		}else{ // normal product feed
			$products = $product->get_search($q, $user_gender_slash, $user_age_slash, "");
		}

		/* GET ALL PRODUCTS STOCK */

		$id_products 	= array(-1);
		foreach ($products as $row) {
			$id_products[] = $row->get_id_product();
		}
		$id_products 	= implode(', ', $id_products);

		$products_stock = $product_stock->get_all(" where id_product in ($id_products) ");

		/* GET THE STOCK OF ONLY ONE PRODUCT */

		function get_product_stock($id_product){
			global $products_stock;

			$output = array();
			foreach ($products_stock as $row) {
				if($row->get_id_product() == $id_product){
					$output[] = $row;
				}
			}

			return $output;
		}
	}

?>

<!-- 
	/* ====================================================================== *
  		CHECK IF THERE'S A GLOBAL DISCOUNT
	 * ====================================================================== */ 
-->		

	<?php 
		$keyword->map($q);

		if($keyword->get_discount() > 0){
			$discount_txt = $keyword->get_discount();

			if($keyword->get_discount_type() == 'percentage'){
				$discount_txt .= '%';
			}else{
				$discount_txt = '₹'.$discount_txt;
			}
	?>
			<div class="alert alert-info text-center" style="background: #f1dfc0; border: none; color: inherit;">We have a <?php echo $discount_txt; ?> store wide discount for this brand. Hurry</div>
	<?php
		}
	?>

<!-- 
	/* ====================================================================== *
  		MEDIA BOXES GRID (PRODUCTS)
	 * ====================================================================== */ 
-->		
	
	<style>
		.custom-filter{
			padding: 0;
			text-align: center;
			margin-bottom: 20px;
			display: inline-block;
		}
		.custom-filter li{
			list-style: none;
			display: inline-block;
			font-size: 12px;
		}
		.custom-filter li a{
			color: #999;
			text-decoration: none;
			padding: 10px;
			width: 200px;
			display: block;
		}
		.custom-filter li a:hover{
			color: #333;
		}
		.custom-filter li a.selected{
			color: #6D4D1C !important;
			border: 1px solid #6D4D1C;
		}
		@media only screen and (max-width: 768px) {
			.custom-filter li a{
				width: 130px;
			}
		}
	</style>
		
	<style>
		.filters_container{
			white-space: nowrap;
			text-align: center;
		}
		@media only screen and (max-width: 450px) {
			.filters_container{
				overflow-x: scroll;
			}
			.filters_container {
			    -ms-overflow-style: none;
			    overflow: -moz-scrollbars-none;
			}
			.filters_container::-webkit-scrollbar { 
			    display: none;
			}
		}
		#sort{
			display: none;
		}
		#filter{
			display: none;
		}
	</style>
	
	<?php $keyword_globalinfo->map($q); ?>
	<?php if($keyword_globalinfo->get_expiry_date()>0){ ?>
		<div class="countdown_keyword_global">
			<div id="countdown_k"></div>
			<script>
				var tillDate = new Date('<?php echo $keyword_globalinfo->get_expiry_date(); ?>');
				tillDate.setHours(0,0,0,0);
				$('#countdown_k').countdown({until: tillDate, format:'DHMS', padZeroes: true, layout: ' <span class="cd_time">{dn} <span class="cd_time_txt">{dl}</span></span> <span class="cd_time">{hn}:{mn}:{snn} <span class="cd_time_txt">{hl}</span></span>' });
			</script>
		</div>
	<?php } ?>

	<div class="filters_container">
		<ul class="custom-filter" id="sort">
			<li><a href="#" data-sort-by="media-box-id-product" class="selected">Newest First</a></li>
			<li><a href="#" data-sort-by="media-box-view-count">Most Popular</a></li>
		</ul>
		
		<ul class="custom-filter" id="filter">
			<li style="display:none;"><a class="selected" href="#" data-filter=".normal_products">Normal Products</a></li>
	  		<li><a href="#" data-filter=".lucky_sizes">Lucky Sizes</a></li>
		</ul>
	</div>

	<script>
		$('#sort').on('click', '[data-sort-by]', function(){
			$('#filter').find('a[data-filter=".normal_products"]').trigger('click');
		})

		$('#filter').on('click', '[data-filter=".lucky_sizes"]', function(){
			$('#sort').find('a[data-sort-by]').removeClass('selected');
		})
	</script>
	
	<div id="grid"> 

		<?php 

			$normal_products 		= 0;
			$lucky_size_products 	= 0;

			foreach ($products as $row) {

				/* [START] IF DATE IS SPECIFIED THEN FILTER BY IT */

				if( isset($_GET['date']) && $_GET['date'] != '' ){
					$product_date 	= $product_lang->get_date_add($row->get_id_product_prestashop());

					if( $product_date == '' || strtotime( date("F Y", strtotime($product_date)) ) != $_GET['date'] ){
						continue;
					}
				}

				/* [END] IF DATE IS SPECIFIED THEN FILTER BY IT */

				$id_product_prestashop 	= $row->get_id_product_prestashop();
				$id_product 			= $row->get_id_product();

				$img_link_last_chars 	= substr($row->get_image_link(), strrpos($row->get_image_link(), '/') + 1);
				$id_image 				= str_replace(".jpg", "", $img_link_last_chars); // get the 75 from this: http://url.com/9-75/75.jpg
				$img_url 				= "http://miracas.miracaslifestyle.netdna-cdn.com/$id_product_prestashop-$id_image-thickbox/$id_image.jpg";
				$price 					= get_the_price($id_product);
				$discount 				= get_the_discount($id_product, $price);
				$colors 				= $attribute->get_colors_of_product($id_product_prestashop);
				$sizes 					= $attribute->get_sizes_of_product($id_product_prestashop);
				$list_stock 			= get_product_stock($id_product);

				// TOTAL STOCK AND WHAT COLORS/SIZES ARE AVAILABLE
				$total_stock 			= 0;
				$colors_with_stock 		= [];
				$sizes_with_stock 		= [];
				foreach ($list_stock as $current_stock) {
					$total_stock += $current_stock->get_stock();
					
					if($current_stock->get_stock()>0){
						if (!in_array($current_stock->get_color(), $colors_with_stock)) {
						    $colors_with_stock[] = $current_stock->get_color();
						}

						if (!in_array($current_stock->get_size(), $sizes_with_stock)) {
						    $sizes_with_stock[] = $current_stock->get_size();
						}
					}
				}

				// ONLY DO THIS CHECKING FOR THE NORMAL PRODUCTS
				if(count($list_stock) <= 0){ 
					if($product_lang->get_product_active($id_product_prestashop) == '0') continue;

					$normal_products++;
				}
				// IF ITS A LUCKY SIZE PRODUCT AND THE TOTAL STOCK IS 0 THEN IS NOT VALID
				else{
					if($total_stock == 0)continue;

					$lucky_size_products++;
				}

				// If its a global product then remove colors

				if($row->get_global() == 'yes'){
					$colors = array();
				}

		?>				
	        <div class="media-box <?php if(count($list_stock)>0){echo 'lucky_sizes';}else{echo 'normal_products';} ?>" style="display:none;">
	            <div class="media-box-image">
	                <div data-thumbnail="<?php echo $img_url; ?>" data-width="600" data-height="600"></div>
	                
	                <div class="thumbnail-overlay trigger-lightbox">
              			<i class="fa fa-plus open-lightbox" data-idproductprestashop="<?php echo $id_product_prestashop; ?>"></i>
	                </div>
	            </div>

	            <div class="media-box-content" data-idproductprestashop="<?php echo $id_product_prestashop; ?>" data-idproduct="<?php echo $row->get_id_product(); ?>">
	                <div class="media-box-text" style="margin: 0 !important;">
	                	
	                	<!--
							========= THE PRICE AND VIEW COUNT =========
	                	-->

	                	<p>
	                		<span class="media-box-price">
	                			<?php if($discount>0){ ?>
	                				<span style="font-size: 12px;color: rgba(145,145,145,.5);text-decoration: line-through;">₹<?php echo number_format($price, 2); ?></span>
	                			<?php } ?>

	                			₹<?php echo number_format($price-$discount, 2); ?>
	                		</span>
							
							<?php if($row->get_global() == 'yes'){ ?>
	                			&nbsp;<span class="show_product_breakup">Click to see the product breakup</span>
	                		<?php } ?>

	                		<span class="count pull-right">
	                			<i class="fa fa-heart heart-selected"></i>
	                			<?php echo $row->get_like_count(); ?>
	                		</span>
	                	</p>
						
						<?php if($row->get_global() == 'yes'){ ?>
							<!--
								========= PRODUCT BREAKUP =========
		                	-->	

							<?php 
								$product_globalinfo = new product_globalinfo();
								$keyword_globalinfo = new keyword_globalinfo();

								$product_globalinfo->map($row->get_id_product());
								$keyword_globalinfo->map(str_replace("/", "", $row->get_keywords()));

								$custom_duty_percentage 		= ( (float) $keyword_globalinfo->get_custom_duty_percentage() ) / 100;
								$international_shipping_cost 	= (float) $keyword_globalinfo->get_international_shipping_cost();
								$domestic_shipping_cost  		= (float) $keyword_globalinfo->get_domestic_shipping_cost();
								$vendor_commission_percentage 	= ( (float) $keyword_globalinfo->get_vendor_commission_percentage() ) / 100;
								$vendor_price 					= (float) $product_globalinfo->get_vendor_price();

								$original_price  				= $vendor_price;
								$original_price 				= $original_price * 10;
								$original_price 				= $original_price + ( round($original_price*$vendor_commission_percentage, 2) );
							?>
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
		                <?php } ?>

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
	                			<div class="col-xs-4 padding-top">
	                				<strong>Available in</strong>
	                			</div>
	                			<div class="col-xs-8">
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
                	
	                	<hr>
                		
                		<div class="product_bottom">

                			<div class="row">
                				<div class="col-xs-6">
                					
                					<?php if($active_session === true){ ?>
		                				<?php 
		                					$fb_user_product_save->set_id_fb_user($id_fb_user);
		                					$fb_user_product_save->set_id_product($id_product);
		                				?>
			                			<a href="#" class="save_id_product" data-id-product="<?php echo $id_product; ?>" data-saved="<i class='fa fa-check'></i>&nbsp; In Whishlist" data-notsaved="<i class='fa fa-bookmark'></i>&nbsp; Whishlist">
					                		<?php if ($fb_user_product_save->exists()){ echo "<i class='fa fa-check'></i>&nbsp; In Whishlist"; }else{ echo "<i class='fa fa-bookmark'></i>&nbsp; Whishlist"; } ?>
					                	</a>
					                <?php } ?>

                				</div>	
                				<div class="col-xs-6">
                					<a target="_blank" href="product_details_global.php?id_product=<?php echo $row->get_id_product(); ?><?php echo str_replace("?", "&", $extra_param); ?>">
                						<span class="fa fa-search"></span>&nbsp; View
                					</a>
                				</div>

                				<!--
                				<div class="col-xs-5">

									<?php if($active_session === true){ ?>
										<?php 
											$product_in_cart = $order_detail->exists_product_prestashop_in_order($current_id_order, $id_product_prestashop); // if product is not added yet to the cart
										?>
										<?php if(!$product_in_cart){ ?>
											<div class="add_controls">
			                					<a class="add_to_cart" href="#"><span class="fa fa-shopping-cart"></span>&nbsp; Add to Cart</a>
			                					<div class="combination_not_available" style="color:#d44950;position:absolute;bottom:-33px">
			                						The selected product combination is not available. Please select a different color or size.
			                					</div>
			                				</div>
										<?php } ?>

										<div class="added_controls" <?php if($product_in_cart){ echo 'style="display:block;"'; } ?>>
		                					<a href="../cart"><span class="fa fa-check"></span>&nbsp; Go To Cart</a>
		                				</div>						
			            			<?php }else{ ?>	
				                		<a class="add_to_cart_without_login" href="#"><span class="fa fa-shopping-cart"></span>&nbsp; Add to Cart</a>
				                		<div class="combination_not_available" style="color:#d44950;position:absolute;bottom:-33px">
			        						The selected product combination is not available. Please select a different color or size.
			        					</div>
			                		<?php } ?>

                				</div>
                				-->
                			</div>

	                	</div>

	                	<!--
							========= ORDER =========
	                	-->

	                	<div class="media-box-view-count" style="display:none;"><?php echo $row->get_like_count(); ?></div>
	                	<div class="media-box-id-product" style="display:none;"><?php echo $id_product; ?></div>

	                </div>
	            </div>
	        </div>
		<?php 
			} 
		?>
	</div> <!-- END OF GRID -->

	<?php if($normal_products>0){ ?>
		<style> #sort{ display: inline-block; } </style>
	<?php }else{ ?>
		<script> jQuery('#sort').remove(); </script>
	<?php } ?>

	<?php if($lucky_size_products>0){ ?>
		<style> #filter{ display: inline-block; } </style>
	<?php }else{ ?>
		<script> jQuery('#filter').remove(); </script>
	<?php } ?>

<!-- 
	/* ====================================================================== *
		PIXELS LINK
	 * ====================================================================== */ 
-->	

	<?php if(count( $pixel->get_list_by_keyword($q, " order by pixel_count desc ") )>0){ ?>
		<style>
			.media-boxes-no-more-entries{
				display: none;
			}
			.see-pixels{
				margin-top: 20px;
				display: none;
			}
			.see-pixels .btn-green{
				width: 200px;
				padding: 12px 12px 11px 12px;
			}
		</style>

		<div class="text-center see-pixels">
			<a class="btn btn-green" href="../pixels?q=<?php echo $q; ?><?php echo str_replace("?", "&", $extra_param); ?>">See More Styles</a>
		</div>
	<?php } ?>
	
<!-- 
	/* ====================================================================== *
		TO TOP BUTTON
	* ====================================================================== */ 
-->		
	
	<div class="fixed">
		<div class="to-top">
			<i class="glyphicon glyphicon-chevron-up"></i>
		</div>
	</div>
	<style>
		.fixed{
			position: fixed;
			right: 19px;
			bottom: 17px;
			z-index: 999;
		}
		.to-top{
			height: 29px;
			width: 29px;
			background-color: rgba(171,119,42,.6);
			color: #fff;
			cursor: pointer;
			text-align: center;
			font-size: 12px;

			-webkit-border-radius: 2px;
			   -moz-border-radius: 2px;
			     -o-border-radius: 2px;
			        border-radius: 2px;
		}
		.to-top>i{
			margin-top: 6px;
		}
	</style>
	<script>
		$('.to-top').on('click', function(){
			$('html,body').animate({ scrollTop: 0 }, 'fast');
		})
	</script>

<!-- 
	/* ====================================================================== *
  		GLOBAL PRODUCT BREAKUP
	 * ====================================================================== */ 
-->		

	<style>
		.show_product_breakup{
			cursor: pointer;
			font-size: 10px;
		}
		.product_breakup{
			padding: 10px; 
			display: none;
			margin-bottom: 10px;
		}
		.product_breakup .row{
			margin-bottom: 3px;
		}
	</style>
	<script>
		$('body').on('click', '.show_product_breakup', function(){
			$(this).parents('.media-box').find('.product_breakup').stop().slideToggle("fast","swing", function(){
				$('#grid').mediaBoxes('resize');
			});
		});
	</script>

<!-- 
	/* ====================================================================== *
  		MEDIA BOXES
	 * ====================================================================== */ 
-->		

	<script>

		$('#sort').find('*[data-sort-by]').on('click', function(e){
			e.preventDefault();

			var $this 	= $(this);

			$this.parents('#sort').find('*[data-sort-by]').removeClass('selected');
			$this.addClass('selected');

			$('.see-pixels').hide();
			
			// destroy media boxes

			$('#grid').mediaBoxes('destroy');

			// re order the HTML

			var media_boxes = $('.media-box');

			media_boxes.sort(function(a, b) {
				var $a 	= parseFloat( $(a).find('.'+$this.attr('data-sort-by')).text().toUpperCase() );
				var $b 	= parseFloat( $(b).find('.'+$this.attr('data-sort-by')).text().toUpperCase() );

			   	return $b - $a;
			});

			$.each(media_boxes, function(index, item) {
			   $('#grid').append(item); 
			});

			init_media_boxes();
		})

		function init_media_boxes(){
			// initialize the media boxes

			$('#grid').mediaBoxes({
		    	columns: 4,
		    	horizontalSpaceBetweenBoxes: 8,
	        	verticalSpaceBetweenBoxes: 8,
	        	boxesToLoadStart: 8,
		    	boxesToLoad: 4,
		    	minBoxesPerFilter: 8,
		    	deepLinkingOnPopup: false,
    			deepLinkingOnFilter: false,
		    });
		}

		$('.selected[data-sort-by]').trigger('click');

	</script>

	<?php if(isset($normal_products) && $normal_products==0){ ?>
		<script>
			$('*[data-filter=".normal_products"]').removeClass('selected');
			$('*[data-filter=".lucky_sizes"]').addClass('selected');
			init_media_boxes();
		</script>
	<?php } ?>	