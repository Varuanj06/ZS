<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/espresso_products.php"); ?>
<?php require_once("../../classes/espresso_keywords.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
</head>
<body>

	<?php require_once("../navbar.php"); ?>

	<?php 

		$espresso_products 	= new espresso_products();
		$espresso_keywords 	= new espresso_keywords();

		// DELETE IMAGE FROM KEYWORD
		$error  = "";
		if( isset($_POST['action']) ){
			
			$espresso_keywords->set_id_keyword($_POST['id_keyword']);

			if( $espresso_keywords->exists($_POST['id_keyword']) ){
				if(!$espresso_products->keyword_exists($_POST['id_keyword'])){
					if($espresso_keywords->delete()){
						// do nothing
					}else{
						$error = '<div class="alert alert-danger"><strong>Oops</strong>, there was an error.</div>';
					}
				}else{
					$error = '<div class="alert alert-danger"><strong>Oops</strong>, you can not erase this keyword because it has products.</div>';
				}
			}
		}

		//GET KEYWORDS
		$espresso_keywords 		= new espresso_keywords();
		$keywords 				= $espresso_keywords->get_list(" ORDER BY 1 ");
		
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

			<p>
				<a href="add.php" class="btn btn-blue btn-sm">
					<i class="glyphicon glyphicon-pencil"></i> &nbsp;Add a new keyword
				</a>
			</p>

			<p>
				<input type="text" class="custom-input" placeholder="Search" id="search" style="max-width:200px;">
			</p>

			<?php echo $error; ?>

			<form action="" method="post" name="form">
				<input type="hidden" name="action" />
				<input type="hidden" name="id_keyword" />
			
				<table class="table table-condensed table-bordered" id="table">
					<thead>
						<tr>
							<th>#</th>
							<th>Action</th>
							<th>Keyword</th>
							<th>Image</th>
							<th>Genders</th>
							<th>Ages</th>
							<th>Status</th>
							<th>Description</th>
						</tr>
					</thead>
					<?php 
						$cont = 0;
						foreach ($keywords as $value) { 
							$cont++;
					?>
							<tr>
								<td><?php echo $cont; ?></td>
								<td>
									<a href="javascript:erase('<?php echo $value->get_id_keyword(); ?>');" class="btn btn-red btn-sm">
										<i class="glyphicon glyphicon-trash"></i>
									</a>
									<a href="edit.php?id_keyword=<?php echo $value->get_id_keyword(); ?>" class="btn btn-green btn-sm">
										<i class="glyphicon glyphicon-pencil"></i> &nbsp;Edit
									</a>
									<a href="import.php?to=espresso@<?php echo $value->get_id_keyword(); ?>" class="btn btn-gray btn-sm">
										<i class="glyphicon glyphicon-arrow-down"></i> &nbsp;Import products
									</a>
								</td>
								<td>
									<a href="../espresso_products?id_keyword=<?php echo $value->get_id_keyword(); ?>">
										<?php echo $value->get_keyword(); ?>
									</a>
								</td>
								<td>
									<img src="<?php echo $value->get_image(); ?>" height="60px" alt="">
								</td>
								<td><?php echo $value->get_genders(); ?></td>
								<td><?php echo $value->get_ages(); ?></td>
								<td><?php echo $value->get_status(); ?></td>
								<td><?php echo $value->get_description(); ?></td>
							</tr>
					<?php 
						} 
					?>
				</table>
			</form>

		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../espresso_keywords"]').parents('li').addClass('active');</script>

	<script>
		function erase(id_keyword){
			if( confirm("Are you sure?") ){
				document.form.action.value 		= "1";
				document.form.id_keyword.value 	= id_keyword;
				document.form.submit();
			}
		}
	</script>

	<script src="../includes/js/search.js"></script>
	<script>
		$('#search').focus().search({table:$("#table")});
	</script>
	
</body>
</html>