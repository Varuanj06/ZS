<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/shopping_assistant_conversation.php"); ?>
<?php require_once("../classes/keyword.php"); ?>
<?php require_once("../classes/fb_user_profile.php"); ?>

<?php 
	
	$id_fb_user 							= $user['id'];
	$id_shopping_assistant_conversation 	= $_POST['id_shopping_assistant_conversation'];
	$gender 								= $_POST['gender'];
	$age 									= $_POST['age'];

	$shopping_assistant_conversation 		= new shopping_assistant_conversation();
	$product 								= new product();
	$keyword 								= new keyword();
	$fb_user_profile 						= new fb_user_profile();

	$shopping_assistant_conversation->map($id_fb_user, $id_shopping_assistant_conversation);
	$fb_user_profile->map_last_profile($id_fb_user);

	if($shopping_assistant_conversation->get_price_range() != ""){

?>
		
		<?php 

			// BRING KEYWORDS FROM PRODUCTS
			$product_list 	= $product->get_all_keywords($gender, $age, "");

			// STRACT KEYWORDS ONLY
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

		?>
	
		<div class="item-container">
			<?php foreach ($keywords_list as $value) { ?>
				<?php if(strtoupper(substr( $value, 0, 4 )) !== "MOOD"){ continue; } ?>
				<?php if (strpos($keyword->get_profiles_from_keyword($value), '/'.$fb_user_profile->get_profile().'/') !== false) { /* all good */ }else{ continue; } ?>
				<div class="item">
				 	<a href="javascript:send_msg('<?php echo $value; ?>');">
				 		<div class="item-image-container">
			            	<div class="item-image" style="background-image:url('<?php echo $keyword->get_image_from_keyword($value); ?>')"></div>
						</div>

			            <div class="item-text">
			                <?php echo $value; ?>
			            </div>
		            </a>
	        	</div>
			<?php } ?>
		</div>	
		
<?php

	}

 ?>
