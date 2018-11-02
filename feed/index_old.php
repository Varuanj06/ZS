<?php require_once("../fb_validator.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/attribute.php"); ?>
<?php require_once("../classes/product_lang.php"); ?>
<?php require_once("../classes/love_count.php"); ?>
<?php require_once("../classes/search_history.php"); ?>
<?php require_once("../classes/search_history_detail.php"); ?>
<?php require_once("../classes/functions.php"); ?>

<?php 
	//print_r( $user );
	$user_birthday 		= isset($user['birthday'])?$user['birthday']:"";
	$user_gender 		= isset($user['gender'])?$user['gender']:"";
	$user_name 			= isset($user['first_name'])?$user['first_name']:"";
	$user_last_name 	= isset($user['last_name'])?$user['last_name']:"";

	$age 		= "";
	$gender 	= "";

	//GET GENDER
	if( isset($user['gender']) ){
		$gender = $user['gender'];
		$gender  = "/".$gender."/";
	}

	//GET AGE
	if( isset($user['birthday']) ){
		$from = new DateTime($user['birthday']);
		$to   = new DateTime('today');
		$age  = $from->diff($to)->y;
		$age  = "/".$age."/";
	}
	
	//GET KEYWORDS
	$product 		= new product();
	$product_list 	= $product->get_all_keywords($gender, $age, "");
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


	//GENERATE KEYWORDS FOR JS
	$JS_keywords = "";
	foreach ($keywords_list as $value) {
		$JS_keywords .= "'$value',";
	}

	//GET RESULTS USING KEYWORD
	$products 	= array();
	$q 			= "";
	if( isset($_GET['q']) ){
		$q = $_GET['q'];

		if(isset($_GET['a'])){
			$age = $_GET['a'];
			$age  = "/".$age."/";	
			if($_GET['a'] == ''){
				$age = '';
			}
		}

		$products = $product->get_search($q, $gender, $age, " order by final_count desc");


		/* *** SAVE THE CURRENT SEARCH *** */
		$error = false;
		$conn->beginTransaction();

		
		/* erase old search if this new search already exists */			
		$search_history 		= new search_history();
		$search_history_detail 	= new search_history_detail();
		$old_id_search_history = $search_history->get_id_by_keyword_and_user($q, $user['id']);

		if($old_id_search_history != ""){
			$search_history->set_id_search_history($old_id_search_history);
			if(!$search_history->delete()){
				$error = true;
			}

			$search_history_detail->set_id_search_history($old_id_search_history);
			if(!$search_history_detail->delete_by_id_search_history()){
				$error = true;
			}
		}


		/* save new search */
		$search_history 		= new search_history();
		$search_history_detail 	= new search_history_detail();
		$id_search_history  	= $search_history->max_id_search_history();

		$search_history->set_id_search_history($id_search_history);
		$search_history->set_id_fb_user($user['id']);
		$search_history->set_keyword($q);
		$search_history->set_results_count(count($products));
		$search_history->set_name_fb_user($user_name);
		$search_history->set_last_name_fb_user($user_last_name);
		$search_history->set_gender_fb_user($user_gender);
		$search_history->set_birthday_fb_user($user_birthday);

		if(!$search_history->insert()){
			$error = true;
		}

		$cont = 0;
		foreach ($products as $product) {
			$cont++;
			if($cont <= 5){
				$id_search_history_detail 	= $search_history_detail->max_id_search_history_detail($id_search_history);

				$search_history_detail->set_id_search_history($id_search_history);
				$search_history_detail->set_id_search_history_detail($id_search_history_detail);
				$search_history_detail->set_id_product($product->get_id_product());
				$search_history_detail->set_id_product_prestashop($product->get_id_product_prestashop());
				$search_history_detail->set_name($product->get_name());
				$search_history_detail->set_link($product->get_link());
				$search_history_detail->set_image_link($product->get_image_link());

				if(!$search_history_detail->insert()){
					$error = true;
				}
			}
		}



		if($error){
	      $conn->rollBack();
	      echo "ERROR";
		}else{
	      $conn->commit();
		}
	}

?>

