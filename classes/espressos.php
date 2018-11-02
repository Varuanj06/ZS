<?php

class espressos{
	
	//VARIABLES
	private $id;
	private $user_id;
	private $keyword_id;
	private $product_id;
	private $created_at;
	private $updated_at;
	private $prestashop_id;

	//CONSTRUCTOR
	public function __construct(){
		$this->id 				= "";
		$this->user_id 					= "";
		$this->keyword_id 				= "";
		$this->product_id 				= "";
		$this->created_at 				= "";
		$this->updated_at 				= "";
		$this->prestashop_id 	= "";
	}

	//GETTERS AND SETTERS
	public function get_id(){
		return $this->id;
	}

	public function set_id($id){
		$this->id = $id;
	}

	public function get_user_id(){
		return $this->user_id;
	}

	public function set_user_id($user_id){
		$this->user_id = $user_id;
	}

	public function get_keyword_id(){
		return $this->keyword_id;
	}

	public function set_keyword_id($keyword_id){
		$this->keyword_id = $keyword_id;
	}

	public function get_product_id(){
		return $this->product_id;
	}

	public function set_product_id($product_id){
		$this->product_id = $product_id;
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

	public function get_prestashop_id(){
		return $this->prestashop_id;
	}

	public function set_prestashop_id($prestashop_id){
		$this->prestashop_id = $prestashop_id;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into espressos(id, user_id, keyword_id, product_id, created_at, updated_at, prestashop_id) 
				values (:id, :user_id, :keyword_id, :product_id, :created_at, :updated_at, :prestashop_id) ");

			$stmt->execute( array( 
				":id"						=> $this->id,
				":user_id"					=> $this->user_id,
				":keyword_id"				=> $this->keyword_id,
				":product_id"				=> $this->product_id,
				":created_at"				=> $this->created_at,
				":updated_at"				=> $this->updated_at,
				":prestashop_id"			=> $this->prestashop_id,
			) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - espressos.php|insert: ' . $e->getMessage();
		    return false;
		}
	}

	//UPDATE
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update espressos
				set
					user_id 				= :user_id,
					keyword_id 				= :keyword_id,
					product_id  			= :product_id,
					created_at  			= :created_at, 
					updated_at  			= :updated_at,
					prestashop_id  	= :prestashop_id
				where id 			= :id ");

			$stmt->execute( array( 
				":user_id"					=> $this->user_id,
				":keyword_id"				=> $this->keyword_id,
				":product_id"				=> $this->product_id,
				":created_at"				=> $this->created_at,
				":updated_at"				=> $this->updated_at,
				":prestashop_id"			=> $this->prestashop_id
			));

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - espressos.php|update: ' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id  						= $obj->id;
		$this->user_id  				= $obj->user_id;
		$this->keyword_id  				= $obj->keyword_id;
		$this->product_id  				= $obj->product_id;
		$this->created_at  				= $obj->created_at;
		$this->updated_at  				= $obj->updated_at;
		$this->prestashop_id  			= $obj->prestashop_id;

	}

	public function map($id, $id_fb_user){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from espressos
				where id 	= :id
			");

			$stmt->execute(array( 
				":id" => $id
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - espressos.php|map' . $e->getMessage();
		    return false;
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from espressos 
				where id 	= :id
			");

			$stmt->execute(array( 
				":id"  	=> $this->id
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - espressos.php|delete: ' . $e->getMessage();
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
				from espressos ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$espressos = new espressos();
				$espressos->mapea($reg);

				array_push($list, $espressos);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - espressos.php|get_list' . $e->getMessage();
		}

		return $list;

	}

	public function get_list_by_user_id($user_id, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from espressos 
				where user_id = :user_id
				and (select status from espresso_keywords where id_keyword=espressos.keyword_id) = 'BREWED'
			".$order);

			$stmt->execute(array(
				"user_id" => $user_id
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$espressos = new espressos();
				$espressos->mapea($reg);

				array_push($list, $espressos);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - espressos.php|get_list_by_user_id' . $e->getMessage();
		}

		return $list;

	}
	
	
	
	
	public function get_brewing_list_by_user_id($user_id, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from espressos 
				where user_id = :user_id
				and (select status from espresso_keywords where id_keyword=espressos.keyword_id) = 'active'
			".$order);

			$stmt->execute(array(
				"user_id" => $user_id
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$espressos = new espressos();
				$espressos->mapea($reg);

				array_push($list, $espressos);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - espressos.php|get_list_by_user_id' . $e->getMessage();
		}

		return $list;

	}

}

?>






