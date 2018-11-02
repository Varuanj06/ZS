<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/keyword.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
</head>
<body>

	<?php require_once("../navbar.php"); ?>


	<?php 

		$keyword = new keyword();

		$error  = "";
		if( isset($_POST['action']) ){

			$keyword->set_keyword($_GET['keyword']);
			$keyword->set_image($_POST['image']);

			if( $keyword->insert() ){
				$error = '<div class="alert alert-success"><strong>Awesome</strong>, You successfully added a new image.</div>';
			}else{
				$error = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}
			
		}
		
	?>

	<div class="section">
		<div class="content">

			
			<h2>New image for "<?php echo $_GET['keyword']; ?>"</h2>
			<a href="./" class="btn btn-default btn-sm">Go back</a>

			<hr>

			<?php echo $error; ?>

			<?php if($error != ''){exit();} ?>
			
			<form action="" method="post" name="form">
				<input type="hidden" name="action" />

				<p>
					<label for="image">Image url</label>
					<input class="custom-input" name="image" id="image" maxlength="600" placeholder="Write the image URL" />
				</p>

				<p>
					<a href="javascript:save();" class="btn btn-default btn-large btn-green">Save</a>
				</p>

			</form>


		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../products"]').parents('li').addClass('active');</script>

	<script>
		function save(){

			$('.alert').hide();

			if(document.form.name.value != ""){
				document.form.action.value = "1";
				document.form.submit();
			}else{
				$('<div class="alert alert-danger">All fields are required.</div>').fadeIn().prependTo(form);
			}
		}	
	</script>

</body>
</html>