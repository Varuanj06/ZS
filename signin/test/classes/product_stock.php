<?php

class product_stock{
	//VARIABLES
	private $id_product;
	private $id_product_stock;
	private $color;
	private $size;
	private $stock;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_product 			= "";
		$this->id_product_stock 	= "";
		$this->color 				= "";
		$this->size 		 		= "";
		$this->stock 				= "";
	}

	//GETTERS AND SETTERS
	public function get_id_product(){
		return $this->id_product;
	}

	public function set_id_product($id_product){
		$this->id_product = $id_product;
	}

	public function get_id_product_stock(){
		return $this->id_product_stock;
	}

	public function set_id_product_stock($id_product_stock){
		$this->id_product_stock = $id_product_stock;
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

	public function get_stock(){
		return $this->stock;
	}

	public function set_stock($stock){
		$this->stock = $stock;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into product_stock(id_product, id_product_stock, color, size, stock) 
				values (:id_product, :id_product_stock, :color, :size, :stock) ");

			$stmt->execute( array( 
				":id_product"		=> $this->id_product,
				":id_product_stock"	=> $this->id_product_stock,
				":color"			=> $this->color,
				":size"				=> $this->size,
				":stock"			=> $this->stock ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - product_stock.php|insert: ' . $e->getMessage();
		    return false;
		}
	}

	//update
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update product_stock
				set
					color				= :color,
					size				= :size,
					stock				= :stock
				where id_product 	= :id_product
				and id_product_stock = :id_product_stock ");

			$stmt->execute(array( 
				":color"				=> $this->color,
				":size"					=> $this->size,
				":stock"				=> $this->stock,
				":id_product"			=> $this->id_product,
				":id_product_stock"		=> $this->id_product_stock 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - product_stock.php|update: ' . $e->getMessage();
		    return false;
		}
	}

	public function update_stock($id_product, $color, $size, $new_stock){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update product_stock
				set
					stock			= stock - :new_stock
				where id_product 	= :id_product
				and color 			= :color
				and size 			= :size ");

			$stmt->execute(array( 
				":new_stock"		=> $new_stock,
				":id_product"		=> $id_product,
				":color"			=> $color,
				":size"				=> $size
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - product_stock.php|update: ' . $e->getMessage();
		    return false;
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from product_stock 
				where id_product = :id_product
				and id_product_stock = :id_product_stock ");

			$stmt->execute(array( 
				":id_product"  			=> $this->id_product,
				":id_product_stock" 	=> $this->id_product_stock
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - product_stock.php|delete: ' . $e->getMessage();
		}
	}

	public function delete_product($id_product){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from product_stock 
				where id_product = :id_product");

			$stmt->execute( array( ":id_product"  => $id_product ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - product_stock.php|delete: ' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_product  			= $obj->id_product;
		$this->id_product_stock  	= $obj->id_product;
		$this->color  				= $obj->color;
		$this->size  				= $obj->size;
		$this->stock  				= $obj->stock;

	}

	public function map($id_product){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from product_stock
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
		    echo 'ERROR: - product_stock.php|map' . $e->getMessage();
		}
	}

	//GET CURRENT STOCK
	public function get_current_stock($id_product, $color, $size){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select stock 
				from product_stock
				where id_product 	= :id_product
				and color 			= :color
				and size 			= :size ");

			$stmt->execute(array( 
				":id_product"  	=> $id_product,
				":color" 	 	=> $color,
				":size"  		=> $size
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->stock;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product_stock.php|get_stock' . $e->getMessage();
		}
	}

	//MAXIMUM
	public function max_id_product_stock($id_product){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select COALESCE(MAX(id_product_stock)+1,1) AS maximo 
				from product_stock
				where id_product = :id_product ");

			$stmt->execute(array( 
				":id_product"  => $id_product
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->maximo;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product_stock.php|max_id_product_stock' . $e->getMessage();
		}
	}

	//LIST
	public function get_all($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from product_stock ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$product_stock = new product_stock();
				$product_stock->mapea($reg);

				array_push($list, $product_stock);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product_stock.php|get_all' . $e->getMessage();
		}

		return $list;

	}

	public function get_list_stock($id_product, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from product_stock
				where id_product = :id_product ".$order);

			$stmt->execute(array( 
				":id_product"  => $id_product
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$product_stock = new product_stock();
				$product_stock->mapea($reg);

				array_push($list, $product_stock);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product_stock.php|get_all' . $e->getMessage();
		}

		return $list;

	}

}

?>






