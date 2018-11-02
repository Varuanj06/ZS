<?php

require_once("pdf/tcpdf.php"); // Inlcude the PDF library
require_once("pdf/tcpdf_barcodes_1d.php"); // Inlcude the PDF library
require_once("excel/PHPExcel/IOFactory.php"); // Inlcude the EXCEL library

if( isset($_FILES['spreadsheet']) && $_FILES['spreadsheet']['tmp_name'] && !$_FILES['spreadsheet']['error'] ){

	$inputFile = $_FILES['spreadsheet']['tmp_name'];
	$extension = strtoupper(pathinfo($_FILES['spreadsheet']['name'], PATHINFO_EXTENSION));

	
	if($extension == 'XLSX' || $extension == 'XLS'){





		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('MIRACAS');
		$pdf->SetTitle('Way Bills');
		$pdf->SetSubject('Shipment Way Bills');
		$pdf->SetKeywords('waybill');

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		    require_once(dirname(__FILE__).'/lang/eng.php');
		    $pdf->setLanguageArray($l);
		}

		// remove header and foooter
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);

		// set font
		$pdf->SetFont('helvetica', '', 12);

		// ---------------------------------------------------------


		//Read spreadsheeet workbook
		$inputFile = $_FILES['spreadsheet']['tmp_name'];

		try {
		     $inputFileType = PHPExcel_IOFactory::identify($inputFile);
		     $objReader = PHPExcel_IOFactory::createReader($inputFileType);
		     $objPHPExcel = $objReader->load($inputFile);
		} catch(Exception $e) {
		     die($e->getMessage());
		}

		//Get worksheet dimensions
		$sheet 			= $objPHPExcel->getSheet(0); 
		$highestRow 	= $sheet->getHighestRow(); 
		$rows 			= $sheet->toArray(null, true, true, true);

		//Loop through each row of the worksheet
		for ($row = 2; $row <= $highestRow; $row++){ 
		    $rowData = $rows[$row];
		    //var_dump($rowData);

		    
		    $Name 				= $rowData["C"];
		    $address			= $rowData["F"];
		    $landmark			= $rowData["G"];

		    $city	 			= $rowData["H"];
		    $state	 			= $rowData["I"];
			$postCode 			= $rowData["J"];
		    $Email 			    = $rowData["D"];
		    $mobile	 			= $rowData["E"];
			
		    $orderNum 			= $rowData["B"];
		    $barCodeNum			= $rowData["A"];
		    $paymentMethod     	= $rowData["K"];
			
		    $amountCollected   	= ($rowData["K"]=="Pay Online" || $rowData["K"]=="Free Order" )?"0":$rowData["L"];
		    $description		= $rowData["M"];

		    $products 			= explode("|", $description);

		    // add some pages
			$pdf->AddPage();

			$params = TCPDF_STATIC::serializeTCPDFtagParameters(array($barCodeNum, 'C39', '', '', 80, 30, 0.4, array('position'=>'S', 'border'=>false, 'padding'=>4, 'fgcolor'=>array(0,0,0), 'bgcolor'=>array(255,255,255), 'text'=>true, 'font'=>'helvetica', 'fontsize'=>8, 'stretchtext'=>4), 'N'));

			
			$html = 
<<<EOD
				<p><img src="includes/img/logo.png" width="150px" /></p>
				<table>
					<tr>
						<td>
							<p><strong style="font-size:13px;">$Name</strong><br>
							<strong style="font-size:13px;">Mobile( $mobile )</strong></p>
							<p>$address</p>
							<p>$city, $state<br>
							PINCODE - $postCode</p>
							<p><strong style="font-size:13px;">LANDMARK</strong> - $landmark</p>
							
						</td>
						<td style="text-align:center;">
							<h1>ORDER # $orderNum</h1>
							<p>
EOD;

			$html .= 			
								'<tcpdf method="write1DBarcode" params="'.$params.'" />';

			$html.= 
<<<EOD
							</p>
						</td>
					</tr>
				</table>
				<br><br><br>
				<table border="1" cellpadding="8">
					<tr>
						<th colspan="3" style="background-color: #eaeaea;">
							Invoice draft
						</th>
					</tr>
					<tr>
						<td style="font-size:11px;width:20%;">
							Order # $orderNum
						</td>
						<td style="font-size:11px;width:40%;">
							Amount to be collected <strong style="font-size:13px;">$amountCollected</strong>
						</td>
						<td style="font-size:11px;width:40%;">
							Payment method: <strong style="font-size:13px;">$paymentMethod</strong>
						</td>
					</tr>
				</table>
				<br><br>
				<table border="1" cellpadding="8">
					<tr>
						<th style="background-color: #eaeaea;">
							Product description
						</th>
					</tr>
EOD;
					for($i = 0; $i < count($products); $i++){
						$product = $products[$i];
			$html .=
						'<tr>
							<th>
								'.$product.'
							</th>
						</tr>';
					}
				
			$html.= 
<<<EOD
				</table>
EOD;

			// Print text using writeHTMLCell()
			$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

		}


		// ---------------------------------------------------------

		//Close and output PDF document
		$pdf->Output('waybills.pdf', 'I');





	}else{
        echo "Please upload an XLSX file";
    }
}