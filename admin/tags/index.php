<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/tag_super.php"); ?>
<?php require_once("../../classes/tag.php"); ?>
<?php require_once("../../classes/product_tag.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
</head>
<body>

	<?php require_once("../navbar.php"); ?>

	<?php 

		if(isset($_GET['current_id_tag_super'])){
			$_SESSION['current_id_tag_super'] = $_GET['current_id_tag_super'];
		}
		if(!isset($_SESSION['current_id_tag_super'])){
			echo "<script>location.href='../tags_super';</script>";
			exit();
		}

		// CLASSES

		$tag 			= new tag();
		$tag_super 		= new tag_super();
		$product_tag	= new product_tag();

		$tag_super->map($_SESSION['current_id_tag_super']);

		// DELETE

		$error  = "";
		if( isset($_POST['action']) ){

			$error = false;
			$conn->beginTransaction();
			
			$tag->set_id_tag($_POST['id_tag']);

			if( $tag->exists() ){
				if($product_tag->delete_by_tag($_POST['id_tag']) && $tag->delete()){
					//all good
				}else{
					$error = true;
				}
			}

			if($error){
				$conn->rollBack();
			  	$msg = '<div class="alert alert-danger"><strong>Oops</strong>, there was an error.</div>';
			}else{
			  	$conn->commit();
			}
		}

		// GET SUPER TAGS

		$list_super_tags 		= $tag->get_list_by_tag_super($_SESSION['current_id_tag_super'], " ORDER BY 1 ");
		
	?>

	<style>
		td .btn{
			vertical-align: bottom;
		}
	</style>

	<div class="section">
		<div class="content">

			<h2>
				Tags under
			</h2>

			<p>
				<a href="../tags_super" class="btn btn-gray btn-sm">
					Go back
				</a>
				<a href="add.php" class="btn btn-blue btn-sm">
					<i class="glyphicon glyphicon-pencil"></i> &nbsp;Add a new tag
				</a>
			</p>

			<?php echo $error; ?>

			<form action="" method="post" name="form">
				<input type="hidden" name="action" />
				<input type="hidden" name="id_tag" />
			
				<table class="table table-condensed table-bordered">
					<thead>
						<tr>
							<th>#</th>
							<th>Action</th>
							<th>Name</th>
							<th>Image</th>
						</tr>
					</thead>
					<?php 
						$cont = 0;
						foreach ($list_super_tags as $row) { 
							$cont++;
					?>
							<tr>
								<td><?php echo $cont; ?></td>
								<td>
									<a href="javascript:erase('<?php echo $row->get_id_tag(); ?>');" class="btn btn-red btn-sm">
										<i class="glyphicon glyphicon-trash"></i>
									</a>
									<a href="edit.php?id_tag=<?php echo $row->get_id_tag(); ?>" class="btn btn-green btn-sm">
										<i class="glyphicon glyphicon-pencil"></i> &nbsp;Edit
									</a>
								</td>
								<td><?php echo $row->get_name(); ?></td>
								<td>
									<img src="<?php echo $row->get_image(); ?>" height="60px" alt="">
								</td>
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
		function erase(id_tag){
			if( confirm("Are you sure?") ){
				document.form.action.value = "1";
				document.form.id_tag.value = id_tag;
				document.form.submit();
			}
		}
	</script>
	
</body>
</html>