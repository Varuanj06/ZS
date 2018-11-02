<style>
	.mobile_app{
		display: none;
		position: fixed;
		bottom: 0;
		left: 0;
		width: 100%;
		height: 60px;
		background: #FBEED8;
		z-index: 99999;
		padding: 10px; 

		color: #5C3B13 !important;
		text-decoration: none !important;
	}
	.mobile_app_name,
	.mobile_app img,
	.column2{
		height: 40px;
	}
	.mobile_app_name{
		line-height: 40px;
		font-size: 16px;
	}
	.mobile_app img{
		max-width: 100%;
		margin-right: 10px;
	}
	.column1{
		border-right: 1px solid #d6b68f;
	}
	.column2{
		line-height: 40px;
		text-align: center;
		font-weight: 500;
		font-size: 14px;
	}
</style>

<a href="https://play.google.com/store/apps/details?id=com.miracas&hl=en">
	<div class="mobile_app">
		<div class="row">
			<div class="col-xs-8 column1">
				<img src="https://lh3.googleusercontent.com/iCDZLxm0dTp7Gh03VRVs1kb4Zx4zB53TrNveBQtd4_fhkJVcxvlMnplTNdyqs0KCDw=w300-rw" alt="" class="pull-left">
				<div class="mobile_app_name">
					Miracas Espresso
				</div>
			</div>
			<div class="col-xs-4 column2">
				GET APP &nbsp;<i class="fa fa-chevron-right"></i>
			</div>
		</div>
	</div>
</a>

<script>
	var ua = navigator.userAgent.toLowerCase();
	var isAndroid = ua.indexOf("android") > -1; //&& ua.indexOf("mobile");
	if(isAndroid) {
	  $('.mobile_app').show();
	}
</script>