<!doctype html>
<html lang="en">
<head>

  	<?php require_once("../head.php"); ?>

  	<!-- Include select2 plugin -->
  	<link rel="stylesheet" href="../includes/plugins/select2/css/select2.css">
  	<script src="../includes/plugins/select2/js/select2.min.js"></script>

	<!-- Include typeahead plugin -->
	<script src="../includes/plugins/typeahead/typeahead.jquery.min.js"></script>
  	
  	<!-- Media Boxes CSS files -->
	<link rel="stylesheet" href="../includes/plugins/Media Boxes/css/magnific-popup.css">
	<link rel="stylesheet" type="text/css" href="../includes/plugins/Media Boxes/css/mediaBoxes.css">

	<style>
		.trigger-lightbox{
			cursor: pointer;
		}
	</style>

</head>
<body>

	<!-- SOCIAL FEATURE -->
	<style>
		.fb-like {
		    height:20px;
		    overflow: hidden;
		}
	</style>
	
	<div id="fb-root"></div>
	<script src="http://connect.facebook.net/en_US/all.js"></script>
    <script>
	      window.fbAsyncInit = function() {
	        // init the FB JS SDK
	        FB.init({
	          appId      : '<?php echo $app_id; ?>', // App ID from the App Dashboard
	          status     : true, // check the login status upon init?
	          cookie     : true, // set sessions cookies to allow your server to access the session?
	          xfbml      : true  // parse XFBML tags on this page?
	        });
	        //FB.Canvas.setAutoResize();
	      };

      	// facebook callback
	  	FB.Event.subscribe('edge.create', function(href, widget) {
	       //alert('you just liked something!');
	       $.get("update_likes_shares.php?href="+href);
	    });
	    FB.Event.subscribe('edge.remove', function(href, widget) {
	       //alert('you just UNLIKED :( something!');
	       $.get("update_likes_shares.php?href="+href);
	    });
    </script>

	<?php require_once("../menu.php"); ?>
	<script>$('.nav-right a[href="../feed"]').addClass('selected');</script>
  
	<div class="header-container">
	  <div class="mask">

	  	<?php 
	  		$all_keywords_selected = explode(",", $q);
	  	?>
	    
	    <!--<h4>Start searching</h4>-->
	  	<form action="" method="get" name="form" class="form">
	  		<input type="hidden" name="q" value="<?php echo $q; ?>">
		    <input type="text" value="I want to wear something special for a" disabled style="color:black;background: rgba(255,255,255,.85);">
		    <!--<input value="<?php echo $q; ?>" class="keyword-input typeahead" type="text" name="q" value="" placeholder="write something here" style="width:180px;">-->
		    <select class="keyword-input" name="keyword" id="keyword" data-placeholder="Write something here" multiple="multiple">
				<?php foreach ($keywords_list as $value) { ?>
					<option value="<?php echo $value; ?>" <?php if (in_array($value, $all_keywords_selected)){echo "selected";} ?>><?php echo $value; ?></option>
				<?php } ?>
			</select>
			<script>
				$("#keyword").select2({
					tags: true
				});

				function submit_form(){
					var keyword = $('#keyword').attr("disabled", "disabled");
					var val 	= keyword.val(); 
					var output  = "";
					for (var i = 0; i < val.length; i++) {
						output += val[i]+",";
					};

					if(output!=""){
						output = output.substring(0, output.length - 1);
					}

					document.form.q.value = output;
					document.form.submit();
				}
			</script>
		    <a href="javascript:submit_form();" class="btn btn-green">SEARCH</a> 
	    </form>

	    <div class="suggestions">
	    	<div class="suggestions-feed">
	    		<?php
	    			$search_history = new search_history();
	    			$last_keywords 	= $search_history->get_last_keywords($user['id'], $user_gender, " order by date desc limit 0, 5 ");

	    			foreach ($last_keywords as $row){
		    			echo "<a href='#' data-value='$row' class='btn btn-sm btn-white suggest'>".str_replace(",", ", ", $row)."</a>";
	    			}
	    		?>
	    		<script>
	    			$('.suggest').on('click', function(e){
	    				e.preventDefault();

	    				var values = $(this).data('value').split(',');
	    				$("#keyword").val(values).trigger("change");
	    				
	    			})
			    </script>
	    	</div>
	    </div>

	  </div>
	</div>

	<div class="tabs-container">
		

