<?php $force_session = true; ?>
<?php require_once("../fb_validator.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/keyword.php"); ?>
<?php require_once("../classes/keyword_globalinfo.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/tag.php"); ?>

<?php 

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

	$extra_param_global = "&set_gender=".str_replace("/", "", $user_gender);

/* ====================================================================== *
    	CLASSES
 * ====================================================================== */		

    $keyword 				= new keyword();
    $keyword_globalinfo 	= new keyword_globalinfo();
    $product 				= new product();
    $tag 					= new tag();

/* ====================================================================== *
    	GET GLOBAL KEYWORDS
 * ====================================================================== */		

    $sql_global 			= " where global = 'yes' ";
    $sql_gender 			= " and genders like '%$user_gender_slash%' ";
    $sql_age 				= " and (ages like '%$user_age_slash%' or ages like '%/all/%') ";
    $sql_expiry_date 		= " and (select expiry_date from keyword_globalinfo where keyword = keyword.keyword) >= now() ";
    $global_keywords 		= $keyword->get_list(" $sql_global $sql_gender $sql_age $sql_expiry_date order by keyword ")
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

	<!-- COUNTDOWN PLUGIN -->

	<link rel="stylesheet" href="../includes/plugins/countdown/jquery.countdown.css">
  	<script src="../includes/plugins/countdown/jquery.plugin.min.js"></script>
	<script src="../includes/plugins/countdown/jquery.countdown.min.js"></script>

	<!-- MEDIA BOXES -->

  	<link rel="stylesheet" href="../includes/plugins/Media Boxes/plugin/components/Font Awesome/css/font-awesome.min.css"> <!-- only if you use Font Awesome -->
	<link rel="stylesheet" href="../includes/plugins/Media Boxes/plugin/components/Magnific Popup/magnific-popup.css"> <!-- only if you use Magnific Popup -->
	<link rel="stylesheet" type="text/css" href="../includes/plugins/Media Boxes/plugin/css/mediaBoxes.css">

	<!-- NIVO SLIDER -->

	<link rel="stylesheet" type="text/css" href="../includes/plugins/nivo slider/themes/default/default.css" />
	<link rel="stylesheet" type="text/css" href="../includes/plugins/nivo slider/themes/light/light.css" />
	<link rel="stylesheet" type="text/css" href="../includes/plugins/nivo slider/themes/dark/dark.css" />
	<link rel="stylesheet" type="text/css" href="../includes/plugins/nivo slider/themes/bar/bar.css" />
	<link rel="stylesheet" type="text/css" href="../includes/plugins/nivo slider/nivo-slider.css" />
	<style>
		.theme-default{
			position: relative;
		}
		.nivo-controlNav{
			position: absolute;
			bottom: 0;
			left:0;
			right: 0;
			z-index: 99;
		}
		.nivoSlider{
			-webkit-box-shadow: none !important;
		       -moz-box-shadow: none !important;
		         -o-box-shadow: none !important;
		        -ms-box-shadow: none !important;
		            box-shadow: none !important;

		            margin-bottom: 0 !important;
		}
		#slider img{
			display: none;

		}
	</style>

	<!-- CUSTOM STYLE -->

	<style>
		body{
	     	overflow-y: scroll;
	   }
		@media only screen and (max-width: 768px) {
			.tabs-container{
				padding: 0 10px !important;
			}
		}
	</style>

	<!-- GLOBAL STYLE -->

	<style>

		.gloal-info-head{
			padding: 20px;
		}
		.gloal-info-head h3{
			margin: 0;
			margin-bottom: 20px;
		}
		.global_tag{
			margin-bottom: 5px;
		}
		.global_tag a{
			color: #5d5d5d;	
		}
		.global-info-bottom{
			position: absolute;
			bottom: 0;
			left: 0;
			width: 100%;
		}
		.global-countdown{
			overflow: hidden;
			margin-bottom: 10px;
		}

		.global_grid{
			background: #e6e6e6;
			overflow: hidden;
			opacity: 0;

			-webkit-transition: opacity 1s ease-in-out;
			-moz-transition: opacity 1s ease-in-out;
			-ms-transition: opacity 1s ease-in-out;
			-o-transition: opacity 1s ease-in-out;
			transition: opacity 1s ease-in-out;
		}
		.global_grid>div{
			float: left;
			position: relative;
		}
		.global_column1{
			width: 20%;
		}
		.global_column2{
			width: 40%;
		}
		.global_column3{
			width: 20%;
		}
		.global_column4{
			width: 20%;
		}
		.global_img1, .global_img2, .global_img3{
			overflow: hidden;
		}
		.global_img{
			width: 100%;
			height: 100%;
			background-size:100% auto;
			background-repeat: no-repeat;
			background-position: center; 
			background-color: white;
		}

		.global_img{
			border: 10px solid #e6e6e6;
		}
		.global_img2 .global_img, .global_img4 .global_img{
			border-width: 10px 10px 10px 0px;	
		}
		.global_img3 .global_img, .global_img5 .global_img{
			border-width: 0px 10px 10px 0px;	
		}

		@media only screen and (max-width: 768px) {
			.global_grid>.global_column1{
				width: 100%;
				height: auto !important;
			}
			.global_column2{
				width: 70%;
			}
			.global_column3{
				width: 30%;
			}
			.global_column4{
				display: none;
			}
			.global-info-bottom{
				position: relative;
				overflow: hidden;
			}
		}
	</style>

