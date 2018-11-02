<?php 

	echo "<h1>Test cURL</h1>";
/*
	$exists 			= 'no';
	$ch = curl_init("https://detail.1688.com/offer/522992523050.html?spm=a2615.7691456.0.0.N9qqL4");
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_exec($ch);
	$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	print_r("RETCODE:".$retcode."<br>");
	if($retcode == 200 || $retcode == 302 || $retcode == 304 || $retcode == 301){
		$exists = 'yes';
	}
	curl_close($ch);
*/

    $url = "https://detail.1688.com/offer/522992523050.html?spm=a2615.7691456.0.0.N9qqL4";
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, false);
	$data = curl_exec($curl);
	curl_close($curl);

	echo $data;

   