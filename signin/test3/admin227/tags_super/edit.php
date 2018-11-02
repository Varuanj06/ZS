<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/tag_super.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
</head>
<body>

	<?php require_once("../navbar.php"); ?>

	<?php 

		$tag_super 	= new tag_super();

		$tag_super->map($_GET['id_tag_super']);

		$error  = "";
		if( isset($_POST['action']) ){

			$tag_super->set_name($_POST['name']);
			$tag_super->set_gender($_POST['gender']);
			$tag_super->set_image($_POST['image']);

			if( $tag_super->exists() && $tag_super->update() ){
				$error = '<div class="alert alert-success"><strong>Awesome</strong>, You successfully updated the super tag.</div>';
				echo "<script>location.href='./'</script>";
				exit();
			}else{
				$error = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}
		}
		
	?>

	<div class="section">
		<div class="content">

			
			<h2>Edit Super Tag</h2>
			<a href="./" class="btn btn-default btn-sm">Go back</a>

			<hr>

			<?php echo $error; ?>
			
			<form action="" method="post" name="form">
				<input type="hidden" name="action" />

				<p>
					<label for="name">Name</label>
					<input value="<?php echo $tag_super->get_name(); ?>" class="custom-input" name="name" id="name" maxlength="600" placeholder="Write a name" />
				</p>

				<p>
					<label for="gender">Gender</label>
					<select name="gender" id="gender" class="form-control">
						<option value="male" <?php if($tag_super->get_gender()=='male'){echo "selected";} ?>>Male</option>
						<option value="female" <?php if($tag_super->get_gender()=='female'){echo "selected";} ?>>Female</option>
					</select>
				</p>

				<p>
					<label for="image">Image</label>
					<input class="custom-input" name="image" id="image" maxlength="300" placeholder="Write an image url" value="<?php echo $tag_super->get_image(); ?>" />
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