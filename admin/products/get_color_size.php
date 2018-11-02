<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/attribute.php"); ?>
<?php require_once("../../classes/product_stock.php"); ?>

<?php


/* ====================================================================== *
	ALL POSSIBLE COMBINATIONS FROM ARRAYS
* ====================================================================== */								

	function combinations($arrays, $i = 0) {
	    if (!isset($arrays[$i])) {
	        return array();
	    }
	    if ($i == count($arrays) - 1) {
	        return $arrays[$i];
	    }

	    // get combinations from subsequent arrays
	    $tmp = combinations($arrays, $i + 1);

	    $result = array();

	    // concat each array from tmp with each element from $arrays[$i]
	    foreach ($arrays[$i] as $v) {
	        foreach ($tmp as $t) {
	            $result[] = is_array($t) ? 
	                array_merge(array($v), $t) :
	                array($v, $t);
	        }
	    }

	    return $result;
	}

/* ====================================================================== *
	CLASSES
* ====================================================================== */								

	$attribute 				= new attribute();
	$product_stock 			= new product_stock();

/* ====================================================================== *
	VARIABLES
* ====================================================================== */									

	$id_product_prestashop 	= $_GET['id_product_prestashop'];
	$id_product 			= $_GET['id_product'];
	$colors 				= $attribute->get_colors_of_product($id_product_prestashop);
	$sizes 					= $attribute->get_sizes_of_product($id_product_prestashop);

	//$colors[] = '000';
	//$colors[] = 'f2f2f2';
	//$colors[] = 'A8A8A8';

/* ====================================================================== *
	MAKE COMBINATION
* ====================================================================== */		

	if(count($colors) > 0 || count($sizes) > 0){

		if(count($colors) == 0) $colors[] = 'Empty';
		if(count($sizes) == 0) $sizes[] = 'Empty';

		$combinations  			= 	combinations( array($colors, $sizes) );		

?>
		
		<hr>
		<div class="row">
			
			<?php $color_tmp = $colors[0]; ?>
			<?php foreach ($combinations as $row){ ?>

				<?php 
					$color 	= $row[0];
					$size 	= $row[1];
				?>
				
				<?php if($color_tmp != $color){ $color_tmp=$color; ?>
					</div>
					<br>
					<div class="row">
				<?php } ?>

				<div class="col-sm-2">
					<label for="">
						<?php if($color=='Empty'){ ?>
							<?php echo $color; ?>
						<?php }else{ ?>
							<span class="media-box-color"><span style="background:<?php echo strpos($color, '#') !== false ? $color : "#".$color; ?>;"></span></span>
						<?php } ?>
						
						<?php echo $size; ?>
					</label>
					<input type="text" class="form-control" name="color-size[<?php echo $color; ?>@@<?php echo $size; ?>]" value="<?php echo $product_stock->get_current_stock($id_product, $color, $size) ?>" />
				</div>
			<?php } ?>

		</div>

		<br>

<?php

	}else{
?>
	
		<hr>
			<div class="row">
				<div class="col-sm-2">
					<label for="">
						Empty
					</label>
					<input type="text" class="form-control" name="color-size[Empty@@Empty]" value="<?php echo $product_stock->get_current_stock($id_product, 'Empty', 'Empty') ?>" />
				</div>
			</div>

			<br>

<?php
	}

?>
