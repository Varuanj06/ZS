<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/search_history.php"); ?>
<?php require_once("../classes/search_history_detail.php"); ?>

<div class="all-other-users-feed">
	<?php 
		$search_history 		= new search_history();
		$search_history_detail 	= new search_history_detail();

		$other_users_search 	= $search_history->get_list($user['id'], $_GET['user_gender'], " order by date desc limit 0, 2000 ");

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