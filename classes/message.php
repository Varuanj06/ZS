<?php

class message{

	//VARIABLES
	private $id_fb_user;
	private $id_message;
	private $message;
	private $date;
	private $from;
	private $read;
	private $id_message_conversation;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_fb_user 				= "";
		$this->id_message 				= "";
		$this->message 					= "";
		$this->date 					= "";
		$this->from 					= "";
		$this->read 					= "";
		$this->id_message_conversation 	= "";
	}

	//GETTERS AND SETTERS
	public function get_id_fb_user(){
		return $this->id_fb_user;
	}

	public function set_id_fb_user($id_fb_user){
		$this->id_fb_user = $id_fb_user;
	}

	public function get_id_message(){
		return $this->id_message;
	}

	public function set_id_message($id_message){
		$this->id_message = $id_message;
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

	public function get_read(){
		return $this->read;
	}

	public function set_read($read){
		$this->read = $read;
	}

	public function get_id_message_conversation(){
		return $this->id_message_conversation;
	}

	public function set_id_message_conversation($id_message_conversation){
		$this->id_message_conversation = $id_message_conversation;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into message(id_fb_user, id_message, message, date, `from`, `read`, id_message_conversation) 
				values (:id_fb_user, :id_message, :message, now(), :from, 'no', :id_message_conversation) ");

			$stmt->execute(array( 
				":id_fb_user" 				=> $this->id_fb_user, 
				":id_message" 				=> $this->id_message, 
				":message" 					=> $this->message,
				":from"						=> $this->from,
				":id_message_conversation"	=> $this->id_message_conversation 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - message.php|insert: ' . $e->getMessage();
		}
	}

	//UPDATE
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update message 
				set message     = :message,
				date      		= :date,
				`from` 	 	  	= :from,
				`read` 	 	  	= :read
				where id_fb_user 	= :id_fb_user
				and id_message 		= :id_message ");

			$stmt->execute( array( 
				":message" 		=> $this->message, 
				":date" 		=> $this->date, 
				":from"			=> $this->from,
				":read"			=> $this->read,
				":id_fb_user" 	=> $this->id_fb_user,
				":id_message" 	=> $this->id_message ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - message.php|update: ' . $e->getMessage();
		}
	}

	public function update_read_all($id_fb_user, $id_message_conversation, $from){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update message 
				set 
					`read` 	 	  				= 'yes'
				where id_fb_user 				= :id_fb_user
				and id_message_conversation 	= :id_message_conversation
				and `from` 	 	  				= :from ");

			$stmt->execute(array( 
				":id_fb_user" 				=> $id_fb_user,
				":id_message_conversation" 	=> $id_message_conversation,
				":from" 					=> $from
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - message.php|update_read_all: ' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_fb_user 				= $obj->id_fb_user;
		$this->id_message 				= $obj->id_message;
		$this->message 					= $obj->message;
		$this->date 					= $obj->date;
		$this->from 					= $obj->from;
		$this->read 					= $obj->read;
		$this->id_message_conversation 	= $obj->id_message_conversation;

	}

	public function map($id_fb_user, $id_message){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from message 
				where id_fb_user	= :id_fb_user
				and id_message 		= :id_message ");

			$stmt->execute(array( 
				":id_fb_user" => $id_fb_user,
				":id_message" => $id_message
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - message.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from message 
				where id_fb_user 	= :id_fb_user,
				and id_message 		= :id_message ");

			$stmt->execute(array( 
				":id_fb_user"  => $this->id_fb_user,
				":id_message"  => $this->id_message 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - message.php|delete: ' . $e->getMessage();
		}
	}


	//EXIST
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from message 
				where id_fb_user = :id_fb_user,
				and id_message 		= :id_message ");

			$stmt->execute(array( 
				":id_fb_user"  => $this->id_fb_user,
				":id_message"  => $this->id_message 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - message.php|exists' . $e->getMessage();
		}
	}

	//MAXIMUM
	public function max_id_message($id_fb_user){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select COALESCE(MAX(id_message)+1,1) AS maximo 
				from message
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
		    echo 'ERROR: - message.php|max_id_message' . $e->getMessage();
		}
	}

	public function map_last_by_conversation($id_fb_user, $id_message_conversation){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from message 
				where id_fb_user				= :id_fb_user
				and id_message_conversation 	= :id_message_conversation
				order by date desc
				limit 1 ");

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
		    echo 'ERROR: - message.php|map_last_by_conversation' . $e->getMessage();
		}
	}

	public function get_unread_messages($id_fb_user, $from){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select count(*) AS c
				from message
				where id_fb_user 	= :id_fb_user
				and `from` 			= :from
				and `read` 			= 'no' ");

			$stmt->execute(array( 
				":id_fb_user"  	=> $id_fb_user,
				":from"  		=> $from
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->c;
			}else{
				return 0;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - message.php|get_unread_messages' . $e->getMessage();
		}
	}

	public function get_unread_messages_by_conversation($id_fb_user, $from, $id_message_conversation){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select count(*) AS c
				from message
				where id_fb_user 			= :id_fb_user
				and `from` 					= :from
				and id_message_conversation = :id_message_conversation
				and `read` 					= 'no' ");

			$stmt->execute(array( 
				":id_fb_user"  					=> $id_fb_user,
				":from"  						=> $from,
				":id_message_conversation"  	=> $id_message_conversation
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->c;
			}else{
				return 0;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - message.php|get_unread_messages' . $e->getMessage();
		}
	}

	// List
	public function get_messages_by_user($id_fb_user, $id_message_conversation, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from `message` 
				where id_fb_user = :id_fb_user
				and id_message_conversation = :id_message_conversation ".$order);

			$stmt->execute(array(
				':id_fb_user' 				=> $id_fb_user,
				':id_message_conversation' 	=> $id_message_conversation
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$message = new message();
				$message->mapea($reg);

				array_push($list, $message);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - message.php|get_messages_by_user' . $e->getMessage();
		}

		return $list;

	}

	public function get_all_unread_messages($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				SELECT id_fb_user, id_message, message, date, `from`, `read`
				FROM message msg
				where `from` = 'user'
				and `read` = 'no'
				and id_message = (
					select max(id_message) 
				    from message 
				    where id_fb_user=msg.id_fb_user 
				    and `from` = 'user'
					and `read` = 'no'
				)
				group by id_fb_user ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$message = new message();
				$message->mapea($reg);

				array_push($list, $message);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - message.php|get_all_unread_messages' . $e->getMessage();
		}

		return $list;

	}


	/* ====================================================================== *
            HELPFUL METHODS!
     * ====================================================================== */


	public function get_auto_message($status_admin, $id_order, $courier_allocation){

		$message = "";

		if($status_admin == ""){
			$message = "Your order $id_order has been successfully placed. We will be shipping the product via $courier_allocation. Tracking number of the shipment will be given to you as soon as it shipped.";
		}else if($status_admin == "PROCESSING ORDER"){
			$message = "Your order $id_order is under processing today and will be shipped in the coming hours via $courier_allocation. Tracking number will be updated as soon as it is shipped.";
		}else if($status_admin == "ORDER SHIPPED"){
			$message = "Your order $id_order has been shipped via $courier_allocation. Tracking number will be updated soon.";
		}else if($status_admin == "ORDER PARTIALLY SHIPPED"){
			$message = "One or more products of your order $id_order has been shipped via $courier_allocation. Tracking number will be updated soon.";
		}else if($status_admin == "ORDER REFUSED"){
			$message = "Your order $id_order has been returned back to our ware house with the status \"Refused By The Customer\". Please contact customer care by replying back here if there is a mistake.";
		}else if($status_admin == "ORDER ON HOLD"){
			$message = "Your order $id_order has been put on hold. Please contact the customer care by replying back to this message for details.";
		}else if($status_admin == "ORDER CANCELLED"){
			$message = "Your order $id_order has been cancelled. If you are unaware of the reason, please contact the customer care here by replying back to this message.";
		}
		
		return $message;
	}

	public function send_auto_message($order, $id_order, $status_admin){

		$message = new message();
		$order->map($id_order);
		$id_fb_user_msg 	= $order->get_id_fb_user();
		$id_message 		= $message->max_id_message($id_fb_user_msg);
		$auto_msg 			= $message->get_auto_message($status_admin, $order->get_id_order(), $order->get_courier_allocation());
		$message->set_id_fb_user($id_fb_user_msg);
		$message->set_id_message($id_message);
		$message->set_message($auto_msg);
		$message->set_from('admin');
		$message->set_id_message_conversation("-1");

		// send auto SMS maybe here?

		if($message->insert()){
			return true;
		}else{
			return false;
		}
	}

	public function send_message_only($id_fb_user_msg, $msg){

		$message = new message();
		$id_message 		= $message->max_id_message($id_fb_user_msg);
		$message->set_id_fb_user($id_fb_user_msg);
		$message->set_id_message($id_message);
		$message->set_message($msg);
		$message->set_from('admin');
		$message->set_id_message_conversation("-1");

		if($message->insert()){
			return true;
		}else{
			return false;
		}
	}

	public function send_message($order, $address, $id_order, $msg){

		$message = new message();
		$order->map($id_order);
		$id_fb_user_msg 	= $order->get_id_fb_user();
		$id_message 		= $message->max_id_message($id_fb_user_msg);
		$message->set_id_fb_user($id_fb_user_msg);
		$message->set_id_message($id_message);
		$message->set_message($msg);
		$message->set_from('admin');
		$message->set_id_message_conversation("-1");

		$this->send_SMS_by_id_fb_user($id_fb_user_msg, $address);

		if($message->insert()){
			return true;
		}else{
			return false;
		}
	}

	public function send_SMS_by_order($order, $id_order, $order_address){

		global $config_SMS;
		
		/* #### GET THE ADDRESS USED IN THE ORDER #### */

		$order->map($id_order);
		$order_address->map($order->get_id_order_address(), $order->get_id_fb_user());

		/* #### SEND THE SMS #### */
		$this->send_SMS($order_address->get_mobile_number(), $config_SMS['auto_msg']);

	}

	public function send_SMS_by_id_fb_user($id_fb_user, $address){

		global $config_SMS;

		/* #### GET THE ADDRESS LAST USED BY THE FB USER #### */

		$addresses 			= $address->get_list($id_fb_user, " order by date_update desc ");
		$current_address 	= "";
		foreach ($addresses as $row){ 
			$current_address = $row->get_id_address();
			break;
		}
		$address->map($current_address, $id_fb_user);

		/* #### SEND THE SMS #### */

		$this->send_SMS($address->get_mobile_number(), $config_SMS['auto_msg']);
	}

	public function send_SMS_only($id_fb_user, $address, $msg){

		global $config_SMS;

		/* #### GET THE ADDRESS LAST USED BY THE FB USER #### */

		$addresses 			= $address->get_list($id_fb_user, " order by date_update desc ");
		$current_address 	= "";
		foreach ($addresses as $row){ 
			$current_address = $row->get_id_address();
			break;
		}
		$address->map($current_address, $id_fb_user);

		/* #### SEND THE SMS #### */

		$this->send_SMS($address->get_mobile_number(), $msg);
	}

	public function send_SMS_with_phone($mobile_number, $msg){

		global $config_SMS;

		/* #### SEND THE SMS #### */

		$this->send_SMS($mobile_number, $msg);
	}

	public function send_SMS($phones, $message){

		//echo "<script>alert('$phones - $message');</script>";

		global $config_SMS;

		if($phones == '')return;

		$authKey 		= $config_SMS['authKey']; //Your authentication key	
		$senderId 		= $config_SMS['senderId']; //Sender ID,While using route4 sender id should be 6 characters long.
		$route 			= $config_SMS['route']; //Define route 

		$mobileNumber 	= $phones; //Multiple mobiles numbers separated by comma
		$message 		= urlencode($message); //Your message to send, Add URL encoding here.

		//Prepare you post parameters
		$postData = array(
		    'authkey' 	=> $authKey,
		    'mobiles' 	=> $mobileNumber,
		    'message' 	=> $message,
		    'sender' 	=> $senderId,
		    'route' 	=> $route
		);

		// init the resource
		$ch = curl_init();
		curl_setopt_array($ch, array(
		    CURLOPT_URL => "https://control.msg91.com/api/sendhttp.php", //API URL
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_POST => true,
		    CURLOPT_POSTFIELDS => $postData
		    //,CURLOPT_FOLLOWLOCATION => true
		));

		//Ignore SSL certificate verification
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

		//get response
		$output = curl_exec($ch);

		//Print error if any
		if(curl_errno($ch)){
		    return 'error:' . curl_error($ch);
		}

		curl_close($ch);

		return $output;
	}

}

?>