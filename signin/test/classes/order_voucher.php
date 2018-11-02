<?php

class order_voucher{
	//VARIABLES
	private $id_order;
	private $id_voucher;
	private $code;
	private $email;
	private $till_date;
	private $value_kind;
	private $value;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_order 		= "";
		$this->id_voucher 		= "";
		$this->code 			= "";
		$this->email 			= "";
		$this->till_date 		= "";
		$this->value_kind 		= "";
		$this->value 			= "";
	}

	//GETTERS AND SETTERS
	public function get_id_order(){
		return $this->id_order;
	}

	public function set_id_order($id_order){
		$this->id_order = $id_order;
	}

	public function get_id_voucher(){
		return $this->id_voucher;
	}

	public function set_id_voucher($id_voucher){
		$this->id_voucher = $id_voucher;
	}

	public function get_code(){
		return $this->code;
	}

	public function set_code($code){
		$this->code = $code;
	}

	public function get_email(){
		return $this->email;
	}

	public function set_email($email){
		$this->email = $email;
	}

	public function get_till_date(){
		return $this->till_date;
	}

	public function set_till_date($till_date){
		$this->till_date = $till_date;
	}

	public function get_value_kind(){
		return $this->value_kind;
	}

	public function set_value_kind($value_kind){
		$this->value_kind = $value_kind;
	}

	public function get_value(){
		return $this->value;
	}

	public function set_value($value){
		$this->value = $value;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into order_voucher(id_order, id_voucher, code, email, till_date, value_kind, value) 
				values (:id_order, :id_voucher, :code, :email, :till_date, :value_kind, :value) ");

			$stmt->execute( array( 
				":id_order"				=> $this->id_order,
				":id_voucher"			=> $this->id_voucher,
				":code"					=> $this->code,
				":email"				=> $this->email,
				":till_date"			=> $this->till_date,
				":value_kind"			=> $this->value_kind,
				":value"				=> $this->value
			) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order_voucher.php|insert: ' . $e->getMessage();
		    return false;
		}
	}

	//UPDATE
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update order_voucher
				set
					code 					= :code,
					email 					= :email,
					till_date  				= :till_date,
					value_kind  			= :value_kind, 
					value  					= :value
				where id_voucher 			= :id_voucher
				and id_order 				= :id_order");

			$stmt->execute( array( 
				":code"						=> $this->code,
				":email"					=> $this->email,
				":till_date"				=> $this->till_date,
				":value_kind"				=> $this->value_kind,
				":value"					=> $this->value,
				":id_voucher"				=> $this->id_voucher,
				":id_order"					=> $this->id_order) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order_voucher.php|update: ' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_order  			= $obj->id_order;
		$this->id_voucher  			= $obj->id_voucher;
		$this->code  				= $obj->code;
		$this->email  				= $obj->email;
		$this->till_date  			= $obj->till_date;
		$this->value_kind  			= $obj->value_kind;
		$this->value  				= $obj->value;

	}

	public function map($id_order, $id_voucher){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from order_voucher
				where id_order 		= :id_order
				and id_voucher 		= :id_voucher ");

			$stmt->execute( array( 
				":id_order" 		=> $id_order,
				":id_voucher" 		=> $id_voucher
			) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order_voucher.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from order_voucher 
				where id_order 		= :id_order
				and id_voucher 		= :id_voucher ");

			$stmt->execute( array( 
				":id_order" 		=> $this->id_order,
				":id_voucher" 		=> $this->id_voucher
			) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order_voucher.php|delete: ' . $e->getMessage();
		    return false;
		}
	}

	public function delete_expired_vouchers($id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from order_voucher 
				where id_order 		= :id_order
				and now() > (select till_date from voucher where id_voucher = order_voucher.id_voucher)  ");

			$stmt->execute( array( 
				":id_order" 		=> $id_order
			) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order_voucher.php|delete_expired_vouchers: ' . $e->getMessage();
		    return false;
		}
	}

	//EXISTS?
	public function exists($id_order, $id_voucher){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from `order_voucher`
				where id_order 		= :id_order
				and id_voucher 		= :id_voucher ");

			$stmt->execute( array( 
					":id_order" 		=> $id_order,
					":id_voucher" 		=> $id_voucher
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

	public function delete_all_from_order($id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from order_voucher 
				where id_order 			= :id_order ");

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
		    echo 'ERROR - order_voucher.php|delete_all_from_order: ' . $e->getMessage();
		    return false;
		}	
	}

	//LIST
	public function get_list($id_order, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from order_voucher
				where id_order = :id_order ".$order);

			$stmt->execute(array(
				":id_order" => $id_order
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$order_voucher = new order_voucher();
				$order_voucher->mapea($reg);

				array_push($list, $order_voucher);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order_voucher.php|get_list' . $e->getMessage();
		}

		return $list;

	}

	//GET TOTAL VOUCHER DISCOUNT
	public function get_vouchers_discount($current_id_order, $total_ammount){
		$order_voucher = new order_voucher();

		$order_vouchers = $order_voucher->get_list($current_id_order, "");
		$discount 		= 0;
		foreach ($order_vouchers as $row) {
			if($row->get_value_kind() == 'percentage'){
				$discount += round( (float)$row->get_value() * ($total_ammount/100), 2);
			}else if($row->get_value_kind() == 'amount'){
				$discount += round( (float)$row->get_value(), 2);
			}
		}

		if($discount > $total_ammount){
			$discount = $total_ammount;
		}

		return $discount;
	}

	public function get_vouchers_discount_real($current_id_order, $total_ammount){
		$order_voucher = new order_voucher();

		$order_vouchers = $order_voucher->get_list($current_id_order, "");
		$discount 		= 0;
		foreach ($order_vouchers as $row) {
			if($row->get_value_kind() == 'percentage'){
				$discount += round( (float)$row->get_value() * ($total_ammount/100), 2);
			}else if($row->get_value_kind() == 'amount'){
				$discount += round( (float)$row->get_value(), 2);
			}
		}

		return $discount;
	}

}

?>






