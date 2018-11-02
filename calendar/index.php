<?php $force_session = true; ?>
<?php require_once("../fb_validator.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/espresso_keywords.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>

	<!-- Media Boxes CSS files -->
  	<link rel="stylesheet" href="../includes/plugins/Media Boxes/plugin/components/Font Awesome/css/font-awesome.min.css"> <!-- only if you use Font Awesome -->
	<link rel="stylesheet" href="../includes/plugins/Media Boxes/plugin/components/Magnific Popup/magnific-popup.css"> <!-- only if you use Magnific Popup -->
	<link rel="stylesheet" type="text/css" href="../includes/plugins/Media Boxes/plugin/css/mediaBoxes.css">

	<link rel="stylesheet" href="../includes/css/checkbox.css">
	<style>
		body{
			overflow-y: scroll;
		}
		@media only screen and (max-width: 768px) {
			.tabs-container{
				padding: 0 10px !important;
			}
		}

		.espresso_keyword_img_calendar{
			position: absolute;
			width: 60px;
			top: 30px;
			left: 30px;
		}
		.espresso_keyword_img_month{
			width: 100%;
			background: #fb7272;
			padding: 3px;
			text-align: center;
			color: #fff;
			font-size: 11px;
		}
		.espresso_keyword_img_day{
			background: white;
			padding: 5px;
			text-align: center;
			font-size: 19px;
		}
	</style>

</head>
<body>

	<?php require_once("../menu.php"); ?>
	<?php require_once("../sidebar.php"); ?>
	<div id="menu-page-wraper">

	<div class="page-wrap"><div class="page-wrap-inner">
	
	<?php require_once("../message.php"); ?>

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

	/* ====================================================================== *
	    	GET ESPRESSO KEYWORDS
	 * ====================================================================== */		


		$espresso_keywords 		= new espresso_keywords();
		$espresso_keywords_list = $espresso_keywords->get_lastest_60_created($user_gender_slash, $user_age_slash);

		$extra_param = "&set_gender=".str_replace("/", "", $_GET['set_gender']);
	?>

	<div class="tabs-container">

		<div id="grid">
			<?php 
				$count_bg = 0;
				foreach ($espresso_keywords_list as $row) { 
					$count_bg++;
			?>

				<?php 
					$timestamp = strtotime($row->get_created_at());
				?>

				<div class="media-box <?php echo "bg_$count_bg"; ?>">
					<a href="../feed?id_espresso_keyword=<?php echo $row->get_id_keyword(); ?><?php echo str_replace("?", "&", $extra_param); ?>">
			            <div class="media-box-image">
			                <div data-thumbnail="<?php echo $row->get_image(); ?>" data-width="600" data-height="600"></div>

			                <div class="espresso_keyword_img_calendar">
								<div class="espresso_keyword_img_month"><?php echo date("F", $timestamp); ?></div>
								<div class="espresso_keyword_img_day"><?php echo date("d", $timestamp); ?></div>
							</div>
			            </div>
			        </a>
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
	        	boxesToLoadStart: 12,
		    	boxesToLoad: 4,
		    	deepLinkingOnPopup: false,
        		deepLinkingOnFilter: false,
		    });	
		
	</script>

	</div></div>

	<?php require_once("../footer.php"); ?>

	</div>
</body>
</html>