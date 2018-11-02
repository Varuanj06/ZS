<?php

class message_conversation{

	//VARIABLES
	private $id_fb_user;
	private $id_message_conversation;
	private $status;
	private $category;
	private $step;
	private $id_order;
	private $id_order_detail;
	private $read_by_admin;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_fb_user 				= "";
		$this->id_message_conversation 	= "";
		$this->status 					= "";
		$this->category 				= "";
		$this->step 					= "";
		$this->id_order 				= "";
		$this->id_order_detail 			= "";
		$this->read_by_admin 					= "";
	}

	//GETTERS AND SETTERS
	public function get_id_fb_user(){
		return $this->id_fb_user;
	}

	public function set_id_fb_user($id_fb_user){
		$this->id_fb_user = $id_fb_user;
	}

	public function get_id_message_conversation(){
		return $this->id_message_conversation;
	}

	public function set_id_message_conversation($id_message_conversation){
		$this->id_message_conversation = $id_message_conversation;
	}

	public function get_status(){
		return $this->status;
	}

	public function set_status($status){
		$this->status = $status;
	}

	public function get_category(){
		return $this->category;
	}

	public function set_category($category){
		$this->category = $category;
	}

	public function get_step(){
		return $this->step;
	}

	public function set_step($step){
		$this->step = $step;
	}

	public function get_id_order(){
		return $this->id_order;
	}

	public function set_id_order($id_order){
		$this->id_order = $id_order;
	}

	public function get_id_order_detail(){
		return $this->id_order_detail;
	}

	public function set_id_order_detail($id_order_detail){
		$this->id_order_detail = $id_order_detail;
	}

	public function get_read_by_admin(){
		return $this->read_by_admin;
	}

	public function set_read_by_admin($read_by_admin){
		$this->read_by_admin = $read_by_admin;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into message_conversation(id_fb_user, id_message_conversation, `status`, category, step, id_order, id_order_detail, read_by_admin) 
				values (:id_fb_user, :id_message_conversation, 'pending on customer', '', '0', '', '', 'no') ");

			$stmt->execute(array( 
				":id_fb_user" 				=> $this->id_fb_user, 
				":id_message_conversation" 	=> $this->id_message_conversation
			 ));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - message_conversation.php|insert: ' . $e->getMessage();
		}
	}

	//UPDATE
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update message_conversation 
					set `status` 	 	  	= :status
				where id_fb_user 	= :id_fb_user
				and id_message_conversation 		= :id_message_conversation ");

			$stmt->execute(array( 
				":status"					=> $this->status,
				":id_fb_user" 				=> $this->id_fb_user,
				":id_message_conversation" 	=> $this->id_message_conversation 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - message_conversation.php|update: ' . $e->getMessage();
		}
	}

	public function update_step($id_fb_user, $id_message_conversation, $step){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update message_conversation 
					set `step` 	 	  	= :step
				where id_fb_user 		= :id_fb_user
				and id_message_conversation 		= :id_message_conversation ");

			$stmt->execute(array( 
				":step"					=> $step,
				":id_fb_user" 				=> $id_fb_user,
				":id_message_conversation" 	=> $id_message_conversation 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - message_conversation.php|update_step: ' . $e->getMessage();
		}
	}

	public function update_id_order($id_fb_user, $id_message_conversation, $id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update message_conversation 
					set `id_order` 	 	  	= :id_order
				where id_fb_user 		= :id_fb_user
				and id_message_conversation 		= :id_message_conversation ");

			$stmt->execute(array( 
				":id_order"					=> $id_order,
				":id_fb_user" 				=> $id_fb_user,
				":id_message_conversation" 	=> $id_message_conversation 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - message_conversation.php|update_id_order: ' . $e->getMessage();
		}
	}

	public function update_id_order_detail($id_fb_user, $id_message_conversation, $id_order_detail){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update message_conversation 
					set `id_order_detail` 	 	  	= :id_order_detail
				where id_fb_user 					= :id_fb_user
				and id_message_conversation 		= :id_message_conversation ");

			$stmt->execute(array( 
				":id_order_detail"			=> $id_order_detail,
				":id_fb_user" 				=> $id_fb_user,
				":id_message_conversation" 	=> $id_message_conversation 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - message_conversation.php|update_id_order_detail: ' . $e->getMessage();
		}
	}

	public function update_category($id_fb_user, $id_message_conversation, $category){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update message_conversation 
					set `category` 	 	  	= :category
				where id_fb_user 			= :id_fb_user
				and id_message_conversation 		= :id_message_conversation ");

			$stmt->execute(array( 
				":category"					=> $category,
				":id_fb_user" 				=> $id_fb_user,
				":id_message_conversation" 	=> $id_message_conversation 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - message_conversation.php|update_category: ' . $e->getMessage();
		}
	}

	public function update_status($id_fb_user, $id_message_conversation, $status){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update message_conversation 
					set `status` 	 	  			= :status
				where id_fb_user 					= :id_fb_user
				and id_message_conversation 		= :id_message_conversation ");

			$stmt->execute(array( 
				":status"					=> $status,
				":id_fb_user" 				=> $id_fb_user,
				":id_message_conversation" 	=> $id_message_conversation 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - message_conversation.php|update_status: ' . $e->getMessage();
		}
	}

	public function update_read_by_admin($id_fb_user, $id_message_conversation, $read_by_admin){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update message_conversation 
					set `read_by_admin` 	 	  	= :read_by_admin
				where id_fb_user 					= :id_fb_user
				and id_message_conversation 		= :id_message_conversation ");

			$stmt->execute(array( 
				":read_by_admin"			=> $read_by_admin,
				":id_fb_user" 				=> $id_fb_user,
				":id_message_conversation" 	=> $id_message_conversation 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - message_conversation.php|update_read_by_admin: ' . $e->getMessage();
		    return false;
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_fb_user 				= $obj->id_fb_user;
		$this->id_message_conversation 	= $obj->id_message_conversation;
		$this->status 					= $obj->status;
		$this->category 				= $obj->category;
		$this->step 					= $obj->step;
		$this->id_order 				= $obj->id_order;
		$this->read_by_admin 			= $obj->read_by_admin;

	}

	public function map($id_fb_user, $id_message_conversation){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from message_conversation 
				where id_fb_user	= :id_fb_user
				and id_message_conversation 		= :id_message_conversation ");

			$stmt->execute(array( 
				":id_fb_user" 				=> $id_fb_user,
				":id_message_conversation" 	=> $id_message_conversation
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - message_conversation.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from message_conversation 
				where id_fb_user 	= :id_fb_user,
				and id_message_conversation 		= :id_message_conversation ");

			$stmt->execute(array( 
				":id_fb_user"  				=> $this->id_fb_user,
				":id_message_conversation"  => $this->id_message_conversation 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - message_conversation.php|delete: ' . $e->getMessage();
		}
	}


	//EXIST
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from message_conversation 
				where id_fb_user = :id_fb_user,
				and id_message_conversation 		= :id_message_conversation ");

			$stmt->execute(array( 
				":id_fb_user"  				=> $this->id_fb_user,
				":id_message_conversation"  => $this->id_message_conversation 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - message_conversation.php|exists' . $e->getMessage();
		}
	}

	//MAXIMUM
	public function max_id_message_conversation($id_fb_user){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select COALESCE(MAX(id_message_conversation)+1,1) AS maximo 
				from message_conversation
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
		    echo 'ERROR: - message_conversation.php|max_id_message_conversation' . $e->getMessage();
		}
	}

	// List
	public function get_conversations_by_user($id_fb_user, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from `message_conversation` 
				where id_fb_user = :id_fb_user ".$order);

			$stmt->execute(array(
				':id_fb_user' => $id_fb_user
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$message_conversation = new message_conversation();
				$message_conversation->mapea($reg);

				array_push($list, $message_conversation);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - message_conversation.php|get_conversations_by_user' . $e->getMessage();
		}

		return $list;

	}

	public function get_open_conversations($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from `message_conversation` 
				where status = 'open' ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$message_conversation = new message_conversation();
				$message_conversation->mapea($reg);

				array_push($list, $message_conversation);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - message_conversation.php|get_conversations_from_user' . $e->getMessage();
		}

		return $list;

	}

}

?>