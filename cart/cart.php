<script>
	angular.module('cart', ['ngSanitize']).controller('main_controller', function($scope, $rootScope, $http, $timeout){

		$scope.empty_cart_msg = 'Loading Cart...';

	/* ====================================================================== *
            GET ALL DATA
     * ====================================================================== */       	

		$scope.getAll = function(){
			blockUI();
            $http.post('ajax/getAll.php')
                .then(function(data){                      

                    $scope.setAll(data.data);

                }, function(){
                    show_message('alert-danger', '¡Oops!', 'something went wrong, try again');
                })
                .finally(function(){
                    unblockUI();
                });
		}

		$scope.getAll();

	/* ====================================================================== *
            SET ALL DATA
     * ====================================================================== */       		

		$scope.productList 	= [];
		$scope.addressList 	= [];
		$scope.voucherList 	= [];
		$scope.address 		= '';
		$scope.totalAmount 	= 0;
		$scope.total 		= 0;
		$scope.manager 		= [];
		$scope.groupOrder 	= false;

		$scope.setAll = function(data){

			$scope.empty_cart_msg = 'You Shopping Cart is Empty!';

			$scope.productList 	= data['productList'];
			$scope.groupOrder 	= data['group_order'];

            $scope.addressList 	= data['addressList'];
            $scope.new_address 	= { 
					            	id_address: 'new', 
					            	all_address: 'New Address', 
					            	name: '', 
					            	mobile_number: '', 
					            	address: '', 
					            	landmark: '', 
					            	city: '', 
					            	state: '', 
					            	pin_code: '', 
					            	email: '' 
					            }
            $scope.address 		= $scope.addressList.length > 0 ? $scope.addressList[0] : $scope.new_address;
            $scope.addressList.push($scope.new_address);

            $scope.voucherList 	= data['voucherList'];

            $scope.totalAmount 		= parseFloat(data['totalAmount']);
            $scope.total 			= parseFloat(data['total']);
            $scope.shipping_fee 	= parseFloat(data['shipping_fee']);
            $scope.cod_fee 			= parseFloat(data['cod_fee']);
            $scope.total_plus_fee 	= $scope.total;

            refresh_totals();

            $scope.manager 					= data['manager'];

            $scope.paymentList 				= data['paymentList'];
            $scope.current_payment_method 	= data['current_payment_method'];

            refresh_total_fee();
		}

		function refresh_total_fee(){
			$scope.total_plus_fee 	= $scope.total;

			if($scope.total != 0){
            	$scope.total_plus_fee += $scope.shipping_fee;
            }

            if($scope.total!=0 && $scope.current_payment_method=='Cash on Delivery'){
            	$scope.total_plus_fee += $scope.cod_fee;	
            }
		}

		$scope.$watch('current_payment_method', function(){
			refresh_total_fee();
		})

	/* ====================================================================== *
            CART
     * ====================================================================== */       

     	$scope.$watch('productList', function(row){
            $scope.productList_total     = 0;
            if($scope.productList != undefined){
            	$scope.productList.forEach(function(row){
                	$scope.productList_total += parseFloat( row.price );
            	});
            }
        }, true);

    	$scope.deleteProduct = function(id_order_detail){
     		blockUI();
            $http.post('ajax/deleteProduct.php',{
            	'id_order_detail' 	: id_order_detail,
            })
                .then(function(data){      
                    if( data.data['error'] == false ){

                    	$scope.setAll(data.data);
                    	show_message('alert-success', 'Great!', 'the changes were successfully saved');       
                    	
                    }else{
                    	show_message('alert-danger', '¡Oops!', 'something went wrong, try again');
                    }
                }, function(){
                    show_message('alert-danger', '¡Oops!', 'something went wrong, try again');
                })
                .finally(function(){
                    unblockUI();
                });
     	}

     	$scope.filterProductsByType  = function(order_type){
            return function(item) {
                
                if(order_type == 'espresso'){
                	return item.order_type ==  'espresso';
                }else if(order_type == 'normal'){
                   return item.order_type ==  '' || item.order_type == null;
                }
                
            }
        }

    /* ====================================================================== *
            ADDRESS
     * ====================================================================== */       

     	$scope.saveAddress = function(){
     		if( 
				$scope.address.name == "" || 
				$scope.address.mobile_number == "" || 
				$scope.address.address == "" || 
				$scope.address.landmark == "" || 
				$scope.address.city == "" || 
				$scope.address.state == "" ||
				$scope.address.pin_code == "" ||
				$scope.address.email == "" 
			){
				alert('All fileds are required!');
				jQuery('.form-wizard').bootstrapWizard('show',0);
				return;
			}

     		blockUI();
            $http.post('ajax/saveAddress.php',{
            	'address' 	: $scope.address,
            })
                .then(function(data){    
                    if( data.data['error'] == false ){

                    	$scope.setAll(data.data);
                    	show_message('alert-success', 'Great!', 'the changes were successfully saved');                	
                    	
                    }else{
                    	show_message('alert-danger', '¡Oops!', 'something went wrong, try again');
                    }
                }, function(){
                    show_message('alert-danger', '¡Oops!', 'something went wrong, try again');
                })
                .finally(function(){
                    unblockUI();
                });
     	}

    /* ====================================================================== *
            VOUCHER
     * ====================================================================== */       

     	$scope.voucher_code = '';

     	$scope.copypaste_code = function(code){
			$scope.voucher_code = code;     		
     	}

     	$scope.apply_code = function(){
     		var checkbox = $scope.voucherList.filter(function(obj){ return obj.code+"" === $scope.voucher_code+"";  })[0];

     		if(checkbox != undefined){
				if(checkbox.checked){
					alert('This code is already applied!')
				}else{
					checkbox.checked = true;
				}

				$scope.voucher_code = '';
			}
     	}

     	function refresh_totals(){
     		$scope.totalVoucherList     = 0;
            $scope.voucherList.forEach(function(row){
                if(row.checked){
                	var value = 0;

	                if(row.value_kind == 'percentage'){
						value = $scope.totalAmount * (parseFloat(row.value)/100);
					}else if(row.value_kind == 'amount'){
						value = parseFloat(row.value);
					}

					$scope.totalVoucherList = Math.round(($scope.totalVoucherList + value)* 1e12)/ 1e12;
				}
            });

            $scope.totalAmountMinusVoucher = $scope.totalAmount-$scope.totalVoucherList;
			if($scope.totalAmountMinusVoucher < 0){
				$scope.voucher_difference = Math.abs($scope.totalAmountMinusVoucher);
				$scope.totalAmountMinusVoucher = 0;
			}
     	}

     	$scope.$watch('voucherList', function(row){
            refresh_totals();
        }, true);

     	$scope.saveVoucherList = function(){
     		blockUI();
            $http.post('ajax/saveVoucherList.php',{
            	'voucherList' 	: $scope.voucherList,
            })
                .then(function(data){    
                    if( data.data['error'] == false ){

                    	$scope.setAll(data.data);
                    	show_message('alert-success', 'Great!', 'the changes were successfully saved');                	
                    	
                    }else{
                    	show_message('alert-danger', '¡Oops!', 'something went wrong, try again');
                    }
                }, function(){
                    show_message('alert-danger', '¡Oops!', 'something went wrong, try again');
                })
                .finally(function(){
                    unblockUI();
                });
     	}

     	$scope.deleteVoucher = function(id_voucher){
     		blockUI();
            $http.post('ajax/deleteVoucher.php',{
            	'id_voucher' 	: id_voucher,
            })
                .then(function(data){      
                    if( data.data['error'] == false ){

                    	$scope.setAll(data.data);
                    	show_message('alert-success', 'Great!', 'the changes were successfully saved');       
                    	
                    }else{
                    	show_message('alert-danger', '¡Oops!', 'something went wrong, try again');
                    }
                }, function(){
                    show_message('alert-danger', '¡Oops!', 'something went wrong, try again');
                })
                .finally(function(){
                    unblockUI();
                });
     	}

    /* ====================================================================== *
            PAY
     * ====================================================================== */        	

     	$('body').on('change', 'input[name="payment_method"]', function(){
     		console.log($(this).val());
     	});

     	$scope.confirm_payment = function(){
			if($("input:radio[name='payment_method']").is(":checked")){
				//if(confirm("Are you sure?")){
					document.form.action.value = "1";
					document.form.submit();
				//}
			}else{
				alert("Please select a payment method!");
			}
		}

	});

	function change_title(){
		var selected 	= $('[ng-app="cart"] .tabs li.active').attr('data-id');
		var title 		= '';
		if(selected == 'products'){
			title = '<span class="fa fa-shopping-cart"></span> Shopping Cart';
		}else if(selected == 'address'){
			title = '<i class="fa fa-truck"></i> Ship to';
		}else if(selected == 'voucher'){
			title = '<i class="fa fa-ticket"></i> Voucher';
		}else if(selected == 'payment'){
			title = '<i class="fa fa-inr"></i> Payment';
		}

		$('.shopping-cart-title').html(title);
	}
	
	$(document).ready(function(){
		change_title();
		$('[ng-app="cart"] .pager').on('click', 'a', function(){
			change_title();
		});
	});
