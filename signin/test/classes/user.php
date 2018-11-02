<?php

class user{
	//VARIABLES
	private $user_id;
	private $user;
	private $password;
	private $name;

	//CONSTRUCTOR
	public function __construct(){
		$this->user 	= "";
		$this->password = "";
		$this->name 	= "";
	}

	//GETTERS AND SETTERS
	public function getUser_id(){
		return $this->user_id;
	}

	public function setUser_id($user_id){
		$this->user_id = $user_id;
	}

	public function getUser(){
		return $this->user;
	}

	public function setUser($user){
		$this->user = $user;
	}

	public function getPassword(){
		return $this->password;
	}

	public function setPassword($password){
		$this->password = $password;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into user(user, password, name) 
				values (:user, :password, :name) ");

			$stmt->execute( array( 
				":user" 	=> $this->user, 
				":password" => $this->password, 
				":name"		=> $this->name ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - user.php|insert: ' . $e->getMessage();
		}
	}

	//UPDATE
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update user 
				set user      = :user,
				password      = :password,
				name 	 	  = :name
				where user_id = :user_id ");

			$stmt->execute( array( 
				":user" 	=> $this->user, 
				":password" => $this->password, 
				":name"		=> $this->name,
				":user_id"  => $this->user_id ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - user.php|update: ' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->user_id  = $obj->user_id;
		$this->user 	= $obj->user;
		$this->password = $obj->password;
		$this->name 	= $obj->name;

	}

	public function map($user_id){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from user 
				where user_id = :user_id ");

			$stmt->execute( array( ":user_id" => $user_id ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - user.php|map' . $e->getMessage();
		}
	}

	public function mapByUser($user){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from user 
				where user = :user ");

			$stmt->execute( array( ":user" => $user ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - user.php|mapByUser' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from user 
				where user_id = :user_id");

			$stmt->execute( array( ":user_id"  => $this->user_id ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - user.php|delete: ' . $e->getMessage();
		}
	}


	//EXIST
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from user 
				where user_id = :user_id ");

			$stmt->execute( array( ":user_id" => $this->user_id ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - user.php|exists' . $e->getMessage();
		}
	}

	public function existsByUser($user, $user_id){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from user 
				where user = :user and user_id != :user_id ");

			$stmt->execute( array( ":user" => $user, 
								   ":user_id" => $user_id ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - user.php|existsByUser' . $e->getMessage();
		}
	}

	//MAXIMUM
	public function maxUserId(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select MAX(user_id)+1 AS maximo 
				from user ");

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->maximo;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - user.php|maxUserId' . $e->getMessage();
		}
	}



}

?>