<!-- /* ====================================================================== *
      		SEARCH RESULT
 	  * ====================================================================== */ -->
		
		<?php if(isset($_GET['q'])){ ?>
			<div id="grid">

				<?php 
					$attribute 		= new attribute();
					$product_lang 	= new product_lang();
				?>

				<?php 
					$love_count = new love_count();

					foreach ($products as $row) {
						$last_chars 			= substr($row->get_image_link(), strrpos($row->get_image_link(), '/') + 1);
						$id_image 				= str_replace(".jpg", "", $last_chars); // get the 75 from this: http://miracas.com/9-75/75.jpg
						$id_product_prestashop 	= $row->get_id_product_prestashop();

						$price 					= get_the_price($id_product_prestashop);
						$discount 				= get_the_discount($row->get_id_product(), $price);
						$colors 				= $attribute->get_colors_of_product($id_product_prestashop);
						$sizes 					= $attribute->get_sizes_of_product($id_product_prestashop);

						$love_count->set_id_product($row->get_id_product());
						$love_count->set_id_fb_user($user['id']);
						$heart_selected = "";
						if($love_count->exists() == true){
							$heart_selected = "heart-selected";
						}

						//$colors[] = '#000';
						//$colors[] = 'green';
						//$colors[] = 'red';

						$img_url = "http://miracas.com/".$id_product_prestashop."-".$id_image."-large/".$id_product_prestashop."_.jpg";
						$img_url = $row->get_image_link();
				?>				
			        <div class="media-box">
			            <div class="media-box-image">
			                <div data-thumbnail="<?php echo $img_url; ?>"></div>
			                
			                <div class="thumbnail-overlay trigger-lightbox">
		              			<!--<a target="_blank" href="<?php echo $row->get_link(); ?>"><i class="fa fa-link"></i></a>-->
		              			<i class="fa fa-plus open-lightbox" data-idproductprestashop="<?php echo $id_product_prestashop; ?>"></i>
			                </div>
			            </div>

			            <div class="media-box-content" data-idproductprestashop="<?php echo $id_product_prestashop; ?>" data-idproduct="<?php echo $row->get_id_product(); ?>">
			                <div class="media-box-text">
			                	<p>
			                		<?php echo $row->get_name(); ?>
			                	</p>
			                	<p>
			                		<span class="media-box-price">
			                			<?php if($discount>0){ ?>
			                			<span style="font-size: 12px;color: rgba(145,145,145,.5);text-decoration: line-through;">₹<?php echo number_format($price, 2); ?></span>
			                			<?php } ?>

			                			₹<?php echo number_format($price-$discount, 2); ?>
			                		</span>
			                	</p>
			                	<p>
			                		<div class="row">
			                			<div class="col-sm-2 padding-top">
			                				<strong>Colors:</strong>
			                			</div>
			                			<div class="col-sm-9">
			                				<?php foreach ($colors as $color) {?>
					                			<span class="media-box-color" data-color="<?php echo $color; ?>"><span style="background:<?php echo $color; ?>;"></span></span>
					                		<?php } ?>
			                			</div>
			                		</div>

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
					                			<span class="media-box-size" data-size="<?php echo $size; ?>"><span><?php echo $public_size; ?></span></span>
					                		<?php 
					                			} 
					                		?>
			                			</div>
			                		</div>

			                		<div class="row">
			                			<div class="col-sm-2">
			                				
			                			</div>

			                			<div class="col-sm-9">
			                				<select class="qty form-control">
			                					<option value="1">1</option>
			                					<option value="2">2</option>
			                					<option value="3">3</option>
			                					<option value="4">4</option>
			                				</select>
			                				<a class="add_to_cart btn btn-sm btn-green" href="#">Add to Cart</a>
			                				<a class="btn btn-sm btn-green" href="../cart"><span class="fa fa-shopping-cart"></span></a>
			                			</div>
			                		</div>									
			                	</p>
			                	<hr>
			                	<div class="fb-like" data-href="<?php echo $row->get_link(); ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>
			                	
			                	<div class="love-button" data-idproduct="<?php echo $row->get_id_product(); ?>" data-iduser="<?php echo $user['id']; ?>">
			                		<i class="fa fa-heart <?php echo $heart_selected; ?>"></i>
			                		<span class="count"><?php echo $row->get_love_count(); ?></span>
			                	</div>
			                </div>
			            </div>
			        </div>
				<?php 
					} 
				?>
			</div>
			
			<style>
				body{
					min-height: 1000px;
				}
			</style>
			<script>
				$('html, body').animate({
			        scrollTop: $(".suggestions-feed").offset().top
			    }, 500);
			</script>

