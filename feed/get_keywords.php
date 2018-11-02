<?php $force_session = true; ?>
<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/keyword.php"); ?>
<?php require_once("../classes/pixel_keyword.php"); ?>
<?php require_once("../classes/pixel.php"); ?>
<?php require_once("../classes/shopping_assistant_conversation.php"); ?>
<?php require_once("../classes/fb_user_product_save.php"); ?>
<?php require_once("../classes/tag.php"); ?>
<?php require_once("../classes/tag_super.php"); ?>
<?php require_once("../classes/product_lang.php"); ?>
<?php require_once("../classes/espresso_keywords.php"); ?>

<?php 
	
/* ====================================================================== *
    	CLASSES
 * ====================================================================== */		

	$keyword 							= new keyword();
	$pixel_keyword 						= new pixel_keyword();
	$pixel 								= new pixel();
	$product 							= new product();
	$shopping_assistant_conversation 	= new shopping_assistant_conversation();
	$fb_user_product_save 				= new fb_user_product_save();
	$tag 								= new tag();
	$tag_super 							= new tag_super();
	$product_lang 						= new product_lang();
	$espresso_keywords 					= new espresso_keywords();

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

	$extra_param = "&set_gender=".str_replace("/", "", $_GET['set_gender']);

/* ====================================================================== *
    	GET ALL KEYWORDS
 * ====================================================================== */			

	$product_list 	= $product->get_all_keywords($user_gender_slash, $user_age_slash, "");
	$keywords_list 	= array();

	foreach ($product_list as $row) {
		$arr = explode("/", $row->get_keywords());
		foreach ($arr as $word) {
			if($word != ""){
				$keywords_list[] = $word;
			}
		}
	}
	$keywords_list = array_unique($keywords_list);
	
/* ====================================================================== *
    	DIVIDE KEYWORDS
 * ====================================================================== */			

	$brand_keywords 		= array();
	$category_keywords 		= array();
	$style_keywords 		= array();
	$popular_keywords 		= array();
	foreach ($keywords_list as $value) {
		if(strtoupper(substr( $value, 0, 4 )) === "MOOD"){
			continue;
		}

		if(strtoupper(substr( $value, 0, 5 )) === "BRAND"){
			$brand_keywords[] = $value;
		}else if(strtoupper(substr( $value, 0, 8 )) === "CATEGORY"){
			$category_keywords[] = $value;
		}else{
			$style_keywords[] = $value;	
		}

		$keyword = new keyword();
		$keyword->map($value);

		if($keyword->get_popular() == 'yes'){
			$popular_keywords[] = $value;
		}
	}

/* ====================================================================== *
    	PIXEL KEYWORDS
 * ====================================================================== */			

	$pixel_keyword_list 	= $pixel_keyword->get_all_keywords($user_gender_slash, $user_age_slash, "");

?>

<!--
/* ====================================================================== *
      MOBILE APP BANNER
 * ====================================================================== */	    	
-->	
<?php /* ?>
	<style>
		.mobile_app_banner_container{
			margin: -20px -20px 0px -20px;
			position: relative;
			overflow: hidden;
			text-align: center;
			background-image: url(../includes/img/mobile_app_bg.jpg);
			background-repeat: no-repeat;
			background-size: cover;
			background-position: center center;
			padding: 0 60px;
		}
		.mobile_app_banner_container>div{
			float: left;
			padding: 0px 20px;
			height: 500px;
			width: 50%;
		}

		.iphone_mobile_app{
			padding-top: 40px;
			max-height: 100%;
			max-width: 100%;
		}

		.center-parent{
			height: 100%;
		    width: 100%;
		    display: table !important;
		}
		.center-child{
			display: table-cell !important;
		    vertical-align: middle; 
		    text-align:center;
		}

		.mobile_app_banner_info{
			color: white;
			text-align: left;
			padding: 0 20px;
		}
		.mobile_app_banner_info h2{
			margin-top: 0;
		}

		.btn-transparent{
	  		background-color: transparent;
	  		border: 1px solid #fff;
	  		color: #fff;
	  	}
		
	  	.btn-transparent:hover, .btn-transparent:focus{	
			background-color: transparent;
	  	}

	  	@media only screen and (max-width: 768px) {
	  		.mobile_app_banner_container{
	  			padding: 0px;
	  		}
	  		.mobile_app_banner_container>div:first-child{
	  			height: 350px;
	  		}
			.mobile_app_banner_container>div{
				width: 100%;
			}
			.mobile_app_banner_info{
				text-align: center;
				padding: 0 10px;
			}
		}

		@media only screen and (max-width: 768px) {
			.mobile_app_banner_container{
				margin: -20px -10px 0px -10px;
			}
		}

	</style>
	
	<div class="mobile_app_banner_container">
		<div class="col">
			<div class="center-parent">
				<div class="center-child">
					
					<div class="mobile_app_banner_info">
						<h2>It's Amazing</h2>
						<br>
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laudantium deserunt, neque. Repudiandae distinctio voluptas consequuntur, iste sint eveniet ducimus in, magnam, sapiente 
						suscipit ullam quia culpa iure qui non debitis.</p>
						<br>
						<a href="" class="btn btn-transparent"><i class="fa fa-android"></i>&nbsp; Play Store</a>
					</div>	

				</div>
			</div>
		</div>
		<div class="col">
			<img class="iphone_mobile_app" src="../includes/img/mobile_app.png" alt="">
		</div>
	</div>
<?php */ ?>

<!--
/* ====================================================================== *
      SHOPPING ASSISTANT
 * ====================================================================== */	    	
