<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/keyword.php"); ?>
<?php require_once("../../classes/product.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
</head>
<body>

	<?php require_once("../navbar.php"); ?>


	<?php 

		function format_string($str){

			$str = trim($str);
			if($str[0] != "/"){
				$str = "/".$str;
			}
			if($str[strlen($str)-1] != "/"){
				$str = $str."/";
			}

			$str = str_replace("/ ", "/", $str);

			return $str;
		}

		$product = new product();
		$keyword = new keyword();
		$keyword->map($_GET['keyword']);

		$error  = "";
		if( isset($_POST['action']) ){
			
			$keyword->set_keyword($_POST['keyword']);
			$keyword->set_image($_POST['image']);
			$keyword->set_genders(format_string($_POST['genders']));
			$keyword->set_ages(format_string($_POST['ages']));

			$product->update_keyword_data($keyword->get_keyword(), $keyword->get_genders(), $keyword->get_ages(), $_GET['keyword']);

			if( $keyword->exists($_GET['keyword']) && $keyword->update($_GET['keyword']) ){
				$error = '<div class="alert alert-success"><strong>Awesome</strong>, You successfully updated the keyword.</div>';
				echo "<script>location.href='./'</script>";
				exit();
			}else{
				$error = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}
		}
		
	?>

	<div class="section">
		<div class="content">

			
			<h2>Edit keyword</h2>
			<a href="./" class="btn btn-default btn-sm">Go back</a>

			<hr>

			<?php echo $error; ?>
			
			<form action="" method="post" name="form">
				<input type="hidden" name="action" />

				<p>
					<label for="keyword">Keyword</label>
					<input value="<?php echo $_GET['keyword']; ?>" class="custom-input" name="keyword" id="keyword" maxlength="600" placeholder="Write a keyword" />
				</p>

				<p>
					<label for="image">Image url</label>
					<input value="<?php echo $keyword->get_image(); ?>" class="custom-input" name="image" id="image" maxlength="600" placeholder="Write the image URL" />
				</p>

				<p>
					<label for="genders">Genders</label>
					<textarea class="custom-input" name="genders" id="genders" maxlength="300" placeholder="write genders here, the genders must have slashes between them (i.e. /male/female/)"><?php echo $keyword->get_genders(); ?></textarea>
				</p>

				<p>
					<label for="ages">Ages</label>
					<textarea class="custom-input" name="ages" id="ages" maxlength="600" placeholder="write ages here, the ages must have slashes between them (i.e. /15/16/17/18/19/)"><?php echo $keyword->get_ages(); ?></textarea>
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