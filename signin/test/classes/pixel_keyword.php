<?php

class pixel_keyword{
	//VARIABLES
	private $pixel_keyword;
	private $image;
	private $genders;
	private $ages;
	private $status;
	private $expiry_date;

	//CONSTRUCTOR
	public function __construct(){
		$this->pixel_keyword 		= "";
		$this->image 				= "";
		$this->genders 				= "";
		$this->ages 				= "";
		$this->status 				= "";
		$this->expiry_date 			= "";
	}

	//GETTERS AND SETTERS
	public function get_pixel_keyword(){
		return $this->pixel_keyword;
	}

	public function set_pixel_keyword($pixel_keyword){
		$this->pixel_keyword = $pixel_keyword;
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

	public function get_expiry_date(){
		return $this->expiry_date;
	}

	public function set_expiry_date($expiry_date){
		$this->expiry_date = $expiry_date;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into pixel_keyword(pixel_keyword, image, genders, ages, status, expiry_date) 
				values (:pixel_keyword, :image, :genders, :ages, :status, :expiry_date) ");

			$stmt->execute( array( 
				":pixel_keyword"	=> $this->pixel_keyword,
				":image"			=> $this->image,
				":genders"			=> $this->genders,
				":ages"				=> $this->ages,
				":status"			=> $this->status,
				":expiry_date"		=> $this->expiry_date ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - pixel_keyword.php|insert: ' . $e->getMessage();
		    return false;
		}
	}

	//UPDATE
	public function update($old_pixel_keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update pixel_keyword
				set
					pixel_keyword 			= :pixel_keyword,
					image  					= :image,
					genders  				= :genders,
					ages  					= :ages,
					status  				= :status,
					expiry_date  			= :expiry_date
				where pixel_keyword 			= :old_pixel_keyword ");

			$stmt->execute( array( 
				":pixel_keyword"		=> $this->pixel_keyword,
				":image"				=> $this->image,
				":genders"				=> $this->genders,
				":ages"					=> $this->ages,
				":status"				=> $this->status,
				":expiry_date"			=> $this->expiry_date,
				":old_pixel_keyword"	=> $old_pixel_keyword ) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - pixel_keyword.php|update: ' . $e->getMessage();
		}
	}

	//GET IMAGE
	public function get_image_from_pixel_keyword($pixel_keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select image 
				from pixel_keyword
				where pixel_keyword = :pixel_keyword");

			$stmt->execute(array(
					":pixel_keyword" => $pixel_keyword
				));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->image;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - pixel_keyword.php|get_image_from_pixel_keyword' . $e->getMessage();
		    return false;
		}
	}

	//SELECT
	public function mapea($obj){

		$this->pixel_keyword 	= $obj->pixel_keyword;
		$this->image  			= $obj->image;
		$this->genders  		= $obj->genders;
		$this->ages  			= $obj->ages;
		$this->status  			= $obj->status;
		$this->expiry_date  	= $obj->expiry_date;

	}

	public function map($pixel_keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from pixel_keyword
				where pixel_keyword 	= :pixel_keyword");

			$stmt->execute(array( ":pixel_keyword" => $pixel_keyword ));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - pixel_keyword.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from pixel_keyword 
				where pixel_keyword 	= :pixel_keyword");

			$stmt->execute( array( ":pixel_keyword"  	=> $this->pixel_keyword) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - pixel_keyword.php|delete: ' . $e->getMessage();
		    return false;
		}
	}

	//EXISTS?
	public function exists($pixel_keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from pixel_keyword 
				where pixel_keyword 	= :pixel_keyword ");

			$stmt->execute( array( ":pixel_keyword" => $pixel_keyword ) );

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - pixel_keyword.php|exists' . $e->getMessage();
		}
	}

	//LIST
	public function get_list($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from pixel_keyword ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$pixel_keyword = new pixel_keyword();
				$pixel_keyword->mapea($reg);

				array_push($list, $pixel_keyword);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - pixel_keyword.php|get_list' . $e->getMessage();
		}

		return $list;

	}

	public function get_all_keywords($gender, $age, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from pixel_keyword 
				where genders like :gender
				and (ages like :age or ages like '%/all/%')
				and status = 'active'
				and expiry_date >= now() ".$order);

			$stmt->execute( array( ":gender" => "%$gender%", ":age" => "%$age%"  ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$pixel_keyword = new pixel_keyword();
				$pixel_keyword->mapea($reg);

				array_push($list, $pixel_keyword);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - pixel_keyword.php|get_all_keywords' . $e->getMessage();
		}

		return $list;

	}

}

?>






