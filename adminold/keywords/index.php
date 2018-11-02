<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/product.php"); ?>
<?php require_once("../../classes/keyword.php"); ?>
<?php require_once("../../classes/functions.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
</head>
<body>

	<?php require_once("../navbar.php"); ?>

	<?php 

		$keyword = new keyword();

		// DELETE IMAGE FROM KEYWORD
		$error  = "";
		if( isset($_POST['action']) ){
			
			$keyword->set_keyword($_POST['keyword']);

			if( $keyword->exists() ){
				if($keyword->delete()){
					//all good
				}else{
					$error = '<div class="alert alert-danger"><strong>Oops</strong>, there was an error.</div>';
				}
			}
		}

		//GET KEYWORDS
		$product 		= new product();
		$product_list 	= $product->get_all_keywords('', '', "");
		$keywords_list 	= array();

		foreach ($product_list as $row) {
			$arr = explode("/", $row->get_keywords());
			foreach ($arr as $word) {
				if($word != ""){
					$keywords_list[] = $word;
				}
			}
		}
		$keywords_list = array_unique($keywords_list);	
		
	?>

	<style>
		td .btn{
			vertical-align: bottom;
		}
	</style>

	<div class="section">
		<div class="content">

			<h2>
				Keywords
			</h2>

			<?php echo $error; ?>

			<hr>

			<form action="" method="post" name="form">
				<input type="hidden" name="action" />
				<input type="hidden" name="keyword" />
			
				<table class="table table-condensed table-bordered">
					<tr>
						<th>#</th>
						<th>Image</th>
						<th>Keywords</th>
					</tr>
					<?php 
						$cont = 0;
						foreach ($keywords_list as $value) { 
							$cont++;

							$keyword->set_keyword($value);
					?>
							<tr>
								<td><?php echo $cont; ?></td>
								<td>
									<?php if($keyword->exists()){ ?>
										<img src="<?php echo $keyword->get_image_from_keyword($value); ?>" height="60px" alt="">
										<a href="edit.php?keyword=<?php echo $value; ?>" class="btn btn-blue btn-sm">
											<i class="glyphicon glyphicon-pencil"></i> &nbsp;Edit Image
										</a>
										<a href="javascript:erase('<?php echo $value; ?>');" class="btn btn-red btn-sm">
											<i class="glyphicon glyphicon-trash"></i>
										</a>
									<?php }else{ ?>
										<a href="add.php?keyword=<?php echo $value; ?>" class="btn btn-green btn-sm">
											<i class="glyphicon glyphicon-pencil"></i> &nbsp;New Image
										</a>
									<?php } ?>
								</td>
								<td><?php echo $value; ?></td>
							</tr>
					<?php 
						} 
					?>
				</table>
			</form>

		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../keywords"]').parents('li').addClass('active');</script>

	<script>
		function erase(keyword){
			if( confirm("Are you sure?") ){
				document.form.action.value = "1";
				document.form.keyword.value = keyword;
				document.form.submit();
			}
		}
	</script>
	
</body>
</html>