</script>

<div ng-app="cart" ng-controller="main_controller" class="form-wizard fade-ng-cloak" ng-cloak>

<!--
/* ====================================================================== *
        TABS
 * ====================================================================== */       	
-->

	<ul class="tabs">
		<li data-id="products" class="active"><a href="#products" data-toggle="tab">PRODUCTS <span>&nbsp;</span></a></li>
		<li data-id="address"><a href="#address" data-toggle="tab">ADDRESS <span>&nbsp;</span></a></li>
		<li data-id="voucher"><a href="#voucher" data-toggle="tab">VOUCHER <span>&nbsp;</span></a></li>
		<li data-id="payment"><a href="#payment" data-toggle="tab">PAYMENT <span>&nbsp;</span></a></li>
	</ul>

<!--
/* ====================================================================== *
        PROGRESS INDICATOR
 * ====================================================================== */       	
-->

  	<div class="progress-indicator">
		<span></span>
	</div>

<!--
/* ====================================================================== *
        TABS CONTENT
 * ====================================================================== */       	
-->

	<div class="tab-content">

	<!--
	/* ====================================================================== *
	        PRODUCTS
	 * ====================================================================== */       	
	-->

		<div class="tab-pane active" id="products">

		<!--
		/* ====================================================================== *
		        NORMAL PRODUCTS
		 * ====================================================================== */       	
		-->
			
			<table ng-show="productList.length" class="table">
				<thead>
					<tr>
						<th>PRODUCTS</th>
						<th>DESCRIPTION</th>
						<th class="text-right">PRICE</th>
					</tr>
				</thead>
				<tbody>
			        <tr ng-repeat="row in productList">
			        	<td style="width: 195px;">
			        		<a href="../feed/product_details.php?{{ row.order_type == 'espresso' ? 'id_espresso_product' : 'id_product' }}={{row.id_product}}">
			        			<img style="width:100% !important;" src="{{row.img}}" alt="">
			        		</a>
			        	</td>
			        	<td class="item_description">
			        		<div>
			        			<p class="item_name">
			        				{{row.name}}
			        				<div class="item_attribute_space"></div>
			        				<span class="badge" ng-show="row.order_type == 'espresso'">Espresso</span>
			        				<span class="badge" ng-show="row.order_type == '' || row.order_type == null">Added from web</span>
			        				<span class="badge" ng-show="row.with_keyword_discount == 'yes'">Group Product</span>
			        			</p>
								
								<div ng-show="row.color!=''">
									<span class="item_attribute">COLOR</span>
									<span 
		                				class="media-box-color" 
		                				style="
		                					background:{{row.color}};
		                					{{ (row.color=='white' || row.color=='#fff' || row.color=='#ffffff') ? 'border: 1px solid #dedddd !important;' : '' }}
		                				"
		                			></span>
		                			<div class="item_attribute_space"></div>	
		                		</div>
								
								<div ng-show="row.size!=''">
		                			<span class="item_attribute">SIZE</span>
									{{row.size}}
									<div class="item_attribute_space"></div>	
								</div>

								<span class="item_attribute">QTY</span>
								{{row.qty}}
								<div class="item_attribute_space"></div>	

			        			<a ng-click="deleteProduct(row.id_order_detail)"" class="">Remove</a>

			        		</div>
			        	</td>
			        	<td class="text-right">{{row.price | currency:'₹'}}</td>
			        </tr>
			        <tr>
						<td colspan="2" class="text-right">TOTAL:</td>
						<td class="text-right" style="width: 1%;">{{productList_total | currency:'₹'}}</td>
					</tr>
				</tbody>
			</table>

			<div ng-hide="productList.length" class="cart-item text-center" style="padding: 70px 40px;">{{empty_cart_msg}}</div>

		<!--
		/* ====================================================================== *
		        ESPRESSO PRODUCTS (this will not longer be needed)
		 * ====================================================================== */       	
		

			<br ng-show="espressoProductList.length > 0">

			<table class="table" ng-show="espressoProductList.length > 0">
				<tr>
					<td colspan="10" class="alert alert-info text-center">
						<h5>
							You can only place espresso orders from the mobile app. 
							<br>
							<small>While checkout, the espresso products listed below will be automatically deleted from your cart.</small>
						</h5>
					</td>
				</tr>
		        <tr ng-repeat="row in espressoProductList = (productList | filter : filterProductsByType('espresso'))">
		        	<td class="item_description alert alert-info">
		        		<a href="../feed/product_details.php?id_product={{row.id_product}}">
		        			<img style="width:50px !important;" src="{{row.img}}" alt="">
		        		</a>
		        		
		        		<div>
		        			<p class="item_name">
		        				{{row.name}}
		        				&nbsp;
		        				<span class="badge" ng-show="row.order_type == 'espresso'">Espresso</span>
		        				<span class="badge" ng-show="row.order_type == '' || row.order_type == null">Added from web</span>
		        			</p>
		        			<p><a ng-click="deleteProduct(row.id_order_detail)"" class="btn btn-sm btn-red">Remove</a></p>
		        		</div>
		        	</td>
		        	<td class="alert alert-info">
		        		<span ng-show="row.color!=''" class="media-box-color"><span style="background:{{row.color}};"></span></span>
		        	</td>
		        	<td class="alert alert-info">{{row.size}}</td>
		        	<td class="alert alert-info">{{row.qty}}</td>
		        	<td class="text-right alert alert-info">{{row.price | currency:'₹'}}</td>
		        </tr>
			</table>

		-->	

		</div>

	<!--
	/* ====================================================================== *
	        ADDRESS
	 * ====================================================================== */       	
	-->

		<div class="tab-pane" id="address">

			<br>

			<p>
				<select class="form-control" ng-model="address" ng-options="row as row.all_address for row in addressList"></select>	
			</p>

			<div class="row">
				<div class="col-sm-2"><label for="name">Name</label></div>
				<div class="col-sm-6"><input ng-model="address.name" type="text" name="name" maxlength="300" class="form-control"></div>
			</div>				
			
			<div class="row">
				<div class="col-sm-2"><label for="mobile_number">Mobile Number</label></div>
				<div class="col-sm-6"><input ng-model="address.mobile_number" type="text" name="mobile_number" maxlength="300" class="form-control"></div>
			</div>
			
			<div class="row">
				<div class="col-sm-2"><label for="address_text">Address</label></div>
				<div class="col-sm-6"><input ng-model="address.address" type="text" name="address_text" maxlength="400" class="form-control"></div>
			</div>
			
			<div class="row">
				<div class="col-sm-2"><label for="landmark">Landmark</label></div>
				<div class="col-sm-6"><input ng-model="address.landmark" type="text" name="landmark" maxlength="300" class="form-control"></div>
			</div>
			
			<div class="row">
				<div class="col-sm-2"><label for="city">City</label></div>
				<div class="col-sm-6"><input ng-model="address.city" type="text" name="city" maxlength="300" class="form-control"></div>
			</div>
			
			<div class="row">
				<div class="col-sm-2"><label for="state">State</label></div>
				<div class="col-sm-6"><input ng-model="address.state" type="text" name="state" maxlength="300" class="form-control"></div>
			</div>
			
			<div class="row">
				<div class="col-sm-2"><label for="pin_code">Pin code</label></div>
				<div class="col-sm-6"><input ng-model="address.pin_code" type="text" name="pin_code" maxlength="300" class="form-control"></div>
			</div>

			<div class="row">
				<div class="col-sm-2"><label for="email">Email</label></div>
				<div class="col-sm-6"><input ng-model="address.email" type="text" name="email" maxlength="300" class="form-control"></div>
			</div>

		</div>

	<!--
	/* ====================================================================== *
	        VOUCHER
	 * ====================================================================== */       	
	-->

		<div class="tab-pane" id="voucher">
			
			<div class="alert alert-info text-center">
				<h5 style="margin:0;" >If you are unable to see your voucher here, it is possible that your voucher may have expired. This normally happens if the voucher code starts with the letter "V". To get a new voucher issued, please contact our customer care at 022 3077 0240 
				</h5>
			</div>

			<div class="alert alert-info text-center" ng-if="voucherList.length == 0">
				<h5>No vouchers available found, please continue!</h5>
			</div>

			<div ng-show="voucherList.length > 0"> 

				<div class="row available_vouchers" ng-repeat="row in voucherList" ng-show="row.visibility">
					<div class="col-md-2">
						<div ng-if="row.available == false">
							-{{row.value_kind == 'amount' ? '₹' : ''}}{{row.value}}{{row.value_kind == 'percentage' ? '%' : ''}}
						</div>
						<div ng-if="row.available == true">
							<input type="checkbox" name="voucher_{{row.id_voucher}}" style="display:none;" ng-checked="row.checked" />
							<i ng-if="row.exists_in_order" class="fa fa-check"></i>
							-{{row.value_kind == 'amount' ? '₹' : ''}}{{row.value}}{{row.value_kind == 'percentage' ? '%' : ''}}
						</div>
					</div>
					<div class="col-md-5">
						{{row.description}} 
					</div>
					<div class="col-md-5">
						<div ng-if="groupOrder">
							<div class="label label-warning">Voucher not available for group order</div>
						</div>
						<div ng-if="!groupOrder">
							<div ng-if="row.available == false">
								<div class="label label-warning">The cart value is less for this voucher to apply</div>
							</div>
							<div ng-if="row.available == true">
								<a ng-if="row.exists_in_order" ng-click="deleteVoucher(row.id_voucher)">Remove</a>
								<a ng-if="!row.exists_in_order" ng-click="copypaste_code(row.code)">Add the voucher code</a>
							</div>
						</div>
					</div>
				</div>
				
				<div ng-if="!groupOrder">
					<h3>Voucher code</h3>
					<p><input type="text" class="form-control" ng-model="voucher_code" placeholder="Write your purchase code here"></p>
					<a  class="btn btn-sm btn-green" ng-click="apply_code()">Apply</a>
				</div>
				<div ng-if="groupOrder" class="alert alert-warning">
					Since group purchases have maximum amount of discount, you will not be able to apply your vouchers on the cart. To use the vouchers please remove the group discounted products from your cart and try again
				</div>

				<hr>

				<table class="table-totals">
					<tr>
						<td style="width: 150px;">Items:</td>
						<td class="text-right">{{totalAmount | currency:'₹'}}</td>
					</tr>
					<tr>
						<td>Discount:</td>
						<td class="text-right">{{totalVoucherList | currency:'₹'}}</td>
					</tr>
					<tr>
						<td>Order total:</td>
						<td class="text-right">{{totalAmountMinusVoucher | currency:'₹'}}</td>
					</tr>
				</table>

				<div ng-if="voucher_difference>0" class="alert alert-warning" style="margin-top:20px;">In your next order you will recive a new voucher for {{voucher_difference | currency:'₹'}}</div>

			</div>

		</div>

	<!--
	/* ====================================================================== *
	        PAYMENT
	 * ====================================================================== */       	
	-->

		<div class="tab-pane" id="payment">

			<form action="" method="post" name="form">
				<input type="hidden" name="action">
			
				<div class="manager_card" ng-show="manager != ''">
					<div class="manager_card_title">
						<img src="{{manager.img_src}}" alt="" />
						<div class="manager_card_title_text">
							{{manager.name}}<br>{{manager.email}}
						</div>
					</div>
					I am your relationship manager and will help you with the fulfullment of this order. Feel free to drop a mail with your questions or concerns and I will be happy to help. You can ask about product details, payment methods, status of the order, etc.
				</div>

				<img src="../pay/payments.png"/>
				<br>
				<br>
				<img src="../pay/payments1.png"/>

				<br><br>

				<table class="table table-condensed table-bordered" style="width: auto;">
					<tr>
						<td>Base price</td>
						<td>{{total | currency:'₹'}}</td>
					</tr>
					<tr ng-show="total!=0">
						<td>Shipping fee</td>
						<td>{{shipping_fee | currency:'₹'}}</td>
					</tr>
					<tr ng-show="total!=0 && current_payment_method=='Cash on Delivery'">
						<td>Cash on delivery fee</td>
						<td>{{cod_fee | currency:'₹'}}</td>
					</tr>
					<tr>
						<td><strong>Total Amount</strong></td>
						<td>{{total_plus_fee | currency:'₹'}}</td>
					</tr>
				</table>

				Select a payment method:
				<br><br>

				<!--<div class="btn-group" data-toggle="buttons">-->
				  
				  <!--<label ng-repeat="row in paymentList" class="btn btn-primary {{row.active ? 'active' : ''}} {{row.payment_method=='PAYTM' ? 'with_img' : ''}}">-->
				  	<label ng-repeat="row in paymentList" class="payment_methods">
				  		<!--<i class="glyphicon glyphicon-ok"></i>-->
				    	<input type="radio" name="payment_method" ng-value="row.payment_method" autocomplete="off" ng-model="$parent.current_payment_method"> 
				    	<span ng-bind-html="row.name"></span>
				  	</label>

				<!--</div>-->
				<!--
				<script>
					jQuery(document).ready(function(){ jQuery('.form-wizard').bootstrapWizard('show',3); });
				</script>
				-->

				<br>
				
				<p>
					<a ng-click="confirm_payment()" class="btn btn-green btn-default pull-right">Confirm Payment</a>
				</p>

			</form>

		</div>

	<!--
	/* ====================================================================== *
	        BOTTOM NAVIGATION
	 * ====================================================================== */       	
	-->

		<!-- Tabs Pager -->
		<ul class="pager wizard" style="margin-bottom: 0;">
			<li class="previous">
				<a href="#"><i class="fa fa-arrow-left"></i>&nbsp; Previous</a>
			</li>
			
			<li class="next">
				<a href="#" ng-show="productList.length">Next &nbsp;<i class="fa fa-arrow-right"></i></a>
			</li>
		</ul>

	</div>

</div>
