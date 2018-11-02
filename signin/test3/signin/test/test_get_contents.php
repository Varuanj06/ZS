<?php 

	echo "<h1>Test get contentes</h1>";

	$page = file_get_contents("http://detail.1688.com/offer/522992523050.html?spm=a2615.7691456.0.0.N9qqL4");
	//$page = file_get_contents("https://www.youtube.com/watch?v=ZjplPP6HvF8");

	$search = strpos($page, "商品已下架");

	if ($search !== false) {
	    echo "<p>FOUND</p>";
	} else {
	    echo "<p>NOT FOUND</p>";
	}
