<?php

class voucher{
	//VARIABLES
	private $id_voucher;
	private $code;
	private $emails;
	private $till_date;
	private $value_kind;
	private $value;
	private $made_from_id_order;
	private $description;
	private $min_cart_value;
	private $visibility;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_voucher 			= "";
		$this->code 				= "";
		$this->emails 		 		= "";
		$this->till_date 			= "";
		$this->value_kind 			= "";
		$this->value 				= "";
		$this->made_from_id_order 	= "";
		$this->description 			= "";
		$this->min_cart_value 		= "";
		$this->visibility 			= "";
	}

	//GETTERS AND SETTERS
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

	public function get_emails(){
		return $this->emails;
	}

	public function set_emails($emails){
		$this->emails = $emails;
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

	public function get_made_from_id_order(){
		return $this->made_from_id_order;
	}

	public function set_made_from_id_order($made_from_id_order){
		$this->made_from_id_order = $made_from_id_order;
	}

	public function get_description(){
		return $this->description;
	}

	public function set_description($description){
		$this->description = $description;
	}

	public function get_min_cart_value(){
		return $this->min_cart_value;
	}

	public function set_min_cart_value($min_cart_value){
		$this->min_cart_value = $min_cart_value;
	}

	public function get_visibility(){
		return $this->visibility;
	}

	public function set_visibility($visibility){
		$this->visibility = $visibility;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into voucher(id_voucher, code, emails, till_date, value_kind, value, made_from_id_order, description, min_cart_value, visibility) 
				values (:id_voucher, :code, :emails, :till_date, :value_kind, :value, :made_from_id_order, :description, :min_cart_value, :visibility) ");

			$stmt->execute(array( 
				":id_voucher"			=> $this->id_voucher,
				":code"					=> $this->code,
				":emails"				=> $this->emails,
				":till_date"			=> $this->till_date,
				":value_kind"			=> $this->value_kind,
				":value"				=> $this->value,
				":made_from_id_order"	=> $this->made_from_id_order, 
				":description"			=> $this->description, 
				":min_cart_value"		=> $this->min_cart_value, 
				":visibility"			=> $this->visibility, 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - voucher.php|insert: ' . $e->getMessage();
		    return false;
		}
	}

	//update
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update voucher
				set
					emails				= :emails,
					till_date			= :till_date,
					value_kind			= :value_kind,
					value				= :value,
					made_from_id_order	= :made_from_id_order,
					description			= :description,
					min_cart_value		= :min_cart_value,
					visibility			= :visibility
				where id_voucher 	= :id_voucher ");

			$stmt->execute( array( 
				":emails"				=> $this->emails,
				":till_date"			=> $this->till_date,
				":value_kind"			=> $this->value_kind,
				":value"				=> $this->value,
				":made_from_id_order"	=> $this->made_from_id_order,
				":description"			=> $this->description,
				":min_cart_value"		=> $this->min_cart_value,
				":visibility"			=> $this->visibility,
				":id_voucher"			=> $this->id_voucher, ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - voucher.php|update: ' . $e->getMessage();
		    return false;
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_voucher  			= $obj->id_voucher;
		$this->code  				= $obj->code;
		$this->emails  				= $obj->emails;
		$this->till_date  			= $obj->till_date;
		$this->value_kind  			= $obj->value_kind;
		$this->value  				= $obj->value;
		$this->made_from_id_order  	= $obj->made_from_id_order;
		$this->description  		= $obj->description;
		$this->min_cart_value  		= $obj->min_cart_value;
		$this->visibility  			= $obj->visibility;

	}

	public function map($id_voucher){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from voucher
				where id_voucher = :id_voucher ");

			$stmt->execute( array( ":id_voucher" => $id_voucher ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - voucher.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from voucher 
				where id_voucher = :id_voucher");

			$stmt->execute( array( ":id_voucher"  => $this->id_voucher ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - voucher.php|delete: ' . $e->getMessage();
		}
	}

	public function delete_automatic_voucher($id_order){
		global $conn;

		if($id_order!='' && $id_order!='0'){

			try {

				$stmt = $conn->prepare("
					delete from voucher 
					where made_from_id_order = :made_from_id_order");

				$stmt->execute( array( ":made_from_id_order"  => $id_order ) );

				$count = $stmt->rowCount();

				if($count>0){
					return true;
				}else{
					return true;
				}

		    } catch(PDOException $e) {
			    echo 'ERROR - voucher.php|delete: ' . $e->getMessage();
			    return false;
			}

		}
	}

	//MAXIMUM
	public function max_id_voucher(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select COALESCE(MAX(id_voucher)+1,1) AS max 
				from voucher ");

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->max;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - voucher.php|max_id_voucher' . $e->getMessage();
		}
	}

	//LIST
	public function get_all($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from voucher ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$voucher = new voucher();
				$voucher->mapea($reg);

				array_push($list, $voucher);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - voucher.php|get_all' . $e->getMessage();
		}

		return $list;

	}

	public function get_all_by_search($email, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from voucher
				where emails like :email ".$order);

			$stmt->execute(array( 
				":email"  		=> "%$email%",
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$voucher = new voucher();
				$voucher->mapea($reg);

				array_push($list, $voucher);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - voucher.php|get_all' . $e->getMessage();
		}

		return $list;

	}

	public function get_all_for_user($email, $id_order, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * from voucher
				where (emails like :email or emails like '%/all/%')
				and id_voucher not in(
					select id_voucher 
				    from order_voucher 
				    where email = :email_inner
				    and (select status from `order` where id_order = order_voucher.id_order) = 'ORDER FINISHED'
				)
				and now() <= till_date 
				and (made_from_id_order is null or made_from_id_order != :id_order) ".$order);

			$stmt->execute(array( 
				":email"  		=> "%/$email/%",
				":email_inner"  => $email,
				":id_order"  	=> $id_order,
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$voucher = new voucher();
				$voucher->mapea($reg);

				array_push($list, $voucher);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - voucher.php|get_all_for_user' . $e->getMessage();
		}

		return $list;

	}

	public function get_vouchers_unused($email, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * from voucher
				where (emails like :email or emails like '%/all/%')
				and id_voucher not in(
					select id_voucher 
				    from order_voucher 
				    where email = :email_inner
				)
				and now() <= till_date  ".$order);

			$stmt->execute(array( 
				":email"  		=> "%/$email/%",
				":email_inner"  => $email,
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$voucher = new voucher();
				$voucher->mapea($reg);

				array_push($list, $voucher);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - voucher.php|get_vouchers_unused' . $e->getMessage();
		}

		return $list;

	}

}

?>






