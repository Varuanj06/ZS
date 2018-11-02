<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/voucher.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
</head>
<body>

	<?php require_once("../navbar.php"); ?>

	<?php 

		$voucher 		= new voucher();
		$email_query 	= isset($_GET['search']) ? $_GET['search'] : "";

		$vouchers 		= array();
		if($email_query != ""){
			$vouchers = $voucher->get_all_by_search($email_query, " ORDER BY 1 ");
		}

		// DELETE VOUCHER
		$error  = "";
		if( isset($_POST['action']) ){
			
			$voucher->set_id_voucher($_POST['id_voucher']);

			if($voucher->delete()){
				//all good
			}else{
				$error = '<div class="alert alert-danger"><strong>Oops</strong>, there was an error.</div>';
			}

			if($email_query != ""){
				$vouchers 		= $voucher->get_all_by_search($email_query, " ORDER BY 1 ");
			}
		}

		$param = "";
		if($email_query != ""){
			$param = "?search=".$_GET['search'];
		}
		
	?>

	<div class="section">
		<div class="content">

			<h2>
				Vouchers
			</h2>

			<p>
				<a href="add.php<?php echo $param; ?>" class="btn btn-blue btn-sm">
					<i class="glyphicon glyphicon-pencil"></i> &nbsp;Add a new voucher
				</a>
			</p>

			<form action="" name="form_search" method="get">
				<p>
					<input name="search" value="<?php echo $email_query; ?>" type="text" class="custom-input" placeholder="Write some email here" id="search" style="max-width:200px;">
				</p>
				<p>
					<a href="javascript:document.form_search.submit();" class="btn btn-sm btn-gray">Search</a>
				</p>
			</form>

			<?php echo $error; ?>

			<form action="" method="post" name="form">
				<input type="hidden" name="action" />
				<input type="hidden" name="id_voucher" />
			
				<table class="table table-condensed table-bordered" id="table">
					<thead>
						<tr>
							<th>#</th>
							<th>Action</th>
							<th>Id voucher</th>
							<th>Code</th>
							<th>Description</th>
							<th>Emails</th>
							<th>Till date</th>
							<th>value kind</th>
							<th>value</th>
							<th>Min Cart Value</th>
							<th>Visibility</th>
						</tr>
					</thead>
					<?php 
						$cont = 0;
						foreach ($vouchers as $value) { 
							$cont++;
					?>
							<tr>
								<td><?php echo $cont; ?></td>
								<td>
									<a href="javascript:erase('<?php echo $value->get_id_voucher(); ?>');" class="btn btn-red btn-sm">
										<i class="glyphicon glyphicon-trash"></i>
									</a>
									<a href="edit.php?id_voucher=<?php echo $value->get_id_voucher(); ?><?php echo str_replace("?", "&", $param); ?>" class="btn btn-green btn-sm">
										<i class="glyphicon glyphicon-pencil"></i> &nbsp;Edit
									</a>
								</td>
								<td><?php echo $value->get_id_voucher(); ?></td>
								<td><?php echo $value->get_code(); ?></td>
								<td><?php echo $value->get_description(); ?></td>
								<td><?php echo $value->get_emails(); ?></td>
								<td><?php echo $value->get_till_date(); ?></td>
								<td><?php echo $value->get_value_kind(); ?></td>
								<td><?php echo $value->get_value(); ?></td>
								<td><?php echo $value->get_min_cart_value(); ?></td>
								<td><?php echo $value->get_visibility(); ?></td>
							</tr>
					<?php 
						} 
					?>
				</table>
			</form>

		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../vouchers"]').parents('li').addClass('active');</script>

	<script>
		function erase(id_voucher){
			if( confirm("Are you sure?") ){
				document.form.action.value = "1";
				document.form.id_voucher.value = id_voucher;
				document.form.submit();
			}
		}
	</script>

	<!--<script src="../includes/js/search.js"></script>
	<script>
		$('#search').focus().search({table:$("#table")});
	</script>-->
	
</body>
</html>