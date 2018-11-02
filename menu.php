<?php require_once("../classes/message.php"); ?>

<?php 
	$extra_param = "";
	if($active_session !== true){ 
		$extra_param = "?set_gender=".str_replace("/", "", $_GET['set_gender']);
	}
?>

<link rel="stylesheet" href="../includes/css/style_global.css">

<!--
/* ====================================================================== *
      NAVBAR
 * ====================================================================== */        
--> 

  <nav class="navbar navbar-default fixed-element">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand js-slideout-toggle">
          <i class="fa fa-bars" aria-hidden="true"></i> &nbsp;
          <!--MIRACAS-->
          <img src="../includes/img/logo_small.png" alt="" height="30px">
        </a>
      </div>
      
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        
        <ul class="nav navbar-nav navbar-right">
          <li><a href="../feed<?php echo $extra_param; ?>"><span class="fa fa-home"></span> &nbsp;Home</a></li>
          <li><a href="../feed?set_gender=male"><span class="fa fa-male"></span>For Men</a></li>
          <li><a href="../feed?set_gender=female"><span class="fa fa-female"></span>For Women</a></li>

      		<?php if($active_session === true){ ?>
      			<?php if($disable_things !== true){ ?>
      				<li><a href="../cart"><span class="fa fa-shopping-cart"></span> &nbsp;Cart</a></li>
      				<li><a href="../orders"><span class="fa fa-th-list"></span> &nbsp;My Orders</a></li>
              <li><a href="../messages"><span class="fa fa-comment"></span> Messages</a></li>
      			<?php } ?>
      			<li><a href="../signout.php"><span class="fa fa-power-off"></span> &nbsp;Logout</a></li>
      		<?php }else{ ?>
      			<li><a href="../signin"><span class="fa fa-arrow-right"></span> &nbsp;Sign in</a></li>
      		<?php } ?>
        </ul>
        
      </div>

    </div>
  </nav>
  