<?php require_once("../session.php"); ?>
<?php require_once("../../includes/plugins/PHPExcel/Classes/PHPExcel.php"); ?>
<?php 
	
	if(!isset($_SESSION['manifest_export']) || !isset($_SESSION['manifest_export_courier'])){
		echo "<script>location.href='manifest.php';</script>";
		exit();
	}

	$output 	= $_SESSION['manifest_export'];
	$courier 	= $_SESSION['manifest_export_courier'];
	unset($_SESSION['manifest_export']);
	unset($_SESSION['manifest_export_courier']);

	
/* ******************************
		START EXPORT TO EXCEL
   ****************************** */

	
	// Create new PHPExcel object
	$objPHPExcel 	= new PHPExcel();
	$sheet 			= $objPHPExcel->setActiveSheetIndex(0);

	$sheet->setCellValue("A1", 'Id Order');
	$sheet->setCellValue("B1", 'Name');
	$sheet->setCellValue("C1", 'Email');
	$sheet->setCellValue("D1", 'Mobile Number');
	$sheet->setCellValue("E1", 'Address');
	$sheet->setCellValue("F1", 'Landmark');
	$sheet->setCellValue("G1", 'City');
	$sheet->setCellValue("H1", 'State');
	$sheet->setCellValue("I1", 'Pin Code');
	$sheet->setCellValue("J1", 'Payment Method');
	$sheet->setCellValue("K1", 'Final Amount');
	$sheet->setCellValue("L1", 'Details');

	foreach ($output as $key => $value) {

		$row_num = ++$value['cont'];
		
		$sheet->setCellValue("A$row_num", $value['id_order']);
		$sheet->setCellValue("B$row_num", $value['name']);
		$sheet->setCellValue("C$row_num", $value['email']);
		$sheet->setCellValue("D$row_num", $value['mobile_number']);
		$sheet->setCellValue("E$row_num", $value['address']);
		$sheet->setCellValue("F$row_num", $value['landmark']);
		$sheet->setCellValue("G$row_num", $value['city']);
		$sheet->setCellValue("H$row_num", $value['state']);
		$sheet->setCellValue("I$row_num", $value['pin_code']);
		$sheet->setCellValue("J$row_num", $value['payment_method']);
		$sheet->setCellValue("K$row_num", number_format($value['final_amount'], 2));

		$products 	= "";
		foreach ($value['details'] as $details) {

			$id 	= $details['id_product_prestashop'];
			$name 	= $details['name'];
			$color 	= $details['color'];
			$size 	= $details['size'];
			$qty 	= $details['qty'];
			$products 	.= "ID: $id, name: $name, color: $color, size: $size, qty: $qty | ";
		}

		if($products != ""){
			$products = substr($products, 0, -2);
		}

		$sheet->setCellValue("L$row_num", $products);

	}

	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle('courier');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="Manifest_'.$courier.'_'.date("Y-m-d").'.xls"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;
