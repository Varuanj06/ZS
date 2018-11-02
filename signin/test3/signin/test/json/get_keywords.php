<?php
	
	require_once("../dbconnect.php");    
	require_once("../classes/product.php");
	require_once("../classes/keyword.php");
	require_once("../classes/search_history.php");

	//OBJETCS
	$product 		= new product();
	$keyword 		= new keyword();
	$search_history = new search_history();

	$gender 		= "";

	if( isset($_GET['gender']) ){
		$gender = $_GET['gender'];
		if($gender != ''){
			$gender  = "/".$gender."/";
		}
	}

	// GET ALL KEYWORDS
	$product_list 	= $product->get_all_keywords($gender, $_GET['age'], "");
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

	// GET LATEST SEARCHED KEYWORDS
	$last_keywords 	= $search_history->get_all_last_keywords(" order by date desc ");

	// COMBINE ALL KEYWORDS AND LATEST SEARCHED
	$final_keywords = array();
	foreach ($last_keywords as $row){
		$kw 	= $row->get_keyword();
		if(in_array($kw, $keywords_list) && !in_array($kw, $final_keywords)){
			$final_keywords[] = $kw;
		}
	}

	foreach ($keywords_list as $value) {
		if(!in_array($value, $final_keywords)){
			$final_keywords[] = $value;
		}
	}

	// FORMAT OUTPUT AND BRING IMAGE
	$output = array();
	foreach ($final_keywords as $row){
		$tmp 					= array();
		$tmp['keyword'] 		= $row;
		$tmp['image'] 			= $keyword->get_image_from_keyword($row);
		$tmp['total_products'] 	= count($product->get_search($tmp['keyword'], $_GET['gender'], $_GET['age'], ""));

		if(strtoupper(substr( $tmp['keyword'], 0, 8 )) === "CATEGORY"){
			// category keywords, so ignore
			continue;
		}

		$output[] 				= $tmp;
	}

	//echo json_encode($output, JSON_PRETTY_PRINT);
	echo json_encode($output);

//get_keywords.php?gender=female&age=