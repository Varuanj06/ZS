<?php require_once("../classes/message.php"); ?>

<?php 
  $extra_param = "";
  if($active_session !== true){ 
    $extra_param = "?set_gender=".str_replace("/", "", $_GET['set_gender']);
  }
?>

<!--
/* ====================================================================== *
      NAVBAR
 * ====================================================================== */        
--> 

  <nav class="navbar navbar-default fixed-element">
    <div class="container-fluid">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
        <a class="navbar-brand js-slideout-toggle">
          <i class="fa fa-bars" aria-hidden="true"></i> &nbsp;
          <img src="../includes/img/logo_white.png" alt="" style="height:28px;margin-top:-10px;margin-left:10px;">
        </a>
      </div>
      
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        
        <ul class="nav navbar-nav navbar-right">
          <li><a href="../feed<?php echo $extra_param; ?>"><span class="fa fa-home"></span>Home</a></li>
          <li><a href="../feed?set_gender=male"><span class="fa fa-female"></span>For Men</a></li>
          <li><a href="../feed?set_gender=female"><span class="fa fa-male"></span>For Women</a></li>

          <?php if($active_session === true){ ?>
            <?php if($disable_things !== true){ ?>
              <li><a href="../cart"><span class="fa fa-shopping-cart"></span>Cart</a></li>
              <li><a href="../orders"><span class="fa fa-truck"></span>My Orders</a></li>
              <li><a href="../messages"><span class="fa fa-comment"></span> Messages</a></li>
            <?php } ?>
            <li><a href="../signout.php"><span class="fa fa-power-off"></span>Logout</a></li>
          <?php }else{ ?>
            <li><a href="javascript:menu_fb_login();"><span class="fa fa-arrow-right"></span>Sign in</a></li>
          <?php } ?>
        </ul>
        
      </div>

    </div>
  </nav>

  <link rel="stylesheet" href="../includes/css/style_global.css">

<!-- 
/* ====================================================================== *
        FB LOGIN IN MODAL
 * ====================================================================== */ 
--> 

  <script>
    function menu_fb_login(){
      $('#myModal_menu_fb_login').modal('show').appendTo('body');
    }
  </script>

  <!-- Modal -->
  <div class="modal-login modal fade" id="myModal_menu_fb_login" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-mask">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <a class="close-modal" data-dismiss="modal" aria-label="Close">
            <i class="fa fa-close"></i>
          </a>

          <div class="modal-body">
            <a href="<?php echo $loginUrl; ?>" class="btn btn-blue btn-block">
              <i class="fa fa-facebook-official"></i> &nbsp;
              Sign in with Facebook
            </a>
          </div>
        </div>
      </div>
      </div>
  </div>

