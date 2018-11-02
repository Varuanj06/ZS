<?php require_once("../../fb_validator.php"); ?>
<?php require_once("../../session_check.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("getAll_data.php"); ?>

<?php 
	
	$output = array();

	set_all($output);

	echo json_encode($output);
	
?>
