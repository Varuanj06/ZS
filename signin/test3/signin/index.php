<?php require_once("../fb_validator.php"); ?>
<?php require_once("../classes/fb_user_profile.php"); ?>
<?php require_once("../classes/fb_user_personal_info.php"); ?>
<?php require_once("../classes/shopping_assistant_conversation.php"); ?>
<div class="text-center login-loading" style="margin-top:50px;"><i class="fa fa-refresh fa-spin" style="font-size: 40px;"></i></div>

<!doctype html>
<html lang="en">
<head>

  <title>Sign In</title>
  <meta charset="utf-8">

  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=0"> <!-- needed for mobile devices -->
    
  <!-- Include CSS -->
  <link rel="stylesheet" href="../includes/plugins/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../includes/plugins/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="../includes/css/style.css">
  <link rel="stylesheet" href="../includes/css/login.css">

  <!-- Include JS -->
  <script src="../includes/js/jquery-1.10.2.min.js"></script>
  <script src="../includes/plugins/bootstrap/js/bootstrap.min.js"></script>

  <?php

    if( $active_session === true ){

      /* ### SAVE PROFILE ### */

      require_once("../dbconnect.php");

      $fb_user_profile                  = new fb_user_profile();
      $shopping_assistant_conversation  = new shopping_assistant_conversation();
      
      $fb_user_profile->save_profile( $user, $shopping_assistant_conversation->get_last_price_range_by_user($user['id']) );

      /* ### SAVE PERSONAL INFO ### */

      $id_fb_user             = isset($user['id'])?$user['id']:'';
      $user_birthday          = isset($user['birthday'])?$user['birthday']:'';
      $user_gender            = isset($user['gender'])?$user['gender']:'';
      $user_name              = isset($user['first_name'])?$user['first_name']:'';
      $user_last_name         = isset($user['last_name'])?$user['last_name']:'';

      $fb_user_personal_info  = new fb_user_personal_info();

      $fb_user_personal_info->set_id_fb_user($id_fb_user);
      $fb_user_personal_info->set_name($user_name);
      $fb_user_personal_info->set_last_name($user_last_name);
      $fb_user_personal_info->set_gender($user_gender);
      $fb_user_personal_info->set_birthday($user_birthday);

      if($fb_user_personal_info->exists()){
        $fb_user_personal_info->update();
      }else{
        $fb_user_personal_info->insert();
      }

      /* ### WHEN USER WITHOUT FB LOGIN CLICKED THE "LOGIN TO OPEN SHOPPING ASSISTANT" BUTTON IN THE KEYWORDS PAGE, REDIRECT HIM TO THE SHOPPING ASSISTANT PAGE ### */

      if(isset($_SESSION['shopping_assistant_without_login'])){
        
        echo "<script>location.href='../shopping_assistant';</script>";
        
        unset($_SESSION['shopping_assistant_without_login']);
        exit();
      }

      /* ### WHEN USER WITHOUT FB LOGIN CLICKED THE "LOGIN TO SEE MORE STYLES" BUTTON AT THE BOTTOM OF THE PRODUCT FEED PAGE, HE WANTS TO SEE THE PIXELS ### */

      else if(isset($_SESSION['pixels_without_login'])){
        
        echo "<script>location.href='../pixels?pixel_keyword=".$_SESSION['pixels_pixel_keyword']."&q=".$_SESSION['pixels_without_login_q']."';</script>";
        
        unset($_SESSION['pixels_without_login']);
        unset($_SESSION['pixels_without_login_q']);
        unset($_SESSION['pixels_pixel_keyword']);
        exit();
      }

      /* ### WHEN THE USER WITHOUT FB LOGIN ADDED A PRODUCT TO HIS CART, THIS HAPPENS IN THE PRODUCT FEED PAGE AND IN THE PRODUCT_DETAILS PAGE (HE WILL BE REDIRECTED TO THE CART PAGE) ### */

      else if(isset($_SESSION['add_to_cart_without_login'])){
  ?>
        <script>
          var color               = '<?php echo $_SESSION['without_login_color']; ?>';
          var size                = '<?php echo $_SESSION['without_login_size']; ?>';
          var qty                 = '<?php echo $_SESSION['without_login_qty']; ?>';
          var idProduct           = '<?php echo $_SESSION['without_login_id_product']; ?>';
          var idProductPrestashop = '<?php echo $_SESSION['without_login_id_product_prestashop']; ?>';
          jQuery.get('../feed/add_to_cart.php?color='+encodeURIComponent(color)+'&size='+size+'&qty='+qty+'&id_product_prestashop='+idProductPrestashop+'&id_product='+idProduct, function(r){
            if($.trim(r) == "ERROR"){
              alert("Oops! something went wrong!");
            }else{
              location.href='../cart';
            }
          });
        </script>
  <?php
        unset($_SESSION['add_to_cart_without_login']);
        exit();
      }

      /* ### THE NORMAL FB LOGIN ### */
      else{
        if (strpos($_SERVER['HTTP_HOST'], 'miracas.in') !== false) {
          echo "<script>location.href='../feed/global.php';</script>";
        }else{
          echo "<script>location.href='../feed';</script>";  
        }

        exit();
      }

    }

    unset($_SESSION['add_to_cart_without_login']); // IN CASE HE WANTED TO ADD A PRODUCTO TO THE CART BUT CHANGED HIS MIND AND CLOSES THE FB LOGIN POPUP
    unset($_SESSION['pixels_without_login']); // IN CASE HE WANTED TO GO TO THE PIXEL PAGE BUT CHANGED HIS MIND AND CLOSES THE FB LOGIN POPUP
    unset($_SESSION['shopping_assistant_without_login']); // IN CASE HE WANTED TO GO TO THE ASSISTANT SHOPPING PAGE BUT CHANGED HIS MIND AND CLOSES THE FB LOGIN POPUP
  ?>

</head>
<body>

  <script>
    jQuery('.login-loading').css('display','none');
  </script>
  <style>
    .big-circle{
      line-height: normal !important;
      vertical-align: top !important;
    }
    .big-circle span{
      position: relative;
      top: 50%; 
      transform: translateY(-50%);
      -webkit-transform: translateY(-50%);
    }
  </style>

  <div class="background">
    <div class="mask"></div>
  </div>
  
	<div class="login-box">
    <div class="title">
      <h2>Product Feed</h2>
      <p>
        Get the products that you need
      </p>
    </div>
  
    <div class="big-circle-container">
      <a href="../feed?set_gender=male"><div class="big-circle"><span>Men</span></div></a>
      <a href="../feed?set_gender=female"><div class="big-circle"><span>Women</span></div></a>
      <a href="#"><div class="big-circle"><span>Home<br>decor</span></div></a>
    </div>

    <a href="<?php echo $loginUrl; ?>" class="btn btn-blue btn-block">
      <i class="fa fa-facebook-official"></i> &nbsp;
      Sign in with Facebook
    </a>
  </div>

</body>

</html>