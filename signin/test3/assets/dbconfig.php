
<?php

  $production = true;
  
	$config              = array();
  $config_prestashop   = array();
  
  if($production){

    // DB
    $config['host']  = 'devmiracas.c8wcnlt4xa3a.ap-southeast-1.rds.amazonaws.com';
  	$config['db']    = 'ZS';
  	$config['user']  = 'devmiracasuser';
  	$config['pass']  = 'hJh[4!:5e\RVqsG';
	
	// REPORTS DB
    $config_reports['host']  = 'devmiracas.c8wcnlt4xa3a.ap-southeast-1.rds.amazonaws.com';
    $config_reports['db']    = 'miracasdev';
  	$config_reports['user']  = 'devmiracasuser';
  	$config_reports['pass']  = 'hJh[4!:5e\RVqsG';

    // PRESTASHOP DB
    $config_prestashop['host']  = 'miracasdb.c8wcnlt4xa3a.ap-southeast-1.rds.amazonaws.com';  //database host
    $config_prestashop['db']    = 'rdsmiracasdb';   //database name
    $config_prestashop['user']  = 'miracasdbuser';       //database user
    $config_prestashop['pass']  = "3EW\8w57e7m'1EJ7vO";      //database password
	
	// CONFIG SMS APP
	  global $config_SMS;
	  $config_SMS                 = array();
	  $config_SMS['authKey']      = "101501AFc3RHRYY56869e0d"; //Your authentication key  
	  $config_SMS['senderId']     = "MIRCAS"; //Sender ID,While using route4 sender id should be 6 characters long.
	  $config_SMS['route']        = "route4"; //Define route
	  $config_SMS['auto_msg']     = "Dear customer, You have a new message in your miracas.com account. Please check here http://goo.gl/p4pAcD";
	  
	// COD KEYWOR
		$COD_keywords     = array("Todays Offers", "Season Sale","Cash On Delivery","Enjoy Cash On Delivery","Mercerized Cotton","Under Rs 500");

  // MESSAGES FOR RETURN REQUESTS
  $msg_request      = "Your return request has been successfully placed.Our team will check the availabilty of reveser pickup in your area and contact you soon";
  $msg_approved     = "We have arranged reverse pickup for your order. Please keep the product in its original condition with tags on and in the same envelope. The courier agent will come and pickup the parcel.";
  $msg_rejected     = "We are sorry to inform you that, we do not have reverse pickup facility in your region. Please courier back the product to the following address. Upon receiving the product we will issue a voucher for the same amount and Rs 100 /- for courier charges.<br><br>Miracas Lifestyle Pvt Ltd, B1, Parmar Corner, Fatima Nagar, Opposite Ganapati Mandir, Diamond Bakery Lane, Pune - 411 040";
  
  
  // STATIC BLOCKS ON KEYWORDS PAGE
  $brand_static    = array();
  $brand_static[]  = array("1", "#3498db", "EXPERIENCE GLOBAL", "Now experience International Brand Stores with MIRACAS.<br><br> You will have access to stores from all over south east asia.<br><br><img src='http://www.clker.com/cliparts/5/9/6/3/11954216681952214657molumen_world_map_1.svg.hi.png'/>");

  $style_static = array();
  $style_static[]  = array("2", "#1abc9c", "Trust Pay", "100% Payment protection for your order. You can invoke this if you have paid for the item but didn't receive it within 30 days.");
  //$style_static[]  = array("4", "#2c3e50", "SOME STATIC BLOCK", "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Animi, dolorum libero quo, nesciunt eligendi eaque totam culpa illo aliquam sequi.");
  
  }else{

    // DB
    $config['host']  = 'localhost';
    $config['db']    = 'reports'; 
    $config['user']  = 'root';     
    $config['pass']  = 'root';    
	
	  // REPORTS DB
    $config_reports['host']  = 'localhost';
    $config_reports['db']    = 'reports';
    $config_reports['user']  = 'root';
    $config_reports['pass']  = 'root'; 

    // PRESTASHOP DB
    $config_prestashop['host']  = 'localhost';
    $config_prestashop['db']    = 'miracasi_zs'; 
    $config_prestashop['user']  = 'root';     
    $config_prestashop['pass']  = 'root';   

  }
  
?>
