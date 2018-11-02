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
	  $config_SMS['route']        = "4"; //Define route
	  $config_SMS['auto_msg']     = "Dear customer, You have a new message in your miracas.com account. Please check here http://goo.gl/p4pAcD";
	  
	// COD KEYWOR
		$COD_keywords     = array("Brand Store : ABC","Brand Store: DEF");

  // MESSAGES FOR RETURN REQUESTS
  $msg_request      = "Your return request has been successfully placed.Our team will check the availabilty of reveser pickup in your area and contact you soon";
  $msg_approved     = "We have arranged reverse pickup for your order. Please keep the product in its original condition with tags on and in the same envelope. The courier agent will come and pickup the parcel.";
  $msg_rejected     = "We are sorry to inform you that, we do not have reverse pickup facility in your region. Please courier back the product to the following address. Upon receiving the product we will issue a voucher for the same amount and Rs 100 /- for courier charges.<br><br>Miracas Lifestyle Pvt Ltd, B1, Madhurang Heights, Opposite Nine Hills, NIBM Road, Pune - 411 048";
  
  
  // STATIC BLOCKS ON KEYWORDS PAGE
  $brand_static    = array();
  $brand_static[]  = array("1", "#3498db", "EXPERIENCE GLOBAL", "Now experience International Brand Stores with MIRACAS.<br><br> You will have access to stores from all over south east asia.<br><br><img src='http://www.clker.com/cliparts/5/9/6/3/11954216681952214657molumen_world_map_1.svg.hi.png'/>");
  $brand_static[]  = array("1", "#aa4069", "New Collection", '<iframe width="95%" height="200px" src="https://www.youtube.com/embed/yWt-MYgtELw" frameborder="0" allowfullscreen></iframe>');
  //$brand_static[]  = array("4", "#145245", "UPTO 50% SAVINGS", 'Do you know that MIRACAS products are upto 50% less priced than its couter parts. This is because we source the products directly from the factories or from the brand. No middle men involved. <br><br><img src="http://www.onepath.com.au/content_images/shared_images/OP_Icon_RGB_Pos_Savings.png" width="50%"/>');
  //$brand_static[]  = array("4", "#3b5998", "IT FEELS GOOD TO BE LIKED" , '850,000+ Fans and counting....<br><br>    <img src="http://vectorise.net/logo/wp-content/uploads/2012/08/Facebook-Like.png" width="50%" />');
  $brand_static[]  = array("4", "#3b5998", "", '<iframe src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Fmiracaslife%2F&tabs&width=500&height=214&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId=470493872995494" width="95%" height="214" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>');
  //$brand_static[]  = array("6", "#b5651d", "CASH ON DELIVERY", 'Cash on Delivery on all products between 12 am and 1 am tonight <br><br><img src="http://www.tococlothing.com/wp-content/uploads/2015/08/expo-delivery.png" width="70%" />');

  $style_static = array();
  $style_static[]  = array("2", "#1abc9c", "Trust Pay", "100% Payment protection for your order. <br><br><img src='http://miracas.com/ZS/assets/trustPay_logo.png' width='50%'/><br><br>You can invoke this if you have paid for the item but didn't receive it within 30 days.");
  //$style_static[]  = array("7", "#d26d62", "SURPRISE", "10% DISCOUNT ON YOUR ORDER. USE THE COUPON CODE <b>CD10</b> <br><br><img src='http://www.principal-hayley.com/townhousecompany/wp-content/uploads/sites/35/2015/10/Blythswood-Gift-Icon.png' /><br><br>");

  $style_static[]  = array("10", "#4e3300", "INDIAN FASHION", "India doesn't have seasonal fashion. We wear what we love, throughout the year.<br><br><img src='https://www.ifm.com/img/k_locations_ind.png' width='50%'/>");
  
  
  // RATIO FOR THE CATEGORY AND 
  $brand_width      = 400;
  $brand_height     = 400;
  $category_width   = 75;
  $category_height  = 100;
  
  $pixel_keyword_width  = 200;
  $pixel_keyword_height = 200;
  $super_tag_width      = 100;
  $super_tag_height     = 100;
  
    // DEFAULT MESSAGE FOR CUSTOMERS WHILE CONVERTING PIXELS INTO PRODUCTS
	$pixel_msg          = "The product you bookmarked is now available, check it out here: {{product_link}}";
	$pixel_mail_subject = "Your Wishlist is now available";
	
	$on_pixel_increase_emails   = array("sreelaj.john@iduple.com", "shrikant.z@miracas.com","roopam.nyk@miracas.com");
	$on_pixel_increase_subject  = "New pixel count increase for {{vendor_name}}!";
	$on_pixel_increase_msg      = "There is a now booking for a pixel named {{pixel_name}} for {{vendor_name}}. Please check.";

	// REMINDER MESSAGE
	$pixel_reminder_msg = "Your shortlisted product is going out of season in {{pixel_keyword_expiry}}. Hurry. You can purchase the product at {{product_link}}";
	$pixel_reminder_subject = "Reminder: Your Wishlist Is Now Available";

	// PAY ONLINE RESPONSES
	$orders_success_url  = "http://miracas.com/ZS/orders/?payu_success=1";
	$orders_failure_url  = "http://miracas.com/ZS/orders/?payu_failure=1";
	$pay_success_url  = "http://miracas.com/ZS/pay/?payu_success=1";
	$pay_failure_url  = "http://miracas.com/ZS/pay/?payu_failure=1";
	
	// RELATIONSHIP MANAGERS 
  $relationship_managers = array(
    
    array(
      'range'     => '4001-999999999999999',
      'img_src'   => 'https://cdn1.iconfinder.com/data/icons/user-pictures/100/female1-512.png',
      'name'      => 'Carol Arokhyaswami',
      'email'     => 'carol.as@miracas.com'
    )
  );
  
  
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