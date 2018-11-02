<!doctype html>
<html lang="en">
<head>

	<title>Bills</title>
	<meta charset="utf-8">

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=0"> <!-- needed for mobile devices -->
  	
	<!-- Include CSS -->
	<link rel="stylesheet" href="includes/plugins/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="includes/css/style.css">

	<!-- Include JS -->
	<script src="includes/js/jquery-1.10.2.min.js"></script>
	<script src="includes/plugins/bootstrap/js/bootstrap.min.js"></script>

</head>

<body>

	<!-- HEADER WITH NAVIGATION -->
	
	<div class="header">
		<div class="content">

			<div class="navbar navbar-default" role="navigation">
			  <!-- Brand and toggle get grouped for better mobile display -->
			  <div class="navbar-header">
			    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
			      <span class="sr-only">Toggle navigation</span>
			      <span class="icon-bar"></span>
			      <span class="icon-bar"></span>
			      <span class="icon-bar"></span>
			    </button>
			    <a class="navbar-brand" href="./">
			    	Shipment Way Bills
			    </a>
			  </div>
			</div>
		</div>
	</div>

	<div class="section">
		<div class="content">
			<form method="post" action="makePDF.php" enctype="multipart/form-data">
				<h1>Upload an Excel file <small>(.xls, .xlsx)</small></h1>
				<p><input type="file" name="spreadsheet" /></p>
				<p><input class="btn btn-primary" type="submit" name="submit" value="Submit" /></p>
			</form>
		</div>
	</div>


</body>	

</html>