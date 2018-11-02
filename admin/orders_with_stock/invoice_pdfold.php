<?php
	
	require_once("../../includes/plugins/tcpdf/tcpdf.php");
	require_once("../../dbconnect.php");
	require_once("../../classes/order.php");
	require_once("../../classes/order_address.php");
	require_once("../../classes/order_detail.php");
	require_once("../../classes/order_invoice.php");
	require_once("../../classes/product.php");
	
/* ====================================================================== *
        DEFAULT
 * ====================================================================== */    	

	$order 			= new order();    
	$order_address 	= new order_address();
	$order_detail 	= new order_detail();   
	$order_invoice 	= new order_invoice();    
	$product		= new product();

	$orders 		= $_GET['orders'];
	$orders 		= explode('@@', $orders);
	$htmls 			= [];

	foreach ($orders as $row) {

		$arr 		= explode('@', $row);
		$id_order 	= $arr[0];
		$date 		= $arr[1];

		$order->map($id_order);
		$order_address->map($order->get_id_order_address(), $order->get_id_fb_user());
		$order_invoice->map_by_order_and_date($id_order, $date);

	/* ====================================================================== *
	        FINAL TOTALS
	 * ====================================================================== */    	

		$final_qty 		= 0;
		$final_amount 	= 0;
		$final_CGST 	= 0;
		$final_SGST 	= 0;
		$final_IGST 	= 0;
		$final_GST 		= 0;

	/* ====================================================================== *
	        DETAILS
	 * ====================================================================== */    	

		$details 		= $order_detail->get_details_sent_in_date_and_order($date, $date, $id_order, " order by 1 ");
		$details_html 	= '';

		$cont = 0;
		foreach ($details as $row) {
			$product->map($row->get_id_product());

			$price 		= ( $row->get_amount()-$row->get_discount() ) * $row->get_qty(); // price with tax

		/* ====================================================================== *
		        VARIABLES
		 * ====================================================================== */    		

		    $cont++;
		    $qty 				= $row->get_qty();
			$amount 			= 0; // price without tax
			$CGST 				= 0;
			$SGST 				= 0;
			$IGST 				= 0;
			$GST 				= 0;

		/* ====================================================================== *
		        GET TYPE OF TAX (5% or 12%) 
		 * ====================================================================== */    				

			$tax_percentage 	= 0;
			$packing_fee 		= 0;

			if( $price <= 1000 ){
				$tax_percentage = .05;
			}else{

				if( ($price/1.05) <= 1000 ){
					$tax_percentage = .05;	
				}else{

					if( ($price/1.12) > 1000 ){
						$tax_percentage = .12;		
					}else{

						$packing_fee	= $price - 1048.95;

						$price 			= 1048.95;
						$tax_percentage = .05;

					}

				}

				$tax_percentage = .12;
			}

		/* ====================================================================== *
		        GET AMOUNT AND TAX NUMBER
		 * ====================================================================== */ 

			$amount 	= $price / (1+$tax_percentage); // PRICE / 1.05 or PRICE / 1.12
			$amount 	= round($amount, 2); // 2 decimals only

			if(
				substr( $order_address->get_pin_code(), 0, 2 ) == '40' || 
				substr( $order_address->get_pin_code(), 0, 2 ) == '41' || 
				substr( $order_address->get_pin_code(), 0, 2 ) == '42' || 
				substr( $order_address->get_pin_code(), 0, 2 ) == '43' || 
				substr( $order_address->get_pin_code(), 0, 2 ) == '44'
			){
				$CGST 		= ($price - $amount)/2;
				$CGST 		= round($CGST, 2); // 2 decimals only

				$SGST 		= ($price - $amount - $CGST);
			}else{
				$IGST 		= ($price - $amount);
			}

			$GST 	= $CGST+$SGST+$IGST;

		/* ====================================================================== *
		        SUM TOTALS AND PRINT HTML
		 * ====================================================================== */ 

			$final_qty 		+= $qty;
			$final_amount 	+= $amount;
			$final_CGST 	+= $CGST;
			$final_SGST 	+= $SGST;
			$final_IGST 	+= $IGST;
			$final_GST 		+= $GST;

			$details_html .= '
				<tr>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right">'. $cont .'</td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="left">'. $product->get_name() .'</td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right"></td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right"></td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right">'. $qty .'</td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right"></td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right">'. number_format($amount, 2) .'</td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right"></td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right"></td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right">'. ($CGST == '' ? '' : (($tax_percentage/2)*100).'%') .'</td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right">'. ($CGST == '' ? '' : number_format($CGST, 2)).'</td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right">'. ($SGST == '' ? '' : (($tax_percentage/2)*100).'%') .'</td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right">'. ($SGST == '' ? '' : number_format($SGST, 2)).'</td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right">'. ($IGST == '' ? '' : ($tax_percentage*100).'%') .'</td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right">'. ($IGST == '' ? '' : number_format($IGST, 2)).'</td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right">'. number_format($amount+$GST, 2) .'</td>
				</tr>
			';

		/* ====================================================================== *
		        SUM TOTALS AND PRINT HTML
		 * ====================================================================== */ 
		 
		 	if($packing_fee>0){

		 		$cont++;
			    $qty 				= 1;
				$amount 			= 0; // price without tax
				$CGST 				= 0;
				$SGST 				= 0;
				$IGST 				= 0;
				$GST 				= 0;

				$amount 			= $packing_fee / (1.05);
				$amount 			= round($amount, 2); // 2 decimals only

				if(
					substr( $order_address->get_pin_code(), 0, 2 ) == '40' || 
					substr( $order_address->get_pin_code(), 0, 2 ) == '41' || 
					substr( $order_address->get_pin_code(), 0, 2 ) == '42' || 
					substr( $order_address->get_pin_code(), 0, 2 ) == '43' || 
					substr( $order_address->get_pin_code(), 0, 2 ) == '44' || true
				){
					$CGST 		= ($packing_fee - $amount)/2;
					$CGST 		= round($CGST, 2); // 2 decimals only

					$SGST 		= ($packing_fee - $amount - $CGST);
				}else{
					$IGST 		= ($packing_fee - $amount);
				}

				$GST 	= $CGST+$SGST+$IGST;

				$final_qty 		+= $qty;
				$final_amount 	+= $amount;
				$final_CGST 	+= $CGST;
				$final_SGST 	+= $SGST;
				$final_IGST 	+= $IGST;
				$final_GST 		+= $GST;

				$details_html .= '
					<tr>
						<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right">'. $cont .'</td>
						<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="left">Packing Fee</td>
						<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right"></td>
						<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right"></td>
						<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right">'. $qty .'</td>
						<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right"></td>
						<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right">'. number_format($amount, 2) .'</td>
						<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right"></td>
						<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right"></td>
						<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right">'. ($CGST == '' ? '' : ((.05/2)*100).'%') .'</td>
						<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right">'. ($CGST == '' ? '' : number_format($CGST, 2)).'</td>
						<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right">'. ($SGST == '' ? '' : ((.05/2)*100).'%') .'</td>
						<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right">'. ($SGST == '' ? '' : number_format($SGST, 2)).'</td>
						<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right">'. ($IGST == '' ? '' : (.05*100).'%') .'</td>
						<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right">'. ($IGST == '' ? '' : number_format($IGST, 2)).'</td>
						<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;" align="right">'. number_format($amount+$GST, 2) .'</td>
					</tr>
				';	

			}

		}

	/* ====================================================================== *
	        CREATE HTML
	 * ====================================================================== */    		

		$html = '
			<table border=".3" cellpadding="2">
				<tr>
					<td colspan="4" align="center">
						<br>
						<p style="font-size:12px;"><strong>Miracas Lifestyle Pvt Ltd</strong></p>
						<p>C7/1, Oxford comforts, Salunkhe Vihar Road, Pune 411040</p>
						<p>
							GSTIN: 27AAICM2069G1ZD<br>
							PAN : AAICM2069G<br>
							CIN : U74900PN2012PTC144998
						</p>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center" style="font-size:12px;"><strong>INVOICE '. 'PNQI'.sprintf('%05d', $order_invoice->get_id_order_invoice()) .'</strong></td>
					<td>Original for Receipient</td>
				</tr>
			</table>

			<table border=".3">
				<tr>
					<td>
						<table cellpadding="1">
							<tr>
								<td><strong>Reverse Carge :</strong></td>
								<td></td>
							</tr>
							<tr>
								<td><strong>Invoice No. :</strong></td>
								<td>'. 'PNQI'.sprintf('%05d', $order_invoice->get_id_order_invoice()) .'</td>
							</tr>
							<tr>
								<td><strong>Order Number :</strong></td>
								<td>'. $order->get_id_order() .'</td>
							</tr>
							<tr>
								<td><strong>Invoice Date :</strong></td>
								<td>'. date('d F, Y', strtotime($date)) .'</td>
							</tr>
							<tr>
								<td><strong>State :</strong></td>
								<td>
									<table cellpadding="2">
										<tr>
											<td>Maharashtra</td>
											<td style="border-collapse: collapse; border: .3px solid black;">State Code  :</td>
											<td style="border-collapse: collapse; border: .3px solid black;">27</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
					<td>
						<table cellpadding="1">
							<tr>
								<td><strong>Transporting Mode :</strong></td>
								<td></td>
							</tr>
							<tr>
								<td><strong>Vehicle Number :</strong></td>
								<td></td>
							</tr>
							<tr>
								<td><strong>e-Way Bill Ref No. :</strong></td>
								<td></td>
							</tr>
							<tr>
								<td><strong>Date of Supply :</strong></td>
								<td>'. date('d F, Y', strtotime($date)) .'</td>
							</tr>
							<tr>
								<td><strong>Place of Supply :</strong></td>
								<td>Pune</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr>
					<td align="center">Details of Receiver | Billed to :</td>
					<td align="center">Details of Consignee  | Shipped to:</td>
				</tr>

				<tr>
					<td>
						<table cellpadding="1">
							<tr>
								<td><strong>Name :</strong></td>
								<td>'. $order_address->get_name() .'</td>
							</tr>
							<tr>
								<td><strong>Address :</strong></td>
								<td>'. $order_address->get_address() .'</td>
							</tr>
							<tr>
								<td><strong>Mobile number :</strong></td>
								<td>'. $order_address->get_mobile_number() .'</td>
							</tr>
							<tr>
								<td><strong>GSTIN :</strong></td>
								<td></td>
							</tr>
							<tr>
								<td><strong>PAN :</strong></td>
								<td></td>
							</tr>
							<tr>
								<td><strong>State :</strong></td>
								<td>
									<table cellpadding="2">
										<tr>
											<td>'. $order_address->get_state() .'</td>
											<td style="border-collapse: collapse; border: .3px solid black;">Zip Code  :</td>
											<td style="border-collapse: collapse; border: .3px solid black;">'. $order_address->get_pin_code() .'</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
					<td>
						<table cellpadding="1">
							<tr>
								<td><strong>Name :</strong></td>
								<td>'. $order_address->get_name() .'</td>
							</tr>
							<tr>
								<td><strong>Address :</strong></td>
								<td>'. $order_address->get_address() .'</td>
							</tr>
							<tr>
								<td><strong>Mobile number :</strong></td>
								<td>'. $order_address->get_mobile_number() .'</td>
							</tr>
							<tr>
								<td><strong>GSTIN :</strong></td>
								<td></td>
							</tr>
							<tr>
								<td><strong>PAN :</strong></td>
								<td></td>
							</tr>
							<tr>
								<td><strong>State :</strong></td>
								<td>
									<table cellpadding="2">
										<tr>
											<td>'. $order_address->get_state() .'</td>
											<td style="border-collapse: collapse; border: .3px solid black;">Zip Code  :</td>
											<td style="border-collapse: collapse; border: .3px solid black;">'. $order_address->get_pin_code() .'</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="4">&nbsp;</td>
				</tr>
			</table>

			<table cellpadding="1">
				<tr>
					<th style="width: 4%; border-collapse: collapse; border: .3px solid black;" align="center" rowspan="2"><strong>S.No</strong></th>
					<th style="width:20%; border-collapse: collapse; border: .3px solid black;" align="center" rowspan="2"><strong>Description of Product/ Service</strong></th>
					<th style="width: 7%; border-collapse: collapse; border: .3px solid black;" align="center" rowspan="2"><strong>HSN / SAC</strong></th>
					<th style="width: 4%; border-collapse: collapse; border: .3px solid black;" align="center" rowspan="2"><strong>UOM</strong></th>
					<th style="width: 4%; border-collapse: collapse; border: .3px solid black;" align="center" rowspan="2"><strong>QTY</strong></th>
					<th style="width: 4%; border-collapse: collapse; border: .3px solid black;" align="center" rowspan="2"><strong>Rate</strong></th>
					<th style="width: 7%; border-collapse: collapse; border: .3px solid black;" align="center" rowspan="2"><strong>Amount</strong></th>
					<th style="width: 6%; border-collapse: collapse; border: .3px solid black;" align="center" rowspan="2"><strong>Less: Discount</strong></th>
					<th style="width: 6%; border-collapse: collapse; border: .3px solid black;" align="center" rowspan="2"><strong>Taxable value</strong></th>
					<th style="width:10%; border-collapse: collapse; border: .3px solid black;" align="center" colspan="2"><strong>CGST</strong></th>
					<th style="width:10%; border-collapse: collapse; border: .3px solid black;" align="center" colspan="2"><strong>SGST</strong></th>
					<th style="width:10%; border-collapse: collapse; border: .3px solid black;" align="center" colspan="2"><strong>IGST</strong></th>
					<th style="width: 8%; border-collapse: collapse; border: .3px solid black;" align="center" rowspan="2"><strong>Total</strong></th>
				</tr>
				<tr>
					<th style="border-collapse: collapse; border: .3px solid black;" align="center"><strong>Rate</strong></th>
					<th style="border-collapse: collapse; border: .3px solid black;" align="center"><strong>Amount</strong></th>
					<th style="border-collapse: collapse; border: .3px solid black;" align="center"><strong>Rate</strong></th>
					<th style="border-collapse: collapse; border: .3px solid black;" align="center"><strong>Amount</strong></th>
					<th style="border-collapse: collapse; border: .3px solid black;" align="center"><strong>Rate</strong></th>
					<th style="border-collapse: collapse; border: .3px solid black;" align="center"><strong>Amount</strong></th>
				</tr>
				'.$details_html.'
				<tr>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;"> <br><br> </td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;"> <br><br> </td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;"> <br><br> </td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;"> <br><br> </td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;"> <br><br> </td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;"> <br><br> </td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;"> <br><br> </td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;"> <br><br> </td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;"> <br><br> </td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;"> <br><br> </td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;"> <br><br> </td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;"> <br><br> </td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;"> <br><br> </td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;"> <br><br> </td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;"> <br><br> </td>
					<td style="border-collapse: collapse; border-right: .3px solid black; border-left: .3px solid black;"> <br><br> </td>
				</tr>
				<tr>
					<th style="border-collapse: collapse; border: .3px solid black;" align="right" colspan="4"><strong>Total:</strong></th>
					<th style="border-collapse: collapse; border: .3px solid black;" align="right"><strong>'. $final_qty .'</strong></th>
					<th style="border-collapse: collapse; border: .3px solid black;" align="right"><strong></strong></th>
					<th style="border-collapse: collapse; border: .3px solid black;" align="right"><strong>'. number_format($final_amount, 2) .'</strong></th>
					<th style="border-collapse: collapse; border: .3px solid black;" align="right"><strong></strong></th>
					<th style="border-collapse: collapse; border: .3px solid black;" align="right"><strong></strong></th>
					<th style="border-collapse: collapse; border: .3px solid black;" align="right" colspan="2">'. ($final_CGST == '' ? '' : number_format($final_CGST, 2)) .'<strong></strong></th>
					<th style="border-collapse: collapse; border: .3px solid black;" align="right" colspan="2">'. ($final_SGST == '' ? '' : number_format($final_SGST, 2)) .'<strong></strong></th>
					<th style="border-collapse: collapse; border: .3px solid black;" align="right" colspan="2">'. ($final_IGST == '' ? '' : number_format($final_IGST, 2)) .'<strong></strong></th>
					<th style="border-collapse: collapse; border: .3px solid black;" align="right"><strong>'. number_format($final_amount+$final_GST, 2) .'</strong></th>
				</tr>
			</table>

			<table border=".3" cellpadding="0">
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2" align="center">Total Invoice Amount in Words :</td>
					<td rowspan="3">
						<table border=".3" cellpadding="1">
							<tr>
								<td><strong>Taxable Amount Before Tax :</strong></td>
								<td>'. number_format($final_amount, 2) .'</td>
							</tr>
							<tr>
								<td><strong>Add : CGST :</strong></td>
								<td>'. ($final_CGST == '' ? '' : number_format($final_CGST, 2)) .'</td>
							</tr>
							<tr>
								<td><strong>Add : SGST :</strong></td>
								<td>'. ($final_SGST == '' ? '' : number_format($final_SGST, 2)) .'</td>
							</tr>
							<tr>
								<td><strong>Add : IGST :</strong></td>
								<td>'. ($final_IGST == '' ? '' : number_format($final_IGST, 2)) .'</td>
							</tr>
							<tr>
								<td><strong>Total Amount : GST :</strong></td>
								<td>'. ($final_GST == '' ? '' : number_format($final_GST, 2)) .'</td>
							</tr>
							<tr>
								<td><strong>Total  Amount After Tax :</strong></td>
								<td>'. number_format($final_amount+$final_GST, 2) .'</td>
							</tr>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td><strong>GST Payable on Reverse Charge :</strong></td>
								<td></td>
							</tr>
							<tr>
								<td colspan="2" align="center">
									<p>Certified that the particulars given above are true and correct.</p>
									<p style="font-size:12px;"><strong>For Miracas Lifestyle Pvt Ltd</strong></p>
									<br><br>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td></td>
					<td rowspan="2"></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td colspan="3" align="right">[E&OE]</td>
				</tr>
			</table> ';

		$htmls[] = $html;
	}

/* ====================================================================== *
        PDF
 * ====================================================================== */    	  

	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Miracas');
	$pdf->SetTitle('Invoice');
	$pdf->SetSubject('Invoice');
	$pdf->SetKeywords('invoice');
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	    require_once(dirname(__FILE__).'/lang/eng.php');
	    $pdf->setLanguageArray($l);
	}

	$pdf->SetMargins(4,4,4);
	$pdf->SetPrintHeader(false);
	$pdf->SetPrintFooter(false);
	$pdf->SetFont('helvetica', '', 7);
	
	foreach ($htmls as $html) {
		$pdf->AddPage();
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->endPage();
  		$pdf->lastPage();
	}

	$pdf->Output('invoice_'.$id_order.'.pdf', 'I'); // 'I' => it will load it in the browser, 'D' => it will download the pdf

