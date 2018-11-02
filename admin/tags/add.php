<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/tag.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
</head>
<body>

	<?php require_once("../navbar.php"); ?>


	<?php 

		if(!isset($_SESSION['current_id_tag_super'])){
			echo "<script>location.href='../tags_super';</script>";
			exit();
		}

		$tag = new tag();

		$error  = "";
		if( isset($_POST['action']) ){

			$tag->set_name($_POST['name']);
			$tag->set_id_tag_super($_SESSION['current_id_tag_super']);
			$tag->set_image($_POST['image']);

			if( $tag->insert() ){
				$error = '<div class="alert alert-success"><strong>Awesome</strong>, You successfully added a new tag.</div>';
			}else{
				$error = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}
			
		}
		
	?>

	<div class="section">
		<div class="content">

			
			<h2>Add a new tag</h2>
			<a href="./" class="btn btn-default btn-sm">Go back</a>

			<hr>

			<?php echo $error; ?>
			
			<form action="" method="post" name="form">
				<input type="hidden" name="action" />
				
				<p>
					<label for="name">Name</label>
					<input class="custom-input" name="name" id="name" maxlength="600" placeholder="Write a name" />
				</p>

				<p>
					<label for="image">Image</label>
					<input class="custom-input" name="image" id="image" maxlength="300" placeholder="Write an image url" />
				</p>

				<p>
					<a href="javascript:save();" class="btn btn-default btn-large btn-green">Save</a>
				</p>

			</form>


		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../keywords"]').parents('li').addClass('active');</script>

	<script>
		function save(){

			if(document.form.name.value != ""){
				document.form.action.value = "1";
				document.form.submit();
			}else{
				alert('All fields are required.');
			}
		}	
	</script>

</body>
</html>