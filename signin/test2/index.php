<?php require_once("fb_validator.php"); ?>

<!doctype html>
<html lang="en">
<head>

  <meta charset="utf-8">

  <?php

    if( $active_session === true ){
      echo "<script>location.href='success.php';</script>";
    }

  ?>

</head>
<body>

  <h1>Login Test</h1>

  <a href="<?php echo $loginUrl; ?>" class="btn btn-blue btn-block">
    <i class="fa fa-facebook-official"></i> &nbsp;
    Sign in with Facebook
  </a>

</body>

</html>