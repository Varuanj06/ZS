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
        <!--
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        -->
        <a class="navbar-brand js-slideout-toggle">
          <i class="fa fa-bars" aria-hidden="true"></i> &nbsp;
          MIRACAS
        </a>
      </div>
      
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        
        <ul class="nav navbar-nav navbar-right">
          <li><a href="../feed<?php echo $extra_param; ?>"><span class="fa fa-home"></span> &nbsp;Home</a></li>
          <li><a href="../feed?set_gender=male"> &nbsp;For Men</a></li>
          <li><a href="../feed?set_gender=female"> &nbsp;For Women</a></li>

      		<?php if($active_session === true){ ?>
      			<?php if($disable_things !== true){ ?>
      				<li><a href="../cart"><span class="fa fa-shopping-cart"></span> &nbsp;Cart</a></li>
      				<li><a href="../orders"><span class="fa fa-th-list"></span> &nbsp;My Orders</a></li>
              <?php $message    = new message(); ?>
              <li>
                <a href="../messages">
                  <span class="badge"><?php echo $message->get_unread_messages($user['id'], 'admin'); ?></span>&nbsp;
                  Messages
                </a>
              </li>
      			<?php } ?>
      			<li><a href="../signout.php"><span class="fa fa-power-off"></span> &nbsp;Logout</a></li>
      		<?php }else{ ?>
      			<li><a href="../signin"><span class="fa fa-arrow-right"></span> &nbsp;Sign in</a></li>
      		<?php } ?>
        </ul>
        
      </div>

    </div>
  </nav>
  