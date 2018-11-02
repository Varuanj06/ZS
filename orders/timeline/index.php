<!doctype html>
<html>
<head>
	
	<link href='http://fonts.googleapis.com/css?family=Droid+Serif|Open+Sans:400,700' rel='stylesheet' type='text/css'>

	<link rel="stylesheet" href="css/style.css"> <!-- Resource style -->
	<script src="js/modernizr.js"></script> <!-- Modernizr -->
  	
	
</head>
<body>

	<?php
	
	require_once("../dbconnect.php");
	require_once("../classes/order.php");

	function make_tracking_path($id_order){
		$error      = "";
	    $msg        = "";


		$order = new order();
		$order->map($id_order);

		$staticDate     = $order->get_date_done();
		$status_admin   = $order->get_status_admin();
		if( $status_admin == 'ORDER ON HOLD' ){
		  $error = "<div class='alert alert-danger'>Order no hold</div>";
		}else if($status_admin == 'ORDER REFUSED'){
		  $error = "<div class='alert alert-danger'>Order refused</div>";
		}else if($status_admin == 'ORDER CANCELLED'){
		  $error = "<div class='alert alert-danger'>Order cancelled</div>";
		}

		// SET TIME ZONE DATE (USED FOR NOW)
		date_default_timezone_set('Mexico/General');
		$output	 	= array();

		$date1 				= date('Y-m-d H:i:s', strtotime($staticDate));
		$output['date'][]   = $date1;
		$output['date'][]   = date('Y-m-d H:i:s', strtotime($date1 . ' + 1 weekdays'));
		$output['date'][]   = date('Y-m-d H:i:s', strtotime($date1 . ' + 6 weekdays'));
		$output['date'][]   = date('Y-m-d H:i:s', strtotime($date1 . ' + 7 weekdays'));
		$output['date'][]   = date('Y-m-d H:i:s', strtotime($date1 . ' + 8 weekdays'));
		$output['date'][]   = date('Y-m-d H:i:s', strtotime($date1 . ' + 12 weekdays'));
		$output['date'][]   = date('Y-m-d H:i:s', strtotime($date1 . ' + 13 weekdays'));

		$output['status'][] = "You place the order online. Order is approved.";
		$output['status'][] = "Pune Fullfillment Team forwards the order to Hong Kong.";
		$output['status'][] = "Hong Kong Team process the order.";
		$output['status'][] = "Export process in progress - with Hong Kong Customs.";
		$output['status'][] = "Hong Kong to New Delhi - International transportation.";
		$output['status'][] = "Custom Clearance in New Delhi.";
		$output['status'][] = "Order Dispatch from Pune Fullfillment Center";
		

		$now       	= date('Y-m-d H:i:s');
		$done1      = ($now >= $output['date'][0]) ? "done" : "";
		$done2      = ($now >= $output['date'][1]) ? "done" : "";
		$done3      = ($now >= $output['date'][2]) ? "done" : "";
		$done4      = ($now >= $output['date'][3]) ? "done" : "";
		$done5      = ($now >= $output['date'][4]) ? "done" : "";
		$done6      = ($now >= $output['date'][5]) ? "done" : "";
		$done7      = ($now >= $output['date'][6]) ? "done" : "";

		if($status_admin == 'ORDER SHIPPED' || $status_admin == 'ORDER PARTIALLY SHIPPED'){
		  $msg = "<div class='alert alert-success'>Order shipped</div>";
		  $done1 = $done2 = $done3 = $done4 = $done5 = 'done';
		}

		$rs  = '';
		$rs .= '<section id="cd-timeline" class="cd-container">';
		
		for ($i=0; $i < 7; $i++) {
			$rs .= '<div class="cd-timeline-block">';
				$rs .= '<div class="cd-timeline-img cd-green">'.($i+1).'</div>';
				$rs .= '<div class="cd-timeline-content">';
					$rs .= '<p>'.$output['status'][$i].'</p>';
					$rs .= '<span class="cd-date">'.date('M d,y', strtotime($output['date'][$i])).'</span>';
				$rs .= '</div>';
			$rs .= '</div>';
		}

		$rs .= '</section>';

		return $rs;
	}

    
	echo make_tracking_path(33);
  ?>

	
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="js/main.js"></script> <!-- Resource jQuery -->

</body>
</html>