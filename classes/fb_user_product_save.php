<?php

class fb_user_product_save{

	//VARIABLES
	private $id_fb_user;
	private $id_product;
	private $date;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_fb_user 				= "";
		$this->id_product 				= "";
		$this->date 					= "";
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

	public function get_date(){
		return $this->date;
	}

	public function set_date($date){
		$this->date = $date;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into fb_user_product_save(id_fb_user, id_product, date) 
				values (:id_fb_user, :id_product, now()) ");

			$stmt->execute(array( 
				":id_fb_user" 				=> $this->id_fb_user, 
				":id_product" 				=> $this->id_product
			 ));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - fb_user_product_save.php|insert: ' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_fb_user 				= $obj->id_fb_user;
		$this->id_product 				= $obj->id_product;
		$this->date 					= $obj->date;

	}

	public function map($id_fb_user, $id_product){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from fb_user_product_save 
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
		    echo 'ERROR: - fb_user_product_save.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from fb_user_product_save 
				where id_fb_user 		= :id_fb_user
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
		    echo 'ERROR - fb_user_product_save.php|delete: ' . $e->getMessage();
		}
	}


	//EXIST
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from fb_user_product_save 
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
		    echo 'ERROR: - fb_user_product_save.php|exists' . $e->getMessage();
		}
	}

	//GET PRODUCT VIEW COUNT
	public function get_product_count($id_fb_user){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select coalesce(count(*),0) as total
				from fb_user_product_save
				where id_fb_user = :id_fb_user  ");

			$stmt->execute(array( 
				":id_fb_user"  => $id_fb_user
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->total;
			}else{
				return '0';
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - fb_user_product_save.php|get_product_count' . $e->getMessage();
		}
	}

	public function get_list($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from `fb_user_product_save` ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$fb_user_product_save = new fb_user_product_save();
				$fb_user_product_save->mapea($reg);

				array_push($list, $fb_user_product_save);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - fb_user_product_save.php|get_list' . $e->getMessage();
		}

		return $list;

	}

	public function get_list_users($gender, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select 
					id_fb_user, 
				    ifnull( (select concat_ws(' ', name, last_name) from fb_user_personal_info where id_fb_user = fb_user_product_save.id_fb_user limit 1), '') as id_product, 
				    date 
				from (
				    select * 
				    from fb_user_product_save
				    order by date desc
				) as fb_user_product_save
				where (select gender from fb_user_personal_info where id_fb_user = fb_user_product_save.id_fb_user) = '$gender'
				group by id_fb_user
				having count(id_product) > 15
				$order
				limit 100 ");

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$fb_user_product_save = new fb_user_product_save();
				$fb_user_product_save->mapea($reg);

				array_push($list, $fb_user_product_save);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - fb_user_product_save.php|get_list' . $e->getMessage();
		}

		return $list;

	}

	public function get_few_products($id_fb_user){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select id_fb_user, id_product, date
				from fb_user_product_save
				where id_fb_user = '$id_fb_user'
				order by (select sum(qty) from fb_user_product_view where id_product=fb_user_product_save.id_product) desc
				limit 4 ");

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$fb_user_product_save = new fb_user_product_save();
				$fb_user_product_save->mapea($reg);

				array_push($list, $fb_user_product_save);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - fb_user_product_save.php|get_five_products' . $e->getMessage();
		}

		return $list;

	}

}

?>