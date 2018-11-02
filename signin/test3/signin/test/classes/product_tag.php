<?php

class product_tag{
	//VARIABLES
	private $id_product;
	private $id_tag;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_product 				= "";
		$this->id_tag 				= "";
	}

	//GETTERS AND SETTERS
	public function get_id_product(){
		return $this->id_product;
	}

	public function set_id_product($id_product){
		$this->id_product = $id_product;
	}

	public function get_id_tag(){
		return $this->id_tag;
	}

	public function set_id_tag($id_tag){
		$this->id_tag = $id_tag;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into product_tag(id_product, id_tag) 
				values (:id_product, :id_tag) ");

			$stmt->execute( array( 
				":id_product"	=> $this->id_product,
				":id_tag"		=> $this->id_tag ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - product_tag.php|insert: ' . $e->getMessage();
		    return false;
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_product  	= $obj->id_product;
		$this->id_tag  		= $obj->id_tag;

	}

	public function map($id_product, $id_tag){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from product_tag
				where id_product 	= :id_product
				and id_tag 			= :id_tag ");

			$stmt->execute( array( 
				":id_product" 	=> $id_product,
				":id_tag" 		=> $id_tag ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product_tag.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from product_tag 
				where id_product 		= :id_product
				and id_tag 				= :id_tag ");

			$stmt->execute( array( 
				":id_product"  	=> $this->id_product,
				":id_tag" 		=> $this->id_tag ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - product_tag.php|delete: ' . $e->getMessage();
		    return false;
		}
	}

	public function delete_by_product($id_product){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from product_tag 
				where id_product 		= :id_product ");

			$stmt->execute(array( 
				":id_product"  	=> $id_product
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - product_tag.php|delete_from_product: ' . $e->getMessage();
		    return false;
		}
	}

	//EXISTS?
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from product_tag 
				where id_product 	= :id_product
				and id_tag 			= :id_tag ");

			$stmt->execute( array( 
					":id_product" 	=> $this->id_product,
					":id_tag" 		=> $this->id_tag ) );

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product_tag.php|exists' . $e->getMessage();
		}
	}

	//LIST
	public function get_list($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from product_tag ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$product_tag = new product_tag();
				$product_tag->mapea($reg);

				array_push($list, $product_tag);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product_tag.php|get_list' . $e->getMessage();
		}

		return $list;

	}

	public function get_list_by_product($id_product, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from product_tag
				where id_product = :id_product ".$order);

			$stmt->execute(array( 
					":id_product" 	=> $id_product
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$product_tag = new product_tag();
				$product_tag->mapea($reg);

				array_push($list, $product_tag);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product_tag.php|get_list_by_product' . $e->getMessage();
		}

		return $list;

	}

}

?>






