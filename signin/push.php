
<?php 
$url = 'http://13.251.70.41/socials/callback/';
$params = array(
    'share_id' => utf8_encode('test')
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);

// This should be the default Content-type for POST requests
//curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded"));

$result = curl_exec($ch);
if(curl_errno($ch) !== 0) {
    error_log('cURL error when connecting to ' . $url . ': ' . curl_error($ch));
}

curl_close($ch);
print_r($result);



echo '<script type="text/javascript">
           window.location = "http://miracas.com/ZS/signin/emiracasmobile.html"
      </script>';	
	  
?>