</head>
<body>
	
	<?php require_once("../menu_global.php"); ?>
	<?php require_once("../sidebar_global.php"); ?>
	<div id="menu-page-wraper">

<!--
/* ====================================================================== *
    	START PAGE
 * ====================================================================== */	
-->

	<div class="page-wrap"><div class="page-wrap-inner">

		<div class="theme-default">
			<div id="slider">
				<img src="http://miracas.com/ZS/static/img3.jpg">
				<img src="http://miracas.com/ZS/static/img2.jpg" >
				<img src="https://i2.wp.com/247computers.in/shop/wp-content/uploads/2016/12/WELCOME-banner-shop.gif?resize=965%2C280" >
			</div>
		</div>

		<?php require_once("../message.php"); ?>

		<?php 
			function get_product_img($id_product){
				global $product;
				$product->map($id_product);
				
				$id_product_prestashop 	= $product->get_id_product_prestashop();
				$img_link_last_chars 	= substr($product->get_image_link(), strrpos($product->get_image_link(), '/') + 1);
				$id_image 				= str_replace(".jpg", "", $img_link_last_chars); // get the 75 from this: http://url.com/9-75/75.jpg
				$img_url 				= "http://miracas.miracaslifestyle.netdna-cdn.com/$id_product_prestashop-$id_image-thickbox/$id_image.jpg";

				return $img_url;
			}
		?>

		<div class="tabs-container">

			<div id="grid_global_keywords"> 
				<?php $i=0; ?>
				<?php foreach ($global_keywords as $row) { ?>

					<?php 
						$i++;

						/* GET GLOBAL INFO */

						$keyword_globalinfo->map($row->get_keyword());

						/* GET PRODUCTS FROM KEYWORD */

						$products 	= $product->get_search($row->get_keyword(), $user_gender_slash, $user_age_slash, " order by like_count ");
						if(count($products) < 5) continue; // needs at least 3 products for the 3 images shown

						/* GET TAGS FROM THE PRODUCTS INSIDE THIS KEYWORD */

						$id_products_arr 	= array();
						foreach ($products as $row_product){
							$id_products_arr[] = $row_product->get_id_product();
						}
						$id_products 		= implode("','", $id_products_arr);

						$tags 		= $tag->get_list_by_products("'$id_products'", " order by name ");

					?>
					
					<div class="media-box">
						
						<div class="global_grid" onclick="location.href='./?q=<?php echo $row->get_keyword(); ?><?php echo $extra_param_global; ?>'" style="cursor:pointer;">
							<div class="global_column1">

								<div class="gloal-info-head">
									<h3><?php echo $row->get_keyword(); ?></h3>
									<?php foreach ($tags as $tag) { ?>
										<div class="global_tag">
											<a href="./?id_tag=<?php echo $tag->get_id_tag(); ?><?php echo $extra_param_global; ?>"><?php echo $tag->get_name(); ?></a>
										</div>
									<?php } ?>
								</div>

								<div class="global-info-bottom">
									<div class="global-countdown">
										<div id="countdown_<?php echo $i; ?>"></div>
										<script>
											var tillDate = new Date('<?php echo $keyword_globalinfo->get_expiry_date(); ?>');
											tillDate.setHours(0,0,0,0);
											$('#countdown_<?php echo $i; ?>').countdown({until: tillDate, format:'DHMS', padZeroes: true, layout: ' <span class="cd_time">{dn} <span class="cd_time_txt">{dl}</span></span> <span class="cd_time">{hn}:{mn}:{snn} <span class="cd_time_txt">{hl}</span></span>' });
										</script>
									</div>

									<a href="./?q=<?php echo $row->get_keyword(); ?><?php echo $extra_param_global; ?>" class="btn btn-green btn-default btn-block">
										SHOW NOW 
									</a>
								</div>

							</div>

							<div class="global_column2">
								<div class="global_img1">
									<div class="global_img" style="background-image:url('<?php echo get_product_img($products[0]->get_id_product()); ?>');"></div>
								</div>	
							</div>

							<div class="global_column3">
								<div class="global_img2">
									<div class="global_img" style="background-image:url('<?php echo get_product_img($products[1]->get_id_product()); ?>');"></div>
								</div>
								<div class="global_img3">
									<div class="global_img" style="background-image:url('<?php echo get_product_img($products[2]->get_id_product()); ?>');"></div>
								</div>
							</div>

							<div class="global_column4">
								<div class="global_img4">
									<div class="global_img" style="background-image:url('<?php echo get_product_img($products[3]->get_id_product()); ?>');"></div>
								</div>
								<div class="global_img5">
									<div class="global_img" style="background-image:url('<?php echo get_product_img($products[4]->get_id_product()); ?>');"></div>
								</div>
							</div>
						</div>

					</div>

				<?php } ?>
			</div>

		</div> <!-- End tabs-container -->

	<!--
	/* ====================================================================== *
	    	PLUGINS AND JS
	 * ====================================================================== */	
	-->

		<!-- MEDIA BOXES -->

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
			$('#grid_global_keywords').mediaBoxes({
		    	columns: 1,
		    	resolutions: [],
		    	horizontalSpaceBetweenBoxes: 8,
	        	verticalSpaceBetweenBoxes: 8,
	        	boxesToLoadStart: 8,
		    	boxesToLoad: 4,
		    	minBoxesPerFilter: 8,
		    	deepLinkingOnPopup: false,
				deepLinkingOnFilter: false,
				lazyLoad: true,
				loadMoreWord: 'Load More Collections',
	        	noMoreEntriesWord: 'No More Collections',
		    });

		    var imgWidth 	= 1;
		    var imgHeight 	= 1;

		    function resize_grid_items(delay){
		    	setTimeout(function(){

			    	var global_img1_h		= $('.global_img1').outerWidth(true)-5;		
	            	var gloabl_img1_h_new   = (imgHeight * global_img1_h)/imgWidth;

			    	var css 				= " .global_grid{ "+ 
				    						  " 	opacity: 1; "+ 
				    						  " } " +
				    						  " .global_column1{ "+ 
				    						  " 	height: "+gloabl_img1_h_new+"px !important; "+ 
				    						  " } " +
				    						  " .global_img1{ "+ 
				    						  " 	height: "+gloabl_img1_h_new+"px !important; "+ 
				    						  " } " +
				    						  " .global_img2, .global_img3, .global_img4, .global_img5{ "+ 
				    						  " 	height: "+ (gloabl_img1_h_new/2)+"px !important; "+ 
				    						  " } ";

			    	var $stylesheet = $('<style type="text/css" media="screen" />');
					$stylesheet.html(css);
					$('body').append($stylesheet);
			    	
			    	$('#grid_global_keywords').mediaBoxes('resize');

			    }, delay);
		    }

		    resize_grid_items(300);
		    $(window).resize(function(){
		    	resize_grid_items(300);
		    });
		</script>

		<!-- NIVO SLIDER -->
		
		<script type="text/javascript" src="../includes/plugins/nivo slider/jquery.nivo.slider.pack.js"></script>
		<script>
			$('#slider').imagesLoadedMB(function() {
				$('#slider').nivoSlider({
					effect:'fade', //Specify sets like: 'fold,fade,sliceDown'
					slices:9,
					animSpeed:500, //Slide transition speed
					pauseTime:3500,
					startSlide:0, //Set starting Slide (0 index)
					directionNav:true, //Next & Prev
					directionNavHide:true, //Only show on hover
					controlNav:true, //1,2,3...
					controlNavThumbs:false, //Use thumbnails for Control Nav
			      	controlNavThumbsFromRel:false, //Use image rel for thumbs
					controlNavThumbsSearch: '.jpg', //Replace this with...
					controlNavThumbsReplace: '_thumb.jpg', //...this in thumb Image src
					keyboardNav:true, //Use left & right arrows
					pauseOnHover:true, //Stop animation while hovering
					manualAdvance:false, //Force manual transitions
					captionOpacity:0.8, //Universal caption opacity
					beforeChange: function(){},
					afterChange: function(){},
					slideshowEnd: function(){} //Triggers after all slides have been shown
				});
			});
		</script>
	
	</div></div>

	<?php require_once("../footer.php"); ?>
	
	</div>

</body>
</html>

