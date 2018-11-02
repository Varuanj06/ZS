<!DOCTYPE html>
<html>
  	<head>
		<META HTTP-EQUIV="content-type" CONTENT="text/html; charset=utf-8">
	</head>

	<h1>Test link</h1>

	<form action="" method="post">
		<input type="text" name="link" style="width:100%;" value="<?php echo isset($_POST['link'])?$_POST['link']:"https://detail.1688.com/offer/38317618708.html?spm=a2615.7691456.0.0.DmRQfN"; ?>" />
		<br>
		<input type="submit">
	</form>

<?php

	if(isset($_POST['link'])){

		require_once("includes/plugins/snoopy/Snoopy.class.php");

		$exists 		= 'no';

		if(filter_var($_POST['link'], FILTER_VALIDATE_URL) === FALSE){// this checks if the link is valid
			$exists = 'no';
		}else{
			$snoopy 		= new Snoopy;
			$snoopy->fetchtext($_POST['link']);
			$page 			=  mb_convert_encoding($snoopy->results, 'utf-8', "gb18030");
			$response_code 	= $snoopy->response_code;

			if(strpos($response_code, "404 Not Found") !== false || strpos($page, "商品已下架") !== false){
			    $exists 	= 'no';
			}else{
			    $exists 	= 'yes';
			}
		}

		if($exists === 'no'){
		    echo "<p>404 ERROR</p>";
		} else {
		    echo "<p>GOOD LINK</p>";
		}
	}
?>

</html>

	