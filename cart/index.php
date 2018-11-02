<?php require_once("../fb_validator.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/order.php"); ?>

<!doctype html>
<html lang="en">
<head>

	<link rel="stylesheet" href="../includes/plugins/show_msg/show_msg.css">
  	<link rel="stylesheet" href="../includes/plugins/blockui/blockui.css">
  	<link rel="stylesheet" href="../includes/plugins/bootstrap wizard/wizard.css">

  	<?php require_once("../head.php"); ?>

  	<style>
  		body{
  			overflow-y: scroll;
  		}
  		[ng-click]{
  			cursor: pointer;
  		}
  		input[type="text"]{
			border: 1px solid #ccc;
			height:24px;
			padding: 6px 8px;
			display: block;
		}
		.row{
			margin-bottom: 10px;
		}
		.available_vouchers{
			border: 1px dotted gray;
			margin-right: 0 !important;
    		margin-left: 0 !important;
			max-width: 560px;
		}
		.available_vouchers>div{
			padding: 5px 10px;
			font-size:12px;
			font-weight:bold;
		}
		.row{
			margin-bottom: 10px;
		}
		label{
			font-weight: 600;
		}
		
		.btn-group .btn:first-child{
			border: none !important;
		}
		.btn-group .btn{
			border: none !important;
		    background-color: #DEC395;
    		color: #6D4D1C !important;
		    padding: 10px;
		    font-size: 13px;
		    font-weight: 200;
		}
		.btn-group .btn:hover{
			background-color: #DABB84;
		}
		.btn-group .active{
			background-color: #D3AA64 !important;
		}
		.btn-group i{
			display: none;
		}
		.btn-group label.active i{
			display: inline-block;
			margin-right: 4px;
		}
		.with_img{
			padding: 8px !important;
		}
		.with_img i{
			top: 4px !important;
		}
		.with_img img{
			height:22px;
		}
		.with_img span{
			position:relative;
			top:2px;
		}
		.wizard .disabled{
			display: none;
		}
		.form-wizard .tab-content {
			overflow: hidden;
		}

		@media only screen and (max-width: 768px) {
		    #products th,
		    #products td{
		    	padding: 3px;
		    }
		    .form-wizard .tab-content {
			    padding: 10px;
			}
		}

		.payment_methods{
			display: block;
			margin-bottom: 10px;
		}
		.payment_methods input{
			margin-right: 10px;
		}
		.payment_methods img{
			height: 20px;
		}
		.payment_methods span{
			font-weight: normal;
		}
  	</style>

  	<script src="../includes/js/angular.min.js"></script>
  	<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.6.6/angular-sanitize.js"></script>
  	<script src="../includes/plugins/show_msg/show_msg.js"></script>
  	<script src="../includes/plugins/blockui/blockui.js"></script>
  	<script src="../includes/plugins/bootstrap wizard/jquery.bootstrap.wizard.min.js"></script>
  	<script src="../includes/plugins/bootstrap wizard/wizard.js"></script>

</head>
<body>

	<div class="spinner-container" ng-cloak>
	    <span class="spinner"></span>
	</div>

	<?php if (strpos($_SERVER['HTTP_HOST'], 'miracas.in') !== false) { ?>
		<?php require_once("../menu_global.php"); ?> 
		<?php require_once("../sidebar_global.php"); ?> 
	<?php }else{ ?>
		<?php require_once("../menu.php"); ?> 
		<?php require_once("../sidebar.php"); ?> 
	<?php } ?>

	<style>
  		body, #menu-page-wraper {
		    background: #fff !important;
		}
	</style>

	<div id="menu-page-wraper">

		<div class="page-wrap"><div class="page-wrap-inner">
		
		<?php require_once("../message.php"); ?>
		<script>$('.nav-right a[href="../cart"]').addClass('selected');</script>

		<div class="tabs-container">

			<h2 class="shopping-cart-title"><span class="fa fa-shopping-cart"></span> Shopping Cart</h2>
			<br>

			<?php require_once("pay.php"); ?>
			<?php require_once("cart.php"); ?>	

			<br>

		</div> <!-- End tabs-container -->
		
		</div></div>
		<?php require_once("../footer.php"); ?>
		
	</div>

</body>

</html>