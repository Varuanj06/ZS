<?php

class vendor_product{
	//VARIABLES
	private $id_product;
	private $id_vendor;
	private $id_product_lang;
	private $image_url;
	private $product_link;
	private $size_difference;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_product 			= "";
		$this->id_vendor 			= "";
		$this->id_product_lang 		= "";
		$this->image_url 			= "";
		$this->product_link 		= "";
		$this->size_difference 		= "";
	}

	//GETTERS AND SETTERS
	public function get_id_product(){
		return $this->id_product;
	}

	public function set_id_product($id_product){
		$this->id_product = $id_product;
	}

	public function get_id_vendor(){
		return $this->id_vendor;
	}

	public function set_id_vendor($id_vendor){
		$this->id_vendor = $id_vendor;
	}

	public function get_id_product_lang(){
		return $this->id_product_lang;
	}

	public function set_id_product_lang($id_product_lang){
		$this->id_product_lang = $id_product_lang;
	}

	public function get_image_url(){
		return $this->image_url;
	}

	public function set_image_url($image_url){
		$this->image_url = $image_url;
	}

	public function get_product_link(){
		return $this->product_link;
	}

	public function set_product_link($product_link){
		$this->product_link = $product_link;
	}

	public function get_size_difference(){
		return $this->size_difference;
	}

	public function set_size_difference($size_difference){
		$this->size_difference = $size_difference;
	}

	//INSERT
	public function insert(){
		global $conn_reports;

		try {

			$stmt = $conn_reports->prepare("
				insert into vendor_product(id_vendor, id_product_lang, image_url, product_link, size_difference) 
				values (:id_vendor, :id_product_lang, :image_url, :product_link, :size_difference) ");

			$stmt->execute( array( 
				":id_vendor"			=> $this->id_vendor,
				":id_product_lang"		=> $this->id_product_lang,
				":image_url"			=> $this->image_url,
				":product_link"			=> $this->product_link,
				":size_difference"		=> $this->size_difference ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo "string"; 'ERROR - vendor_product.php|insert: ' . $e->getMessage();
		}
	}

	//UPDATE
	public function update(){
		global $conn_reports;

		try {

			$stmt = $conn_reports->prepare("
				update vendor_product
				set
					id_vendor 		= :id_vendor,
					id_product_lang = :id_product_lang,
					image_url 		= :image_url,
					product_link 	= :product_link,
					size_difference  = :size_difference 
				where id_product 	= :id_product ");

			$stmt->execute( array( 
				":id_vendor"			=> $this->id_vendor,
				":id_product_lang"		=> $this->id_product_lang,
				":image_url"			=> $this->image_url,
				":product_link"			=> $this->product_link,
				":size_difference"		=> $this->size_difference,
				":id_product" 			=> $this->id_product ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - vendor_product.php|update: ' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_product  		= $obj->id_product;
		$this->id_vendor  		= $obj->id_vendor;
		$this->id_product_lang  = $obj->id_product_lang;
		$this->image_url 		= $obj->image_url;
		$this->product_link  	= $obj->product_link;
		$this->size_difference  = $obj->size_difference;

	}

	public function map($id_product){
		global $conn_reports;

		try {

			$stmt = $conn_reports->prepare("
				select * 
				from vendor_product
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
		    echo 'ERROR: - vendor_product.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn_reports;

		try {

			$stmt = $conn_reports->prepare("
				delete from vendor_product 
				where id_product = :id_product");

			$stmt->execute( array( ":id_product"  => $this->id_product ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - vendor_product.php|delete: ' . $e->getMessage();
		}
	}

	//EXISTS?
	public function exists(){
		global $conn_reports;

		try {

			$stmt = $conn_reports->prepare("
				select * 
				from vendor_product 
				where id_product = :id_product ");

			$stmt->execute( array( ":id_product" => $this->id_product ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - vendor_product.php|exists' . $e->getMessage();
		}
	}

	//LIST
	public function get_all_from_vendor($id_vendor, $order){

		global $conn_reports;

		$list = array();

		try {

			$stmt = $conn_reports->prepare("
				select * 
				from vendor_product where id_vendor = :id_vendor ".$order);

			$stmt->execute( array( ":id_vendor" => $id_vendor ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$vendor_product = new vendor_product();
				$vendor_product->mapea($reg);

				array_push($list, $vendor_product);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - vendor_product.php|get_all_from_vendor' . $e->getMessage();
		}

		return $list;

	}

	public function get_all($order){

		global $conn_reports;

		$list = array();

		try {

			$stmt = $conn_reports->prepare("
				select * 
				from vendor_product ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$vendor_product = new vendor_product();
				$vendor_product->mapea($reg);

				array_push($list, $vendor_product);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - vendor_product.php|get_all' . $e->getMessage();
		}

		return $list;

	}

	//PRODUCT NAME
	public function get_product_price($id_product_lang){
		global $conn_prestashop;

		try {

			$stmt = $conn_prestashop->prepare("
				select * 
				from product
				where id_product = :id_product ");

			$stmt->execute( array(":id_product" => $id_product_lang) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->price;
			}else{
				return "";
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - vendor_product.php|get_product_price' . $e->getMessage();
		}
	}

	//PRODUCT NAME
	public function get_product_image_url($id_product_lang){
		global $conn_reports;

		try {

			$stmt = $conn_reports->prepare("
				select * 
				from vendor_product
				where id_product_lang = :id_product_lang ");

			$stmt->execute( array(":id_product_lang" => $id_product_lang) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->image_url;
			}else{
				return "";
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - vendor_product.php|get_product_image_url' . $e->getMessage();
		}
	}

	public function get_product_link_lang($id_product_lang){
		global $conn_reports;

		try {

			$stmt = $conn_reports->prepare("
				select * 
				from vendor_product
				where id_product_lang = :id_product_lang ");

			$stmt->execute(array(":id_product_lang" => $id_product_lang));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->product_link;
			}else{
				return "";
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - vendor_product.php|get_product_link_lang' . $e->getMessage();
		}
	}

	//PRODUCT STOCK
	public function get_product_stock($id_product){
		global $conn_reports;

		try {

			$stmt = $conn_reports->prepare("
				select sum(stock) as stock
				from vendor_product_stock
				where id_product = :id_product ");

			$stmt->execute( array(":id_product" => $id_product) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->stock;
			}else{
				return "";
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - vendor_product.php|get_product_stock' . $e->getMessage();
		}
	}

	public function get_vendor($id_product_lang){
		global $conn_reports;

		try {

			$stmt = $conn_reports->prepare("
				select * 
				from vendor_product
				where id_product_lang = :id_product_lang ");

			$stmt->execute(array(":id_product_lang" => $id_product_lang));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->id_vendor;
			}else{
				return "";
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - vendor_product.php|get_vendor' . $e->getMessage();
		}
	}

}

?>






