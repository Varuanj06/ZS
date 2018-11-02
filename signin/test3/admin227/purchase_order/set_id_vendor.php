<?php require_once("../session.php"); ?>

<?php 
	
	$_SESSION['id_vendor'] = $_GET['id_vendor'];
	echo "<script>location.href='purchase_order.php';</script>";

?>
