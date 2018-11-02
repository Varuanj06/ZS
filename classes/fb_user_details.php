<?php

class fb_user_details{
	//VARIABLES
	private $id_fb_user;
	private $email;
	private $mobile_number;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_fb_user 		= "";
		$this->email 			= "";
		$this->mobile_number 	= "";
	}

	//GETTERS AND SETTERS
	public function get_id_fb_user(){
		return $this->id_fb_user;
	}

	public function set_id_fb_user($id_fb_user){
		$this->id_fb_user = $id_fb_user;
	}

	public function get_email(){
		return $this->email;
	}

	public function set_email($email){
		$this->email = $email;
	}

	public function get_mobile_number(){
		return $this->mobile_number;
	}

	public function set_mobile_number($mobile_number){
		$this->mobile_number = $mobile_number;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into fb_user_details(id_fb_user, email, mobile_number) 
				values (:id_fb_user, :email, :mobile_number) ");

			$stmt->execute( array( 
				":id_fb_user"			=> $this->id_fb_user,
				":email"				=> $this->email,
				":mobile_number"		=> $this->mobile_number,
			) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - fb_user_details.php|insert: ' . $e->getMessage();
		    return false;
		}
	}

	//UPDATE
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update fb_user_details
				set
					email  					= :email,
					mobile_number 			= :mobile_number
				where id_fb_user 				= :id_fb_user ");

			$stmt->execute( array( 
				":email"					=> $this->email,
				":mobile_number"			=> $this->mobile_number,
				":id_fb_user" 				=> $this->id_fb_user ) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - fb_user_details.php|update: ' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_fb_user  			= $obj->id_fb_user;
		$this->email  				= $obj->email;
		$this->mobile_number  		= $obj->mobile_number;

	}

	public function map($id_fb_user){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from fb_user_details
				where id_fb_user 		= :id_fb_user ");

			$stmt->execute( array( 
				":id_fb_user" => $id_fb_user
			) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - fb_user_details.php|map' . $e->getMessage();
		    return false;
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from fb_user_details 
				where id_fb_user 		= :id_fb_user ");

			$stmt->execute( array( 
				":id_fb_user"  	=> $this->id_fb_user
			) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - fb_user_details.php|delete: ' . $e->getMessage();
		    return false;
		}
	}

	//EXISTS?
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from fb_user_details 
				where id_fb_user = :id_fb_user ");

			$stmt->execute( array( ":id_fb_user" => $this->id_fb_user ) );

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - fb_user_details.php|exists' . $e->getMessage();
		}
	}

	//LIST
	public function get_list($id_fb_user, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from fb_user_details
				where id_fb_user = :id_fb_user ".$order);

			$stmt->execute(array(
				":id_fb_user" => $id_fb_user
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$fb_user_details = new fb_user_details();
				$fb_user_details->mapea($reg);

				array_push($list, $fb_user_details);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - fb_user_details.php|get_list' . $e->getMessage();
		}

		return $list;

	}

}

?>






