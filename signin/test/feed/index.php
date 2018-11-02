<?php $force_session = true; ?>
<?php require_once("../fb_validator.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/product.php"); ?>

<?php 

/* ====================================================================== *
    	CLASSES
 * ====================================================================== */		

    $product 				= new product();

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

/* ====================================================================== *
    	PARAMETERS
 * ====================================================================== */	

    $q 				= isset($_GET['q'])?$_GET['q']:'';	
	$lucky_size 	= isset($_GET['size'])?$_GET['size']:'';
	$id_tag 		= isset($_GET['id_tag'])?$_GET['id_tag']:'';
	$q_fb_user 		= isset($_GET['usr'])?$_GET['usr']:'';
	$date 			= isset($_GET['date'])?$_GET['date']:'';

	if($lucky_size != ''){
		$q = "Lucky Size: $lucky_size";
	}	

	if($id_tag != ''){
		$q = "Tag Search";
	}	

	if($q_fb_user != ''){
		$q = "User Search";
	}

?>

<!doctype html>
<html lang="en">
<head>

  	<?php require_once("../head.php"); ?>

<!--
/* ====================================================================== *
    	PLUGINS AND STYLES
 * ====================================================================== */	
-->

	<!-- countdown plugin -->
	<link rel="stylesheet" href="../includes/plugins/countdown/jquery.countdown.css">
  	<script src="../includes/plugins/countdown/jquery.plugin.min.js"></script>
	<script src="../includes/plugins/countdown/jquery.countdown.min.js"></script>

	<!-- Media Boxes CSS files -->
  	<link rel="stylesheet" href="../includes/plugins/Media Boxes/plugin/components/Font Awesome/css/font-awesome.min.css"> <!-- only if you use Font Awesome -->
	<link rel="stylesheet" href="../includes/plugins/Media Boxes/plugin/components/Magnific Popup/magnific-popup.css"> <!-- only if you use Magnific Popup -->
	<link rel="stylesheet" type="text/css" href="../includes/plugins/Media Boxes/plugin/css/mediaBoxes.css">

	<!-- Animate CSS -->
	<link rel="stylesheet" type="text/css" href="../includes/css/animate.css">

	<style>
		body{
	     	overflow-y: scroll;
	   }
		@media only screen and (max-width: 768px) {
			.tabs-container{
				padding: 0 10px !important;
			}
		}

		.trigger-lightbox{
			cursor: pointer;
		}
	</style>

</head>
<body>
	
	<?php require_once("../menu.php"); ?>
	<?php require_once("../sidebar.php"); ?>
	<div id="menu-page-wraper">

<!--
/* ====================================================================== *
    	GOOGLE ANALYTICS
 * ====================================================================== */	
-->

	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-31078008-1', 'auto');
	  ga('send', 'pageview');
	</script>

<!--
/* ====================================================================== *
    	START PAGE
 * ====================================================================== */	
-->

	<div class="page-wrap"><div class="page-wrap-inner">

		<?php require_once("../message.php"); ?>

		<div class="tabs-container">

	<!-- 
	/* ====================================================================== *
      		KEYWORDS
 	 * ====================================================================== */ 
 	-->
			
			<?php if( $q == '' ){ ?>
				
				<div id="keywords"></div>

				<script>
					$('#keywords').html('<img src="../includes/img/loader.gif" class="loading" />');
					$.get('get_keywords.php?set_gender=<?php echo $user_gender; ?>', function(rs){
						$('#keywords').html($.trim(rs));	

						$keywords_container = $('.keywords-container');

						$keywords_container.isotope({
						  itemSelector: '.keyword-item',
						  percentPosition: true,
						})

						$keywords_container.imagesLoadedMB().progress( function() {
						  $keywords_container.isotope('layout');
						});

					});
				</script>

	<!-- 
	/* ====================================================================== *
      		PRODUCTS
 	 * ====================================================================== */ 
 	-->			
			
			<?php }else if( $q != ''){ ?>

				<?php $products 		= $product->get_search($q, $user_gender_slash, $user_age_slash, " order by like_count desc "); ?>
				
				<!-- SHOW COLLECTIONS IF THE Q PARAMETER IS SET (WHICH MEANS ITS A SEARCH BY KEYWORD) AND THE PRODUCTS IN THAT KEYWORD ARE MORE THAN 100 -->

				<?php if(isset($_GET['q']) && !isset($_GET['date']) && count($products) >= 100){ ?>

					<div id="collections"></div>

					<script>
						$('#collections').html('<img src="../includes/img/loader.gif" class="loading" />');
						$.get('get_collections.php?set_gender=<?php echo $user_gender; ?>&q=<?php echo $q; ?>', function(rs){
							$('#collections').html($.trim(rs));	
						});
					</script>	

				<!-- SHOW PRODUCT FEED IF THE ABOVE IS FALSE -->

				<?php }else{ ?>

					<div id="products"></div>

					<script>
						$('#products').html('<img src="../includes/img/loader.gif" class="loading" />');
						$.get('get_products.php?set_gender=<?php echo $user_gender; ?>&q=<?php echo $q; ?>&size=<?php echo $lucky_size; ?>&id_tag=<?php echo $id_tag; ?>&usr=<?php echo $q_fb_user; ?>&date=<?php echo $date; ?>', function(rs){
							$('#products').html($.trim(rs));	
						});
					</script>			

				<?php } ?>

			<?php } ?>

		</div> <!-- End tabs-container -->

	<!-- 
	/* ====================================================================== *
      		FB LOGIN IN MODAL
 	 * ====================================================================== */ 
 	-->	

		<!-- Modal -->
		<div class="modal-login modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-mask">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<a class="close-modal" data-dismiss="modal" aria-label="Close">
							<i class="fa fa-close"></i>
						</a>

						<div class="modal-body">
							<a href="<?php echo $loginUrl; ?>" class="btn btn-blue btn-block">
								<i class="fa fa-facebook-official"></i> &nbsp;
								Sign in with Facebook
							</a>
						</div>
					</div>
				</div>
		  	</div>
		</div>

	<!--
	/* ====================================================================== *
	    	PLUGINS AND JS
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

		<script src="../includes/plugins/Isotope/jquery.isotope.min.js"></script>

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
		    		
		    		$this.addClass('adding');

		    		if(size == undefined || size == null){
		    			size = '';
		    		}
		    		if(color == undefined || color == null){
		    			color = '';
		    		}

		    		$this.html("<i class='fa fa-circle-o-notch fa-spin'></i> &nbsp;adding...");
		    		$.get('add_to_cart.php?color='+encodeURIComponent(color)+'&size='+size+'&qty='+qty+'&id_product_prestashop='+idProductPrestashop+'&id_product='+idProduct, function(r){
		    			
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
		    				$('#myModal').modal('show').appendTo('body');;
		    			}else{
		    				alert("Oops! something went wrong, refresh the page and try again.");
		    			}
		    		});
		    		
		    	}
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
		    	var newItems 			= '<span class="mb-open-popup mfp-image" data-popuptrigger="yes" data-mfp-src="http://www.davidbo.dreamhosters.com/plugins/mediaBoxes/example/gallery/img-2.jpg" data-src="http://www.davidbo.dreamhosters.com/plugins/mediaBoxes/example/gallery/img-2.jpg"></span>';
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

