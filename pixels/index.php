<?php $force_session = true; ?>
<?php require_once("../fb_validator.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/pixel.php"); ?>
<?php require_once("../classes/pixel_keyword.php"); ?>
<?php require_once("../classes/pixel_count.php"); ?>
<?php require_once("../classes/fb_user_details.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>

	<!-- COUNTDOWN PLUGIN -->
	<link rel="stylesheet" href="../includes/plugins/countdown/jquery.countdown.css">
  	<script src="../includes/plugins/countdown/jquery.plugin.min.js"></script>
	<script src="../includes/plugins/countdown/jquery.countdown.min.js"></script>

	<!-- Media Boxes CSS files -->
  	<link rel="stylesheet" href="../includes/plugins/Media Boxes/plugin/components/Font Awesome/css/font-awesome.min.css"> <!-- only if you use Font Awesome -->
	<link rel="stylesheet" href="../includes/plugins/Media Boxes/plugin/components/Magnific Popup/magnific-popup.css"> <!-- only if you use Magnific Popup -->
	<link rel="stylesheet" type="text/css" href="../includes/plugins/Media Boxes/plugin/css/mediaBoxes.css">

	<link rel="stylesheet" href="../includes/css/checkbox.css">
	<style>
		.media-box-text{
			margin-bottom: 0 !important;
		}
		.book_now_container{
			font-size: 15px;
  			line-height: 28px;
		}
		body{
			overflow-y: scroll;
		}
		@media only screen and (max-width: 768px) {
			/*#keywords, #search-results{
				margin: 0 10px;
			}*/
			.tabs-container{
				padding: 0 10px !important;
			}
		}
	</style>

</head>
<body>

<!--
/* ====================================================================== *
      [START] FB LOGIN IF SESSION IS NOT ACTIVE
 * ====================================================================== */
-->

	<?php 
		if( $active_session !== true ){

			/* IF THE USER DOESN'T HAVE A FB SESSION AND HE GETS DIRECTLY HERE VIA URL, THEN ASK FOR A FB LOGIN AND REDIRECT HIM BACK HERE ONCE HE LOGS IN */

			if(isset($_GET['q'])){
				$pixel 				= new pixel();
				$get_pixels 		= array();
				if(isset($_GET['pixel_keyword']) && $_GET['pixel_keyword']=='yes'){
					$get_pixels 	= $pixel->get_list_by_pixel_keyword($_GET['q'], " order by pixel_count desc ");
				}else{
					$get_pixels 	= $pixel->get_list_by_keyword($_GET['q'], " order by pixel_count desc ");
				}
				

				if(count($get_pixels)>0){

					unset($_SESSION['add_to_cart_without_login']); // IN CASE HE WANTED TO ADD A PRODUCTO TO THE CART BUT CHANGED HIS MIND AND CLOSES THE FB LOGIN POPUP
					unset($_SESSION['shopping_assistant_without_login']); // IN CASE HE WANTED TO GO TO THE ASSISTANT SHOPPING PAGE BUT CHANGED HIS MIND AND CLOSES THE FB LOGIN POPUP

					$_SESSION['pixels_without_login'] 				= 'yes';
					$_SESSION['pixels_without_login_q'] 			= $_GET['q'];
					$_SESSION['pixels_pixel_keyword'] 				= isset($_GET['pixel_keyword'])?$_GET['pixel_keyword']:'';
	?>
					<!-- Modal -->
					<div class="modal-login modal fade" id="myModal_fb_login" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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

					<script>
						function pixels_show_fb_login(){
							$('#myModal_fb_login').modal('show').appendTo('body');;
							return true;
						}
					</script>
	<?php
				}
			}else{
				echo "<script>location.href='../signin';</script>";
				exit();
			}
		}else{
	?>
			<script>
				function pixels_show_fb_login(){
					return false;
				}
			</script>
	<?php 
		}
	?>

<!--
/* ====================================================================== *
      [END] FB LOGIN IF SESSION IS NOT ACTIVE
 * ====================================================================== */	
-->

	<?php require_once("../menu.php"); ?>
	<?php require_once("../sidebar.php"); ?>
	<div id="menu-page-wraper">

	<div class="page-wrap"><div class="page-wrap-inner">
	
	<?php require_once("../message.php"); ?>

	<?php 
		if( !isset($_GET['q'])){
			echo "<script>location.href='../feed';</script>";
		}

		$q 					= $_GET['q'];

		$pixel 				= new pixel();
		$get_pixels 		= array();
		if(isset($_GET['pixel_keyword']) && $_GET['pixel_keyword']=='yes'){
			$get_pixels 	= $pixel->get_list_by_pixel_keyword($_GET['q'], " order by pixel_count desc ");
		}else{
			$get_pixels 	= $pixel->get_list_by_keyword($_GET['q'], " order by pixel_count desc ");
		}
	?>

	<div class="tabs-container">

		<?php if(isset($_GET['pixel_keyword']) && $_GET['pixel_keyword']=='yes'){ ?>
			
			<?php 
				$pixel_keyword = new pixel_keyword();
				$pixel_keyword->map($_GET['q']);
			?>

			<div class="global-countdown">
				<div id="countdown"></div>
				<script>
					var tillDate = new Date('<?php echo $pixel_keyword->get_expiry_date(); ?>');
					tillDate.setHours(0,0,0,0);
					$('#countdown').countdown({until: tillDate, format:'DHMS', padZeroes: true, layout: ' <span class="cd_time">{dn} <span class="cd_time_txt">{dl}</span></span> <span class="cd_time">{hn}:{mn}:{snn} <span class="cd_time_txt">{hl}</span></span>' });
				</script>
			</div>

			<br>

		<?php } ?>

		<div id="grid">
			<?php 
				$count_bg = 0; 
				foreach ($get_pixels as $row) { 
			
					$count_bg++;

					$price 		= (float)$row->get_price();
					$discount 	= (float)$row->get_discount();

					if($row->get_discount_type() == "percentage" ){
						$discount = round( (float)$row->get_discount() * (float)0.01 * $price, 2);
					}else{
						$discount = round( (float)$row->get_discount(), 2);
					}

					$pixel_count 	= new pixel_count();
					$pixel_count->set_id_fb_user(isset($user['id'])?$user['id']:'');
					$pixel_count->set_id_pixel($row->get_id_pixel());

					$current_image_link = $row->get_image_link();
					$current_image_link = str_replace(".search", "", $current_image_link);
					$current_image_link = str_replace(".220*220", "", $current_image_link);
				?>

				<div class="media-box <?php echo "bg_$count_bg"; ?>">
		            <div class="media-box-image">
		                <div data-thumbnail="<?php echo $current_image_link; ?>" data-width="600" data-height="600"></div>
		                
		                <div class="thumbnail-overlay trigger-lightbox mb-open-popup" data-src="<?php echo $current_image_link; ?>">
	              			<i class="fa fa-plus"></i>
		                </div>
		            </div>

		            <div class="media-box-content">
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

							<hr>
							<?php 
								$checked_msg = 'Successfully booked. We will notify you soon.';
								if( $row->get_type() == 'product' ){
									// this is currently not needed, but maybe in the future!
									$checked_msg = "The booked product is available for purchase here. <a target='_blank' href='".$row->get_product_link()."'>Buy Now</a>";
								}
							?>
							<div class="book_now_container" data-id_fb_user="<?php echo $user['id']; ?>" data-id_pixel="<?php echo $row->get_id_pixel(); ?>">
								<input class="book_now" type="checkbox" id="test_<?php echo $row->get_id_pixel(); ?>" <?php if($pixel_count->exists()){echo "checked='checked'";} ?> />
								<label for="test_<?php echo $row->get_id_pixel(); ?>" data-checked_msg="<?php echo $checked_msg; ?>">
									<?php 
										if(!$pixel_count->exists()){
											echo 'Book Now';
										}else{
											echo '<span style="font-size:12px;">'.$checked_msg.'</span>';
										} 
									?> 
								</label>
							</div>

		                </div>
		            </div>
		        </div>
				
			<?php 
					if($count_bg==4)$count_bg=0;
				} 
			?>
		</div>

		<!-- TO TOP BUTTON -->
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
			.to-top:hover{
				/*background-color:#42414d!important;*/
			}
		</style>
		<script>
			$('.to-top').on('click', function(){
				$('html,body').animate({ scrollTop: 0 }, 'fast');
			})
		</script>
		<!-- END TO TOP BUTTON -->
				
	</div>

	<?php 
		$fb_user_details 	= new fb_user_details();
		$fb_user_details->set_id_fb_user(isset($user['id'])?$user['id']:'');
	?>

	<?php if(!$fb_user_details->exists() && isset($user['id'])){ ?>
		<style>
			input[type="text"] {
			    border: 1px solid #ccc;
			    height: 24px;
			    padding: 6px 8px;
			    display: block;
			}
			.modal-content{
				background: #FBF8E9;
			}

		</style>
		
		<!-- Modal -->
		<div class="modal fade" id="myModal_fb_user_details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  	<div class="modal-dialog" role="document">
		    	<div class="modal-content">
		      		<div class="modal-header">
		        		<h4 class="modal-title" id="myModalLabel">We just need some information from you</h4>
		      		</div>
		      		<div class="modal-body">
						<div class="row">
							<div class="col-sm-3"><label for="email">Email</label></div>
							<div class="col-sm-6"><input type="text" name="email" maxlength="300" class="form-control"></div>
						</div>		
						<br>
						<div class="row">
							<div class="col-sm-3"><label for="mobile_number">Mobile Number</label></div>
							<div class="col-sm-6"><input type="text" name="mobile_number" maxlength="300" class="form-control"></div>
						</div>
						<br>
						<div class="row">
							<div class="col-sm-3">&nbsp;</div>
							<div class="col-sm-6">
								<a href="" class="btn btn-green btn-default save_fb_user_details">Save</a>
							</div>
						</div>
		      		</div>
		    	</div>
		  	</div>
		</div>

		<script>
			$('#myModal_fb_user_details').modal({backdrop: 'static', keyboard: false});
			$('#myModal_fb_user_details').modal('show').appendTo('body');;

			$('.save_fb_user_details').click(function(e){
				e.preventDefault();

				var $this 			= $(this);			
				var email 			= $('input[name="email"]').val();
				var mobile_number 	= $('input[name="mobile_number"]').val();

				if(email=='' || mobile_number==''){
					alert('All fields are required.');
					return;
				}
				if($this.hasClass('saving'))return;

				$this.addClass('saving');
				$this.html('Loading...');

				$.get('insert_fb_user_details.php?email='+email+"&mobile_number="+mobile_number, function(r){
					$this.removeClass('saving');
					//$this.html('Save');

					if($.trim(r) == "ERROR"){
	    				alert("Oops! something went wrong, refresh the page and try again.");
	    			}else{
	    				$('#myModal_fb_user_details').modal('hide');
	    			}
				});
			});
		</script>
	<?php } ?>

	<!-- Media Boxes JS files -->
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

		/* ***** MEDIA BOXES ***** */	
		
		var $grid = $('#grid').mediaBoxes({
		    	columns: 4,
		    	horizontalSpaceBetweenBoxes: 8,
	        	verticalSpaceBetweenBoxes: 8,
	        	boxesToLoadStart: 8,
		    	boxesToLoad: 4,
		    	deepLinkingOnPopup: false,
        		deepLinkingOnFilter: false,
		    });	

		/* ***** BOOK NOW ***** */

		$('.book_now').click(function(e){

			if(pixels_show_fb_login()){
				e.preventDefault();
				return;
			}

			var $this 		= $(this);
			var parent 		= $this.parents('.book_now_container');
			var label 		= parent.find('label');
			var id_pixel 	= parent.attr('data-id_pixel');
			var id_fb_user 	= parent.attr('data-id_fb_user');
			var insert 		= '';
			var msg 		= '';

			if(this.checked){
				insert 	= 'yes';
				msg 	= '<span style="font-size:12px;">'+label.attr('data-checked_msg')+'</span>';
			}else{
				insert = 'no';
				msg 	= '<span style="font-size:15px;">Book Now</span>';
			}

			label.html('<span style="font-size:15px;">Loading...</span>')
			$this.attr('disabled',true);

			$.get("update_pixel_count.php?id_pixel="+id_pixel+"&insert="+insert, function(r){
				
				if($.trim(r) == "NO_DETAILS"){
					alert("Oops! you haven't specify your email and mobile number, refresh the page and try again.");
				}else if($.trim(r) == "ERROR"){
    				alert("Oops! something went wrong, refresh the page and try again.");
    			}else{

    				label.html(msg);
					$this.attr('disabled',false);
					$grid.isotopeMB('layout');

					if($.trim(r)=='pixel_count_increase' || $.trim(r)=='pixel_is_product'){
	    				$.get("send_msg.php?id_pixel="+id_pixel+"&type="+$.trim(r), function(r){
	    					// all good, sending SMS and email in the background...
	    				});	
    				}

    			}
			});
		});
		
	</script>

	</div></div>

	<?php require_once("../footer.php"); ?>

	</div>
</body>
</html>