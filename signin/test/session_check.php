<?php 

	$disable_things = false;
  
  	if( $active_session !== true && !isset($force_session) ){
		echo "<script>location.href='../signin';</script>";
	}

?>