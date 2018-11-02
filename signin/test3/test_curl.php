<?php 

	echo "<h1>Test cURL</h1>";

	$exists 			= 'no';
	$ch = curl_init("https://detail.1688.com/offer/37466122786.html?spm=a2615.7691456.0.0.EhkD7J");
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_exec($ch);
	$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	print_r("RETCODE:".$retcode." ".$product_link."<br>");
	if($retcode == 200 || $retcode == 302 || $retcode == 304){
		$exists = 'yes';
	}
	curl_close($ch);