<!-- /* ====================================================================== *
      		OTHER USERS SEARCH
 	  * ====================================================================== */ -->			

		<?php }else{ ?>
				
				<div class="all-other-users-feed">
					<?php 
						$search_history 		= new search_history();
						$search_history_detail 	= new search_history_detail();

						$other_users_search 	= $search_history->get_list($user['id'], $user_gender, " order by date desc limit 0, 2000 ");

						foreach ($other_users_search as $row) {
							$id_fb_user 		= $row->get_id_fb_user();
							$fb_profile_img 	= "http://graph.facebook.com/$id_fb_user/picture?width=150&height=150 ";

							$row_age = "";
							if($row->get_birthday_fb_user() != ""){	
								$from 		= new DateTime($row->get_birthday_fb_user());
								$to   		= new DateTime('today');
								$row_age  	= $from->diff($to)->y;
							}
					?>
							<div class="feed-container feed-hidden" data-idsearch="<?php echo $row->get_id_search_history(); ?>" data-q="<?php echo $row->get_keyword(); ?>" data-age="<?php echo $row_age; ?>">
								<div class="feed-head">
									<div class="feed-img" data-url="<?php echo $fb_profile_img; ?>"></div>
									<div class="feed-head-text">
										<span><?php echo $row->get_name_fb_user()." ".$row->get_last_name_fb_user(); ?></span>
						            	has searched for outfits to wear for a <strong><?php echo str_replace(",", ", ", $row->get_keyword()); ?></strong>
										<br>
										<span class="date"><?php echo $row->get_date(); ?></span>
					            	</div>
					            </div>

					            <div class="feed-content">
					                
					            </div>
					        </div>
					<?php 
						}
					?>

					<div class="load-more-feed">See More</div>
				</div>
	
		<?php } ?>

	</div> <!-- End tabs-container -->


	<!-- Media Boxes JS files -->
	<script src="../includes/plugins/Media Boxes/js/jquery.isotope.min.js"></script>
	<script src="../includes/plugins/Media Boxes/js/jquery.imagesLoaded.min.js"></script>
	<script src="../includes/plugins/Media Boxes/js/jquery.transit.min.js"></script>
	<script src="../includes/plugins/Media Boxes/js/jquery.easing.js"></script>
	<script src="../includes/plugins/Media Boxes/js/waypoints.min.js"></script>
	<script src="../includes/plugins/Media Boxes/js/modernizr.custom.min.js"></script>
	<script src="../includes/plugins/Media Boxes/js/jquery.magnific-popup.min.js"></script>
	<script src="../includes/plugins/Media Boxes/js/jquery.mediaBoxes.js"></script>

	<script>

	    $('#grid').mediaBoxes({
	    	columns: 4,
	    	horizontalSpaceBetweenBoxes: 20,
        	verticalSpaceBetweenBoxes: 20,
        	boxesToLoadStart: 4,
	    	boxesToLoad: 4,
	    	deepLinking: false,
	    });

	    /* ***** SHOPPING CART ***** */	

	    $('.media-box-size').on('click', function(){
	    	var $this = $(this);

	    	$this.siblings('.media-box-size').removeClass('size-selected');
	    	$this.addClass('size-selected');
	    });

	    $('.media-box-color').on('click', function(){
	    	var $this = $(this);

	    	$this.siblings('.media-box-color').removeClass('color-selected');
	    	$this.addClass('color-selected');
	    });

	    $("#grid").on('click', '.add_to_cart', function(e){
	    	e.preventDefault();
	    	var $this 				= $(this);
	    	var content 			= $this.parents('.media-box-content');
	    	var color 				= content.find('.color-selected').data('color');
	    	var size 				= content.find('.size-selected').data('size');
	    	var qty 				= content.find('.qty').val();
	    	var idProductPrestashop = content.data('idproductprestashop');
	    	var idProduct 			= content.data('idproduct');
	    	var something_missing 	= false;

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

	    /* ***** OTHER USERS FEED ***** */
	    var mainContainer 	= $('.all-other-users-feed');
	    var loadMoreFeed 	= $('.load-more-feed');
	    var feedNumber 		= 10;

	    function loadFeed(amount){
	    	loadMoreFeed.text("Loading...");
	    	var feeds = $('.feed-hidden');

	    	feeds.slice(0,amount).each(function(){
	    		var $this 			= $(this);
	    		var imgContainer 	= $this.find('.feed-img');
	    		var imgUrl 			= imgContainer.data('url');
	    		var img 			= $("<img src='"+imgUrl+"' />");
	    		var idsearch 		= $this.data('idsearch');
	    		var feedContent 	= $this.find('.feed-content');
	    		var q 				= $this.data('q');
	    		var age 			= $this.data('age');
	    		
	    		imgContainer.append(img);
	    		$this.addClass('all-ready');


	    		/* get the products of each search via ajax */
	    		$.get('get_others_search_products.php?idsearch='+idsearch, function(r){
	    			var products = $($.trim(r));
	    			feedContent.append(products);
	    			feedContent.append($("<a class='btn btn-sm btn-gray' href='./?q="+q+"&a="+age+"'>See More</a>"));
	    		});
	    	});


	    	mainContainer.find('.feed-hidden .feed-img img').imagesLoadedMB()
                        .always(function(instance){
							$('.all-ready').removeClass('feed-hidden');

                            if($('.feed-hidden').length <= 0){
					    		loadMoreFeed.hide();
					    	}

	    					loadMoreFeed.text("See More");
                        });
	    }
	    loadFeed(feedNumber);

	    loadMoreFeed.on('click', function(){
			loadFeed(feedNumber);	    	
	    });

	    loadMoreFeed.waypoint(function(direction) {
                if( direction == 'down' && loadMoreFeed.text() != 'Loading...'){
                    loadFeed(feedNumber);
                }
             }, {
                context: window,
                continuous: true,
                enabled: true,
                horizontal: false,
                offset: 'bottom-in-view',
                triggerOnce: false,   
             });

		/* ***** LOVE BUTTON ***** */	  
		$('#grid').on('click', '.love-button i', function(){
			var $this 		= $(this);
			var count 		= $this.siblings('.count');
			var count_value = parseInt(count.text());
			var parent 		= $this.parents('.love-button');
			var idUser 		= parent.data('iduser');
			var idProduct 	= parent.data('idproduct');
			var love 		= true;

			if($this.hasClass('heart-selected')){
				count.text(--count_value);
				$this.removeClass('heart-selected');
				love = false;
			}else{
				count.text(++count_value);
				$this.addClass('heart-selected');
				love = true;
			}

			$.get('update_love.php?love='+love+'&count_value='+count_value+'&id_product='+idProduct+'&id_fb_user='+idUser, function(r){
				//console.log(r);
			});

		});


	    /* ***** CUSTOMIZE MAGNIFIC POPUP ***** */

	    function loading($this){
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

	    $('#grid').on('click', '.trigger-lightbox', function(){
	    	$(this).find('.open-lightbox').trigger('click');
	    });

	    $('#grid').on('click', '.open-lightbox', function(e){
	    	e.stopPropagation()
	    	var $this 				= $(this);

	    	loading($this);

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

	
	<script>

		/* --- TYPEAHEAD PLUGIN ---- */
		/*
		var states = [<?php echo $JS_keywords; ?>];

		var substringMatcher = function(strs) {
		  return function findMatches(q, cb) {
		    var matches, substringRegex;
		 	
		    // an array that will be populated with substring matches
		    matches = [];
		 
		    // regex used to determine if a string contains the substring `q`
		    substrRegex = new RegExp(q, 'i');
		 
		    // iterate through the pool of strings and for any string that
		    // contains the substring `q`, add it to the `matches` array
		    $.each(strs, function(i, str) {
		      if (substrRegex.test(str)) {
		        matches.push(str);
		      }
		    });
		 
		    cb(matches);
		  };
		};
		 
		$('.typeahead').typeahead({
		  hint: true,
		  highlight: true,
		  minLength: 1
		},
		{
		  name: 'states',
		  source: substringMatcher(states)
		});*/

	</script>

</body>

</html>