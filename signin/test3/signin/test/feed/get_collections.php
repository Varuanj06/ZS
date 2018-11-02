<?php $force_session = true; ?>
<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/product_lang.php"); ?>

<?php 

/* ====================================================================== *
    	CLASSES
 * ====================================================================== */		

    $product 				= new product();
	$product_lang 			= new product_lang();

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
    	GET PRODUCTS FROM KEYWORD
 * ====================================================================== */	

    $q 				= isset($_GET['q'])?$_GET['q']:'';	
	$products 		= $product->get_search($q, $user_gender_slash, $user_age_slash, " order by like_count desc ");

/* ====================================================================== *
    	GET MONTHS WITH ITS PRODUCTS
 * ====================================================================== */	

   	$output = array(); 	

    foreach ($products as $row){
   		$product_date 	= $product_lang->get_date_add($row->get_id_product_prestashop());
		//if($product_date == '') $product_date = '2017-06-06'; // this line is for testing purposes only
   		if($product_date <> ''){
   			$product_date_format 	= strtotime( date("F Y", strtotime($product_date)) );
   			//$product_date_format = date("d - m - Y", strtotime($product_date_format));

   			if(!array_key_exists($product_date_format, $output)){
   				$output[$product_date_format]   	= array();	
   			}

   			$output[$product_date_format][] 	=	array(  
														'id_product' => $row->get_id_product()
													);
   		}
	}	

	krsort($output);

?>

<!-- 
	/* ====================================================================== *
  		COLLECTIONS
	 * ====================================================================== */ 
-->		
	
	<ul>
	<?php foreach ($output as $key => $value) { break; ?>
		<li>
			<?php echo date("F Y", $key); ?>
			<br>
			<?php 
				$cont = 0;
				foreach ($value as $row) { 
					if( ++$cont > 4 ) break; // only 4 products
			?>
				<?php echo $row['id_product']; ?> <br>
			<?php 
				} 
			?>
		</li>
	<?php } ?>
	</ul>

	<style>
		#grid_collections .media-box{
			cursor: pointer;
		}
		#grid_collections .media-box-container{
			padding: 10px;
			background: #f3e4ca;

		    -webkit-box-shadow: none;
		    -moz-box-shadow: none;
		    -o-box-shadow: none;
		    -ms-box-shadow: none;
		    box-shadow: none;
		}
		.collection_title{
			position: absolute;
			top: 50%;
			left: 50%;

			-webkit-transform   : translateX(-50%) translateY(-50%);
      		-moz-transform      : translateX(-50%) translateY(-50%);
		    -ms-transform       : translateX(-50%) translateY(-50%);
		    transform           : translateX(-50%) translateY(-50%);

			width: 120px;
			height: 120px;
			text-align: center;
			border-radius: 50%;
			background: #f3e4ca;
			display: table;
			font-size: 16px;
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
			background-color: #f3e4ca;
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
	</style>

	<div id="grid_collections"> 
		<?php foreach ($output as $key => $value){ ?>
			<div class="media-box" onclick="location.href='./?q=<?php echo $q; ?>&date=<?php echo $key; ?>&set_gender=<?php echo $user_gender; ?>'">
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
		<?php } ?>
	</div>

	<script>
	    setTimeout(function(){

			$('#grid_collections').mediaBoxes({
		    	columns: 3,
		    	horizontalSpaceBetweenBoxes: 40,
	        	verticalSpaceBetweenBoxes: 40,
	        	resolutions: [
		            {
		                maxWidth: 960,
		                columnWidth: 'auto',
		                columns: 3,
		            },
		            {
		                maxWidth: 650,
		                columnWidth: 'auto',
		                columns: 2,
		            },
		            {
		                maxWidth: 450,
		                columnWidth: 'auto',
		                columns: 1,
		            },
		        ],
	        	boxesToLoadStart: 8,
		    	boxesToLoad: 4,
		    	minBoxesPerFilter: 8,
		    	deepLinkingOnPopup: false,
				deepLinkingOnFilter: false,
				lazyLoad: true,
				loadMoreWord: 'Load More Collections',
	        	noMoreEntriesWord: 'No More Collections',
		    });

		    function resize_collection_img(){
		    	var collection_img 		= $('#grid_collections').find('.collection_img').eq(0);
		    	var collection_img_w	= collection_img.outerWidth(true);

		    	var css 				= 	" #grid_collections .collection_img{ "+ 
			    						  	" 	height: "+collection_img_w+"px !important; "+ 
			    						  	" } ";

		    	var $stylesheet 		= $('<style type="text/css" media="screen" />').html(css);
				$('body').append($stylesheet);
		    	
		    	$('#grid_collections').mediaBoxes('resize');
		    }

		    setTimeout(function(){
		    	// this is also executed after the Media Boxes finish loading, because sometimes it doesn't load on time so it doesn't resize correctly
		    	resize_collection_img();
		    }, 1);

		    $(window).resize(function(){
		    	setTimeout(function(){
		    		resize_collection_img();
		    	}, 1);
		    });

		}, 1);    
    </script>	

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
