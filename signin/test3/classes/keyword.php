<?php

class keyword{
	//VARIABLES
	private $keyword;
	private $image;
	private $genders;
	private $ages;
	private $status;
	private $profiles;
	private $description;
	private $discount_type;
	private $discount;
	private $global;

	//CONSTRUCTOR
	public function __construct(){
		$this->keyword 				= "";
		$this->image 				= "";
		$this->genders 				= "";
		$this->ages 				= "";
		$this->status 				= "";
		$this->profiles 			= "";
		$this->description 			= "";
		$this->discount_type 		= "";
		$this->discount 			= "";
		$this->global 			= "";
	}

	//GETTERS AND SETTERS
	public function get_keyword(){
		return $this->keyword;
	}

	public function set_keyword($keyword){
		$this->keyword = $keyword;
	}

	public function get_image(){
		return $this->image;
	}

	public function set_image($image){
		$this->image = $image;
	}

	public function get_genders(){
		return $this->genders;
	}

	public function set_genders($genders){
		$this->genders = $genders;
	}

	public function get_ages(){
		return $this->ages;
	}

	public function set_ages($ages){
		$this->ages = $ages;
	}

	public function get_status(){
		return $this->status;
	}

	public function set_status($status){
		$this->status = $status;
	}

	public function get_profiles(){
		return $this->profiles;
	}

	public function set_profiles($profiles){
		$this->profiles = $profiles;
	}

	public function get_description(){
		return $this->description;
	}

	public function set_description($description){
		$this->description = $description;
	}

	public function get_discount_type(){
		return $this->discount_type;
	}

	public function set_discount_type($discount_type){
		$this->discount_type = $discount_type;
	}

	public function get_discount(){
		return $this->discount;
	}

	public function set_discount($discount){
		$this->discount = $discount;
	}

	public function get_global(){
		return $this->global;
	}

	public function set_global($global){
		$this->global = $global;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into keyword(keyword, image, genders, ages, status, profiles, description, discount_type, discount, global) 
				values (:keyword, :image, :genders, :ages, :status, :profiles, :description, :discount_type, :discount, :global) ");

			$stmt->execute(array( 
				":keyword"			=> $this->keyword,
				":image"			=> $this->image,
				":genders"			=> $this->genders,
				":ages"				=> $this->ages,
				":status"			=> $this->status, 
				":profiles"			=> $this->profiles,
				":description"		=> $this->description,
				":discount_type"	=> $this->discount_type,
				":discount"			=> $this->discount,
				":global"			=> $this->global
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - keyword.php|insert: ' . $e->getMessage();
		    return false;
		}
	}

	//UPDATE
	public function update($old_keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update keyword
				set
					keyword 			= :keyword,
					image  				= :image,
					genders  			= :genders,
					ages  				= :ages,
					status  			= :status,
					profiles  			= :profiles,
					description  		= :description,
					discount_type  		= :discount_type,
					discount  			= :discount,
					global  			= :global
				where keyword 			= :old_keyword ");

			$stmt->execute(array( 
				":keyword"				=> $this->keyword,
				":image"				=> $this->image,
				":genders"				=> $this->genders,
				":ages"					=> $this->ages,
				":status"				=> $this->status,
				":profiles"				=> $this->profiles,
				":description"			=> $this->description,
				":discount_type"		=> $this->discount_type,
				":discount"				=> $this->discount,
				":global"				=> $this->global,
				":old_keyword"			=> $old_keyword 
			));

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - update.php|update: ' . $e->getMessage();
		}
	}

	//GET IMAGE
	public function get_image_from_keyword($keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select image 
				from keyword
				where keyword = :keyword");

			$stmt->execute(array(
					":keyword" => $keyword
				));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->image;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - keyword.php|get_image_from_keyword' . $e->getMessage();
		    return false;
		}
	}

	public function get_profiles_from_keyword($keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select profiles
				from keyword
				where keyword = :keyword");

			$stmt->execute(array(
					":keyword" => $keyword
				));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->profiles;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - keyword.php|get_profile_from_keyword' . $e->getMessage();
		    return false;
		}
	}

	//SELECT
	public function mapea($obj){

		$this->keyword  	= $obj->keyword;
		$this->image  		= $obj->image;
		$this->genders  	= $obj->genders;
		$this->ages  		= $obj->ages;
		$this->status  		= $obj->status;
		$this->profiles  	= $obj->profiles;
		$this->description  = $obj->description;
		$this->discount_type= $obj->discount_type;
		$this->discount  	= $obj->discount;
		$this->global  		= $obj->global;

	}

	public function map($keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from keyword
				where keyword 	= :keyword");

			$stmt->execute(array( ":keyword" => $keyword ));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - keyword.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from keyword 
				where keyword 	= :keyword");

			$stmt->execute( array( ":keyword"  	=> $this->keyword) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - keyword.php|delete: ' . $e->getMessage();
		    return false;
		}
	}

	//EXISTS?
	public function exists($keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from keyword 
				where keyword 	= :keyword ");

			$stmt->execute( array( ":keyword" => $keyword ) );

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - keyword.php|exists' . $e->getMessage();
		}
	}

	//LIST
	public function get_list($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from keyword ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$keyword = new keyword();
				$keyword->mapea($reg);

				array_push($list, $keyword);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - keyword.php|get_list' . $e->getMessage();
		}

		return $list;

	}
}

?>






