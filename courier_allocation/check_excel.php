<?php

	require_once("../includes/plugins/PHPExcel/Classes/PHPExcel.php");

	//**************** READ EXCEL FILE ****************
	if($final_amount == 0){
		$pay_method = 'Pay Online';
	}

	$target_excel 	= '';
	if($pay_method == 'Pay Online'){
		$target_excel = '../courier_allocation/ONLINE_pincodes.xls';
	}else if($pay_method == 'Cash on Delivery'){
		$target_excel = '../courier_allocation/COD_pincodes.xls';
	}

	$excelReader = PHPExcel_IOFactory::createReader('Excel5');
	$excelReader->setReadDataOnly(true);
	$objPHPExcel  	= $excelReader->load($target_excel);
	$sheet 			= $objPHPExcel->setActiveSheetIndex(0);
	
	$highestColumm 	= $sheet->getHighestColumn(); // e.g. "EL" 
	$highestRow 	= $sheet->getHighestRow();  

	$excel_output 	= array();

	$highestColumm++;
	for ($column = 'A'; $column != $highestColumm; $column++) {
		for ($row = 1; $row < $highestRow + 1; $row++) {     
			$cell = $sheet->getCell($column . $row)->getValue();

			if($cell != null){
				$excel_output[$column][] = $cell;
			}
		}
	}

	foreach ($excel_output as $key => $value) {
		foreach ($value as $key_inner => $value_inner) {
			if($key_inner>0){
				if($pin_code == $value_inner){
					$courier_allocation = $value[0];
					break 2;
				}
			}
		}
	}

	if($courier_allocation == '' && $pay_method == 'Cash on Delivery'){
		$all_good = false;	
		$msj_from_excel = "Cash on Delivery is not available in your region. Please choose online payment for confiming the order";
	}else if($courier_allocation == '' && $pay_method == 'Pay Online'){
		$courier_allocation = 'Indian Post';
	}

	//echo "<h1>COURIER ALLOCATION: $courier_allocation</h1>";exit();
	