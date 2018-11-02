<?php

class order{
	//VARIABLES
	private $id_order;
	private $id_fb_user;
	private $status;
	private $id_order_address;
	private $date_add;
	private $date_update;
	private $date_done;
	private $date_placed;
	private $status_admin;
	private $payment_method;
	private $online_payment;
	private $payed;
	private $courier_allocation;
	private $total_amount;
	private $total_discount;
	private $total_discount_voucher;
	private $free_order;
	private $payu_transaction;
	private $razorpay_order;
	private $paytm_checksum;
	private $paytm_id_order;
	private $gift_shipped;
	private $shipping_fee;
	private $cod_fee;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_order 				= "";
		$this->id_fb_user 				= "";
		$this->status 					= "";
		$this->id_order_address 		= "";
		$this->date_add 				= "";
		$this->date_update 				= "";
		$this->date_done 				= "";
		$this->date_placed 				= "";
		$this->status_admin 			= "";
		$this->payment_method 			= "";
		$this->online_payment 			= "";
		$this->payed 					= "";
		$this->courier_allocation 		= "";
		$this->total_amount 			= "0";
		$this->total_discount 			= "0";
		$this->total_discount_voucher 	= "0";
		$this->free_order 				= "";
		$this->payu_transaction 		= "";
		$this->razorpay_order 			= "";
		$this->paytm_checksum 			= "";
		$this->gift_shipped 			= "";
		$this->paytm_id_order 			= "";
		$this->shipping_fee 			= "";
		$this->cod_fee 					= "";
	}

	//GETTERS AND SETTERS
	public function get_id_order(){
		return $this->id_order;
	}

	public function set_id_order($id_order){
		$this->id_order = $id_order;
	}

	public function get_id_fb_user(){
		return $this->id_fb_user;
	}

	public function set_id_fb_user($id_fb_user){
		$this->id_fb_user = $id_fb_user;
	}

	public function get_status(){
		return $this->status;
	}

	public function set_status($status){
		$this->status = $status;
	}

	public function get_id_order_address(){
		return $this->id_order_address;
	}

	public function set_id_order_address($id_order_address){
		$this->id_order_address = $id_order_address;
	}

	public function get_date_add(){
		return $this->date_add;
	}

	public function set_date_add($date_add){
		$this->date_add = $date_add;
	}

	public function get_date_update(){
		return $this->date_update;
	}

	public function set_date_update($date_update){
		$this->date_update = $date_update;
	}

	public function get_date_done(){
		return $this->date_done;
	}

	public function set_date_done($date_done){
		$this->date_done = $date_done;
	}

	public function get_date_placed(){
		return $this->date_placed;
	}

	public function set_date_placed($date_placed){
		$this->date_placed = $date_placed;
	}

	public function get_status_admin(){
		return $this->status_admin;
	}

	public function set_status_admin($status_admin){
		$this->status_admin = $status_admin;
	}

	public function get_payment_method(){
		return $this->payment_method;
	}

	public function set_payment_method($payment_method){
		$this->payment_method = $payment_method;
	}

	public function get_online_payment(){
		return $this->online_payment;
	}

	public function set_online_payment($online_payment){
		$this->online_payment = $online_payment;
	}

	public function get_payed(){
		return $this->payed;
	}

	public function set_payed($payed){
		$this->payed = $payed;
	}

	public function get_courier_allocation(){
		return $this->courier_allocation;
	}

	public function set_courier_allocation($courier_allocation){
		$this->courier_allocation = $courier_allocation;
	}

	public function get_total_amount(){
		return $this->total_amount;
	}

	public function set_total_amount($total_amount){
		$this->total_amount = $total_amount;
	}

	public function get_total_discount(){
		return $this->total_discount;
	}

	public function set_total_discount($total_discount){
		$this->total_discount = $total_discount;
	}

	public function get_total_discount_voucher(){
		return $this->total_discount_voucher;
	}

	public function set_total_discount_voucher($total_discount_voucher){
		$this->total_discount_voucher = $total_discount_voucher;
	}

	public function get_free_order(){
		return $this->free_order;
	}

	public function set_free_order($free_order){
		$this->free_order = $free_order;
	}

	public function get_payu_transaction(){
		return $this->payu_transaction;
	}

	public function set_payu_transaction($payu_transaction){
		$this->payu_transaction = $payu_transaction;
	}

	public function get_razorpay_order(){
		return $this->razorpay_order;
	}

	public function set_razorpay_order($razorpay_order){
		$this->razorpay_order = $razorpay_order;
	}

	public function get_paytm_checksum(){
		return $this->paytm_checksum;
	}

	public function set_paytm_checksum($paytm_checksum){
		$this->paytm_checksum = $paytm_checksum;
	}

	public function get_paytm_id_order(){
		return $this->paytm_id_order;
	}

	public function set_paytm_id_order($paytm_id_order){
		$this->paytm_id_order = $paytm_id_order;
	}

	public function get_gift_shipped(){
		return $this->gift_shipped;
	}

	public function set_gift_shipped($gift_shipped){
		$this->gift_shipped = $gift_shipped;
	}

	public function get_shipping_fee(){
		return $this->shipping_fee;
	}

	public function set_shipping_fee($shipping_fee){
		$this->shipping_fee = $shipping_fee;
	}

	public function get_cod_fee(){
		return $this->cod_fee;
	}

	public function set_cod_fee($cod_fee){
		$this->cod_fee = $cod_fee;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into `order`(id_order, id_fb_user, status, date_add, date_update, status_admin, payment_method, online_payment, payed, courier_allocation, total_amount, total_discount, total_discount_voucher, free_order, payu_transaction, razorpay_order, paytm_checksum, paytm_id_order, gift_shipped, shipping_fee, cod_fee) 
				values (:id_order, :id_fb_user, 'ORDER CREATED', now(), now(), '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '') ");

			$stmt->execute( array( 
				":id_order"			=> $this->id_order,
				":id_fb_user"		=> $this->id_fb_user
			) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|insert: ' . $e->getMessage();
		    return false;
		}
	}

	//UPDATE
	public function update_address(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update `order`
				set
					id_order_address 		= :id_order_address
				where id_order 				= :id_order ");

			$stmt->execute( array( 
				":id_order" 				=> $this->id_order,
				":id_order_address" 		=> $this->id_order_address 
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_address: ' . $e->getMessage();
		}
	}

	//UPDATE
	public function update_order($id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update `order`
				set
					date_update 	= now()
				where id_order 		= :id_order ");

			$stmt->execute( array( 
				":id_order" 		=> $id_order 
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_order: ' . $e->getMessage();
		}
	}

	//UPDATE
	public function finish_order(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update `order`
				set
					date_update 	= now(),
					date_done 		= now(),
					status 			= 'ORDER FINISHED'
				where id_order 		= :id_order ");

			$stmt->execute( array( 
				":id_order" 		=> $this->id_order 
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|finish_order: ' . $e->getMessage();
		}
	}

	public function update_date_placed($id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update `order`
				set
					date_placed 	= now()
				where id_order 		= :id_order ");

			$stmt->execute( array( 
				":id_order" 		=> $id_order 
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_date_placed: ' . $e->getMessage();
		}
	}

	public function update_status_admin($status_admin, $id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update `order`
				set
					status_admin = :status_admin
				where id_order 		= :id_order ");

			$stmt->execute( array( 
				":status_admin" 		=> $status_admin, 
				":id_order" 			=> $id_order 
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_status_admin: ' . $e->getMessage();
		    return false;
		}
	}

	public function update_gift_shipped($gift_shipped, $id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update `order`
				set
					gift_shipped 	= :gift_shipped
				where id_order 		= :id_order ");

			$stmt->execute( array( 
				":gift_shipped" 		=> $gift_shipped, 
				":id_order" 			=> $id_order 
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_gift_shipped: ' . $e->getMessage();
		    return false;
		}
	}

	public function update_payment_method_and_courier_allocation($payment_method, $courier_allocation, $id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update `order`
				set
					payment_method 		= :payment_method,
					courier_allocation 	= :courier_allocation
				where id_order 			= :id_order ");

			$stmt->execute( array( 
				":payment_method" 		=> $payment_method, 
				":courier_allocation" 	=> $courier_allocation,
				":id_order" 			=> $id_order 
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_payment_method_and_courier_allocation: ' . $e->getMessage();
		    return false;
		}
	}

	public function update_payment_method($payment_method, $id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update `order`
				set
					payment_method 		= :payment_method
				where id_order 			= :id_order ");

			$stmt->execute( array( 
				":payment_method" 		=> $payment_method,
				":id_order" 			=> $id_order 
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_payment_method: ' . $e->getMessage();
		    return false;
		}
	}

	public function update_online_payment($online_payment, $id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update `order`
				set
					online_payment 		= :online_payment
				where id_order 			= :id_order ");

			$stmt->execute( array( 
				":online_payment" 		=> $online_payment,
				":id_order" 			=> $id_order 
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_online_payment: ' . $e->getMessage();
		    return false;
		}
	}

	public function update_totals($total_amount, $total_discount, $total_discount_voucher, $id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update `order`
				set
					total_amount 			= :total_amount,
					total_discount 			= :total_discount,
					total_discount_voucher 	= :total_discount_voucher
				where id_order 				= :id_order ");

			$stmt->execute( array( 
				":total_amount" 			=> $total_amount, 
				":total_discount" 			=> $total_discount,
				":total_discount_voucher" 	=> $total_discount_voucher,
				":id_order" 				=> $id_order 
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_totals: ' . $e->getMessage();
		    return false;
		}
	}

	public function update_totals_and_discount($total_amount, $total_discount, $id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update `order`
				set
					total_amount 			= :total_amount,
					total_discount 			= :total_discount
				where id_order 				= :id_order ");

			$stmt->execute( array( 
				":total_amount" 			=> $total_amount, 
				":total_discount" 			=> $total_discount,
				":id_order" 				=> $id_order 
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_totals_and_discount: ' . $e->getMessage();
		    return false;
		}
	}

	public function update_free_order($free_order, $id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update `order`
				set
					free_order 			= :free_order
				where id_order 			= :id_order ");

			$stmt->execute( array( 
				":free_order" 			=> $free_order, 
				":id_order" 			=> $id_order 
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_totals: ' . $e->getMessage();
		    return false;
		}
	}

	public function update_payed($payed, $id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update `order`
				set
					payed = :payed
				where id_order 		= :id_order ");

			$stmt->execute( array( 
				":payed" 				=> $payed, 
				":id_order" 			=> $id_order 
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_payed: ' . $e->getMessage();
		    return false;
		}
	}

	public function update_payu_transaction($payu_transaction, $id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update `order`
				set
					payu_transaction = :payu_transaction
				where id_order 		= :id_order ");

			$stmt->execute( array( 
				":payu_transaction" 	=> $payu_transaction, 
				":id_order" 			=> $id_order 
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_payu_transaction: ' . $e->getMessage();
		    return false;
		}
	}

	public function update_razorpay_order($razorpay_order, $id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update `order`
				set
					razorpay_order = :razorpay_order
				where id_order 		= :id_order ");

			$stmt->execute( array( 
				":razorpay_order" 	=> $razorpay_order, 
				":id_order" 			=> $id_order 
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_razorpay_order: ' . $e->getMessage();
		    return false;
		}
	}

	public function update_paytm_checksum($paytm_checksum, $id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update `order`
				set
					paytm_checksum 	= :paytm_checksum
				where id_order 		= :id_order ");

			$stmt->execute( array( 
				":paytm_checksum" 		=> $paytm_checksum, 
				":id_order" 			=> $id_order 
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_paytm_checksum: ' . $e->getMessage();
		    return false;
		}
	}

	public function update_paytm_id_order($paytm_id_order, $id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update `order`
				set
					paytm_id_order 	= :paytm_id_order
				where id_order 		= :id_order ");

			$stmt->execute( array( 
				":paytm_id_order" 		=> $paytm_id_order, 
				":id_order" 			=> $id_order 
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_paytm_id_order: ' . $e->getMessage();
		    return false;
		}
	}

	public function update_fee($shipping_fee, $cod_fee, $id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update `order`
				set
					shipping_fee 	= :shipping_fee,
					cod_fee 		= :cod_fee
				where id_order 		= :id_order ");

			$stmt->execute( array( 
				":shipping_fee" 		=> $shipping_fee, 
				":cod_fee" 				=> $cod_fee, 
				":id_order" 			=> $id_order 
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_fee: ' . $e->getMessage();
		    return false;
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_order  				= $obj->id_order;
		$this->id_fb_user  				= $obj->id_fb_user;
		$this->status  					= $obj->status;
		$this->id_order_address  		= $obj->id_order_address;
		$this->date_add  				= $obj->date_add;
		$this->date_update  			= $obj->date_update;
		$this->date_done  				= $obj->date_done;
		$this->date_placed  			= $obj->date_placed;
		$this->status_admin  			= $obj->status_admin;
		$this->payment_method  			= $obj->payment_method;
		$this->online_payment  			= $obj->online_payment;
		$this->payed  					= $obj->payed;
		$this->courier_allocation 		= $obj->courier_allocation;
		$this->total_amount 			= $obj->total_amount;
		$this->total_discount 			= $obj->total_discount;
		$this->total_discount_voucher 	= $obj->total_discount_voucher;
		$this->free_order 				= $obj->free_order;
		$this->payu_transaction 		= $obj->payu_transaction;
		$this->razorpay_order 			= $obj->razorpay_order;
		$this->paytm_checksum			= $obj->paytm_checksum;
		$this->paytm_id_order			= $obj->paytm_id_order;
		$this->gift_shipped 			= $obj->gift_shipped;
		$this->shipping_fee 			= $obj->shipping_fee;
		$this->cod_fee 					= $obj->cod_fee;

	}

	public function map($id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from `order`
				where id_order 	= :id_order ");

			$stmt->execute( array( 
				":id_order" => $id_order
			) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order.php|map' . $e->getMessage();
		}
	}

	public function map_by_payu_transaction($payu_transaction){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from `order`
				where payu_transaction 	= :payu_transaction ");

			$stmt->execute( array( 
				":payu_transaction" => $payu_transaction
			) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order.php|map_by_payu_transaction' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from `order`
				where id_order 	= :id_order ");

			$stmt->execute( array( 
				":id_order"  	=> $this->id_order
			) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|delete: ' . $e->getMessage();
		    return false;
		}
	}

	public function id_order_more_than_30_days($id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from `order`
				where id_order 		= :id_order
				and status 			= 'ORDER FINISHED'
				and date_done       < NOW() - INTERVAL 30 DAY ");

			$stmt->execute( array( 
					":id_order" => $id_order
			) );

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order.php|id_order_more_than_30_days' . $e->getMessage();
		}
	}

	//EXISTS?
	public function exists_by_id_order($id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from `order`
				where id_order 	= :id_order
				and status 			= 'ORDER FINISHED' ");

			$stmt->execute( array( 
					":id_order" => $id_order
			) );

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order.php|exists_by_id_order' . $e->getMessage();
		}
	}

	public function exists($id_fb_user){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from `order`
				where id_fb_user 	= :id_fb_user
				and status 			= 'ORDER CREATED' ");

			$stmt->execute( array( 
					":id_fb_user" => $id_fb_user
			) );

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order.php|exists' . $e->getMessage();
		}
	}

	public function get_id_order_by_fb_user($id_fb_user){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select id_order 
				from `order`
				where id_fb_user = :id_fb_user
				and status = 'ORDER CREATED' ");

			$stmt->execute(array(
					":id_fb_user" => $id_fb_user
				));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->id_order;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
	    	return false;
		    echo 'ERROR: - order.php|get_id_order_by_fb_user' . $e->getMessage();
		}
	}

	public function get_created_order_with_courier_by_fb_user($id_fb_user){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select id_order 
				from `order`
				where id_fb_user = :id_fb_user
				and status = 'ORDER CREATED'
				and courier_allocation <> '' ");

			$stmt->execute(array(
					":id_fb_user" => $id_fb_user
				));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->id_order;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
	    	return false;
		    echo 'ERROR: - order.php|get_created_order_with_courier_by_fb_user' . $e->getMessage();
		}
	}

	//MAXIMUM
	public function max_id_order(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select COALESCE(MAX(id_order)+1,1) AS max 
				from `order` ");

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->max;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order_detail.php|max_id_order' . $e->getMessage();
		}
	}

	//LIST
	public function get_list($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from `order`
				where status = 'ORDER FINISHED' ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$order = new order();
				$order->mapea($reg);

				array_push($list, $order);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order.php|get_list' . $e->getMessage();
		}

		return $list;
	}

	public function get_list_per_user($id_fb_user, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from `order`
				where status = 'ORDER FINISHED'
				and id_fb_user = :id_fb_user ".$order);

			$stmt->execute(array(":id_fb_user" => $id_fb_user));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$order = new order();
				$order->mapea($reg);

				array_push($list, $order);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order.php|get_list_per_user' . $e->getMessage();
		}

		return $list;

	}

	public function get_orders_between_dates($from_date, $till_date, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from `order` 
				where date_done between :from_date and :till_date
				and status = 'ORDER FINISHED' ".$order);

			$stmt->execute( array(':from_date' => $from_date . ' 00:00:00', ':till_date' => $till_date . ' 23:59:59') );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$order = new order();
				$order->mapea($reg);

				array_push($list, $order);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order.php|get_orders_between_dates' . $e->getMessage();
		}

		return $list;

	}

	public function get_orders_with_sent_products($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from `order` 
				where (select count(*) from order_detail where id_order = `order`.id_order and sent = 'yes' and (shipped <> 'yes' or shipped is null)) > 0
				and status = 'ORDER FINISHED' ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$order = new order();
				$order->mapea($reg);

				array_push($list, $order);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order.php|get_orders_with_sent_products' . $e->getMessage();
		}

		return $list;

	}

	public function get_orders_without_sent_products($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from `order` 
				where (select count(*) from order_detail where id_order = `order`.id_order and (sent <> 'yes' or sent is null)) > 0
				and status_admin in ('ORDER SHIPPED', 'ORDER PARTIALLY SHIPPED', 'ORDER CANCELLED')
				and (gift_shipped <> 'yes' or gift_shipped is null)
				and (free_order = 'yes' or payment_method = 'Pay Online') ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$order = new order();
				$order->mapea($reg);

				array_push($list, $order);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order.php|get_orders_with_sent_products' . $e->getMessage();
		}

		return $list;

	}

	public function get_all_users_with_orders($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from `order` 
				group by id_fb_user ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$order = new order();
				$order->mapea($reg);

				array_push($list, $order);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order.php|get_all_users_with_orders' . $e->getMessage();
		}

		return $list;

	}

	public function get_orders_sent_between_dates($from_date, $till_date, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from `order` 
				where status = 'ORDER FINISHED'
				and id_order in (
					select id_order
					from order_detail
					where sent_date between :from_date and :till_date
					and (shipped <> 'yes' or shipped is null)
				) ".$order);

			$stmt->execute( array(':from_date' => $from_date . ' 00:00:00', ':till_date' => $till_date . ' 23:59:59') );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$order = new order();
				$order->mapea($reg);

				array_push($list, $order);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order.php|get_orders_between_dates' . $e->getMessage();
		}

		return $list;

	}

	public function get_grouped_orders($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from `order` 
				where status_admin = 'GROUP ORDER CREATED' ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$order = new order();
				$order->mapea($reg);

				array_push($list, $order);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order.php|get_grouped_orders' . $e->getMessage();
		}

		return $list;

	}

	public function get_users_that_used_code($code){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select *
				from `order`
				where id_order in (select id_order from order_detail where keyword_discount_code = :code) 
				order by 1 desc ");

			$stmt->execute(array(
				":code" 	=> $code
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$order = new order();
				$order->mapea($reg);

				array_push($list, $order);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - keyword_discount_code.php|get_list' . $e->getMessage();
		}

		return $list;

	}

}

?>