-->
<?php /* ?>

	<?php 
		$url_assisted_shopping 			= '';
		$text_assisted_shopping 		= '';
		$count_fashion_conversations 	= count($shopping_assistant_conversation->get_all_conversations(""));
		if($active_session === true){
			$url_assisted_shopping 		= '../shopping_assistant/'.str_replace("&", "?", $extra_param);
			$text_assisted_shopping  	= 'Turn on Assisted Shopping';
		}else{
			$url_assisted_shopping 		= 'javascript:login_to_turn_on_assisted_shopping();';
			$text_assisted_shopping  	= 'Login to Turn on Assisted Shopping';
		}
	?>

	<div class="open_shopping_asistant animate-on-load" id="particles-js">
		
		<p style="font-size:13px;">
			Completed <?php echo $count_fashion_conversations; ?> assisted shoppings
		</p>
		
		<div class="flipper-container" onclick="setTimeout(function(){ location.href='<?php echo $url_assisted_shopping; ?>'; }, 300);">
		  <div class="flipper">
		    <div class="front-face"><span data-hover="Clicked"><?php echo $text_assisted_shopping; ?></span></div>
		    <div class="back-face">Turning On</div>
		  </div>
		</div>

    </div>

    <script>
    	function login_to_turn_on_assisted_shopping(){
			var $this 				= $('.open_shopping_asistant');

			if($this.hasClass('loading')){
	    		return;
	    	}

    		$this.addClass('loading');
    		$.get('../shopping_assistant/set_params_for_shopping_assistant_without_login.php', function(r){		    			
    			setTimeout(function(){
    				$this.find('.flipper').removeClass('rotate-flipper');
    				$this.removeClass('loading');
    			}, 400);

    			if($.trim(r) == "success"){
    				$('#myModal').modal('show').appendTo('body');
    			}else{
    				alert("Oops! something went wrong, refresh the page and try again.");
    			}
    		});
    	}
    </script>

	<link rel="stylesheet" href="../includes/plugins/particlesjs/particles.css">
	<script src="../includes/plugins/particlesjs/particles.min.js"></script>
	<script src="../includes/plugins/particlesjs/particles_app.js"></script>
	<style>
		#particles-js{
			width: auto;
			margin: -20px -20px 20px -20px;
			background: #382402 !important;
			position: relative;
		}
		#particles-js canvas{
			position: absolute;
			top:0;
			left: 0;
			z-index: 0;
		}
		@media only screen and (max-width: 768px) {
			#particles-js{
				margin: -20px -10px 20px -10px;
			}
		}
	</style>

	<link rel="stylesheet" href="../includes/plugins/button_flipper/button_flipper.css">
	<script src="../includes/plugins/button_flipper/button_flipper.js"></script>
	<style>
		.flipper-container{
			margin: auto;
  			font-size: 16px;
  		}
  		@media only screen and (max-width: 768px) {
			.flipper-container{
	  			font-size: 13px;
	  		}
		}
	</style>

<?php */ ?>		

<!--
/* ====================================================================== *
      BANNER
 * ====================================================================== */	    	
-->	

<?php /* ?>	

	<style>
		.banner_container{
			margin: 0 -20px !important;
			position: relative;
			height: 182px;
		}
		.banner_img{
			position: absolute;
			top: 0;
			left: 0;
			width: 50%;
			height: 100%;
			background-image: url('../includes/img/clothes.png');
		}
		.banner_content{
			position: absolute;
			top: 0;
			right: 0;
			width: 50%;
			height: 100%;
			text-align: center;
			background: #FF883E;
			color: #fff;
		}
		.banner_container_arrow_left{
			width: 0; 
			height: 0; 
			border-top: 91px solid #FF883E; 
			border-bottom: 91px solid #FF883E; 
			border-left: 91px solid transparent;
		    position:absolute;
		    left:-91px;
		}
		

		@media only screen and (max-width: 500px) {
			
		}
	</style>

	<br>
	
	<div class="banner_container">
		<div class="banner_img"></div>
		<div class="banner_content">
			<div class="banner_container_arrow_left"></div>	
			<h3>Some random text</h3>
			<p>some more text</p>
		</div>
	</div>

<?php */ ?>	

<!--
/* ====================================================================== *
      SIZE PICKER
 * ====================================================================== */	    	
-->	

<?php /* ?>	

	<div class="size_picker animate-on-load">
		
		<p style="font-size:15px;margin-bottom:20px;">
			Select your size to avail great discounts.
		</p>
		
		<div class="flipper-container" onclick="setTimeout(function(){ location.href='./?size=Small<?php echo $extra_param; ?>'; }, 300);">
		  <div class="flipper">
		    <div class="front-face"><span data-hover="Clicked">S</span></div>
		    <div class="back-face"><i class="fa fa-circle-o-notch fa-spin"></i></div>
		  </div>
		</div>

		<div class="flipper-container" onclick="setTimeout(function(){ location.href='./?size=Medium<?php echo $extra_param; ?>'; }, 300);">
		  <div class="flipper">
		    <div class="front-face"><span data-hover="Clicked">M</span></div>
		    <div class="back-face"><i class="fa fa-circle-o-notch fa-spin"></i></div>
		  </div>
		</div>

		<div class="flipper-container" onclick="setTimeout(function(){ location.href='./?size=Large<?php echo $extra_param; ?>'; }, 300);">
		  <div class="flipper">
		    <div class="front-face"><span data-hover="Clicked">L</span></div>
		    <div class="back-face"><i class="fa fa-circle-o-notch fa-spin"></i></div>
		  </div>
		</div>

		<div class="flipper-container" onclick="setTimeout(function(){ location.href='./?size=Extra Large<?php echo $extra_param; ?>'; }, 300);">
		  <div class="flipper xl_and_more">
		    <div class="front-face"><span data-hover="Clicked">XL and More</span></div>
		    <div class="back-face"><i class="fa fa-circle-o-notch fa-spin"></i></div>
		  </div>
		</div>

    </div>

    <style>
		.size_picker {
			margin: 0 -20px !important;
		}
		@media only screen and (max-width: 768px) {
			.size_picker{
				margin: 0 -10px !important;
			}
		}

    	.size_picker .flipper-container{
			height: auto;
			display: inline-block;
			margin: 0 5px;
    	}

		.size_picker .flipper{
			width: 40px;
			font-size: 14px;
			background: #91611A;
			height: 40px;
		}

		.size_picker .flipper>div{
			line-height: 40px;
			text-transform: none;
			font-size: 13px;
		}

		.size_picker .xl_and_more{
			width: 130px;
		}

		.size_picker .fa{
			line-height: 40px !important;
			text-shadow: none !important;
			box-shadow: none !important;
			letter-spacing: normal !important;
		}
    </style>

<?php */ ?>	    

<!--
/* ====================================================================== *
      TOP CONTAINER
 * ====================================================================== */	    	
-->	    

<style>
	.full_width_container{
		margin: -20px -10px 0px -10px;
	}
	.full_width_container::after {
	    content: "";
	    clear: both;
	    display: table;
	}
	@media only screen and (max-width: 972px) {
		.full_width_container{
			margin: -20px -0px 0px -0px;
		}
	}
	@media only screen and (max-width: 768px) {
		.full_width_container{
			margin: -20px -10px 0px -10px;
		}
	}
</style>

<div class="full_width_container">

	<br>

<!--
/* ====================================================================== *
      TAGS
 * ====================================================================== */	    	
