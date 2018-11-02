<?php

class shopping_assistant{

	//VARIABLES
	private $id_fb_user;
	private $id_shopping_assistant;
	private $message;
	private $date;
	private $from;
	private $id_shopping_assistant_conversation;
	private $step;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_fb_user 							= "";
		$this->id_shopping_assistant 				= "";
		$this->message 								= "";
		$this->date 								= "";
		$this->from 								= "";
		$this->id_shopping_assistant_conversation 	= "";
		$this->step  								= "";
	}

	//GETTERS AND SETTERS
	public function get_id_fb_user(){
		return $this->id_fb_user;
	}

	public function set_id_fb_user($id_fb_user){
		$this->id_fb_user = $id_fb_user;
	}

	public function get_id_shopping_assistant(){
		return $this->id_shopping_assistant;
	}

	public function set_id_shopping_assistant($id_shopping_assistant){
		$this->id_shopping_assistant = $id_shopping_assistant;
	}

	public function get_message(){
		return $this->message;
	}

	public function set_message($message){
		$this->message = $message;
	}

	public function get_date(){
		return $this->date;
	}

	public function set_date($date){
		$this->date = $date;
	}

	public function get_from(){
		return $this->from;
	}

	public function set_from($from){
		$this->from = $from;
	}

	public function get_id_shopping_assistant_conversation(){
		return $this->id_shopping_assistant_conversation;
	}

	public function set_id_shopping_assistant_conversation($id_shopping_assistant_conversation){
		$this->id_shopping_assistant_conversation = $id_shopping_assistant_conversation;
	}

	public function get_step(){
		return $this->step;
	}

	public function set_step($step){
		$this->step = $step;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into shopping_assistant(id_fb_user, id_shopping_assistant, message, date, `from`, id_shopping_assistant_conversation, step) 
				values (:id_fb_user, :id_shopping_assistant, :message, now(), :from, :id_shopping_assistant_conversation, :step) ");

			$stmt->execute(array( 
				":id_fb_user" 							=> $this->id_fb_user, 
				":id_shopping_assistant" 				=> $this->id_shopping_assistant, 
				":message" 								=> $this->message,
				":from"									=> $this->from,
				":id_shopping_assistant_conversation"	=> $this->id_shopping_assistant_conversation,
				":step"									=> $this->step
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - shopping_assistant.php|insert: ' . $e->getMessage();
		}
	}

	//UPDATE
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update shopping_assistant 
				set message     			= :message,
				date      					= :date,
				`from` 	 	  				= :from
				where id_fb_user 			= :id_fb_user
				and id_shopping_assistant 	= :id_shopping_assistant ");

			$stmt->execute( array( 
				":message" 					=> $this->message, 
				":date" 					=> $this->date, 
				":from"						=> $this->from,
				":id_fb_user" 				=> $this->id_fb_user,
				":id_shopping_assistant" 	=> $this->id_shopping_assistant ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - shopping_assistant.php|update: ' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_fb_user 							= $obj->id_fb_user;
		$this->id_shopping_assistant 				= $obj->id_shopping_assistant;
		$this->message 								= $obj->message;
		$this->date 								= $obj->date;
		$this->from 								= $obj->from;
		$this->id_shopping_assistant_conversation 	= $obj->id_shopping_assistant_conversation;
		$this->step 								= $obj->step;

	}

	public function map($id_fb_user, $id_shopping_assistant){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from shopping_assistant 
				where id_fb_user				= :id_fb_user
				and id_shopping_assistant 		= :id_shopping_assistant ");

			$stmt->execute(array( 
				":id_fb_user" 				=> $id_fb_user,
				":id_shopping_assistant" 	=> $id_shopping_assistant
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - shopping_assistant.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from shopping_assistant 
				where id_fb_user 				= :id_fb_user,
				and id_shopping_assistant 		= :id_shopping_assistant ");

			$stmt->execute(array( 
				":id_fb_user" 	 			=> $this->id_fb_user,
				":id_shopping_assistant"  	=> $this->id_shopping_assistant 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - shopping_assistant.php|delete: ' . $e->getMessage();
		}
	}


	//EXIST
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from shopping_assistant 
				where id_fb_user 				= :id_fb_user,
				and id_shopping_assistant 		= :id_shopping_assistant ");

			$stmt->execute(array( 
				":id_fb_user"  				=> $this->id_fb_user,
				":id_shopping_assistant"  	=> $this->id_shopping_assistant 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - shopping_assistant.php|exists' . $e->getMessage();
		}
	}

	//MAXIMUM
	public function max_id_shopping_assistant($id_fb_user){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select COALESCE(MAX(id_shopping_assistant)+1,1) AS maximo 
				from shopping_assistant
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
		    echo 'ERROR: - shopping_assistant.php|max_id_shopping_assistant' . $e->getMessage();
		}
	}

	public function map_last_by_conversation($id_fb_user, $id_shopping_assistant_conversation){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from shopping_assistant 
				where id_fb_user						= :id_fb_user
				and id_shopping_assistant_conversation 	= :id_shopping_assistant_conversation
				and `from` 								= 'user'
				order by id_shopping_assistant desc
				limit 1 ");

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
		    echo 'ERROR: - shopping_assistant.php|map_last_by_conversation' . $e->getMessage();
		}
	}

	// List
	public function get_messages_by_user($id_fb_user, $id_shopping_assistant_conversation, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from `shopping_assistant` 
				where id_fb_user 						= :id_fb_user
				and id_shopping_assistant_conversation = :id_shopping_assistant_conversation ".$order);

			$stmt->execute(array(
				':id_fb_user' 							=> $id_fb_user,
				':id_shopping_assistant_conversation' 	=> $id_shopping_assistant_conversation
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$shopping_assistant = new shopping_assistant();
				$shopping_assistant->mapea($reg);

				array_push($list, $shopping_assistant);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - shopping_assistant.php|get_messages_by_user' . $e->getMessage();
		}

		return $list;

	}

}

?>