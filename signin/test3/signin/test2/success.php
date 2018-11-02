<?php require_once("fb_validator.php"); ?>

<?php 
	if( $active_session !== true ){
		echo "<script>location.href='index.php';</script>";
	}
?>

<!doctype html>
<html lang="en">
<head>
  
  <meta charset="utf-8">

</head>
<body>

  <h1>Login Success!</h1>

  <?php print_r($user); ?>

  <a href="signout.php" class="btn btn-blue btn-block">
    Sign out
  </a>

</body>

</html>