<?php

class love_count{
	//VARIABLES
	private $id_product;
	private $id_fb_user;

	//CONSTRUCTOR
	public function love_count(){
		$this->id_product 				= "";
		$this->id_fb_user 				= "";
	}

	//GETTERS AND SETTERS
	public function get_id_product(){
		return $this->id_product;
	}

	public function set_id_product($id_product){
		$this->id_product = $id_product;
	}

	public function get_id_fb_user(){
		return $this->id_fb_user;
	}

	public function set_id_fb_user($id_fb_user){
		$this->id_fb_user = $id_fb_user;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into love_count(id_product, id_fb_user) 
				values (:id_product, :id_fb_user) ");

			$stmt->execute( array( 
				":id_product"			=> $this->id_product,
				":id_fb_user"			=> $this->id_fb_user ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - love_count.php|insert: ' . $e->getMessage();
		    return false;
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_product  	= $obj->id_product;
		$this->id_fb_user  	= $obj->id_fb_user;

	}

	public function map($id_product, $id_fb_user){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from love_count
				where id_product 	= :id_product
				and id_fb_user 		= :id_fb_user ");

			$stmt->execute( array( 
				":id_product" => $id_product,
				":id_fb_user" => $id_fb_user ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - love_count.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from love_count 
				where id_product 	= :id_product
				and id_fb_user 		= :id_fb_user ");

			$stmt->execute( array( 
				":id_product"  	=> $this->id_product,
				":id_fb_user" 	=> $this->id_fb_user ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - love_count.php|delete: ' . $e->getMessage();
		    return false;
		}
	}

	//EXISTS?
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from love_count 
				where id_product 	= :id_product
				and id_fb_user 		= :id_fb_user ");

			$stmt->execute( array( 
					":id_product" => $this->id_product,
					":id_fb_user" => $this->id_fb_user ) );

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - love_count.php|exists' . $e->getMessage();
		}
	}

	//LIST
	public function get_list($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from love_count ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$love_count = new love_count();
				$love_count->mapea($reg);

				array_push($list, $love_count);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - love_count.php|get_list' . $e->getMessage();
		}

		return $list;

	}
}

?>






