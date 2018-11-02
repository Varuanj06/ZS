<?php 
	session_start();

	if( isset($_SESSION['userId-product-feed']) ){
		header ("Location: ../keywords");
	}
?>

<html>
<head>
	<title>Sign In</title>

	<link rel="stylesheet" type="text/css" href="../../includes/plugins/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="../includes/css/login.css">
</head>
<body>

	<div class="section">
		<div class="content">

			<div class="row" style="margin:0;">
				<div class="col-md-6" style="padding:0;">
			
					<h2>Sign In</h2>
					
					<hr>
					
					<form action="" method="post">
				    
				    	<p><input class="custom-input" id="user" name="user" type="text" placeholder="User"></p>
				    	
				    	<p><input class="custom-input" id="password" name="password" type="password" placeholder="Password"></p>
						
						<p><span class="login-error"></span></p>
						
						<p><button id="sign-in" type="submit" class="btn btn-default btn-block">Sign In</button></p>

				    </form>

				</div>
			</div>

		</div>
	</div>

	<script src="../../includes/js/jquery-1.10.2.min.js"></script>
	<script src="../includes/js/signin.js"></script>
</body>
</html>
