<?php

class espresso{
	
	//VARIABLES
	private $id_espresso;
	private $id_user;
	private $id_keyword;
	private $id_product;
	private $created_at;
	private $updated_at;
	private $id_product_prestashop;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_espresso 				= "";
		$this->id_user 					= "";
		$this->id_keyword 				= "";
		$this->id_product 				= "";
		$this->created_at 				= "";
		$this->updated_at 				= "";
		$this->id_product_prestashop 	= "";
	}

	//GETTERS AND SETTERS
	public function get_id_espresso(){
		return $this->id_espresso;
	}

	public function set_id_espresso($id_espresso){
		$this->id_espresso = $id_espresso;
	}

	public function get_id_user(){
		return $this->id_user;
	}

	public function set_id_user($id_user){
		$this->id_user = $id_user;
	}

	public function get_id_keyword(){
		return $this->id_keyword;
	}

	public function set_id_keyword($id_keyword){
		$this->id_keyword = $id_keyword;
	}

	public function get_id_product(){
		return $this->id_product;
	}

	public function set_id_product($id_product){
		$this->id_product = $id_product;
	}

	public function get_created_at(){
		return $this->created_at;
	}

	public function set_created_at($created_at){
		$this->created_at = $created_at;
	}

	public function get_updated_at(){
		return $this->updated_at;
	}

	public function set_updated_at($updated_at){
		$this->updated_at = $updated_at;
	}

	public function get_id_product_prestashop(){
		return $this->id_product_prestashop;
	}

	public function set_id_product_prestashop($id_product_prestashop){
		$this->id_product_prestashop = $id_product_prestashop;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into espresso(id_espresso, id_user, id_keyword, id_product, created_at, updated_at, id_product_prestashop) 
				values (:id_espresso, :id_user, :id_keyword, :id_product, :created_at, :updated_at, :id_product_prestashop) ");

			$stmt->execute( array( 
				":id_espresso"				=> $this->id_espresso,
				":id_user"					=> $this->id_user,
				":id_keyword"				=> $this->id_keyword,
				":id_product"				=> $this->id_product,
				":created_at"				=> $this->created_at,
				":updated_at"				=> $this->updated_at,
				":id_product_prestashop"	=> $this->id_product_prestashop,
			) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - espresso.php|insert: ' . $e->getMessage();
		    return false;
		}
	}

	//UPDATE
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update espresso
				set
					id_user 				= :id_user,
					id_keyword 				= :id_keyword,
					id_product  			= :id_product,
					created_at  			= :created_at, 
					updated_at  			= :updated_at,
					id_product_prestashop  	= :id_product_prestashop
				where id_espresso 			= :id_espresso ");

			$stmt->execute( array( 
				":id_user"					=> $this->id_user,
				":id_keyword"				=> $this->id_keyword,
				":id_product"				=> $this->id_product,
				":created_at"				=> $this->created_at,
				":updated_at"				=> $this->updated_at,
				":id_product_prestashop"	=> $this->id_product_prestashop
			));

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - espresso.php|update: ' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_espresso  			= $obj->id_espresso;
		$this->id_user  				= $obj->id_user;
		$this->id_keyword  				= $obj->id_keyword;
		$this->id_product  				= $obj->id_product;
		$this->created_at  				= $obj->created_at;
		$this->updated_at  				= $obj->updated_at;
		$this->id_product_prestashop  	= $obj->id_product_prestashop;

	}

	public function map($id_espresso, $id_fb_user){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from espresso
				where id_espresso 	= :id_espresso
			");

			$stmt->execute(array( 
				":id_espresso" => $id_espresso
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - espresso.php|map' . $e->getMessage();
		    return false;
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from espresso 
				where id_espresso 	= :id_espresso
			");

			$stmt->execute(array( 
				":id_espresso"  	=> $this->id_espresso
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - espresso.php|delete: ' . $e->getMessage();
		    return false;
		}
	}

	//LIST
	public function get_list($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from espresso ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$espresso = new espresso();
				$espresso->mapea($reg);

				array_push($list, $espresso);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - espresso.php|get_list' . $e->getMessage();
		}

		return $list;

	}

	public function get_list_by_id_user($id_user, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from espresso 
				where id_user = :id_user
				and (select status from espresso_keywords where id_keyword=espresso.id_keyword) = 'BREWED'
			".$order);

			$stmt->execute(array(
				"id_user" => $id_user
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$espresso = new espresso();
				$espresso->mapea($reg);

				array_push($list, $espresso);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - espresso.php|get_list' . $e->getMessage();
		}

		return $list;

	}

}

?>






