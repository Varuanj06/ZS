<?php require_once("../fb_validator.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/shopping_assistant.php"); ?>
<?php require_once("../classes/shopping_assistant_conversation.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/keyword.php"); ?>

<!doctype html>
<html lang="en">
<head>
	
	<?php require_once("../head.php"); ?>

	<link rel="stylesheet" href="../includes/css/checkbox.css">
	<link rel="stylesheet" href="../includes/css/chat.css">

	<!-- CHANGE THEME -->

	<style>
		#particles-js{
			background: #DCD3BF;
		}
		.chat{
			/*background: #FFF;*/
			background: rgba(255,255,255,.2);
		    border-radius: 3px;
		    border: 1px solid rgba(0,0,0,.09);
		    box-shadow: 0 1px 4px 0 rgba(0,0,0,.04);
			min-height: auto;
		}
		.self .msg, .other .msg{
		    padding: 10px 11px 12px 13px;
		    font-size: 14px;
		    background: #fff !important;
		    border-color: #fff !important;
		    color: #999 !important;
		}
		.other .msg:before{
			border-right-color: #fff;
    		border-top-color: #fff;
		}
		.self .msg:after{
			border-left-color: #fff;
    		border-bottom-color: #fff;
		}
		.msg_loading, .select_product_loading, .select_keyword_loading{
    		background-color: rgba(0,0,0,.05);
		}		
		.new_message{
			margin-top: 15px;
		}
	</style>

