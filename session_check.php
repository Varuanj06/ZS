<?php 

	$disable_things = false;
  
  	if( $active_session !== true && !isset($force_session) ){
		echo "<script>location.href='../signin';</script>";
		/*echo "log_session: $log_session";
		echo "<br>";
		echo "log_valid_session: $log_valid_session";
		exit();*/
	}

?>