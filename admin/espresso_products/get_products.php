<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/product_lang.php"); ?>

<?php

// Data to parse
$data = array();

$q = isset($_REQUEST['q']) ? $_REQUEST['q'] : '';

// Searching
$product_lang 	= new product_lang();
$products 		= $product_lang->search(trim($q), "ORDER BY NAME");
foreach ($products as $row) {
	$data[] = array('id' => $row->get_id_product(),	'name' => $row->get_id_product()." - ".$row->get_name());
}

echo json_encode($data);