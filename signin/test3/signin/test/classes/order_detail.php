<?php

class order_detail{
	//VARIABLES
	private $id_order;
	private $id_order_detail;
	private $id_product;
	private $id_product_prestashop;
	private $color;
	private $size;
	private $qty;
	private $sent;
	private $sent_date;
	private $shipped;
	private $returned;
	private $refunded;
	private $POmade;
	private $return_request_from_customer;
	private $amount;
	private $discount;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_order 						= "";
		$this->id_order_detail 					= "";
		$this->id_product 						= "";
		$this->id_product_prestashop 			= "";
		$this->color 							= "";
		$this->size 							= "";
		$this->qty 								= "";
		$this->sent 							= "";
		$this->sent_date 						= "";
		$this->shipped 							= "";
		$this->returned 						= "";
		$this->refunded 						= "";
		$this->POmade 							= "";
		$this->return_request_from_customer 	= "";
		$this->amount 							= "";
		$this->discount 						= "";
	}

	//GETTERS AND SETTERS
	public function get_id_order(){
		return $this->id_order;
	}

	public function set_id_order($id_order){
		$this->id_order = $id_order;
	}

	public function get_id_order_detail(){
		return $this->id_order_detail;
	}

	public function set_id_order_detail($id_order_detail){
		$this->id_order_detail = $id_order_detail;
	}

	public function get_id_product(){
		return $this->id_product;
	}

	public function set_id_product($id_product){
		$this->id_product = $id_product;
	}

	public function get_id_product_prestashop(){
		return $this->id_product_prestashop;
	}

	public function set_id_product_prestashop($id_product_prestashop){
		$this->id_product_prestashop = $id_product_prestashop;
	}

	public function get_color(){
		return $this->color;
	}

	public function set_color($color){
		$this->color = $color;
	}

	public function get_size(){
		return $this->size;
	}

	public function set_size($size){
		$this->size = $size;
	}

	public function get_qty(){
		return $this->qty;
	}

	public function set_qty($qty){
		$this->qty = $qty;
	}

	public function get_sent(){
		return $this->sent;
	}

	public function set_sent($sent){
		$this->sent = $sent;
	}

	public function get_sent_date(){
		return $this->sent_date;
	}

	public function set_sent_date($sent_date){
		$this->sent_date = $sent_date;
	}

	public function get_shipped(){
		return $this->shipped;
	}

	public function set_shipped($shipped){
		$this->shipped = $shipped;
	}

	public function get_returned(){
		return $this->returned;
	}

	public function set_returned($returned){
		$this->returned = $returned;
	}

	public function get_refunded(){
		return $this->refunded;
	}

	public function set_refunded($refunded){
		$this->refunded = $refunded;
	}

	public function get_POmade(){
		return $this->POmade;
	}

	public function set_POmade($POmade){
		$this->POmade = $POmade;
	}

	public function get_return_request_from_customer(){
		return $this->return_request_from_customer;
	}

	public function set_return_request_from_customer($return_request_from_customer){
		$this->return_request_from_customer = $return_request_from_customer;
	}

	public function get_amount(){
		return $this->amount;
	}

	public function set_amount($amount){
		$this->amount = $amount;
	}

	public function get_discount(){
		return $this->discount;
	}

	public function set_discount($discount){
		$this->discount = $discount;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into order_detail(id_order, id_order_detail, id_product, id_product_prestashop, color, size, qty) 
				values (:id_order, :id_order_detail, :id_product, :id_product_prestashop, :color, :size, :qty) ");

			$stmt->execute( array( 
				":id_order"					=> $this->id_order,
				":id_order_detail"			=> $this->id_order_detail,
				":id_product"				=> $this->id_product,
				":id_product_prestashop"	=> $this->id_product_prestashop,
				":color"					=> $this->color,
				":size"						=> $this->size,
				":qty"						=> $this->qty
			) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order_detail.php|insert: ' . $e->getMessage();
		    return false;
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_order  						= $obj->id_order;
		$this->id_order_detail  				= $obj->id_order_detail;
		$this->id_product  						= $obj->id_product;
		$this->id_product_prestashop  			= $obj->id_product_prestashop;
		$this->color  							= $obj->color;
		$this->size  							= $obj->size;
		$this->qty  							= $obj->qty;
		$this->sent  							= $obj->sent;
		$this->sent_date  						= $obj->sent_date;
		$this->shipped  						= $obj->shipped;
		$this->returned  						= $obj->returned;
		$this->refunded  						= $obj->refunded;
		$this->POmade  							= $obj->POmade;
		$this->return_request_from_customer  	= $obj->return_request_from_customer;
		$this->amount  							= $obj->amount;
		$this->discount  						= $obj->discount;

	}

	public function map($id_order, $id_order_detail){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from order_detail
				where id_order 			= :id_order
				and id_order_detail 	= :id_order_detail ");

			$stmt->execute( array( 
				":id_order" 		=> $id_order,
				":id_order_detail" 	=> $id_order_detail
			) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order_detail.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from order_detail 
				where id_order 		= :id_order
				and id_order_detail 	= :id_order_detail ");

			$stmt->execute( array( 
				":id_order"  			=> $this->id_order,
				":id_order_detail"  	=> $this->id_order_detail
			) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order_detail.php|delete: ' . $e->getMessage();
		    return false;
		}
	}

	public function delete_by_id_order(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from order_detail 
				where id_order 		= :id_order ");

			$stmt->execute( array( 
				":id_order"  			=> $this->id_order
			) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order_detail.php|delete_by_id_order: ' . $e->getMessage();
		    return false;
		}
	}


	//EXISTS?
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from order_detail 
				where id_order 				= :id_order
				and id_product 				= :id_product
				and id_product_prestashop 	= :id_product_prestashop
				and color 					= :color
				and size 					= :size ");

			$stmt->execute(array(
					":id_order"  				=> $this->id_order,
					":id_product"  				=> $this->id_product,
					":id_product_prestashop"  	=> $this->id_product_prestashop,
					":color"  					=> $this->color,
					":size"  					=> $this->size
				));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->id_order_detail;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order_detail.php|exists' . $e->getMessage();
		}
	}

	public function exists_product_prestashop_in_order($id_order, $id_product_prestashop){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from order_detail 
				where id_order 				= :id_order
				and id_product_prestashop 	= :id_product_prestashop ");

			$stmt->execute(array(
					":id_order"  				=> $id_order,
					":id_product_prestashop"  	=> $id_product_prestashop
				));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - exists_product_prestashop_in_order.php|exists' . $e->getMessage();
		}
	}

	//UPDATE
	public function update_qty_using_current(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update order_detail
				set
					qty 			= qty + :qty
				where id_order 		= :id_order
				and id_order_detail	= :id_order_detail ");

			$stmt->execute( array( 
				":qty" 					=> $this->qty,
				":id_order" 			=> $this->id_order,
				":id_order_detail" 		=> $this->id_order_detail 
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_qty_using_current: ' . $e->getMessage();
		}
	}

	//UPDATE
	public function update_qty(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update order_detail
				set
					qty 			= :qty
				where id_order 		= :id_order
				and id_order_detail	= :id_order_detail ");

			$stmt->execute( array( 
				":qty" 					=> $this->qty,
				":id_order" 			=> $this->id_order,
				":id_order_detail" 		=> $this->id_order_detail 
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_qty: ' . $e->getMessage();
		}
	}

	public function update_sent(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update order_detail
				set
					sent 			= :sent,
					sent_date 		= now()
				where id_order 		= :id_order
				and id_order_detail	= :id_order_detail ");

			$stmt->execute( array( 
				":sent" 				=> $this->sent,
				":id_order" 			=> $this->id_order,
				":id_order_detail" 		=> $this->id_order_detail 
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_sent: ' . $e->getMessage();
		    return false;
		}
	}

	public function update_shipped(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update order_detail
				set
					shipped 		= :shipped
				where id_order 		= :id_order
				and sent 			= 'yes' ");

			$stmt->execute( array( 
				":shipped" 				=> $this->shipped,
				":id_order" 			=> $this->id_order
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_shipped: ' . $e->getMessage();
		    return false;
		}
	}

	public function update_returned(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update order_detail
				set
					returned 		= :returned
				where id_order 		= :id_order
				and id_order_detail = :id_order_detail ");

			$stmt->execute( array( 
				":returned" 				=> $this->returned,
				":id_order" 				=> $this->id_order,
				":id_order_detail" 			=> $this->id_order_detail
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_returned: ' . $e->getMessage();
		    return false;
		}
	}

	public function update_refunded(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update order_detail
				set
					refunded 		= :refunded
				where id_order 		= :id_order
				and id_order_detail = :id_order_detail ");

			$stmt->execute( array( 
				":refunded" 				=> $this->refunded,
				":id_order" 				=> $this->id_order,
				":id_order_detail" 			=> $this->id_order_detail
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_refunded: ' . $e->getMessage();
		    return false;
		}
	}

	public function update_POmade(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update order_detail
				set
					POmade 		= :POmade
				where id_order 		= :id_order
				and id_order_detail = :id_order_detail ");

			$stmt->execute( array( 
				":POmade" 					=> $this->POmade,
				":id_order" 				=> $this->id_order,
				":id_order_detail" 			=> $this->id_order_detail
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_POmade: ' . $e->getMessage();
		    return false;
		}
	}

	public function update_return_request_from_customer(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update order_detail
				set
					return_request_from_customer 	= :return_request_from_customer
				where id_order 		= :id_order
				and id_order_detail = :id_order_detail ");

			$stmt->execute( array( 
				":return_request_from_customer" 				=> $this->return_request_from_customer,
				":id_order" 				=> $this->id_order,
				":id_order_detail" 			=> $this->id_order_detail
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_return_request_from_customer: ' . $e->getMessage();
		    return false;
		}
	}

	public function update_amount_and_discount($id_order, $id_order_detail, $amount, $discount){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update order_detail
				set
					amount 		= :amount,
					discount 	= :discount
				where id_order 		= :id_order
				and id_order_detail = :id_order_detail ");

			$stmt->execute( array( 
				":amount" 					=> $amount,
				":discount" 				=> $discount,
				":id_order" 				=> $id_order,
				":id_order_detail" 			=> $id_order_detail
			) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order.php|update_POmade: ' . $e->getMessage();
		    return false;
		}
	}

	//MAXIMUM
	public function max_id_order_detail($id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select COALESCE(MAX(id_order_detail)+1,1) AS max 
				from order_detail
				where id_order = :id_order ");

			$stmt->execute(array(
					":id_order" => $id_order
				));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->max;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order_detail.php|max_id_order_detail' . $e->getMessage();
		}
	}

	//LIST
	public function get_list($id_order, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from order_detail
				where id_order = :id_order ".$order);

			$stmt->execute(array( ":id_order" => $id_order ));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$order_detail = new order_detail();
				$order_detail->mapea($reg);

				array_push($list, $order_detail);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order_detail.php|get_list' . $e->getMessage();
		}

		return $list;

	}

	public function get_all_from_orders($id_orders, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from order_detail 
				where id_order in ($id_orders) ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$order_detail = new order_detail();
				$order_detail->mapea($reg);

				array_push($list, $order_detail);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order_detail.php|get_all_from_orders' . $e->getMessage();
		}

		return $list;

	}

	public function get_details_sent($id_order, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from order_detail
				where id_order = :id_order
				and sent = 'yes'
				and (shipped <> 'yes' or shipped is null) ".$order);

			$stmt->execute(array( ":id_order" => $id_order ));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$order_detail = new order_detail();
				$order_detail->mapea($reg);

				array_push($list, $order_detail);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order_detail.php|get_details_sent' . $e->getMessage();
		}

		return $list;

	}

	public function get_details_not_sent($id_order, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from order_detail
				where id_order = :id_order
				and (sent <> 'yes' or sent is null) ".$order);

			$stmt->execute(array( ":id_order" => $id_order ));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$order_detail = new order_detail();
				$order_detail->mapea($reg);

				array_push($list, $order_detail);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order_detail.php|get_details_sent' . $e->getMessage();
		}

		return $list;

	}

	public function get_return_requests($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from order_detail
				where return_request_from_customer = 'request' ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$order_detail = new order_detail();
				$order_detail->mapea($reg);

				array_push($list, $order_detail);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order_detail.php|get_return_requests' . $e->getMessage();
		}

		return $list;

	}

	public function get_orders_by_product($id_product_prestashop, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from order_detail
				where id_product_prestashop = '$id_product_prestashop'

				/* and (refunded is null or refunded = '') */
				/* and (select status_admin from `order` where id_order = order_detail.id_order) != 'ORDER CANCELLED' */
				
				and (select status_admin from `order` where id_order = order_detail.id_order) in ('', 'ORDER PARTIALLY SHIPPED')
				and (select status from `order` where id_order = order_detail.id_order) = 'ORDER FINISHED'
				group by id_order ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$order_detail = new order_detail();
				$order_detail->mapea($reg);

				array_push($list, $order_detail);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order_detail.php|get_orders_by_product' . $e->getMessage();
		}

		return $list;

	}

	public function get_details_sent_in_date($from_date, $till_date, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select *
				from order_detail
				where sent_date between :from_date and :till_date
				".$order);

			$stmt->execute(array(
				':from_date' 	=> $from_date . ' 00:00:00', 
				':till_date' 	=> $till_date . ' 23:59:59',
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$order_detail = new order_detail();
				$order_detail->mapea($reg);

				array_push($list, $order_detail);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order_detail.php|get_details_sent_in_date' . $e->getMessage();
		}

		return $list;

	}

	public function get_details_sent_in_date_and_order($from_date, $till_date, $id_order, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select *
				from order_detail
				where id_order = :id_order
				and sent_date between :from_date and :till_date
				".$order);

			$stmt->execute(array(
				':id_order' 	=> $id_order,
				':from_date' 	=> $from_date . ' 00:00:00', 
				':till_date' 	=> $till_date . ' 23:59:59',
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$order_detail = new order_detail();
				$order_detail->mapea($reg);

				array_push($list, $order_detail);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order_detail.php|get_details_sent' . $e->getMessage();
		}

		return $list;

	}

}

?>