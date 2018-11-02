<?php $force_session = true; ?>
<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/keyword.php"); ?>
<?php require_once("../classes/pixel_keyword.php"); ?>
<?php require_once("../classes/shopping_assistant_conversation.php"); ?>
<?php require_once("../classes/fb_user_product_save.php"); ?>
<?php require_once("../classes/tag.php"); ?>
<?php require_once("../classes/tag_super.php"); ?>

<?php 
	
/* ====================================================================== *
    	CLASSES
 * ====================================================================== */		

	$keyword 							= new keyword();
	$pixel_keyword 						= new pixel_keyword();
	$product 							= new product();
	$shopping_assistant_conversation 	= new shopping_assistant_conversation();
	$fb_user_product_save 				= new fb_user_product_save();
	$tag 								= new tag();
	$tag_super 							= new tag_super();

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
	}

/* ====================================================================== *
    	PIXEL KEYWORDS
 * ====================================================================== */			

	$pixel_keyword_list 	= $pixel_keyword->get_all_keywords($user_gender_slash, $user_age_slash, "");

?>

<!--
/* ====================================================================== *
      SHOPPING ASSISTANT
 * ====================================================================== */	    	
-->

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

<!--
/* ====================================================================== *
      SIZE PICKER
 * ====================================================================== */	    	
-->	

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

<!--
/* ====================================================================== *
      TAGS
 * ====================================================================== */	    	
-->	    

	<style>
		.tags-items .keyword-text{
			background: #f3e4ca; 
		}
		.tags-items hr{
			margin: 15px;
			border-color: #6D4D1C;
		}
		.tags-items .tag{
			margin: 5px 0;
		}
		.tags-items .tag a{
			color: #6D4D1C;	
		}

		.tags-items .row {
		    margin-right: -8px;
		    margin-left: -8px;
		}
		.tags-items .row .col-xs-6{
			padding-right: 8px;
    		padding-left: 8px;
		}

		@media only screen and (max-width: 1185px) {
			.tags-items .keyword-item{
				width: 33.33% !important;
			}
		}
		@media only screen and (max-width: 810px) {
			.tags-items .keyword-item{
				width: 50% !important;
			}
		}
		@media only screen and (max-width: 500px) {
			.tags-items .keyword-item{
				width: 100% !important;
			}
			.tags-items .col-xs-6{
				width: 100%;
			}
			.tags-items .row{
				height: auto !important;
			}
		}
	</style>

	<?php
		$super_tags = $tag_super->get_all_by_gender($user_gender, "order by 1");
	?>

	<div class="keywords-container tags-items animate-on-load">
		
		<?php foreach ($super_tags as $row) { ?>
			<div class="keyword-item">
			 	<div>
		            <div class="keyword-text">
		            	<div class="keyword-image" style="margin-bottom:15px;">
			                <img src="<?php echo $row->get_image(); ?>">
			            </div>
		                <?php echo $row->get_name(); ?>
		                <hr>
		                <div class="row" style="height:84px;">
			                <?php $tags = $tag->get_list_by_tag_super($row->get_id_tag_super(), " order by 1 "); ?>
			                <?php foreach ($tags as $row_inner) { ?>
								<div class="col-xs-6">
									<div class="tag">
										<a href="./?id_tag=<?php echo $row_inner->get_id_tag(); ?><?php echo $extra_param; ?>"><?php echo $row_inner->get_name(); ?></a>
									</div>
								</div>
			                <?php } ?>
			            </div>    
		            </div>
	        	</div>
	        </div>	
		<?php } ?>

    </div>

<!--
/* ====================================================================== *
      CATEGORY KEYWORDS (KEYWORDS THAT STARTS WITH THE WORD "CATEGORY")
 * ====================================================================== */	    	
-->
<!--
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
-->
<!--
/* ====================================================================== *
      PIXEL KEYWORDS
 * ====================================================================== */		
-->

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

<!--
/* ====================================================================== *
      BRAND STORES (KEYWORDS THAT STARTS WITH THE WORD "BRAND")
 * ====================================================================== */	    
-->
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
-->
<!--
/* ====================================================================== *
      STYLE STORES (THE NORMAL KEYWORDS)
 * ====================================================================== */		
-->

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

<!--
/* ====================================================================== *
      OTHERS SAVED PRODUCTS
 * ====================================================================== */	    	
-->

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