</head>
<body>
	<?php if(isset($_GET['from_history'])){ ?>
		
		<?php if (strpos($_SERVER['HTTP_HOST'], 'miracas.in') !== false) { ?>
			<?php require_once("../menu_global.php"); ?> 
			<?php require_once("../sidebar_global.php"); ?> 
		<?php }else{ ?>
			<?php require_once("../menu.php"); ?> 
			<?php require_once("../sidebar.php"); ?> 
		<?php } ?>

		<div id="menu-page-wraper" style="background:transparent;">
	<?php } ?>
	
	<div id="particles-js">

	<br><br>

	<div class="page-wrap"><div class="page-wrap-inner">

	<?php 
		$id_fb_user 			= $user['id'];

	/* ====================================================================== *
        	CLASSES
 	 * ====================================================================== */	

		$shopping_assistant 				= new shopping_assistant();
		$shopping_assistant_conversation 	= new shopping_assistant_conversation();
		$product 							= new product();
		$keyword 							= new keyword();

	/* ====================================================================== *
        	GET ID_CONVERSATION
 	 * ====================================================================== */		

        $id_shopping_assistant_conversation = "";

        if(isset($_GET['id_conversation'])){
        	$id_shopping_assistant_conversation = $_GET['id_conversation'];
        }else{
	        
	        // GET CURRENT ID_CONVERSATION (CHECK IF THERE'S A "PENDING ON CUSTOMER" CONVERSATION IF SO THEN USE THAT AS CURRENT ID_CONVERSATION)
	        if($shopping_assistant_conversation->get_current_conversation($id_fb_user)){
	        	$id_shopping_assistant_conversation = $shopping_assistant_conversation->get_current_conversation($id_fb_user);
	        }
	        // CREATE A NEW ID_CONVERSATION
	        else{

	        	/* Begin transaction */

	        	$conn->beginTransaction();
				$error 			= false;

				/* Create conversation */

	        	$id_shopping_assistant_conversation = $shopping_assistant_conversation->max_id_shopping_assistant_conversation($id_fb_user);

				$shopping_assistant_conversation->set_id_fb_user($id_fb_user);
				$shopping_assistant_conversation->set_id_shopping_assistant_conversation($id_shopping_assistant_conversation);
				if( !$shopping_assistant_conversation->insert() ){ $error = true; }

				/* Save initial message */

				$shopping_assistant->set_id_fb_user($id_fb_user);
				$shopping_assistant->set_id_shopping_assistant($shopping_assistant->max_id_shopping_assistant($id_fb_user));
				$shopping_assistant->set_message('We have loaded your selected price range from the previous conversations. Feel free to modify.');
				$shopping_assistant->set_from('smart_response');
				$shopping_assistant->set_id_shopping_assistant_conversation($id_shopping_assistant_conversation);
				$shopping_assistant->set_step('1');

				if( !$shopping_assistant->insert() ){ $error = true; };
				if( !$shopping_assistant_conversation->update_step($id_fb_user, $id_shopping_assistant_conversation, '1') ){ $error = true; };

				/* End transaction */

				if($error){
				  $conn->rollBack();
				  echo '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
				  exit();
				}else{
				  $conn->commit();
				}
	        }

	    }

    /* ====================================================================== *
        	INFO FROM COVNERSATION
 	 * ====================================================================== */		    

        $shopping_assistant_conversation->map($id_fb_user, $id_shopping_assistant_conversation);

        $last_price_range = '2000 - 4000';
        if($shopping_assistant_conversation->get_last_price_range_used($id_fb_user)){
        	$last_price_range = $shopping_assistant_conversation->get_last_price_range_used($id_fb_user);
        }
        $last_price_range_explode = explode(' - ', $last_price_range);

	/* ====================================================================== *
        	GET THE MESSAGES
 	 * ====================================================================== */			

		$all_messages 		= $shopping_assistant->get_messages_by_user($id_fb_user, $id_shopping_assistant_conversation, " order by date ");

	/* ====================================================================== *
        	PARAMTERS, GENDER AND AGE
 	 * ====================================================================== */					

		$extra_param = "";
		if(isset($_GET['set_gender']) && $_GET['set_gender'] != ''){ 
			$extra_param = "&set_gender=".$_GET['set_gender'];
		}

		$age 		= "";
		$gender 	= "";

		//SET GENDER, WHEN THE USER DIDN'T LOGIN USING FB
		if(isset($_GET['set_gender'])){
			$user['gender'] = $_GET['set_gender'];
		}

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

	?>

	<?php if(isset($_GET['from_history'])){ ?>
		<?php require_once("../message.php"); ?>
	<?php }else if($shopping_assistant_conversation->get_status() == 'pending on customer'){ ?>
		<style>
			.navbar-header:before, .navbar-header:after {
			    display: none !important;
			    content: " ";
			}
			.navbar-nav {
			    margin: 0 !important;
			}
		</style>
		<!--<a href="javascript:close_conversation();" class="btn btn-primary btn-green btn-sm close_conversation">Close Assistant Shopping</a>-->
		<nav class="navbar navbar-default">
  			<div class="container-fluid">
    			<div class="navbar-header">
      				<a class="navbar-brand">MIRACAS</a>
    			</div>

    			<div class="navbar-header pull-right">
      				<ul class="nav navbar-nav ">
        				<li><a href="javascript:close_conversation();"><i class="glyphicon glyphicon-remove"></i>&nbsp; Close Assistant Shopping</a></li>
    				</ul>
    			</div>

    			<div class="collapase navbar-collapse" id="bs-example-navbar-collapse-1">
      				
    			</div>
  			</div>
		</nav>
		
	<?php } ?>

	<div class="tabs-container">

	    <div class="chat">

			<?php foreach($all_messages as $row){ ?>
				<?php $who = ( $row->get_from() == 'admin' || $row->get_from() == 'system' || $row->get_from() == 'smart_response' ) ? 'other' : 'self'; ?>
				<div class="<?php echo $who; ?>">
					<div class="msg format_msg" data-msg="<?php echo $row->get_message(); ?>" data-step="<?php echo $row->get_step(); ?>" data-who="<?php echo $who; ?>"></div>
				</div>	
			<?php } ?>

			<div class="new_message">

			<!--
			/* ====================================================================== *
		        	LOADING
		 	 * ====================================================================== */	
		 	-->

		 		<div class="msg_loading"></div>	

			<!--
			/* ====================================================================== *
		        	CLOSED CONVERSATION
		 	 * ====================================================================== */	
		 	-->

		 		<div class="msg_input step_closed">
		 			<div class="alert alert-info text-center" style="margin:0;"><strong>This conversation is closed</strong></div>
				</div>

			<!--
			/* ====================================================================== *
		        	PRICE RANGE SLIDER
		 	 * ====================================================================== */	
		 	-->
				
				<div class="msg_input step_1">
					<div class="no_ui_slider_container">
						<div id="no_ui_slider"></div>
					</div>
					<a href="javascript:send_price_range();" class="btn btn-green btn-block btn-md">PROCEED</a>
				</div>	

			<!--
			/* ====================================================================== *
		        	WRITE SOME MESSAGE
		 	 * ====================================================================== */	
		 	-->
				
				<div class="msg_input no-padding step_0">
					<div class="col-container col-without-margin">
						<div class="col-80"><input type="text" class="form-control message" placeholder="Write a message here" /></div>
						<div class="col-20"><a href="javascript:send_text_msg();" class="btn btn-green btn-block">Send</a></div>
					</div>
				</div>

			<!--
			/* ====================================================================== *
		        	"MOOD" KEYWORDS
		 	 * ====================================================================== */	
		 	
		 		DISCONTINUED, NOT USED AT THIS MOMENT
				
				<div class="msg_input select_keyword step_2">
					<div class="select_keyword_loading"></div>
				</div>		
			-->				

			<!--
			/* ====================================================================== *
		        	SELECT THE PRODUCTS THAT MATCH THE KEYWORD AND THE PRICE RANGE
		 	 * ====================================================================== */	
		 	-->

				<div class="msg_input select_product step_3 <?php if($shopping_assistant_conversation->get_step() == '3'){?>step_closed<?php } ?>">
					<div class="select_product_loading"></div>
				</div>		
					
			</div>

		</div>

	</div>

	</div></div>

	<!--
	/* ====================================================================== *
        	PARTICLES JS FOR THE BACKGROUND
 	 * ====================================================================== */	
 	-->

	<link rel="stylesheet" href="../includes/plugins/particlesjs/particles.css">
	<script src="../includes/plugins/particlesjs/particles.min.js"></script>
	<script src="../includes/plugins/particlesjs/particles_app.js"></script>
	<style>
		#particles-js canvas{
			position: absolute;
			top:0;
			left: 0;
			z-index: 0;
		}
		.page-wrap{
			position: relative;
			z-index:1 !important;
		}
	</style>

	<!--
	/* ====================================================================== *
        	PRICE RANGE SLIDER
 	 * ====================================================================== */	
 	-->

 	<link rel="stylesheet" href="../includes/plugins/noUiSlider/nouislider.min.css">
	<script src="../includes/plugins/noUiSlider/nouislider.min.js"></script>
	<script src="../includes/plugins/noUiSlider/wNumb.js"></script>
	<style>
		.no_ui_slider_container {
		    position: relative;
		    padding: 40px 30px 50px 30px;
		    overflow: hidden;
		}
		.noUi-horizontal .noUi-handle {
			width: 14px;
			left: -7px;
			cursor: pointer;
		}
		.noUi-handle:after, .noUi-handle:before {
			background: none !important;
		}
	</style>
	<script>
		var no_ui_slider = document.getElementById('no_ui_slider');

		noUiSlider.create(no_ui_slider, {
			start: [ <?php echo $last_price_range_explode[0]; ?>, <?php echo $last_price_range_explode[1]; ?> ], // 4 handles, starting at...
			connect: true, // Display a colored bar between the handles
			behaviour: 'tap-drag', // Move handle on tap, bar is draggable
			step: 500,
			//tooltips: true,
			tooltips: [ wNumb({ decimals: 0 }), wNumb({ decimals: 0 }) ],
			range: {
				'min': 0,
				'max': 10000
			},
			pips: { // Show a scale with the slider
				mode: 'positions',
				values: [0,20,40,60, 80,100],
				//stepped: true,
				density: 5
			}
		});
	</script>

	<!--
	/* ====================================================================== *
        	GRIDS
 	 * ====================================================================== */	
 	-->

 	<style>
		.item-container{
			margin: 0 -3px;
			overflow: hidden;
		}
		.item{
			width: 25%;
			float: left;
			padding: 0 3px 6px 3px;
		}
		@media only screen and (max-width: 768px) {
			.item{
				width: 50%;
			}
		}
		.item a{
			text-decoration: none;
		}
		.item-image-container{
			overflow: hidden;
			position: relative;
			display: block;

			background-color: black;
			background-image: url('../includes/plugins/Media Boxes/plugin/css/icons/loading-image.gif');
			background-position: center center;
    		background-repeat: no-repeat;
		}
		.item-image{
			background-size:100% auto;
			width: 100%;
			height: 100%;
		}
		.check_item{
			position: absolute;
			top: 4px;
			left: 6px;
		}
		.item-text{
			background: #fff !important;
			color: #999 !important;
			font-size: 12px !important;
			text-align: center !important;
			text-decoration: none !important;
			padding: 10px 8px;
		}
 	</style>

 	<!--
	/* ====================================================================== *
        	MEDIA BOXES
 	 * ====================================================================== */	
 	-->

 	<!-- Media Boxes CSS files -->
  	<link rel="stylesheet" href="../includes/plugins/Media Boxes/plugin/components/Font Awesome/css/font-awesome.min.css"> <!-- only if you use Font Awesome -->
	<link rel="stylesheet" href="../includes/plugins/Media Boxes/plugin/components/Magnific Popup/magnific-popup.css"> <!-- only if you use Magnific Popup -->
	<link rel="stylesheet" type="text/css" href="../includes/plugins/Media Boxes/plugin/css/mediaBoxes.css">
	<style>
		.media-boxes-no-more-entries{
			display: none;
		}
	</style>

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

	/* ====================================================================== *
        	FORMAT THE MESSAGES
 	 * ====================================================================== */		

 	 	$('.format_msg').each(function(){
 	 		var $this 	= $(this);
 	 		var msg 	= $this.attr('data-msg');
 	 		var step 	= $this.attr('data-step');
 	 		var who 	= $this.attr('data-who');

 	 		$this.html(format(msg, who, step));
 	 	});

 	 	function format(msg, who, step){

 	 		var new_formated_msg = msg;
 	 		
 	 		if(who=='self' && step=='1'){
 	 			var price_range_arr = msg.split(' - ');

 	 			new_formated_msg = '<div class="no_ui_slider_container"><div id="no_ui_slider_locked"></div></div>';

				setTimeout(function(){

					$('.format_msg[data-step="1"][data-who="self"]').css('width', '70%');

	 	 			var no_ui_slider_locked = document.getElementById('no_ui_slider_locked');

	 	 			no_ui_slider_locked.setAttribute('disabled', true);

					noUiSlider.create(no_ui_slider_locked, {
						start: [ price_range_arr[0], price_range_arr[1] ], // 4 handles, starting at...
						connect: true, // Display a colored bar between the handles
						behaviour: 'tap-drag', // Move handle on tap, bar is draggable
						step: 500,
						//tooltips: true,
						tooltips: [ wNumb({ decimals: 0 }), wNumb({ decimals: 0 }) ],
						range: {
							'min': 0,
							'max': 10000
						},
						pips: { // Show a scale with the slider
							mode: 'positions',
							values: [0,20,40,60, 80,100],
							//stepped: true,
							density: 5
						}
					});

				}, 400);
 	 		}


 	 		return new_formated_msg;
 	 	}

	/* ====================================================================== *
        	RESIZE GRIDS
 	 * ====================================================================== */		

		function resize_grid_thumbs(){
			setTimeout(function(){
		    	var items 	= $('.item-image-container:visible');
		    	var width 	= items.outerWidth(true);
		    	items.height(width-1);
	    	}, 10);
	    }

	    $(window).resize(function(){
	    	resize_grid_thumbs();
	    });

	/* ====================================================================== *
        	SCROLL TO THE BOTTOM
 	 * ====================================================================== */		

		function scroll_to_div(){
			//$(window).scrollTop($('.chat').offset().top + $('.chat').outerHeight(true) - $(window).height());
			$(window).scrollTop($('.new_message').offset().top + 80 - $(window).height());
		}
		scroll_to_div();

	/* ====================================================================== *
        	GET STEP
 	 * ====================================================================== */	

 	 	function get_step(msg){

 	 		$('.msg_loading').show();
 	 		
			$.get('get_step.php?id_shopping_assistant_conversation=<?php echo $id_shopping_assistant_conversation; ?>', function(r){

				$('.msg_loading').hide();								
				update_msg_input($.trim(r));

			});

		}		

		get_step();

	/* ====================================================================== *
        	DISPLAY THE CORRECT INPUT TOOL
 	 * ====================================================================== */

 	 	function update_msg_input(step){
 	 				
			$('.msg_input').hide();
			//$('.step_'+step).show().css('display', 'flex').find('input').focus();
			$('.step_'+step).show().find('input').focus();
			scroll_to_div(); 

			resize_grid_thumbs();

 	 	}

 	 /* ====================================================================== *
        	CLOSE CONVERSATION
 	 * ====================================================================== */		 		

 	 	function close_conversation(){
 	 		location.href='close_conversation.php<?php echo  str_replace("&", "?", $extra_param); ?>';
 	 	}

 	/* ====================================================================== *
        	SAVE SELECTED PRODUCTS
 	 * ====================================================================== */		 	

 	 	$('body').on('click', '.save_id_product', function(){
 	 		var $this = $(this);

 	 		if($this.hasClass('saving'))return;

 	 		$this.html('SAVING...').addClass('saving');

 	 		// SAVE VIA AJAX

			$.post('../feed/save_products.php', 
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

 	 /* ====================================================================== *
        	GET PRODUCTS
 	 * ====================================================================== */		

 	 	$('body').on('click', '.show_me_more', function(){
 	 		var $this = $(this);

 	 		if($this.hasClass('loading')) return;

 	 		$this.addClass('loading');
 	 		$this.html("<i class='fa fa-circle-o-notch fa-spin'></i> &nbsp;Loading...");
 	 		
 	 		get_products($this.attr('data-next-profile'))

 	 	})

 	 	function get_products(profile){

 	 		$.post('get_products.php', 
				{ 
					id_shopping_assistant_conversation 	: "<?php echo $id_shopping_assistant_conversation; ?>",
					gender 								: "<?php echo $gender; ?>",
					age 								: "<?php echo $age; ?>",
					extra_param 						: "<?php echo $extra_param; ?>",
					profile								: profile,
				}, 
				function(r){
					
					if($.trim(r) != ''){
						var new_products = $($.trim(r));

						$('.select_product').append(new_products);

						if($('.select_product').find('.select_product_loading')[0]!==undefined){ // First Time
							$('.select_product').find('.select_product_loading').remove();
							scroll_to_div(); 	
						}

						setTimeout(function(){
							$('.show_me_more[data-next-profile="'+profile+'"]').remove(); // timeout so it gives time to the media boxes to get there! otherwise there's a small jump in the scroll
						}, 100)
					}
					
					resize_grid_thumbs();

				});
	 	 	
 	 	}

 	 	get_products('init');	

 	/* ====================================================================== *
        	GET KEYWORDS
 	 * ====================================================================== */		
/*
 	 	function get_keywords(){
 	 		if($('.select_keyword').find('.select_keyword_loading')[0] !== undefined){

	 	 		$.post('get_keywords.php', 
					{ 
						id_shopping_assistant_conversation 	: "<?php echo $id_shopping_assistant_conversation; ?>",
						gender 								: "<?php echo $gender; ?>",
						age 								: "<?php echo $age; ?>"
					}, 
					function(r){
						console.log($.trim(r));
						if($.trim(r) != ''){
							$('.select_keyword').html($.trim(r));
						}
						scroll_to_div(); 
						resize_grid_thumbs();

					});
	 	 		
			}
 	 	}

 	 	get_keywords();	 	
*/
 	/* ====================================================================== *
        	SEND PRICE RANGE 
 	 * ====================================================================== */	 	

 	 	function send_price_range(){

 	 		var price_range = no_ui_slider.noUiSlider.get();

 	 		send_msg(Math.floor(price_range[0])+" - "+Math.floor(price_range[1]));

 	 	}

	/* ====================================================================== *
        	SEND MSG
 	 * ====================================================================== */	

 	 	$(".msg_input .message").on('keyup', function (e) {
		    if (e.keyCode == 13) {
		        send_text_msg();
		    }
		});

 	 	function send_text_msg(){
 	 		send_msg($('.message').val());
 	 		$('.message').val('').focus();
 	 	}		

 	 	var current_step = '<?php echo $shopping_assistant_conversation->get_step(); ?>';

		function send_msg(msg){

			/* ### CHECK IF MESSAGE IS EMPTY ### */
			
			if(msg == ''){
				alert('Your message is empty!');
				return;
			}

			/* ### PREPARE NEW MESSAGE AND PUT IT IN THE CONVERSATION WITH A LOADING WHEEL ### */

			var new_msg = show_msg('self', msg, true, current_step);
			scroll_to_div();

			/* ### TRY TO SEND THE MSG ### */

			$('.msg_loading').show();
			$('.msg_input').hide();

			$.post('send_message.php', 
				{ 
					message 							: msg, 
					id_shopping_assistant_conversation 	: "<?php echo $id_shopping_assistant_conversation; ?>"
				}, 
				function(r){

					current_step = r.new_step;

					new_msg.find('.msg-loading').html( r.error=='yes' ? '<i class="fa fa-times"></i> Error!' : '' );

					if(r.admin_msg != '' && r.error=='no'){
						show_msg('other', r.admin_msg, false, current_step)
					}

					if(r.error=='no'){
						$('.msg_loading').hide();
						update_msg_input(current_step);

						get_products('init')
						get_keywords();
					}else{
						alert('Oops! Something went wrong, refresh the page and try again!');
					}
				});

		}

		function show_msg(from, msg, loading, current_step){
			var msg_loading = loading===true ? '<i class="fa fa-circle-o-notch fa-spin"></i>' : '';
			var new_msg 	= 	'<div class="'+from+'"> '+
									'<div class="msg format_msg" data-msg="'+msg+'" data-step="'+current_step+'" data-who="'+from+'">'+
										format(msg, from, current_step)+' '+
										'<span class="msg-loading">'+msg_loading+'</span>'+
									'</div>'+
								'</div>';

			return $(new_msg).insertBefore( $('.new_message') );
		}

	</script>

	</div>
	
	<?php if(isset($_GET['from_history'])){ ?>
		</div>
	<?php } ?>
</body>
</html>
