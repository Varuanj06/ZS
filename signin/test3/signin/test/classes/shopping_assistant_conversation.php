<?php

class shopping_assistant_conversation{

	//VARIABLES
	private $id_fb_user;
	private $id_shopping_assistant_conversation;
	private $status;
	private $step; 
	private $price_range; 
	private $keyword; 
	private $id_products; 

	//CONSTRUCTOR
	public function __construct(){
		$this->id_fb_user 							= "";
		$this->id_shopping_assistant_conversation 	= "";
		$this->status 								= "";
		$this->step 								= "";
		$this->price_range 							= "";
		$this->keyword 								= "";
		$this->id_products 							= "";
	}

	//GETTERS AND SETTERS
	public function get_id_fb_user(){
		return $this->id_fb_user;
	}

	public function set_id_fb_user($id_fb_user){
		$this->id_fb_user = $id_fb_user;
	}

	public function get_id_shopping_assistant_conversation(){
		return $this->id_shopping_assistant_conversation;
	}

	public function set_id_shopping_assistant_conversation($id_shopping_assistant_conversation){
		$this->id_shopping_assistant_conversation = $id_shopping_assistant_conversation;
	}

	public function get_status(){
		return $this->status;
	}

	public function set_status($status){
		$this->status = $status;
	}

	public function get_step(){
		return $this->step;
	}

	public function set_step($step){
		$this->step = $step;
	}

	public function get_price_range(){
		return $this->price_range;
	}

	public function set_price_range($price_range){
		$this->price_range = $price_range;
	}

	public function get_keyword(){
		return $this->keyword;
	}

	public function set_keyword($keyword){
		$this->keyword = $keyword;
	}

	public function get_id_products(){
		return $this->id_products;
	}

