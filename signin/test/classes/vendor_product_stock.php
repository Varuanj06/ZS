<?php

class vendor_product_stock{

	//VARIABLES
	private $id_product;
	private $hex_color;
	private $color_name;
	private $size;
	private $id_product_lang;
	private $stock;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_product 			= "";
		$this->hex_color 			= "";
		$this->color_name 			= "";
		$this->size 				= "";
		$this->id_product_lang 		= "";
		$this->stock 				= "";
	}

	//GETTERS AND SETTERS
	public function get_id_product(){
		return $this->id_product;
	}

	public function set_id_product($id_product){
		$this->id_product = $id_product;
	}

	public function get_hex_color(){
		return $this->hex_color;
	}

	public function set_hex_color($hex_color){
		$this->hex_color = $hex_color;
	}

	public function get_color_name(){
		return $this->color_name;
	}

	public function set_color_name($color_name){
		$this->color_name = $color_name;
	}

	public function get_size(){
		return $this->size;
	}

	public function set_size($size){
		$this->size = $size;
	}

	public function get_id_product_lang(){
		return $this->id_product_lang;
	}

	public function set_id_product_lang($id_product_lang){
		$this->id_product_lang = $id_product_lang;
	}

	public function get_stock(){
		return $this->stock;
	}

	public function set_stock($stock){
		$this->stock = $stock;
	}

	//INSERT
	public function insert(){
		global $conn_reports;

		try {

			$stmt = $conn_reports->prepare("
				insert into vendor_product_stock(id_product, hex_color, color_name, size, id_product_lang, stock) 
				values (:id_product, :hex_color, :color_name, :size, :id_product_lang, :stock) ");

			$stmt->execute( array( 
				":id_product"		=> $this->id_product,
				":hex_color"		=> $this->hex_color,
				":color_name"		=> $this->color_name,
				":size"				=> $this->size,
				":id_product_lang"	=> $this->id_product_lang,
				":stock"			=> $this->stock ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - vendor_product_stock.php|insert: ' . $e->getMessage();
		    return false;
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_product  				= $obj->id_product;
		$this->hex_color  				= $obj->hex_color;
		$this->color_name  				= $obj->color_name;
		$this->size  					= $obj->size;
		$this->id_product_lang  		= $obj->id_product_lang;
		$this->stock  					= $obj->stock;

	}

	public function map($id_product, $hex_color, $size){
		global $conn_reports;

		try {

			$stmt = $conn_reports->prepare("
				select * 
				from vendor_product_stock
				where id_product 				= :id_product
				and REPLACE(hex_color, '#', '') = REPLACE(:hex_color, '#', '')
				and size 						= :size");

			$stmt->execute( array( ":id_product" => $id_product, ":hex_color" => $hex_color, ":size" => $size ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - vendor_product_stock.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn_reports;

		try {

			$stmt = $conn_reports->prepare("
				delete from vendor_product_stock 
				where id_product 					= :id_product 
				and REPLACE(hex_color, '#', '') 	= REPLACE(:hex_color, '#', '')
				and size 							= :size");

			$stmt->execute( array( ":id_product" => $id_product, ":hex_color" => $hex_color, ":size" => $size ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - vendor_product_stock.php|delete: ' . $e->getMessage();
		}
	}

	public function delete_all_product($id_product){
		global $conn_reports;

		try {

			$stmt = $conn_reports->prepare("
				delete from vendor_product_stock 
				where id_product 	= :id_product");

			$stmt->execute( array( ":id_product" => $id_product ) );

	    } catch(PDOException $e) {
	    	return false;
		    echo 'ERROR - vendor_product_stock.php|delete_all_product: ' . $e->getMessage();
		}
	}

	//EXISTS?
	public function exists(){
		global $conn_reports;

		try {

			$stmt = $conn_reports->prepare("
				select * 
				from vendor_product_stock 
				where id_product 					= :id_product
				and REPLACE(hex_color, '#', '') 	= REPLACE(:hex_color, '#', '')
				and size 							= :size");

			$stmt->execute( array( ":id_product" => $id_product, ":hex_color" => $hex_color, ":size" => $size ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - vendor_product_stock.php|exists' . $e->getMessage();
		}
	}

	public function exists_product_lang($id_product_lang, $hex_color, $size){
		global $conn_reports;

		try {

			$stmt = $conn_reports->prepare("
				select * 
				from vendor_product_stock 
				where id_product_lang 				= :id_product_lang
				and REPLACE(hex_color, '#', '') 	= REPLACE(:hex_color, '#', '')
				and size 							= :size");

			$stmt->execute( array( ":id_product_lang" => $id_product_lang, ":hex_color" => $hex_color, ":size" => $size ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - vendor_product_stock.php|exists_product_lang' . $e->getMessage();
		}
	}

	//GET STOCK
	public function get_stock_each($id_product, $hex_color, $size){
		global $conn_reports;

		try {

			$stmt = $conn_reports->prepare("
				select *
				from vendor_product_stock
				where id_product 					= :id_product
				and REPLACE(hex_color, '#', '') 	= REPLACE(:hex_color, '#', '')
				and size 							= :size ");

			$stmt->execute( array( ":id_product" => $id_product, ":hex_color" => $hex_color, ":size" => $size ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->stock;
			}else{
				return "";
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - get_stock_each.php|get_stock_each' . $e->getMessage();
		}
	}

	//GET STOCK
	public function get_stock_each_product_lang($id_product_lang, $hex_color, $size){
		global $conn_reports;

		try {

			$stmt = $conn_reports->prepare("
				select *
				from vendor_product_stock
				where id_product_lang 				= :id_product_lang
				and REPLACE(hex_color, '#', '') 	= REPLACE(:hex_color, '#', '')
				and size 							= :size ");

			$stmt->execute( array( ":id_product_lang" => $id_product_lang, ":hex_color" => $hex_color, ":size" => $size ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->stock;
			}else{
				return "";
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - get_stock_each.php|get_stock_each_product_lang' . $e->getMessage();
		}
	}


	//UPDATE STOCK
	public function update_stock($id_product_lang, $hex_color, $size, $new_stock){
		global $conn_reports;

		try {

			$stmt = $conn_reports->prepare("
				update vendor_product_stock
				set 
					stock = :stock
				where id_product_lang 				= :id_product_lang
				and REPLACE(hex_color, '#', '') 	= REPLACE(:hex_color, '#', '')
				and size 							= :size  ");

			$stmt->execute( array( 
				":id_product_lang" 		=> $id_product_lang, 
				":hex_color" 			=> $hex_color, 
				":size" 				=> $size,
				":stock"				=> $new_stock ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - vendor_product.php|update: ' . $e->getMessage();
		    return false;
		}
	}

	public function get_all($order){

		global $conn_reports;

		$list = array();

		try {

			$stmt = $conn_reports->prepare("
				select * 
				from vendor_product_stock ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$vendor_product_stock = new vendor_product_stock();
				$vendor_product_stock->mapea($reg);

				array_push($list, $vendor_product_stock);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - vendor_product_stock.php|get_all' . $e->getMessage();
		}

		return $list;

	}

}

?>






