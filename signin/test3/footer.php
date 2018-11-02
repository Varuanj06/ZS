<?php 
	if (strpos($_SERVER['HTTP_HOST'], 'miracas.in') !== false) { 
		require_once("../footer_global.php");
	}else{
?>

<div class="page-footer">
	<div class="row">
		<div class="col-sm-4">
			Copyright @ Miracas International Limited
      		<p><img src="http://miracas.com/ZS/signin/logo.png" width="300px"  /></p>
		</div>
		<div class="col-sm-4">
			<p><a href="http://miracas.com/ZS/static/Aboutus.php" >About Us</a></p>	
			<p><a href="http://miracas.com/ZS/static/Delivery.php" >Delivery</a></p>		
			<p><a href="http://miracas.com/ZS/static/Returns.php" >Return Policy</a></p>	
			<p><a href="http://miracas.com/ZS/static/Refund.php" >Cancellation And Refund</a></p>
			<p><a href="http://miracas.com/ZS/static/CustomerCare.php" >Customer Care</a></p>			
			<p><a href="http://miracas.com/ZS/static/Modelling.php" >Modelling</a></p>	
			<p><a href="http://iduple.com/content/14-disclaimer-policy" >Disclaimer Policy</a></p>	
			<p><a href="http://iduple.com/content/3-t-c" >Terms And Conditions</a></p>	
			<p><a href="http://iduple.com/content/11-privacy-policy" >Privacy Policy</a></p>
		</div>
		<div class="col-sm-4">
			<a title="Like us on Facebook"  href="http://facebook.com/miracaslife"><img src="http://miracas.com/ZS/signin/footer-facebook.png"></a>
		</div>
	</div>
</div>

<script>
	$('.page-wrap-inner').css('padding-bottom', $('.page-footer').outerHeight());
	$('.page-footer').css('margin-top', -$('.page-footer').outerHeight());
</script>
<?php 
	}
?>