	public function set_id_products($id_products){
		$this->id_products = $id_products;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into shopping_assistant_conversation(id_fb_user, id_shopping_assistant_conversation, `status`, step, price_range, keyword, id_products) 
				values (:id_fb_user, :id_shopping_assistant_conversation, 'pending on customer', '0', '', '', '') ");

			$stmt->execute(array( 
				":id_fb_user" 							=> $this->id_fb_user, 
				":id_shopping_assistant_conversation" 	=> $this->id_shopping_assistant_conversation
			 ));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - shopping_assistant_conversation.php|insert: ' . $e->getMessage();
		}
	}

	//UPDATE
	public function update_status(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update shopping_assistant_conversation 
					set `status` 	 	  				= :status
				where id_fb_user 						= :id_fb_user
				and id_shopping_assistant_conversation 	= :id_shopping_assistant_conversation ");

			$stmt->execute(array( 
				":status"								=> $this->status,
				":id_fb_user" 							=> $this->id_fb_user,
				":id_shopping_assistant_conversation" 	=> $this->id_shopping_assistant_conversation 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - shopping_assistant_conversation.php|update_status: ' . $e->getMessage();
		}
	}

	public function update_step($id_fb_user, $id_shopping_assistant_conversation, $step){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update shopping_assistant_conversation 
					set `step` 	 	  						= :step
				where id_fb_user 							= :id_fb_user
				and id_shopping_assistant_conversation 		= :id_shopping_assistant_conversation ");

			$stmt->execute(array( 
				":step"									=> $step,
				":id_fb_user" 							=> $id_fb_user,
				":id_shopping_assistant_conversation" 	=> $id_shopping_assistant_conversation 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - shopping_assistant_conversation.php|update_step: ' . $e->getMessage();
		}
	}

	public function update_price_range($id_fb_user, $id_shopping_assistant_conversation, $price_range){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update shopping_assistant_conversation 
					set `price_range` 	 	  			= :price_range
				where id_fb_user 						= :id_fb_user
				and id_shopping_assistant_conversation 	= :id_shopping_assistant_conversation ");

			$stmt->execute(array( 
				":price_range"							=> $price_range,
				":id_fb_user" 							=> $id_fb_user,
				":id_shopping_assistant_conversation" 	=> $id_shopping_assistant_conversation 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - shopping_assistant_conversation.php|update_price_range: ' . $e->getMessage();
		}
	}

	public function update_keyword($id_fb_user, $id_shopping_assistant_conversation, $keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update shopping_assistant_conversation 
					set `keyword` 	 	  				= :keyword
				where id_fb_user 						= :id_fb_user
				and id_shopping_assistant_conversation 	= :id_shopping_assistant_conversation ");

			$stmt->execute(array( 
				":keyword"								=> $keyword,
				":id_fb_user" 							=> $id_fb_user,
				":id_shopping_assistant_conversation" 	=> $id_shopping_assistant_conversation 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - shopping_assistant_conversation.php|update_keyword: ' . $e->getMessage();
		}
	}

	public function update_id_products($id_fb_user, $id_shopping_assistant_conversation, $id_products){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update shopping_assistant_conversation 
					set `id_products` 	 	  				= :id_products
				where id_fb_user 						= :id_fb_user
				and id_shopping_assistant_conversation 	= :id_shopping_assistant_conversation ");

			$stmt->execute(array( 
				":id_products"								=> $id_products,
				":id_fb_user" 							=> $id_fb_user,
				":id_shopping_assistant_conversation" 	=> $id_shopping_assistant_conversation 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - shopping_assistant_conversation.php|update_keyword: ' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_fb_user 							= $obj->id_fb_user;
		$this->id_shopping_assistant_conversation 	= $obj->id_shopping_assistant_conversation;
		$this->status 								= $obj->status;
		$this->step 								= $obj->step;
		$this->price_range 							= $obj->price_range;
		$this->keyword 								= $obj->keyword;
		$this->id_products 							= $obj->id_products;

	}

	public function map($id_fb_user, $id_shopping_assistant_conversation){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from shopping_assistant_conversation 
				where id_fb_user							= :id_fb_user
				and id_shopping_assistant_conversation 		= :id_shopping_assistant_conversation ");

			$stmt->execute(array( 
				":id_fb_user" 							=> $id_fb_user,
				":id_shopping_assistant_conversation" 	=> $id_shopping_assistant_conversation
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - shopping_assistant_conversation.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from shopping_assistant_conversation 
				where id_fb_user 							= :id_fb_user,
				and id_shopping_assistant_conversation 		= :id_shopping_assistant_conversation ");

			$stmt->execute(array( 
				":id_fb_user"  							=> $this->id_fb_user,
				":id_shopping_assistant_conversation"  	=> $this->id_shopping_assistant_conversation 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - shopping_assistant_conversation.php|delete: ' . $e->getMessage();
		}
	}


	//EXIST
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from shopping_assistant_conversation 
				where id_fb_user 							= :id_fb_user,
				and id_shopping_assistant_conversation 		= :id_shopping_assistant_conversation ");

			$stmt->execute(array( 
				":id_fb_user"  							=> $this->id_fb_user,
				":id_shopping_assistant_conversation"  	=> $this->id_shopping_assistant_conversation 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - shopping_assistant_conversation.php|exists' . $e->getMessage();
		}
	}

	//MAXIMUM
	public function max_id_shopping_assistant_conversation($id_fb_user){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select COALESCE(MAX(id_shopping_assistant_conversation)+1,1) AS maximo 
				from shopping_assistant_conversation
				where id_fb_user = :id_fb_user ");

			$stmt->execute(array( 
				":id_fb_user"  => $id_fb_user
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->maximo;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - shopping_assistant_conversation.php|max_id_shopping_assistant_conversation' . $e->getMessage();
		}
	}

	//GET "PENDING ON CUSTOMER" CONVERSATION
	public function get_current_conversation($id_fb_user){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select id_shopping_assistant_conversation
				from shopping_assistant_conversation
				where id_fb_user = :id_fb_user
				and status = 'pending on customer' ");

			$stmt->execute(array( 
				":id_fb_user"  => $id_fb_user
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->id_shopping_assistant_conversation;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - get_current_conversation.php|max_id_shopping_assistant_conversation' . $e->getMessage();
		}
	}

	public function get_last_price_range_used($id_fb_user){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select price_range
				from shopping_assistant_conversation
				where id_fb_user = :id_fb_user
				and status = 'closed'
				and price_range <> ''
				order by id_shopping_assistant_conversation desc
				limit 1 ");

			$stmt->execute(array( 
				":id_fb_user"  => $id_fb_user
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->price_range;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - get_current_conversation.php|get_last_price_range_used' . $e->getMessage();
		}
	}

	public function get_last_price_range_by_user($id_fb_user){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select price_range
				from shopping_assistant_conversation
				where id_fb_user = :id_fb_user
				and price_range <> ''
				order by id_shopping_assistant_conversation desc
				limit 1 ");

			$stmt->execute(array( 
				":id_fb_user"  => $id_fb_user
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->price_range;
			}else{
				return '2000 - 4000';
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - get_current_conversation.php|get_last_price_range_by_user' . $e->getMessage();
		}
	}

	// List
	public function get_conversations_by_user($id_fb_user, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from `shopping_assistant_conversation` 
				where id_fb_user = :id_fb_user ".$order);

			$stmt->execute(array(
				':id_fb_user' => $id_fb_user
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$shopping_assistant_conversation = new shopping_assistant_conversation();
				$shopping_assistant_conversation->mapea($reg);

				array_push($list, $shopping_assistant_conversation);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - shopping_assistant_conversation.php|get_conversations_by_user' . $e->getMessage();
		}

		return $list;

	}

	public function get_all_conversations($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from `shopping_assistant_conversation` ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$shopping_assistant_conversation = new shopping_assistant_conversation();
				$shopping_assistant_conversation->mapea($reg);

				array_push($list, $shopping_assistant_conversation);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - shopping_assistant_conversation.php|get_all_conversations' . $e->getMessage();
		}

		return $list;

	}

	public function get_open_conversations($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from `shopping_assistant_conversation` 
				where status = 'open' ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$shopping_assistant_conversation = new shopping_assistant_conversation();
				$shopping_assistant_conversation->mapea($reg);

				array_push($list, $shopping_assistant_conversation);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - shopping_assistant_conversation.php|get_conversations_from_user' . $e->getMessage();
		}

		return $list;

	}

}

?>