<?php

	/*
	function get_next_step($old_step, $msg){
		$new_step = $old_step;

		if($old_step == '1'){

			if (strpos($msg, ' - ') === false && count(explode(' - ', $msg)) != 2){
			    $new_step = 'ERROR';
			}else{
				global $shopping_assistant_conversation;
				global $id_fb_user;
				global $id_shopping_assistant_conversation;

				$shopping_assistant_conversation->update_price_range($id_fb_user, $id_shopping_assistant_conversation, $msg);

				$new_step = '2';

				// ### SAVE PROFILE ### 

				global $fb_user_profile;
				global $user;
			      
			    $fb_user_profile->save_profile( $user, $shopping_assistant_conversation->get_last_price_range_by_user($user['id']) );

			}

		}else if($old_step == '2'){

			if(strtoupper(substr( $msg, 0, 4 )) !== "MOOD"){
			    $new_step = 'ERROR';
			}else{
				global $shopping_assistant_conversation;
				global $id_fb_user;
				global $id_shopping_assistant_conversation;

				$shopping_assistant_conversation->update_keyword($id_fb_user, $id_shopping_assistant_conversation, $msg);

				$new_step = '3';
			}

		}
			
		return $new_step;	
	}
	*/

	function get_next_step($old_step, $msg){
		$new_step = $old_step;

		if($old_step == '1'){

			if (strpos($msg, ' - ') === false && count(explode(' - ', $msg)) != 2){
			    $new_step = 'ERROR';
			}else{

				// global vars

				global $shopping_assistant_conversation;
				global $id_fb_user;
				global $id_shopping_assistant_conversation;

				// update price range

				$shopping_assistant_conversation->update_price_range($id_fb_user, $id_shopping_assistant_conversation, $msg);

				// save profile

				global $fb_user_profile;
				global $user;
			      
			    $fb_user_profile->save_profile( $user, $shopping_assistant_conversation->get_last_price_range_by_user($user['id']) );

			    // new step

			    $new_step = '3';

			}

		}
			
		return $new_step;	
	}

	function get_status($old_step, $new_step){
		$status = 'pending on customer';

		return $status;
	}

	function get_admin_msg($new_step){
		$admin_msg = '';

		if($new_step == '3'){
    		$admin_msg = 'Here are few suggestions. Select a few that you like and I will show you more tailored yo your interest. Click on Buy Now to purchase.';
    	}

    	return $admin_msg;
	}








