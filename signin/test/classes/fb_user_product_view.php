<?php

class fb_user_product_view{

	//VARIABLES
	private $id_fb_user;
	private $id_product;
	private $qty;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_fb_user 				= "";
		$this->id_product 				= "";
		$this->qty 						= "";
	}

	//GETTERS AND SETTERS
	public function get_id_fb_user(){
		return $this->id_fb_user;
	}

	public function set_id_fb_user($id_fb_user){
		$this->id_fb_user = $id_fb_user;
	}

	public function get_id_product(){
		return $this->id_product;
	}

	public function set_id_product($id_product){
		$this->id_product = $id_product;
	}

	public function get_qty(){
		return $this->qty;
	}

	public function set_qty($qty){
		$this->qty = $qty;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into fb_user_product_view(id_fb_user, id_product, qty) 
				values (:id_fb_user, :id_product, :qty) ");

			$stmt->execute(array( 
				":id_fb_user" 				=> $this->id_fb_user, 
				":id_product" 				=> $this->id_product,
				":qty" 						=> $this->qty
			 ));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - fb_user_product_view.php|insert: ' . $e->getMessage();
		}
	}

	//UPDATE
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update fb_user_product_view 
					set `qty` 	 	 				= :qty
				where id_fb_user 				= :id_fb_user
				and id_product 					= :id_product ");

			$stmt->execute(array( 
				":qty"						=> $this->qty,
				":id_fb_user" 				=> $this->id_fb_user,
				":id_product" 				=> $this->id_product 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - fb_user_product_view.php|update: ' . $e->getMessage();
		}
	}

	//GET PRODUCT VIEW COUNT
	public function get_id_product_view_count($id_product){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select coalesce(sum(qty),0) as total
				from fb_user_product_view
				where id_product = :id_product  ");

			$stmt->execute(array( 
				":id_product"  => $id_product
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->total;
			}else{
				return '0';
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - fb_user_product_view.php|get_id_product_view_count' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_fb_user 				= $obj->id_fb_user;
		$this->id_product 				= $obj->id_product;
		$this->qty 						= $obj->qty;

	}

	public function map($id_fb_user, $id_product){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from fb_user_product_view 
				where id_fb_user				= :id_fb_user
				and id_product 					= :id_product ");

			$stmt->execute(array( 
				":id_fb_user" 	=> $id_fb_user,
				":id_product" 	=> $id_product
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - fb_user_product_view.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from fb_user_product_view 
				where id_fb_user 		= :id_fb_user,
				and id_product 			= :id_product ");

			$stmt->execute(array( 
				":id_fb_user"  		=> $this->id_fb_user,
				":id_product"  		=> $this->id_product 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - fb_user_product_view.php|delete: ' . $e->getMessage();
		}
	}


	//EXIST
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from fb_user_product_view 
				where id_fb_user 			= :id_fb_user
				and id_product 				= :id_product ");

			$stmt->execute(array( 
				":id_fb_user"  		=> $this->id_fb_user,
				":id_product"  		=> $this->id_product 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - fb_user_product_view.php|exists' . $e->getMessage();
		}
	}


	public function get_list($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from `fb_user_product_view` ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$fb_user_product_view = new fb_user_product_view();
				$fb_user_product_view->mapea($reg);

				array_push($list, $fb_user_product_view);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - fb_user_product_view.php|get_list' . $e->getMessage();
		}

		return $list;

	}

	public function get_products_by_profile($profile, $current_id_fb_user, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select id_product, sum(qty) as views
				from fb_user_product_view
				where id_fb_user in (
					
					select id_fb_user 
					from fb_user_profile
					where (id_fb_user, id_fb_user_profile) in (
						select id_fb_user, max(id_fb_user_profile)
						from fb_user_profile
						group by id_fb_user
					)
					and profile = :profile
					and id_fb_user != :id_fb_user
					
				)
				group by id_product ".$order);

			$stmt->execute(array( 
				":profile"  		=> $profile,
				":id_fb_user"  		=> $current_id_fb_user
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				array_push($list, $reg->id_product);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - fb_user_product_view.php|get_products_by_profile' . $e->getMessage();
		}

		return $list;

	}

}

?>