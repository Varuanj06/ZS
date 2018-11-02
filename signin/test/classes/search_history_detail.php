<?php

class search_history_detail{
	//VARIABLES
	private $id_search_history;
	private $id_search_history_detail;
	private $id_product;
	private $id_product_prestashop;
	private $name;
	private $link;
	private $image_link;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_search_history 				= "";
		$this->id_search_history_detail 		= "";
		$this->id_product 						= "";
		$this->id_product_prestashop 			= "";
		$this->name 							= "";
		$this->link 							= "";
		$this->image_link 						= "";
	}

	//GETTERS AND SETTERS
	public function get_id_search_history(){
		return $this->id_search_history;
	}

	public function set_id_search_history($id_search_history){
		$this->id_search_history = $id_search_history;
	}

	public function get_id_search_history_detail(){
		return $this->id_search_history_detail;
	}

	public function set_id_search_history_detail($id_search_history_detail){
		$this->id_search_history_detail = $id_search_history_detail;
	}

	public function get_id_product(){
		return $this->id_product;
	}

	public function set_id_product($id_product){
		$this->id_product = $id_product;
	}

	public function get_id_product_prestashop(){
		return $this->id_product_prestashop;
	}

	public function set_id_product_prestashop($id_product_prestashop){
		$this->id_product_prestashop = $id_product_prestashop;
	}

	public function get_name(){
		return $this->name;
	}

	public function set_name($name){
		$this->name = $name;
	}

	public function get_link(){
		return $this->link;
	}

	public function set_link($link){
		$this->link = $link;
	}

	public function get_image_link(){
		return $this->image_link;
	}

	public function set_image_link($image_link){
		$this->image_link = $image_link;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into search_history_detail(id_search_history, id_search_history_detail, id_product, id_product_prestashop, name, link, image_link) 
				values (:id_search_history, :id_search_history_detail, :id_product, :id_product_prestashop, :name, :link, :image_link) ");

			$stmt->execute( array( 
				":id_search_history"				=> $this->id_search_history,
				":id_search_history_detail"			=> $this->id_search_history_detail,
				":id_product"						=> $this->id_product,
				":id_product_prestashop"			=> $this->id_product_prestashop,
				":name"								=> $this->name,
				":link"								=> $this->link,
				":image_link"						=> $this->image_link) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - search_history_detail.php|insert: ' . $e->getMessage();
		    return false;
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_search_history  			= $obj->id_search_history;
		$this->id_search_history_detail  	= $obj->id_search_history_detail;
		$this->id_product  					= $obj->id_product;
		$this->id_product_prestashop  		= $obj->id_product_prestashop;
		$this->name  						= $obj->name;
		$this->link  						= $obj->link;
		$this->image_link  					= $obj->image_link;

	}

	public function map($id_search_history){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from search_history_detail
				where id_search_history 		= :id_search_history
				and id_search_history_detail 	= :id_search_history_detail ");

			$stmt->execute( array( 
				":id_search_history" 		=> $id_search_history,
				":id_search_history_detail" => $id_search_history_detail
			) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - search_history_detail.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from search_history_detail 
				where id_search_history 		= :id_search_history
				and id_search_history_detail 	= :id_search_history_detail ");

			$stmt->execute( array( 
				":id_search_history"  			=> $this->id_search_history,
				":id_search_history_detail"  	=> $this->id_search_history_detail
			) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - search_history_detail.php|delete: ' . $e->getMessage();
		    return false;
		}
	}

	public function delete_by_id_search_history(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from search_history_detail 
				where id_search_history 		= :id_search_history ");

			$stmt->execute( array( 
				":id_search_history"  			=> $this->id_search_history
			) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - delete_by_id_search_history.php|delete: ' . $e->getMessage();
		    return false;
		}
	}

	//EXISTS?
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from search_history 
				where id_search_history 		= :id_search_history
				and id_search_history_detail 	= :id_search_history_detail ");

			$stmt->execute( array( 
					":id_search_history" 			=> $this->id_search_history,
					":id_search_history_detail" 	=> $this->id_search_history_detail
			) );

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - search_history_detail.php|exists' . $e->getMessage();
		}
	}

	//MAXIMUM
	public function max_id_search_history_detail($id_search_history){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select COALESCE(MAX(id_search_history_detail)+1,1) AS max 
				from search_history_detail
				where id_search_history = :id_search_history ");

			$stmt->execute(array(
					":id_search_history" => $id_search_history
				));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->max;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - search_history_detail.php|max_id_search_history_detail' . $e->getMessage();
		}
	}

	//LIST
	public function get_list($id_search_history, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from search_history_detail
				where id_search_history = :id_search_history ".$order);

			$stmt->execute(array( ":id_search_history" => $id_search_history ));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$search_history_detail = new search_history_detail();
				$search_history_detail->mapea($reg);

				array_push($list, $search_history_detail);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - search_history_detail.php|get_list' . $e->getMessage();
		}

		return $list;

	}
}

?>






