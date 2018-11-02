<?php

class purchase_order{
	//VARIABLES
	private $id_purchase_order;
	private $id_vendor;
	private $url;
	private $date;
	private $name;
	private $status;
	private $awb_number;
	private $awb_status;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_purchase_order 	= "";
		$this->id_vendor 			= "";
		$this->url 		 			= "";
		$this->date 				= "";
		$this->name 				= "";
		$this->status 				= "";
		$this->awb_number 			= "";
		$this->awb_status 			= "";
	}

	//GETTERS AND SETTERS
	public function get_id_purchase_order(){
		return $this->id_purchase_order;
	}

	public function set_id_purchase_order($id_purchase_order){
		$this->id_purchase_order = $id_purchase_order;
	}

	public function get_id_vendor(){
		return $this->id_vendor;
	}

	public function set_id_vendor($id_vendor){
		$this->id_vendor = $id_vendor;
	}

	public function get_url(){
		return $this->url;
	}

	public function set_url($url){
		$this->url = $url;
	}

	public function get_date(){
		return $this->date;
	}

	public function set_date($date){
		$this->date = $date;
	}

	public function get_name(){
		return $this->name;
	}

	public function set_name($name){
		$this->name = $name;
	}

	public function get_status(){
		return $this->status;
	}

	public function set_status($status){
		$this->status = $status;
	}

	public function get_awb_number(){
		return $this->awb_number;
	}

	public function set_awb_number($awb_number){
		$this->awb_number = $awb_number;
	}

	public function get_awb_status(){
		return $this->awb_status;
	}

	public function set_awb_status($awb_status){
		$this->awb_status = $awb_status;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into purchase_order(id_purchase_order, id_vendor, url, date, name, awb_number, awb_status) 
				values (:id_purchase_order, :id_vendor, :url, now(), :name, '' , '') ");

			$stmt->execute(array( 
				":id_purchase_order"	=> $this->id_purchase_order,
				":id_vendor"			=> $this->id_vendor,
				":url"					=> $this->url,
				":name"					=> $this->name
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - purchase_order.php|insert: ' . $e->getMessage();
		    return false;
		}
	}

	//UPDATE
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update purchase_order
				set
					id_vendor				= :id_vendor,
					url						= :url,
					name					= :name
				where id_purchase_order 	= :id_purchase_order ");

			$stmt->execute( array( 
				":id_vendor"			=> $this->id_vendor,
				":url"					=> $this->url,
				":name"					=> $this->name,
				":id_purchase_order"	=> $this->id_purchase_order ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - purchase_order.php|update: ' . $e->getMessage();
		    return false;
		}
	}

	public function update_status($status, $id_purchase_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update purchase_order
				set
					status		= :status
				where id_purchase_order 	= :id_purchase_order ");

			$stmt->execute( array( 
				":status"				=> $status,
				":id_purchase_order"	=> $id_purchase_order ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - purchase_order.php|update_status: ' . $e->getMessage();
		    return false;
		}
	}

	public function update_awb_details($awb_number, $awb_status, $id_purchase_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update purchase_order
				set
					awb_number				= :awb_number,
					awb_status				= :awb_status
				where id_purchase_order 	= :id_purchase_order ");

			$stmt->execute( array( 
				":awb_number"				=> $awb_number,
				":awb_status"				=> $awb_status,
				":id_purchase_order"		=> $id_purchase_order ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - purchase_order.php|update_awb: ' . $e->getMessage();
		    return false;
		}
	}

	public function edit_awb_details($old_awb_number, $awb_number, $awb_status){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update purchase_order
				set
					awb_number				= :awb_number,
					awb_status				= :awb_status
				where awb_number 			= :old_awb_number ");

			$stmt->execute( array( 
				":awb_number"				=> $awb_number,
				":awb_status"				=> $awb_status,
				":old_awb_number"			=> $old_awb_number ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - purchase_order.php|update_awb: ' . $e->getMessage();
		    return false;
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_purchase_order  	= $obj->id_purchase_order;
		$this->id_vendor  			= $obj->id_vendor;
		$this->url  				= $obj->url;
		$this->date  				= $obj->date;
		$this->name  				= $obj->name;
		$this->status  				= $obj->status;
		$this->awb_number  			= $obj->awb_number;
		$this->awb_status  			= $obj->awb_status;

	}

	public function map($id_purchase_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from purchase_order
				where id_purchase_order = :id_purchase_order ");

			$stmt->execute( array( ":id_purchase_order" => $id_purchase_order ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - purchase_order.php|map' . $e->getMessage();
		}
	}

	public function map_by_order_and_product($id_order, $id_order_detail){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from purchase_order
				where id_purchase_order in (
					select id_purchase_order
					from purchase_order_row 
					where id_order_details like '%-$id_order@@$id_order_detail-%'
				)
				order by id_purchase_order desc
				limit 1 ");

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - purchase_order.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from purchase_order 
				where id_purchase_order = :id_purchase_order");

			$stmt->execute( array( ":id_purchase_order"  => $this->id_purchase_order ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - purchase_order.php|delete: ' . $e->getMessage();
		}
	}

	//MAXIMUM
	public function max_id_purchase_order(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select COALESCE(MAX(id_purchase_order)+1,1) AS max 
				from purchase_order ");

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->max;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - purchase_order.php|max_id_purchase_order' . $e->getMessage();
		}
	}

	//LIST
	public function get_all_from_vendor($id_vendor, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from purchase_order where id_vendor = :id_vendor ".$order);

			$stmt->execute( array( ":id_vendor" => $id_vendor ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$purchase_order = new purchase_order();
				$purchase_order->mapea($reg);

				array_push($list, $purchase_order);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - purchase_order.php|get_all' . $e->getMessage();
		}

		return $list;

	}

	//LIST
	public function get_all_between_dates($from_date, $till_date, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from purchase_order where date >= :from_date and date <= :till_date ".$order);

			$stmt->execute( array( ":from_date" => $from_date, ":till_date" => $till_date ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$purchase_order = new purchase_order();
				$purchase_order->mapea($reg);

				array_push($list, $purchase_order);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - purchase_order.php|get_all_between_dates' . $e->getMessage();
		}

		return $list;

	}

	//LIST
	public function get_all($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from purchase_order ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$purchase_order = new purchase_order();
				$purchase_order->mapea($reg);

				array_push($list, $purchase_order);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - purchase_order.php|get_all' . $e->getMessage();
		}

		return $list;

	}

}

?>






