<?php

class order_invoice{

	//VARIABLES
	private $id_order_invoice;
	private $id_order;
	private $date;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_order_invoice 			= "";
		$this->id_order 					= "";
		$this->date 						= "";
	}

	//GETTERS AND SETTERS
	public function get_id_order_invoice(){
		return $this->id_order_invoice;
	}

	public function set_id_order_invoice($id_order_invoice){
		$this->id_order_invoice = $id_order_invoice;
	}

	public function get_id_order(){
		return $this->id_order;
	}

	public function set_id_order($id_order){
		$this->id_order = $id_order;
	}

	public function get_date(){
		return $this->date;
	}

	public function set_date($date){
		$this->date = $date;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into order_invoice(id_order, date) 
				values (:id_order, :date) ");

			$stmt->execute(array( 
				":id_order" 				=> $this->id_order,
				":date" 					=> $this->date
			 ));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order_invoice.php|insert: ' . $e->getMessage();
		}
	}

	//UPDATE
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update order_invoice 
					set `date` 	 	 				= :date
					id_order 						= :id_order
				where id_order_invoice 				= :id_order_invoice
			");

			$stmt->execute(array( 
				":date"						=> $this->date,
				":id_order" 				=> $this->id_order,
				":id_order_invoice" 		=> $this->id_order_invoice,
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order_invoice.php|update: ' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_order_invoice 		= $obj->id_order_invoice;
		$this->id_order 				= $obj->id_order;
		$this->date 					= $obj->date;

	}

	public function map($id_order_invoice){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from order_invoice 
				where id_order_invoice				= :id_order_invoice
			");

			$stmt->execute(array( 
				":id_order_invoice" 	=> $id_order_invoice
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order_invoice.php|map' . $e->getMessage();
		}
	}

	public function map_by_order_and_date($id_order, $date){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from order_invoice 
				where id_order	= :id_order
				and date 		= :date
			");

			$stmt->execute(array( 
				":id_order" 	=> $id_order,
				":date" 		=> $date
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order_invoice.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from order_invoice 
				where id_order_invoice 		= :id_order_invoice,
			");

			$stmt->execute(array( 
				":id_order_invoice"  => $this->id_order_invoice
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - order_invoice.php|delete: ' . $e->getMessage();
		}
	}


	//EXIST
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from order_invoice 
				where id_order_invoice 	= :id_order_invoice
			");

			$stmt->execute(array( 
				":id_order_invoice"  => $this->id_order_invoice
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order_invoice.php|exists' . $e->getMessage();
		}
	}

	public function exists_order_and_date($id_order, $date){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from order_invoice 
				where id_order 	= :id_order
				and date 		= :date
			");

			$stmt->execute(array( 
				":id_order"  	=> $id_order,
				":date" 		=> $date,
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order_invoice.php|exists_order_and_date' . $e->getMessage();
		}
	}

	public function get_list($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from `order_invoice` ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$order_invoice = new order_invoice();
				$order_invoice->mapea($reg);

				array_push($list, $order_invoice);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - order_invoice.php|get_list' . $e->getMessage();
		}

		return $list;

	}
}

?>