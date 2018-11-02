<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/pixel_keyword.php"); ?>
<?php require_once("../../classes/pixel.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
</head>
<body>

	<?php require_once("../navbar.php"); ?>

	<?php 

		$pixel_keyword 	= new pixel_keyword();
		$pixel 			= new pixel();

		// DELETE IMAGE FROM KEYWORD
		$error  = "";
		if( isset($_POST['action']) ){
			
			$pixel_keyword->set_keyword($_POST['pixel_keyword']);

			if( $pixel_keyword->exists($_POST['pixel_keyword']) ){
				if(!$pixel->pixel_keyword_exists($_POST['pixel_keyword'])){
					if($pixel_keyword->delete()){
						//all good
					}else{
						$error = '<div class="alert alert-danger"><strong>Oops</strong>, there was an error.</div>';
					}
				}else{
					$error = '<div class="alert alert-danger"><strong>Oops</strong>, you can not erase this keyword because it has pixels.</div>';
				}
			}
		}

		//GET KEYWORDS
		$pixel_keyword 			= new pixel_keyword();
		$list_pixel_keywords 	= $pixel_keyword->get_list(" ORDER BY 1 ");
		
	?>

	<style>
		td .btn{
			vertical-align: bottom;
		}
	</style>

	<div class="section">
		<div class="content">

			<h2>
				Pixel Keywords
			</h2>

			<p>
				<a href="add.php" class="btn btn-blue btn-sm">
					<i class="glyphicon glyphicon-pencil"></i> &nbsp;Add a new pixel keyword
				</a>
			</p>

			<p><input type="text" class="custom-input" placeholder="Search" id="search" style="max-width:200px;"></p>

			<?php echo $error; ?>

			<form action="" method="post" name="form">
				<input type="hidden" name="action" />
				<input type="hidden" name="pixel_keyword" />
			
				<table class="table table-condensed table-bordered" id="table">
					<thead>
						<tr>
							<th>#</th>
							<th>Action</th>
							<th>Pixel Keyword</th>
							<th>Image</th>
							<th>Genders</th>
							<th>Ages</th>
							<th>Status</th>
							<th>Expiry Date</th>
						</tr>
					</thead>
					<?php 
						$cont = 0;
						foreach ($list_pixel_keywords as $row) { 
							$cont++;
					?>
							<tr>
								<td><?php echo $cont; ?></td>
								<td>
									<a href="javascript:erase('<?php echo $row->get_pixel_keyword(); ?>');" class="btn btn-red btn-sm">
										<i class="glyphicon glyphicon-trash"></i>
									</a>
									<a href="edit.php?pixel_keyword=<?php echo $row->get_pixel_keyword(); ?>" class="btn btn-green btn-sm">
										<i class="glyphicon glyphicon-pencil"></i> &nbsp;Edit
									</a>
								</td>
								<td><?php echo $row->get_pixel_keyword(); ?></td>
								<td><img src="<?php echo $row->get_image(); ?>" height="60px" alt=""></td>
								<td><?php echo $row->get_genders(); ?></td>
								<td><?php echo $row->get_ages(); ?></td>
								<td><?php echo $row->get_status(); ?></td>
								<td><?php echo $row->get_expiry_date(); ?></td>
							</tr>
					<?php 
						} 
					?>
				</table>
			</form>

		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../pixel_keywords"]').parents('li').addClass('active');</script>

	<script>
		function erase(keyword){
			if( confirm("Are you sure?") ){
				document.form.action.value = "1";
				document.form.keyword.value = keyword;
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