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

		$tag_super = new tag_super();

		// DELETE

		$error  = "";
		if( isset($_POST['action']) ){
			
			$tag_super->set_id_tag_super($_POST['id_tag_super']);

			if( $tag_super->exists() ){
				if($tag_super->delete()){
					//all good
				}else{
					$error = '<div class="alert alert-danger"><strong>Oops</strong>, there was an error.</div>';
				}
			}
		}

		//GET SUPER TAGS

		$list_super_tags 		= $tag_super->get_all(" ORDER BY 1 ");
		
	?>

	<style>
		td .btn{
			vertical-align: bottom;
		}
	</style>

	<div class="section">
		<div class="content">

			<h2>
				Super Tags
			</h2>

			<p>
				<a href="../keywords" class="btn btn-gray btn-sm">
					Go back
				</a>
				<a href="add.php" class="btn btn-blue btn-sm">
					<i class="glyphicon glyphicon-pencil"></i> &nbsp;Add a new super tag
				</a>
			</p>

			<?php echo $error; ?>

			<form action="" method="post" name="form">
				<input type="hidden" name="action" />
				<input type="hidden" name="id_tag_super" />
			
				<table class="table table-condensed table-bordered">
					<thead>
						<tr>
							<th>#</th>
							<th>Action</th>
							<th>Name</th>
							<th>Gender</th>
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
									<a href="javascript:erase('<?php echo $row->get_id_tag_super(); ?>');" class="btn btn-red btn-sm">
										<i class="glyphicon glyphicon-trash"></i>
									</a>
									<a href="edit.php?id_tag_super=<?php echo $row->get_id_tag_super(); ?>" class="btn btn-green btn-sm">
										<i class="glyphicon glyphicon-pencil"></i> &nbsp;Edit
									</a>
								</td>
								<td>
									<a href="../tags?current_id_tag_super=<?php echo $row->get_id_tag_super(); ?>">
										<?php echo $row->get_name(); ?>
									</a>
								</td>
								<td>
									<?php echo $row->get_gender(); ?>
								</td>
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
		function erase(id_tag_super){
			if( confirm("Are you sure?") ){
				document.form.action.value = "1";
				document.form.id_tag_super.value = id_tag_super;
				document.form.submit();
			}
		}
	</script>
	
</body>
</html>