<?php

class product_lang{
	//VARIABLES
	private $id_product;
	private $name;
	private $description;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_product 		= "";
		$this->name 			= "";
		$this->description 		= "";
	}

	//GETTERS AND SETTERS
	public function get_id_product(){
		return $this->id_product;
	}

	public function set_id_product($id_product){
		$this->id_product = $id_product;
	}

	public function get_name(){
		return $this->name;
	}

	public function set_name($name){
		$this->name = $name;
	}

	public function get_description(){
		return $this->description;
	}

	public function set_description($description){
		$this->description = $description;
	}

	//SELECT
	public function mapea($obj){

		$this->id_product  		= $obj->id_product;
		$this->name  			= $obj->name;
		$this->description  	= $obj->description;

	}

	public function map($id_product){
		global $conn_prestashop;

		try {

			$stmt = $conn_prestashop->prepare("
				select * 
				from miracas_product_lang
				where id_product = :id_product ");

			$stmt->execute( array( ":id_product" => $id_product ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product_lang.php|map' . $e->getMessage();
		}
	}

	//EXISTS?
	public function exists(){
		global $conn_prestashop;

		try {

			$stmt = $conn_prestashop->prepare("
				select * 
				from miracas_product_lang 
				where id_product = :id_product ");

			$stmt->execute( array( ":id_product" => $this->id_product ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product_lang.php|exists' . $e->getMessage();
		}
	}

	//LIST
	public function get_all($order){

		global $conn_prestashop;

		$list = array();

		try {

			$stmt = $conn_prestashop->prepare("
				select * 
				from miracas_product_lang ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$product_lang = new product_lang();
				$product_lang->mapea($reg);

				array_push($list, $product_lang);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product_lang.php|get_all' . $e->getMessage();
		}

		return $list;

	}

	//SEARCH
	public function search($name, $order){

		global $conn_prestashop;

		$list = array();

		try {

			$stmt = $conn_prestashop->prepare("
				select * 
				from miracas_product_lang
				where upper(name) like upper('%$name%') ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$product_lang = new product_lang();
				$product_lang->mapea($reg);

				array_push($list, $product_lang);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product_lang.php|get_all' . $e->getMessage();
		}

		return $list;

	}

	//PRODUCT NAME
	public function get_product_name($id_product){
		global $conn_prestashop;

		try {

			$stmt = $conn_prestashop->prepare("
				select * 
				from miracas_product_lang 
				where id_product = :id_product and
				id_lang = '1' ");

			$stmt->execute( array(":id_product" => $id_product) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->name;
			}else{
				return "";
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product_lang.php|get_product_name' . $e->getMessage();
		}
	}

	//REFERENCE
	public function get_date_add($id_product){
		global $conn_prestashop;

		try {

			$stmt = $conn_prestashop->prepare("
				select * 
				from miracas_product 
				where id_product = :id_product");

			$stmt->execute( array(":id_product" => $id_product) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->date_add;
			}else{
				return "";
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product_lang.php|get_date_add' . $e->getMessage();
		}
	}

	//REFERENCE
	public function get_reference($id_product){
		global $conn_prestashop;

		try {

			$stmt = $conn_prestashop->prepare("
				select * 
				from miracas_product 
				where id_product = :id_product");

			$stmt->execute( array(":id_product" => $id_product) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->reference;
			}else{
				return "";
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product_lang.php|get_reference' . $e->getMessage();
		}
	}

	//SUPPLIER REFERENCE
	public function get_supplier_reference($id_product){
		global $conn_prestashop;

		try {

			$stmt = $conn_prestashop->prepare("
				select * 
				from miracas_product 
				where id_product = :id_product");

			$stmt->execute( array(":id_product" => $id_product) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->supplier_reference;
			}else{
				return "";
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product_lang.php|get_supplier_reference' . $e->getMessage();
		}
	}

	//PRODUCT PRICE
	public function get_product_price($id_product){
		global $conn_prestashop;

		try {

			$stmt = $conn_prestashop->prepare("
				select * 
				from miracas_product 
				where id_product = :id_product");

			$stmt->execute( array(":id_product" => $id_product) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->price;
			}else{
				return "";
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product_lang.php|get_product_price' . $e->getMessage();
		}
	}

	//PRODUCT ACTIVE
	public function get_product_active($id_product){
		global $conn_prestashop;

		try {

			$stmt = $conn_prestashop->prepare("
				select * 
				from miracas_product 
				where id_product = :id_product");

			$stmt->execute( array(":id_product" => $id_product) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->active;
			}else{
				return "";
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product_lang.php|get_product_active' . $e->getMessage();
		}
	}

}

?>






