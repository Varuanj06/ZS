<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/vendor.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
</head>
<body>

	<?php require_once("../navbar.php"); ?>

	<div class="section">
		<div class="content">

			<h2>Vendors</h2>

			<p>
				<!--<a href="invoice.php" class="btn btn-blue btn-sm"><i class="glyphicon glyphicon-arrow-down"></i> &nbsp;Invoice</a>-->
				<a href="manifest.php" class="btn btn-blue btn-sm"><i class="glyphicon glyphicon-arrow-down"></i> &nbsp;Manifest</a>
				<a href="manifest_gift.php" class="btn btn-blue btn-sm"><i class="glyphicon glyphicon-arrow-down"></i> &nbsp;Gift Manifest</a>
			</p>


			<p><input type="text" class="custom-input" placeholder="Search" id="search" style="max-width:200px;"></p>
			
			<table class="table table-condensed table-bordered table-striped" id="table">
				<thead>
					<tr>
						<th>#</th>
						<th>Vendor Catalogs</th>
					</tr>
				</thead>
				<?php
					$vendor 	= new vendor();
					$vendors 	= $vendor->get_all("ORDER BY 1");

					$count  = 0;
					foreach ($vendors as $row) {
						$count++;
				?>
						<tr>
							<td><?php echo $count; ?></td>
							<td>
								<a href="set_id_vendor.php?id_vendor=<?php echo $row->get_id_vendor(); ?>">
									<?php echo $row->get_name(); ?>
								</a>
							</td>
						</tr>
				<?php
					}
				?>
			</table>

		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../orders_with_stock"]').parents('li').addClass('active');</script>

	<script src="../includes/js/search.js"></script>
	<script>
		$('#search').focus().search({table:$("#table")});
	</script>

</body>
</html>