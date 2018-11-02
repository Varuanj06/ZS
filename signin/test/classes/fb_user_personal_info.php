<?php

class fb_user_personal_info{
	//VARIABLES
	private $id_fb_user;
	private $name;
	private $last_name;
	private $gender;
	private $birthday;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_fb_user 		= "";
		$this->name 			= "";
		$this->last_name 		= "";
		$this->gender 			= "";
		$this->birthday 		= "";
	}

	//GETTERS AND SETTERS
	public function get_id_fb_user(){
		return $this->id_fb_user;
	}

	public function set_id_fb_user($id_fb_user){
		$this->id_fb_user = $id_fb_user;
	}

	public function get_name(){
		return $this->name;
	}

	public function set_name($name){
		$this->name = $name;
	}

	public function get_last_name(){
		return $this->last_name;
	}

	public function set_last_name($last_name){
		$this->last_name = $last_name;
	}

	public function get_gender(){
		return $this->gender;
	}

	public function set_gender($gender){
		$this->gender = $gender;
	}

	public function get_birthday(){
		return $this->birthday;
	}

	public function set_birthday($birthday){
		$this->birthday = $birthday;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into fb_user_personal_info(id_fb_user, name, last_name, gender, birthday) 
				values (:id_fb_user, :name, :last_name, :gender, :birthday) ");

			$stmt->execute( array( 
				":id_fb_user"		=> $this->id_fb_user,
				":name"				=> $this->name,
				":last_name"		=> $this->last_name,
				":gender"			=> $this->gender,
				":birthday"			=> $this->birthday,
			) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - fb_user_personal_info.php|insert: ' . $e->getMessage();
		    return false;
		}
	}

	//UPDATE
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update fb_user_personal_info
				set
					name  					= :name,
					last_name 				= :last_name,
					gender 					= :gender,
					birthday 				= :birthday
				where id_fb_user 			= :id_fb_user ");

			$stmt->execute(array( 
				":name"					=> $this->name,
				":last_name"			=> $this->last_name,
				":gender"				=> $this->gender,
				":birthday"				=> $this->birthday,
				":id_fb_user" 			=> $this->id_fb_user 
			));

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - fb_user_personal_info.php|update: ' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_fb_user  			= $obj->id_fb_user;
		$this->name  				= $obj->name;
		$this->last_name  			= $obj->last_name;
		$this->gender  				= $obj->gender;
		$this->birthday  			= $obj->birthday;

	}

	public function map($id_fb_user){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from fb_user_personal_info
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
		    echo 'ERROR: - fb_user_personal_info.php|map' . $e->getMessage();
		    return false;
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from fb_user_personal_info 
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
		    echo 'ERROR - fb_user_personal_info.php|delete: ' . $e->getMessage();
		    return false;
		}
	}

	//EXISTS?
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from fb_user_personal_info 
				where id_fb_user = :id_fb_user ");

			$stmt->execute( array( ":id_fb_user" => $this->id_fb_user ) );

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - fb_user_personal_info.php|exists' . $e->getMessage();
		}
	}

	//LIST
	public function get_list($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from fb_user_personal_info ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$fb_user_personal_info = new fb_user_personal_info();
				$fb_user_personal_info->mapea($reg);

				array_push($list, $fb_user_personal_info);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - fb_user_personal_info.php|get_list' . $e->getMessage();
		}

		return $list;

	}

}

?>






