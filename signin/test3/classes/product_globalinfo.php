<?php

class product_globalinfo{
	//VARIABLES
	private $id_product;
	private $vendor_price;
	private $vendor_link;
	private $status;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_product 			= "";
		$this->vendor_price 		= "";
		$this->vendor_link 			= "";
		$this->status 				= "";
	}

	//GETTERS AND SETTERS
	public function get_id_product(){
		return $this->id_product;
	}

	public function set_id_product($id_product){
		$this->id_product = $id_product;
	}

	public function get_vendor_price(){
		return $this->vendor_price;
	}

	public function set_vendor_price($vendor_price){
		$this->vendor_price = $vendor_price;
	}

	public function get_vendor_link(){
		return $this->vendor_link;
	}

	public function set_vendor_link($vendor_link){
		$this->vendor_link = $vendor_link;
	}

	public function get_status(){
		return $this->status;
	}

	public function set_status($status){
		$this->status = $status;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into product_globalinfo(id_product, vendor_price, vendor_link, status) 
				values (:id_product, :vendor_price, :vendor_link, :status) ");

			$stmt->execute( array( 
				":id_product"			=> $this->id_product,
				":vendor_price"			=> $this->vendor_price,
				":vendor_link"			=> $this->vendor_link,
				":status"				=> $this->status,
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - product_globalinfo.php|insert: ' . $e->getMessage();
		}
	}

	//UPDATE
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update product_globalinfo
				set
					vendor_price 		= :vendor_price,
					vendor_link 		= :vendor_link,
					status 				= :status
				where id_product 		= :id_product ");

			$stmt->execute( array( 
				":vendor_price"			=> $this->vendor_price,
				":vendor_link"			=> $this->vendor_link,
				":status"				=> $this->status,
				":id_product" 			=> $this->id_product ) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - product_globalinfo.php|update: ' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_product  			= $obj->id_product;
		$this->vendor_price  		= $obj->vendor_price;
		$this->vendor_link  		= $obj->vendor_link;
		$this->status 				= $obj->status;

	}

	public function map($id_product){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from product_globalinfo
				where id_product = :id_product ");

			$stmt->execute(array( ":id_product" => $id_product ));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product_globalinfo.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from product_globalinfo
				where id_product = :id_product");

			$stmt->execute(array( ":id_product"  => $this->id_product ));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - product_globalinfo.php|delete: ' . $e->getMessage();
		}
	}

	//EXISTS?
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from product_globalinfo 
				where id_product = :id_product ");

			$stmt->execute(array( ":id_product" => $this->id_product ));

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product_globalinfo.php|exists' . $e->getMessage();
		}
	}

	//LIST
	public function get_list($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from product_globalinfo ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$product_globalinfo = new product_globalinfo();
				$product_globalinfo->mapea($reg);

				array_push($list, $product_globalinfo);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product_globalinfo.php|get_list' . $e->getMessage();
		}

		return $list;

	}

}

?>