-->	    

	<style>
		.tags_container{
			width: 250px;
			background: #fff;
			float: left;
			margin-right: 20px;

			-webkit-box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
    		   -moz-box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
    			 -o-box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
    			-ms-box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
    				box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
		}
		.tags_title,
		.sizepicker_title{
			text-align: center;
			padding: 5px 0px;
			font-size: 13px;
			color: rgba(0,0,0,.6);
		}
		.tags_title{
			color: rgb(9, 136, 110);
			cursor: pointer;
		}
		.tags_title span{
			display: none;
		}
		.super_tag{
			border-top: 1px dashed rgba(165, 42, 25, .23);
			padding: 9px 20px;
			font-size: 14px;
			color: #A52A19;
		}
		.super_tag_space{
			height: 10px;
		}
		.tags_container a{
			text-decoration: none !important;
		}
		.tag{
			margin-bottom: 0px;
			padding: 3px 20px;
			font-size: 12px;
			color: rgba(0,0,0,.6);
			font-weight: 300;
		}
		.tag:hover{
			background: #EDEDED;
		}
		.tag img{
			opacity:0.7;
			
		}
		.tag span{
			min-width: 24px;
			margin-right: 8px;
			display: inline-block;
		}

		@media only screen and (max-width: 1000px) {
			.tags_container{
				width: 200px;
			}
		}
		@media only screen and (max-width: 768px) {
			.tags_container{
				width: 100%;
				float: none;
				padding: 5px;
				margin-bottom: 20px;
			}
			.tags_title span{
				display: inline-block;
			}
		}

		.lucky_size{
			display: inline-block;
			padding: 2px 8px;
			border-radius: 100px;
			background: #A52A19;
			color: white !important;
			text-decoration: none !important;
		}
	</style>	

	
	<?php
		$super_tags = $tag_super->get_all_by_gender($user_gender, "order by 1");
	?>

	<div class="tags_container">
		<div class="tags_title">SHOP BY CATEGORY &nbsp;<span class="fa fa-chevron-down"></span></div>	
		
		<div class="all_tags">
			<div class="sizepicker_title">GREAT DISCOUNTS FOR LUCKY SIZE</div>
	
			<div class="text-center">
				<a href="./?size=Small<?php echo $extra_param; ?>" class="lucky_size">S</a>
				<a href="./?size=Medium<?php echo $extra_param; ?>" class="lucky_size">M</a>
				<a href="./?size=Large<?php echo $extra_param; ?>" class="lucky_size">L</a>
				<a href="./?size=Extra Large<?php echo $extra_param; ?>" class="lucky_size">XL and More</a>
			</div>

			<br>

			<?php foreach ($super_tags as $row_supertag) { ?>
				<div class="super_tag"><?php echo $row_supertag->get_name(); ?></div>
				
				<?php $tags = $tag->get_list_by_tag_super($row_supertag->get_id_tag_super(), " order by 1 "); ?>
				<?php foreach ($tags as $row_tags) { ?>
	            	<a href="./?id_tag=<?php echo $row_tags->get_id_tag(); ?><?php echo $extra_param; ?>">
	            		<div class="tag">
	            			<span>
	            				<img src="<?php echo $row_tags->get_image(); ?>" height="30px">
	            			</span>
	            			<?php echo $row_tags->get_name(); ?>
	            		</div>
					</a>
			    <?php } ?>

			    <div class="super_tag_space"></div>

			<?php } ?>
		</div>
	</div>

	<script>
		$(document).ready(function(){

			function viewport() {
	            var e = window, a = 'inner';
	            if (!('innerWidth' in window )) {
	                a = 'client';
	                e = document.documentElement || document.body;
	            }
	            return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
	        }

	        if(viewport().width < 768){
	        	$('.all_tags').hide();
	        	$('.tags_title').css('cursor', 'pointer');
	        }

			$('.tags_title').on('click', function(){
				if(viewport().width < 768){
					$('.all_tags').slideToggle();
				}
			})

			$(window).resize(function(){
				if(viewport().width < 768){
					//$('.all_tags').hide();	
				}else{
					$('.all_tags').show();	
				}
			});

		});
	</script>

<!--
/* ====================================================================== *
      NIVO SLIDER
 * ====================================================================== */	    	
