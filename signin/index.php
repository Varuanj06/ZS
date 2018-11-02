<?php require_once("../fb_validator.php"); ?>
<?php require_once("../classes/fb_user_profile.php"); ?>
<?php require_once("../classes/fb_user_personal_info.php"); ?>
<?php require_once("../classes/shopping_assistant_conversation.php"); ?>
<style>
    body{
        background: #f3f3f3 !important;
    }
    .fa-refresh{
        color: #d33d0f;
    }
</style>
<div class="text-center login-loading" style="margin-top:50px;"><i class="fa fa-refresh fa-spin" style="font-size: 40px;"></i></div>

<!doctype html>
<html lang="en">
<head>

    <title>Miracas International Fashion</title>
	<meta name="description" content="Big Fashion Days Sale: Miracas is India's Best Fashion and Lifestyle Online Shopping site for men, women &amp; kids. Buy clothing, shoes, Watches, footwear and more from your favorite online fashion store Miracas at best price. *COD *15-Days Returns *Free Shipping.">
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

        if(isset($_SESSION['join_the_deal_withouth_login'])){
            echo "<script>location.href='../feed/product_details.php?id_product=".$_SESSION['without_login_id_product']."';</script>";
            unset($_SESSION['join_the_deal_withouth_login']);
            unset($_SESSION['without_login_id_product']);
            exit();
        }

      /* ### WHEN USER WITHOUT FB LOGIN CLICKED THE "LOGIN TO OPEN SHOPPING ASSISTANT" BUTTON IN THE KEYWORDS PAGE, REDIRECT HIM TO THE SHOPPING ASSISTANT PAGE ### */

        else if(isset($_SESSION['shopping_assistant_without_login'])){
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
            var color                   = '<?php echo $_SESSION['without_login_color']; ?>';
            var size                    = '<?php echo $_SESSION['without_login_size']; ?>';
            var qty                     = '<?php echo $_SESSION['without_login_qty']; ?>';
            var idProduct               = '<?php echo $_SESSION['without_login_id_product']; ?>';
            var idProductPrestashop     = '<?php echo $_SESSION['without_login_id_product_prestashop']; ?>';
            var espressoProduct         = '<?php echo $_SESSION['without_login_espresso_product']; ?>';
            var with_keyword_discount   = '<?php echo $_SESSION['without_login_with_keyword_discount']; ?>';
            var keyword_code            = '<?php echo $_SESSION['without_login_keyword_discount_code']; ?>';

            if(with_keyword_discount == 'yes'){
                location.href='../feed/product_details.php?id_product='+idProduct;
            }else{
                jQuery.get('../feed/add_to_cart.php?color='+encodeURIComponent(color)+'&size='+size+'&qty='+qty+'&id_product_prestashop='+idProductPrestashop+'&id_product='+idProduct+'&espresso_product='+espressoProduct+'&with_keyword_discount='+with_keyword_discount+'&keyword_code='+keyword_code, function(r){
                    if($.trim(r) == "ERROR"){
                        alert("Oops! something went wrong!");
                    }else{
                        location.href='../cart';
                    }
                });    
            }
            
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
        body, 
        #menu-page-wraper 
        .mfp-container, 
        .media-box, 
        .media-boxes-load-more-button, 
        .media-boxes-filter, 
        .media-boxes-drop-down-menu > li > a, 
        .media-boxes-drop-down-header, 
        .media-boxes-search {
            font-family: 'Roboto', sans-serif !important;
            background: white !important;
        }  

        .head{
            padding: 40px 20px 10px 20px;
            text-align: center;
            border-bottom: 1px dashed #d6d6d6;
        }    

        .login_container::after {
            content: "";
            clear: both;
            display: table;
        }
        .login_container>div{
            float: left;
            width: 50%;
            padding: 30px 10px;
        }
        .description{
            text-align: right;
        }
        .login{
            text-align: left;
            padding-left: 0 !important;
            border-left: 1px dashed #d6d6d6;
        }
        .description img{
            max-width: 400px;
            width: 100%;
        }

        @media only screen and (max-width: 768px) {
            .description{
                display: none;
            }
            .login_container>div{
                width: 100%;
                border-left: none;
            }
        }

        .login_btn{
            position: relative;
            display: table;
            font-size: 40px;
            font-weight: bold;
            padding: 0px 30px;
            background: rgb(235,235,235);
            color: rgb(217,101, 96) !important;
            height: 80px;
            margin-bottom: 20px;
            text-decoration: none !important; 
        }
        .login_btn>.center{
            display: table-cell;
            vertical-align: middle;
            text-decoration: none !important; 
        }
        .login_btn:after {
            content: "";
            position: absolute;
            top: 0;
            right: -40px;
            width: 0;
            height: 0;
            border-top: 40px solid transparent;
            border-bottom: 40px solid transparent;
            border-left: 40px solid rgb(235,235,235);
        }

        .login_btn_women{
            background: rgb(219,105,97);
            color: rgb(255,255,255) !important;
        }
        .login_btn_women:after{
            border-left: 40px solid rgb(219,105,97);   
        }

        .login_fb{
            background: rgb(68,103,126);
            color: rgb(255,255,255) !important;   
            font-size: 17px;
            line-height: normal;
            font-weight: normal;
        }
        .login_fb .fa{
            font-size: 40px;
            float: left;
            margin-right: 20px;
        }
        .login_fb:after{
            border-left: 40px solid rgb(68,103,126);   
        }

        .login_android{
            background: rgb(163,177,133);
            color: rgb(255,255,255) !important;   
            font-size: 17px;
            line-height: normal;
            font-weight: normal;
        }
        .login_android .fa{
            font-size: 40px;
            float: left;
            margin-right: 20px;
        }
        .login_android:after{
            border-left: 40px solid rgb(163,177,133);   
        }

        .made{
            color: #a0a0a0;
            font-size: 12px;
        }
        .made .fa{
            color: red;
        }

    </style>
    
    <div class="head">
        <img src="logo.png" alt="" height="60px">
    </div>

    <div class="login_container">
        <div class="description">
            <img src="description.png">
        </div>
        <div class="login">
            
            <a href="../feed?set_gender=female" class="login_btn login_btn_women">
                <span class="center">WOMEN</span>
            </a>

            <a href="../feed?set_gender=male" class="login_btn">
                <span class="center">MEN</span>
            </a>

            <a href="<?php echo $loginUrl; ?>" class="login_btn login_fb">
                <span class="center">
                    <i class="fa fa-facebook"></i>
                    Login with
                    <br>
                    Facebook
                </span>
            </a>

            <a href="market://details?id=com.miracas" class="login_btn login_android">
                <span class="center">
                    <i class="fa fa-android"></i>
                    Amazing Discount on
                    <br>
                    Android App
                </span>
            </a>

        </div>
    </div>

    <br>
    <br>
    <br>

    <div class="made text-center">
        Made with <span class="fa fa-heart"></span> in India
    </div>

    <br>

    <div class="text-center">
        <img src="be_a_seller.png" alt="" height="60px">
    </div>


        
    <!--
    <div class="login_buttons">
        <a href="../feed?set_gender=male" class="btn btn-default">Men</a>
        <a href="<?php echo $loginUrl; ?>" class="btn btn-blue"><i class="fa fa-facebook-official"></i> &nbsp;Log in with Facebook</a>
        <a href="../feed?set_gender=female" class="btn btn-default">Women</a>
    </div>
    
    <a href="market://details?id=com.miracas">
        <div class="android_app_text">
            <span class="fa fa-android"></span>
            <div>Get Amazing Discounts on Android App</div>
            <img src="../includes/img/login_phone.png" alt="">
        </div>
    </a>
    -->

</body>

</html>