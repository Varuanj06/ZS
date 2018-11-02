<?php require_once("../classes/message.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/keyword.php"); ?>
<?php require_once("../classes/voucher.php"); ?>
<?php require_once("../classes/order.php"); ?>
<?php require_once("../classes/address.php"); ?>

<?php 
	$extra_param = "";
	if($active_session !== true){ 
		$extra_param = "?set_gender=".str_replace("/", "", $_GET['set_gender']);
	}
?>

<?php 
  
    $product_class        = new product();
    $keyword_class        = new keyword();
    $voucher_class        = new voucher();
    $order_class          = new order();
    $address_class        = new address();

  /* ====================================================================== *
        BRAND MALE KEYWORDS
   * ====================================================================== */      

    $products_male    = $product_class->get_brand_keywords_per_gender("/male/", "");
    $keywords_male    = array();

    foreach ($products_male as $row) {
      $arr = explode("/", $row->get_keywords());
      foreach ($arr as $word) {
        if($word != ""){
          $keywords_male[] = $word;
        }
      }
    }
    $keywords_male = array_unique($keywords_male);
    
  /* ====================================================================== *
        BRAND FEMALE KEYWORDS
   * ====================================================================== */      

    $products_female    = $product_class->get_brand_keywords_per_gender("/female/", "");
    $keywords_female    = array();

    foreach ($products_female as $row) {
      $arr = explode("/", $row->get_keywords());
      foreach ($arr as $word) {
        if($word != ""){
          $keywords_female[] = $word;
        }
      }
    }
    $keywords_female = array_unique($keywords_female);

?>

<!--
/* ====================================================================== *
      SIDEBAR
 * ====================================================================== */        
--> 

  <nav id="slideout_menu" class="slideout_menu">
  	<section class="menu-section">
    	<ul class="menu-section-list">
          
          <?php if($active_session === true){ ?>
            
            <?php 
              $sb_id_fb_user         = $user['id'];

              /* Current id_order */

              $sb_current_id_order   = '-1';
              if($order_class->get_id_order_by_fb_user($sb_id_fb_user)){
                $sb_current_id_order = $order_class->get_id_order_by_fb_user($sb_id_fb_user);
              }

              /* Current address */

              $sb_addresses        = $address_class->get_list($sb_id_fb_user, " order by date_update desc ");
              $sb_current_address  = "";
              foreach ($sb_addresses as $row){ 
                $sb_current_address = $row->get_id_address();
                break;
              }
              $address_class = new address();
              $address_class->map($sb_current_address, $sb_id_fb_user);

              /* Get vouchers */

              $sb_vouchers_available  = $voucher_class->get_all_for_user($address_class->get_email(), $sb_current_id_order, "order by till_date");
              $sb_vouchers_total      = 0;

              foreach ($sb_vouchers_available as $row) { 
                if($row->get_visibility() == 'N') continue;
                if($row->get_value_kind() != 'amount') continue;

                $sb_vouchers_total += $row->get_value();
              }

            ?>

            <style>
              .wallet_balance{
                padding: 20px;
              }
              .wallet_balance_content{
                padding: 20px;
                border: 1px solid #fff;
                color: #fff;
                text-align: center;
                border-radius: 10px;
              }
              .wallet_balance_content span{
                margin-top: 20px;
                display: block;
                color: #fff;
              }
            </style>
  
            <li>
              <div class="wallet_balance">
                <div class="wallet_balance_content">
                  WALLET BALANCE
                  <span>Rs <?php echo number_format($sb_vouchers_total, 2); ?></span>
                </div>
              </div>
            </li>
    
          <?php } ?>

      		<li><a href="../feed<?php echo $extra_param; ?>"><span class="fa fa-home"></span> &nbsp;Home</a></li>
			<li><a href="../feed?set_gender=male"><span class="fa fa-home"></span> &nbsp;For Men</a></li>
			<li><a href="../feed?set_gender=female"><span class="fa fa-home"></span> &nbsp;for Women</a></li>


           

          	<?php if($active_session === true){ ?>
        			<?php if($disable_things !== true){ ?>
                <li><a href="../feed?usr=<?php echo $user['id']; ?><?php echo str_replace("?", "&", $extra_param); ?>"><span class="fa fa-bookmark"></span> &nbsp;Saved Products</a></li>
        				<li><a href="../cart"><span class="fa fa-shopping-cart"></span> &nbsp;Cart</a></li>
        				<li><a href="../orders"><span class="fa fa-truck"></span> &nbsp;My Orders</a></li>
                		<?php $message    = new message(); ?>
                		<li><a href="../messages"><span class="fa fa-comment"></span> &nbsp;Messages</a></li>
        			<?php } ?>
      			   <li><a href="../signout.php"><span class="fa fa-power-off"></span> &nbsp;Logout</a></li>
      		  <?php }else{ ?>
      			 <li><a href="../signin"><span class="fa fa-arrow-right"></span> &nbsp;Sign in</a></li>
      		  <?php } ?>
    	</ul>
  	</section>
</nav>

<script>
	$( document ).ready(function() {

    /* ==== SLIDEOUT +=== */

  	var slideout = new Slideout({
    	'panel': window.document.getElementById('menu-page-wraper'),
    	'menu': window.document.getElementById('slideout_menu')
  	});

		document.querySelector('.js-slideout-toggle').addEventListener('click', function() {
	  		slideout.toggle();
		});

		document.querySelector('.slideout_menu').addEventListener('click', function(eve) {
      console.log(eve.target.classList.value);
	  		if (eve.target.nodeName === 'A' && eve.target.classList.value != 'open_sub_menu') { slideout.close(); }
		});

    slideout.on('beforeopen', function() {
      //document.querySelector('.fixed-element').classList.add('fixed-open');
    });

    slideout.on('beforeclose', function() {
      //document.querySelector('.fixed-element').classList.remove('fixed-open');
    });

    /* ==== SUBMENU ==== */

    $('.open_sub_menu .label').on('click', function(e){
      e.stopPropagation();
      location.href=$(this).attr('data-url');
    })

    $('.open_sub_menu').on('click', function(e){
      e.preventDefault();

      var $this     = $(this);
      var sub_menu  = $this.siblings('.sub_menu');

      sub_menu.stop();
      if(sub_menu.parent('li').hasClass('open')){
        sub_menu.parent('li').removeClass('open');
        sub_menu.slideUp();
      }else{
        sub_menu.parent('li').addClass('open');
        sub_menu.slideDown();
      }

    });

  });
</script>