-->

	<link rel="stylesheet" type="text/css" href="../includes/plugins/nivo slider/themes/default/default.css" />
	<link rel="stylesheet" type="text/css" href="../includes/plugins/nivo slider/themes/light/light.css" />
	<link rel="stylesheet" type="text/css" href="../includes/plugins/nivo slider/themes/dark/dark.css" />
	<link rel="stylesheet" type="text/css" href="../includes/plugins/nivo slider/themes/bar/bar.css" />
	<link rel="stylesheet" type="text/css" href="../includes/plugins/nivo slider/nivo-slider.css" />
	<style>
		.theme-default{
			position: relative;
			float: right;
			width: calc(100% - 270px);
			margin-bottom: 20px;
		}
		
		@media only screen and (max-width: 1000px) {
			.theme-default{
				float: right;
				width: calc(100% - 220px);
			}
		}

		@media only screen and (max-width: 768px) {
			.theme-default{
				float: none;
				width: 100%;
			}
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

	<div class="theme-default">
		<div id="slider">
			<img src="http://miracas.com/ZS/assets/home2.jpg">
			<img src="http://miracas.com/ZS/assets/home1.jpg" >
		</div>
	</div>
		
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

<!--
/* ====================================================================== *
      ESPRESSO KEYWORDS
 * ====================================================================== */	    	
-->	    	

	<style>
		.espresso_keyword_container_scroll a{
			text-decoration: none !important;
			color: #5d5d5d !important;
		}
		.espresso_keyword_container{
			float: right;
			width: calc(100% - 270px);
			padding-left: 10px;
			background: white;

			-webkit-box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
		    -moz-box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
		    -o-box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
		    -ms-box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
		    box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
		}
		.espresso_keyword{
			width: 25%;
			float: left;
		}
		
		@media only screen and (max-width: 1100px) {
			.espresso_keyword{
				width: 50%;
			}
		}

		@media only screen and (max-width: 950px) {
			.espresso_keyword{
				width: 50%;
			}
		}

		@media only screen and (max-width: 768px) {
			.espresso_keyword{
				width: 25%;
			}
		}

		.espresso_keyword_img_container{
			background-image: url('../includes/plugins/Media Boxes/plugin/css/icons/loading-image.gif');
			background-position: center center;
			background-repeat: no-repeat;
			padding: 10px 0px;
			padding-right: 10px;
			background-color: #fff;
			position: relative;
		}
		.espresso_keyword_img{
			width: 100%;
			height: 100%;
			background-size: 100%;
    		background-color: black;
			background-position: top center;
			background-repeat: no-repeat;
		}
		.espresso_keyword .espresso_keyword_img_container_fullheight{
			padding: 0 !important;
		}
		.espresso_keyword_img_container_fullheight .espresso_keyword_img{
			background-size: cover !important;
		}
		.espresso_keyword_img_text{
			/*
			position: absolute;
			bottom: 0;
			left: 0;
			*/
			margin-top: -10px;
			background-color: #fff;
			width: 100%;
			text-align: center;
			padding: 0px 15px;
			font-weight: 300;
			font-size: 14px;
			color: rgba(65,65,65,.87);
			overflow: hidden;
		    line-height: 50px;
		    height: 50px;
		}
		.espresso_keyword_img_percentage{
			width: 90px;
			height: 90px;
			position: absolute;
			top: 50%;
			left: 50%;
			margin-left: -45px;
			margin-top: -70px;
			border-radius: 50%;
			background: rgba(0,0,0,.4);
			color: #fff;
			text-align: center;
			padding: 5px;
		}
		.espresso_keyword_img_percentage_content{
			border: 4px solid rgba(255,255,255,.6);
			border-radius: 50%;
			width: 100%;
			height: 100%;
			display: table;
		}
		.espresso_keyword_img_percentage_content_cell{
			display: table-cell;
  			vertical-align: middle;
  			font-size: 12px;
  			line-height: 17px;
  			color: rgba(255,255,255,.87);
  			font-weight: 300;
		}
		.espresso_keyword_img_percentage_content_cell span{
			display: block;
			font-size: 20px;
			line-height: 20px;
			margin-bottom: -2px;
			color: rgba(255,255,255,.87);
		}
		.espresso_keyword_img_calendar{
			position: absolute;
			width: 65px;
			top: 30px;
			left: 30px;
		}
		.espresso_keyword_img_month{
			width: 100%;
			background: #FC7373;
			padding: 3px;
			text-align: center;
			color: rgba(255,255,255,.87);
			font-size: 13px;
		}
		.espresso_keyword_img_day{
			background: #fff;
			font-weight: 300;
			color: rgba(3,3,3.87);;
			padding: 5px;
			text-align: center;
			font-size: 19px;
		}

		.espresso_quote{
			float: right;
			width: calc(100% - 270px);
			text-align: center;
			margin: 20px 0;
			font-style: italic;
			color: rgba(93, 93, 93, 0.65) !important;
		}

		.espresso_banner{
			float: right;
			width: calc(100% - 270px);
			text-align: center;
			margin: 20px 0;
			margin-top: 0;
			display: none;
		}
		.espresso_banner img{
			width: 100%;
		}

		@media only screen and (max-width: 1150px) {
			.espresso_keyword_img_calendar{
				position: absolute;
				width: 60px;
			}
			.espresso_keyword_img_month{
				font-size: 12px;
			}
			.espresso_keyword_img_day{
				font-size: 18px;
			}
		}

		@media only screen and (max-width: 1000px) {
			.espresso_keyword_container{
				float: right;
				width: calc(100% - 220px);
			}

			.espresso_keyword_img_calendar{
				position: absolute;
				width: 55px;
			}
			.espresso_keyword_img_month{
				font-size: 11px;
			}
			.espresso_keyword_img_day{
				font-size: 17px;
			}
		}

		@media only screen and (max-width: 850px) {
			.espresso_keyword_img_calendar{
				position: absolute;
				width: 45px;
			}
			.espresso_keyword_img_month{
				font-size: 10px;
			}
			.espresso_keyword_img_day{
				font-size: 15px;
			}
		}
		
		@media only screen and (max-width: 768px) {
			.espresso_keyword_container_scroll{
				overflow-y: hidden;
				overflow-x: scroll;
			}
			.espresso_keyword_container{
				width: 290%;
				float: none;
			}
			.espresso_quote{
				float: none;
				width: 100%;
			}

			.espresso_banner{
				float: none;
				width: 100%;	
				display: block;
			}
			.hide_on_mobile{
				display: none;
			}

			.espresso_keyword_img_calendar{
				position: absolute;
				width: 55px;
			}
			.espresso_keyword_img_month{
				font-size: 11px;
			}
			.espresso_keyword_img_day{
				font-size: 17px;
			}
		}
	</style>

	<?php $latest_3_active = $espresso_keywords->get_lastest_3_active($user_gender_slash, $user_age_slash); ?>

	<div class="espresso_keyword_container_scroll" data-slideout-ignore>
		<div class="espresso_keyword_container">
			<?php foreach ($latest_3_active as $row) { ?>
				<?php 
					$percentage = 0;
					if($row->get_booking_threshold() > 0){
						$percentage = number_format( ($row->get_booking_count()/$row->get_booking_threshold()) * 100 , 0);
					}
				?>
				<div class="espresso_keyword">
					<div class="espresso_keyword_img_container" >
						<div class="espresso_keyword_img" style="background-image:url('<?php echo $row->get_image(); ?>');"></div>
						<div class="espresso_keyword_img_percentage">
							<div class="espresso_keyword_img_percentage_content">
								<div class="espresso_keyword_img_percentage_content_cell">
									<span><?php echo $percentage; ?>%</span>Booked
								</div>
							</div>
						</div>
					</div>
					<div class="espresso_keyword_img_text">
						<div class="row">
							<div class="col-sm-7">
								<?php echo $row->get_keyword(); ?>								
							</div>	
							<div class="col-sm-5" style="color: #2da72d;">
								<?php echo $row->get_discount(); ?>% off	
							</div>
						</div>
					</div>
				</div>
			<?php } ?>

			<div class="espresso_keyword hide_on_mobile">
				<a href="https://play.google.com/store/apps/details?id=com.miracas&hl=en">
					<div class="espresso_keyword_img_container espresso_keyword_img_container_fullheight" >
						<div class="espresso_keyword_img" style="background-image:url('../includes/img/android_app.png');"></div>
					</div>
				</a>		
			</div>
		</div>
	</div>

	<div class="espresso_banner">
		<a href="https://play.google.com/store/apps/details?id=com.miracas&hl=en">
			<img src="../includes/img/banner_1.png" alt="">
		</a>
	</div>
	
	<div class="espresso_quote">
		"I like nice clothes, whether they're dodgy or not"<br>
		- David Beckham
	</div>

	<?php $lastest_7_created = $espresso_keywords->get_lastest_3_created($user_gender_slash, $user_age_slash); ?>

	<div class="espresso_keyword_container_scroll" data-slideout-ignore>
		<a href="../calendar?c=1<?php echo $extra_param; ?>">
			<div class="espresso_keyword_container">
				<?php foreach ($lastest_7_created as $row) { ?>
					<?php 
						$timestamp = strtotime($row->get_created_at());
					?>
					<div class="espresso_keyword">
						<div class="espresso_keyword_img_container" >
							<div class="espresso_keyword_img" style="background-image:url('<?php echo $row->get_image(); ?>');"></div>
							<div class="espresso_keyword_img_calendar">
								<div class="espresso_keyword_img_month"><?php echo date("F", $timestamp); ?></div>
								<div class="espresso_keyword_img_day"><?php echo date("d", $timestamp); ?></div>
							</div>
						</div>
						<div class="espresso_keyword_img_text"><?php echo $row->get_keyword(); ?></div>
					</div>
				<?php } ?>

				<div class="espresso_keyword hide_on_mobile">
						<a href="../calendar?c=1<?php echo $extra_param; ?>">
	
	<div class="espresso_keyword_img_container espresso_keyword_img_container_fullheight" style="/*padding-top:10px !important;*/">
							<div class="espresso_keyword_img" style="background-image:url('../includes/img/new_designs_every_day.png');"></div>
						</div>
					</a>		
				</div>
			</div>
		</a>
	</div>

	<div class="espresso_banner">
		<a href="../calendar?c=1<?php echo $extra_param; ?>">

		<img src="../includes/img/banner_2.png" alt="">
		</a>
	</div>

	<script>
		function resize_espresso_keywords(){
	    	var espresso_keyword_img_container 		= $('.espresso_keyword').find('.espresso_keyword_img_container').eq(0);
	    	var espresso_keyword_img_container_w	= espresso_keyword_img_container.outerWidth(true);

	    	var ratio_w    	= 1;
            var ratio_h   	= 1;

            var newWidth    = espresso_keyword_img_container_w;
            var newHeight   = (ratio_h * newWidth)/ratio_w;

	    	var css 		= 	" .espresso_keyword .espresso_keyword_img_container{ "+ 
    						  	" 	height: "+newHeight+"px !important; "+ 
    						  	" } ";

			css 			+= 	" .espresso_keyword .espresso_keyword_img_container_fullheight{ "+
	    						" 	height: "+ (espresso_keyword_img_container_w+40) +"px !important; "+
	    						" } ";    						  	

	    	var $stylesheet = $('<style type="text/css" media="screen" />').html(css);
			$('body').append($stylesheet);
	    }

	    setTimeout(function(){
	    	resize_espresso_keywords();
	    }, 1);

	    $(window).resize(function(){
	    	setTimeout(function(){
	    		resize_espresso_keywords();
	    	}, 1);
	    });
	</script>

</div>

<!--
/* ====================================================================== *
      CATEGORY KEYWORDS (KEYWORDS THAT STARTS WITH THE WORD "CATEGORY")
 * ====================================================================== */	    	

<?php /* ?>	

	<div class="keywords-container category-keywords animate-on-load">
		
		<?php foreach ($category_keywords as $value) { ?>
			<div class="keyword-item">
			 	<div>
			 		<div class="center_out"><div class="center_in">
					 	<a href="./?q=<?php echo $value; ?><?php echo $extra_param; ?>">
			            	<div class="keyword-image">
				                <img src="<?php echo $keyword->get_image_from_keyword($value); ?>">
				            </div>
			                <?php echo trim(substr($value, 8)); ?>
			            </a>
			        </div></div>
	        	</div>
	        </div>
		<?php } ?>

    </div>

<?php */ ?>	

<!--
/* ====================================================================== *
      POPULAR KEYWORDS
 * ====================================================================== */		
-->

	<style>
		.keyword_title{
			border-bottom: 1px solid rgba(151, 151, 151, .5);;
			display: inline-block;
			width: 450px;
			max-width: 100% !important;
			text-align: center;
			margin: 40px auto;
			font-size: 18px;
			color: rgba(0,0,0,.47);
			padding: 5px 0;
		}

		.popular_keyword_container{
			text-align: center;
		}
		.popular_keyword_container img{
			cursor: pointer;
			border-radius: 50%;
			display: inline-block;
			width: 200px;
			margin: 10px 10px;
			background: #fff;

			-webkit-box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
    		   -moz-box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
    			 -o-box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
    			-ms-box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
    				box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
		}
	</style>
	
	<div class="text-center">
		<div class="keyword_title animate-on-load">EXCLUSIVE INTERNATIONAL BRANDS</div>
	</div>

	<div class="popular_keyword_container">
		<?php foreach ($popular_keywords as $value) { ?>
			<img onclick="location.href='./?q=<?php echo $value; ?><?php echo $extra_param; ?>&collections=yes';" src="<?php echo $keyword->get_image_from_keyword($value); ?>" alt="">
		<?php } ?>
	</div>

<!--
/* ====================================================================== *
      PIXEL KEYWORDS 
 * ====================================================================== */		
-->

	<style>
		.future_fashion_container{
			margin-top: 30px;
			padding: 20px 5px;
			background: white;

			-webkit-box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
		    -moz-box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
		    -o-box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
		    -ms-box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
		    box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
		}
		.future_fashion_container .keyword_title{
			margin-top: 0px;
			margin-bottom: 20px;
		}
		.pixel_keyword_container{
			display: block;
			width: 100%;
			margin-bottom: 30px;
			cursor: pointer;
		}
		.pixel_keyword_container::after {
		    content: "";
		    clear: both;
		    display: table;
		}
		.pixel_keyword{
			width: 20%;
			float: left;
		}
		.pixel_keyword_img_container{
			background-image: url('../includes/plugins/Media Boxes/plugin/css/icons/loading-image.gif');
			background-position: center center;
			background-repeat: no-repeat;
			padding: 10px 5px;
			background-color: #fff;
			position: relative;
		}
		.pixel_keyword_img_container_fullheight .pixel_keyword_img{
			background-size: cover !important;
		}
		.pixel_keyword_img{
			width: 100%;
			height: 100%;
			background-size: 100%;
    		background-color: black;
			background-position: top center;
			background-repeat: no-repeat;
		}
		.pixel_keyword_img_text{
			/*
			position: absolute;
			bottom: 0;
			left: 0;
			*/
			margin-top: -10px;
			background-color: #fff;
			width: 100%;
			text-align: center;
			font-size: 14px;
			padding: 0px 10px;
			overflow: hidden;
		    line-height: 50px;
		    height: 50px;
		}
		.pixel_keyword_img_circle{
			width: 90px;
			height: 90px;
			position: absolute;
			top: 50%;
			left: 50%;
			margin-left: -45px;
			margin-top: -45px;
			border-radius: 50%;
			background: rgba(255,255,255,.75);
			text-align: center;
			padding: 5px;
			display: table;
		}
		.pixel_keyword_img_circle_cell{
			display: table-cell;
			vertical-align: middle;
  			font-size: 17px;
  			color: rgba(0,0,0,.6);
		}
		.pixel_keyword_img_circle_cell span{
			display: block;
			font-size: 13px;
		}
		
		@media only screen and (max-width: 768px) {
			.pixel_keyword_container_scroll{
				overflow-y: hidden;
				overflow-x: scroll;
			}
			.pixel_keyword_container{
				width: 240%;
				float: none;
			}
		}
	</style>

	<div class="future_fashion_container">

		<div class="text-center">
			<div class="keyword_title animate-on-load">FUTURE FASHION</div>
		</div>	

		<?php foreach ($pixel_keyword_list as $row) { ?>
			<?php 
				$now 		= time();
				$timestamp 	= strtotime($row->get_expiry_date());
				$datediff 	= $timestamp-$now;

				$days 		= round($datediff / (60 * 60 * 24));
			?>
			<div class="pixel_keyword_container_scroll" data-slideout-ignore>
				<div class="pixel_keyword_container" onclick="location.href='../pixels/?pixel_keyword=yes&q=<?php echo $row->get_pixel_keyword(); ?><?php echo $extra_param; ?>';">

					<div class="pixel_keyword">
						<div class="pixel_keyword_img_container pixel_keyword_img_container_fullheight" >
							<div class="pixel_keyword_img" style="background-image:url('<?php echo $row->get_image(); ?>');"></div>
							<div class="pixel_keyword_img_circle">
								<div class="pixel_keyword_img_circle_cell">
									<span>Live in</span>
									<?php echo $days; ?> days
								</div>
							</div>
						</div>
					</div>

					<?php 
						$get_pixels 	= $pixel->get_list_by_pixel_keyword($row->get_pixel_keyword(), " order by pixel_count desc ");
						$cont 			= 0;

					 	foreach ($get_pixels as $row_pixel) { 
					 		$cont++;
					 		if($cont > 4) break;

					 		$current_image_link = $row_pixel->get_image_link();
							$current_image_link = str_replace(".search", "", $current_image_link);
							$current_image_link = str_replace(".220*220", "", $current_image_link);
					 ?>
							<div class="pixel_keyword">
								<div class="pixel_keyword_img_container" >
									<div class="pixel_keyword_img" style="background-image:url('<?php echo $current_image_link; ?>');"></div>
								</div>
								<div class="pixel_keyword_img_text"><?php echo $row_pixel->get_name(); ?></div>
							</div>
					<?php 
						} 
					?>

				</div>
			</div>

		<?php } ?>

	</div>

	<script>
		function resize_pixel_keywords(){
	    	var pixel_keyword_img_container 		= $('.pixel_keyword').find('.pixel_keyword_img_container').eq(0);
	    	var pixel_keyword_img_container_w		= pixel_keyword_img_container.outerWidth(true);

	    	var ratio_w    	= 1;
            var ratio_h   	= 1;

            var newWidth    = pixel_keyword_img_container_w;
            var newHeight   = (ratio_h * newWidth)/ratio_w;

	    	var css 		= 	" .pixel_keyword .pixel_keyword_img_container{ "+ 
    						  	" 	height: "+newHeight+"px !important; "+ 
    						  	" } ";

    		css 			+= 	" .pixel_keyword .pixel_keyword_img_container_fullheight{ "+
	    						" 	height: "+ (pixel_keyword_img_container_w+40) +"px !important; "+
	    						" } ";    						  	

	    	var $stylesheet = $('<style type="text/css" media="screen" />').html(css);
			$('body').append($stylesheet);
	    }

	    setTimeout(function(){
	    	resize_pixel_keywords();
	    }, 1);

	    $(window).resize(function(){
	    	setTimeout(function(){
	    		resize_pixel_keywords();
	    	}, 1);
	    });
	</script>

<!--
/* ====================================================================== *
      PIXEL KEYWORDS OLD
 * ====================================================================== */		
-->

<?php /* ?>

	<div class="or-spacer animate-on-load">
	  <div class="mask"></div>
	  <span><i>Future Fashion</i></span>
	</div>

	<style>
		.pixel-keywords .keyword-text{
			color: white !important;
		}
		.keyword_pixel_name{
			font-size:14px;
		}
		@media only screen and (max-width: 768px) {
			.keyword_pixel_name{
				text-align: center !important;
				padding-bottom: 10px;
			}

			.mask{
				padding-left: 20px !important;
				padding-right: 20px !important;
			}

		}	
		.keywords-container{
			margin: 0;
		}
	</style>

	<div class="keywords-container pixel-keywords animate-on-load">
	<?php $i=0; ?>
	<?php foreach ($pixel_keyword_list as $row) { ?>
		<?php $i++; ?>
		<div class="keyword-item">
		 	<div>
			 	<a href="../pixels/?pixel_keyword=yes&q=<?php echo $row->get_pixel_keyword(); ?><?php echo $extra_param; ?>">
		            <div class="keyword-image">
		                <img src="<?php echo $row->get_image(); ?>">
		            </div>

		            <div class="keyword-text">
		            	<div class="row">
		            		<div class="col-sm-5 text-left">

		            			<div style="display: table; height: 34px; overflow: hidden;width:100%;">
     								<div style="display: table-cell; vertical-align: middle;" class="keyword_pixel_name">
		            					<?php echo $row->get_pixel_keyword(); ?>
		            				</div>
		            			</div>

		            		</div>
		            		<div class="col-sm-7 text-right">
		            			<div id="countdown_<?php echo $i; ?>"></div>
								<script>
									var tillDate = new Date('<?php echo $row->get_expiry_date(); ?>');
									tillDate.setHours(0,0,0,0);
									$('#countdown_<?php echo $i; ?>').countdown({until: tillDate, format:'DHMS'});
								</script>

		            		</div>
		            	</div>
		            </div>
	            </a>
        	</div>
        </div>
	<?php } ?>
	</div>	

<?php */ ?>

<!--
/* ====================================================================== *
      BRAND STORES (KEYWORDS THAT STARTS WITH THE WORD "BRAND")
 * ====================================================================== */	    

<?php /* ?>	

<!--
	<div class="or-spacer animate-on-load">
	  <div class="mask"></div>
	  <span><i>Brand stores</i></span>
	</div>

	<div class="keywords-container brand-keywords animate-on-load">
		
		<?php for ($i=0; $i<4; $i++) { ?>
			<?php 
				$value = "";
				if(!isset($brand_keywords[$i])){
					break;
				}else{
					$value = $brand_keywords[$i];
				}
			?>

			<?php
				foreach ($brand_static as $row) {
					if($row[0] == ($i+1)){
						echo '<div class="keyword-item">
							 	<div class="keyword-static-block" style="background:'.$row[1].' !important;">
							 		<div class="keyword-static-block-title">'.$row[2].'</div>		
								 	'.$row[3].'
					        	</div>
					        </div>';
					}
				}
			?>
			<div class="keyword-item">
				<div>
				 	<a href="./?q=<?php echo $value; ?><?php echo $extra_param; ?>">
				        <div class="keyword-image">
				            <img src="<?php echo $keyword->get_image_from_keyword($value); ?>">
				        </div>
				    </a>
				</div>
		    </div>
		<?php } ?>

		<?php if(count($brand_keywords)>=5){ ?>
			<div class="keyword-item">
			 	<div class="keyword-brand-list">
				 	<?php for ($i=4; $i<count($brand_keywords); $i++) { ?>
				 		<div>
				 			href="./?q=<?php echo $brand_keywords[$i]; ?><?php echo $extra_param; ?><?php echo $brand_keywords[$i]; ?></a>
				 		</div>
				 	<?php } ?>
	        	</div>
	        </div>
        <?php } ?>

	</div>

<?php */ ?>	

<!--
/* ====================================================================== *
      STYLE STORES (THE NORMAL KEYWORDS) WITH COLLECTIONS
 * ====================================================================== */		
-->

<?php /* ?>	
	<style>

		.keyword_with_collection_border{
			border: 1px gray;
			margin-bottom: 10px;
			padding: 10px;
			cursor: pointer;
			overflow: hidden;
		}

		.keyword_with_collection{
			height: auto;
			margin: 0 -5px;
		}

		.keyword_with_collection-card{
			height: 100%;
			width: 100%;
			padding: 10px;
    		background: #f3e4ca;
			-webkit-box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
		    -moz-box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
		    -o-box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
		    -ms-box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
		    box-shadow: 0 1px 2px rgba(34,25,25,0.6) !important;
		}

		@media only screen and (max-width: 500px) {
			.keyword_with_collection_container{
				overflow-y: scroll;
			}	

			.keyword_with_collection_border{
				width: 190%;
			}
		}

		.keyword_with_collection-column{
			width: 16.66%;
			float: left;
			position: relative;
			padding: 0 5px;
		}

		@media only screen and (max-width: 1000px) {
			.keyword_with_collection-column{
				width: 20%;
			}
		}

		@media only screen and (max-width: 768px) {
			.keyword_with_collection-column{
				width: 25%;
			}
		}

		@media only screen and (max-width: 500px) {
			.keyword_with_collection-column{
				width: 33%;
			}
		}

		.keyword_with_collection-image{
			width: 100%;
			height: 100%;
			background-size: cover;
			position: relative;
		}

		.keyword_with_collection-text{
			position: absolute;
			bottom: 0px;
			width: 100%;
			padding: 20px 10px;
			background-color: rgba(171, 119, 42, 0.75);
			color: white;
			text-align: center;
		}

		@media only screen and (max-width: 500px) {
			.keyword_with_collection-text{
				font-size: 12px;
    			padding: 7px 5px;
			}
		}

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
			background-image: url('../includes/plugins/Media Boxes/plugin/css/icons/loading-image.gif');
			background-position: center center;
			background-repeat: no-repeat;
		}
		.collection_img>div{
			width: 100%;
			height: 100%;
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

	<div class="or-spacer animate-on-load">
	  	<div class="mask"></div>
	  	<span><i>Style stores</i></span>
	</div>

	<div class="animate-on-load">
		<?php foreach ($popular_keywords as $value) { ?>
			<div class="keyword_with_collection_container">
				<div class="keyword_with_collection_border" onclick="location.href='./?q=<?php echo $value; ?><?php echo $extra_param; ?>&collections=yes';">
					<div class="keyword_with_collection">

					 	<div class="keyword_with_collection-column keyword_with_collection-column-first">
					 		<div class="keyword_with_collection-card">
					            <div class="keyword_with_collection-image" style="background-image:url(<?php echo $keyword->get_image_from_keyword($value); ?>);">
					                <div class="keyword_with_collection-text"><?php echo $value; ?></div>
					            </div>
				        	</div>
			        	</div>
			        

						<?php 
							// ======================================================================
							//    	COLLECTIONS
							// ====================================================================== 

							$products 	= $product->get_search($value, $user_gender_slash, $user_age_slash, " order by like_count desc ");
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

													$product->map($value[$i]['id_product']);

													$id_product_prestashop 	= $product->get_id_product_prestashop();
													$img_link_last_chars 	= substr($product->get_image_link(), strrpos($product->get_image_link(), '/') + 1);
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
			</div>
		<?php } ?>
	</div>

	<!-- #### RESIZE IMAGES #### -->

	<script>
		function resize_keyword_with_collections(){

			// MAKE THE HEIGHT OF THE IMAGES OF THE COLLECTIONS MATCH THEIR WIDTH 

	    	var collection_img 		= $('.keyword_with_collection').find('.collection_img').eq(0);
	    	var collection_img_w	= collection_img.outerWidth(true);

	    	var css 				= 	" .keyword_with_collection .collection_img{ "+ 
		    						  	" 	height: "+collection_img_w+"px !important; "+ 
		    						  	" } ";

	    	var $stylesheet 		= $('<style type="text/css" media="screen" />').html(css);
			$('body').append($stylesheet);

			// MAKE THE FIRST COLUMN MATCH THE HEIGHT OF THE OTHER COLUMNS 

			var collection_column 	= $('.keyword_with_collection').find('.keyword_with_collection-column').eq(1);
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
				hide_show_column(3);
			}else if(viewport().width > 768 && viewport().width <= 1000){
				hide_show_column(4);
			}else{
				hide_show_column(5);
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

<?php */ ?>

<!--
/* ====================================================================== *
      STYLE STORES (THE NORMAL KEYWORDS)
 * ====================================================================== */		
-->

<?php /* ?>	

	<div class="or-spacer animate-on-load">
	  	<div class="mask"></div>
	  	<span><i>Style stores</i></span>
	</div>

	<div class="keywords-container style-keywords animate-on-load">
		<?php $i=0; ?>
		<?php foreach ($style_keywords as $value) { ?>

			<?php
				foreach ($style_static as $row) {
					if($row[0] == ($i+1)){
						echo '<div class="keyword-item">
							 	<div class="keyword-static-block" style="background:'.$row[1].' !important;">
							 		<div class="keyword-static-block-title">'.$row[2].'</div>		
								 	'.$row[3].'
					        	</div>
					        </div>';
					}
				}
				$i++;
			?>
			<div class="keyword-item">
			 	<div>
				 	<a href="./?q=<?php echo $value; ?><?php echo $extra_param; ?>">
			            <div class="keyword-image">
			                <img src="<?php echo $keyword->get_image_from_keyword($value); ?>">
			            </div>

			            <div class="keyword-text">
			                <?php echo $value; ?>
			            </div>
		            </a>
	        	</div>
	        </div>
		<?php } ?>
	</div>

<?php */ ?>		

<!--
/* ====================================================================== *
      OTHERS SAVED PRODUCTS
 * ====================================================================== */	    	
-->

<?php /* ?>	

	<?php 
		$fb_user_list = $fb_user_product_save->get_list_users($user_gender, " order by date desc ");
	?>

	<style>
		.others_saved_products .media-box-content{
			padding: 0 !important;
		}
		.others_header{
		    font-size: 15px;
		    text-align: left;
		    background: #FBEED8;
		    overflow: hidden;
		    padding: 10px;
		}
		.others_badge{
			position: absolute;
			top: 18px;
			right: 0;
			width: 60px;
			color: #6D4D1C;
    		background-color: #DEC395;
			padding: 7px;
			padding-left: 25px;
		}
		.others_badge:after{
		    content:"";
		    display:inline-block;
		    position:absolute;
		    border:16px solid #FBEED8;
		    border-bottom-width: 24px;
    		border-top-width: 24px;
		    border-color:transparent transparent transparent #FBEED8;
		    top:-8px;
		    left:-1px;
		}
		.fb_user_profile_img{
			width: 50px !important;
			height: 50px !important;
			display: inline-block;
			vertical-align: middle;
			margin-right: 10px;
			background-color: black;
			background-image: url('../includes/plugins/Media Boxes/plugin/css/icons/loading-image.gif');
			background-position: center center;
			background-repeat: no-repeat;
		}
		.fb_user_profile_img>div{
			width: 100%;
			height: 100%;
			background-size:100% auto;
		}
		.item_img:first-child{
			width: 75%;
		}
		.item_img{
			float: left;
			width: 25%;
			background-color: black;
			background-image: url('../includes/plugins/Media Boxes/plugin/css/icons/loading-image.gif');
			background-position: center center;
			background-repeat: no-repeat;
		}
		.item_img>div{
			width: 100%;
			height: 100%;
			background-size:100% auto;
		}
	</style>

	<div class="or-spacer animate-on-load">
	  <div class="mask"></div>
	  <span><i>Curated Collections</i></span>
	</div>

	<div class="others_saved_products animate-on-load">

		<div id="grid_others_saved_products"> 
			<?php 
				foreach ($fb_user_list as $row) { 
					if($row->get_id_fb_user() == $id_fb_user)continue;

					$fb_profile_img 	= "http://graph.facebook.com/".$row->get_id_fb_user()."/picture?width=80&height=80";
					$few_products 		= $fb_user_product_save->get_few_products($row->get_id_fb_user());
			?>
					<div class="media-box" style="display:none;cursor:pointer;" onclick="location.href='./?usr=<?php echo $row->get_id_fb_user(); ?><?php echo $extra_param; ?>';">
						<div class="media-box-content">
							<div class="others_header">
								<div class="fb_user_profile_img"><div style="background-image:url('<?php echo $fb_profile_img; ?>');"></div></div>
								<?php echo $row->get_id_product(); ?>
								<div class="others_badge"><?php echo $fb_user_product_save->get_product_count($row->get_id_fb_user()); ?></div>
							</div>
							<div class="others_grid">
								<?php 
									$cont = 0;
									foreach ($few_products as $row_inner) { 
										$cont++;
										$product->map($row_inner->get_id_product());

										$id_product_prestashop 	= $product->get_id_product_prestashop();
										$img_link_last_chars 	= substr($product->get_image_link(), strrpos($product->get_image_link(), '/') + 1);
										$id_image 				= str_replace(".jpg", "", $img_link_last_chars); // get the 75 from this: http://url.com/9-75/75.jpg
										$img_url 				= "http://miracas.miracaslifestyle.netdna-cdn.com/$id_product_prestashop-$id_image-thickbox/$id_image.jpg";

										if($cont>1){
											$img_url 			= "http://miracas.miracaslifestyle.netdna-cdn.com/$id_product_prestashop-$id_image-home/$id_image.jpg";											
										}
								?>
									<div class="item_img"><div style="background-image:url('<?php echo $img_url; ?>');"></div></div>	
								<?php 
									} 
								?>
							</div>
						</div>
					</div>
			<?php 
				} 
			?>
		</div>

    </div>

    <script>
    setTimeout(function(){

		$('#grid_others_saved_products').mediaBoxes({
	    	columns: 4,
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

	    function resize_grid_items(){
	    	var big_image 			= $('.others_grid').eq(0).find('.item_img').eq(0);
	    	var big_image_h			= big_image.outerWidth(true)-5;
	    	var small_image_h 		= ( (big_image_h)/3 ).toString().match(/^-?\d+(?:\.\d{0,2})?/)[0]
	    	var last_small_image_h 	= (big_image_h - (small_image_h*2)) + .01;

	    	//console.log(big_image_h+" => "+small_image_h+" => "+last_small_image_h);

	    	var css 			= " .others_grid .item_img:first-child{ "+ 
	    						  " 	height: "+big_image_h+"px !important; "+ 
	    						  " } " + 
	    						  "	.others_grid .item_img{ "+ 
	    						  "		height: "+small_image_h+"px !important; "+ 
	    						  " }"+
	    						  "	.others_grid .item_img:last-child{ "+ 
	    						  "		height: "+last_small_image_h+"px !important; "+ 
	    						  " }";

	    	var $stylesheet = $('<style type="text/css" media="screen" />');
			$stylesheet.html(css);
			$('body').append($stylesheet);
	    	
	    	$('#grid_others_saved_products').mediaBoxes('resize');
	    }

	    setTimeout(function(){
	    	// this is also executed after the Media Boxes finish loading, because sometimes it doesn't load on time so it doesn't resize correctly
	    	resize_grid_items();
	    }, 300);

	    $(window).resize(function(){
	    	setTimeout(function(){
	    		resize_grid_items();
	    	}, 300);
	    });

	}, 600);    
    </script>	

<?php */ ?>	

<!--
/* ====================================================================== *
      RESIZE THE RATIO
 * ====================================================================== */	
-->

	<style>
		.brand-keywords .keyword-item>div,
		.category-keywords .keyword-item>div,
		.pixel-keywords .keyword-item .keyword-image,
		.tags-items .keyword-item .keyword-image{
			overflow: hidden;
		}
	</style>
	<script>
		var pixel_keyword_width 	= <?php echo $pixel_keyword_width;  ?>;
		var pixel_keyword_height	= <?php echo $pixel_keyword_height; ?>;
		var brand_width 			= <?php echo $brand_width;  ?>;
		var brand_height			= <?php echo $brand_height; ?>;
		var category_width 			= <?php echo $category_width;  ?>;
		var category_height			= <?php echo $category_height; ?>;
		var super_tag_width 		= <?php echo $super_tag_width;  ?>;
		var super_tag_height		= <?php echo $super_tag_height; ?>;

		function setRatio(element, width, height){
			element.each(function(){
				var $this 		= $(this);
				var imgWidth    = width;
	            var imgHeight   = height;

	            var newWidth    = $this.outerWidth(true);
	            var newHeight   = (imgHeight * newWidth)/imgWidth;

	            $this.css('height', Math.floor(newHeight));
	        });
		}

		setRatio($('.tags-items .keyword-item .keyword-image'), super_tag_width, super_tag_height);
		setRatio($('.category-keywords .keyword-item>div'), category_width, category_height);
		setRatio($('.brand-keywords .keyword-item>div'), brand_width, brand_height);
		setRatio($('.pixel-keywords .keyword-item .keyword-image'), pixel_keyword_width, pixel_keyword_height);
	    $(window).resize(function() {
	    	setRatio($('.tags-items .keyword-item .keyword-image'), super_tag_width, super_tag_height);
	    	setRatio($('.category-keywords .keyword-item>div'), category_width, category_height);
	    	setRatio($('.brand-keywords .keyword-item>div'), brand_width, brand_height);
	    	setRatio($('.pixel-keywords .keyword-item .keyword-image'), pixel_keyword_width, pixel_keyword_height);
	    });
	</script>

<!-- 
/* ====================================================================== *
		ANIMATE ON LOAD
 * ====================================================================== */ 
-->	

	<script src="../includes/plugins/Media Boxes/plugin/components/Waypoints/waypoints.min.js"></script>

	<script>
		/* ANIMATE CONTENT ON SCROLL */

		$('.animate-on-load').waypoint(function(direction){
			$(this.element).addClass('animated fadeInUp');
		}, {
		    context: window,
		    continuous: true,
		    enabled: true,
		    horizontal: false,
			offset: '90%',
		    //offset: 'bottom-in-view',
		    triggerOnce: true,   
		});
	</script>
