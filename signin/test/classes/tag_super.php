<?php

class tag_super{
	//VARIABLES
	private $id_tag_super;
	private $name;
	private $gender;
	private $image;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_tag_super 	= "";
		$this->name 			= "";
		$this->gender 			= "";
		$this->image 		= "";
	}

	//GETTERS AND SETTERS
	public function get_id_tag_super(){
		return $this->id_tag_super;
	}

	public function set_id_tag_super($id_tag_super){
		$this->id_tag_super = $id_tag_super;
	}

	public function get_name(){
		return $this->name;
	}

	public function set_name($name){
		$this->name = $name;
	}

	public function get_gender(){
		return $this->gender;
	}

	public function set_gender($gender){
		$this->gender = $gender;
	}

	public function get_image(){
		return $this->image;
	}

	public function set_image($image){
		$this->image = $image;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into tag_super(name, gender, image) 
				values (:name, :gender, :image) ");

			$stmt->execute(array( 
				":name"			=> $this->name,
				":gender"		=> $this->gender,
				":image"		=> $this->image
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo "string"; 'ERROR - tag_super.php|insert: ' . $e->getMessage();
		}
	}

	//UPDATE
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update tag_super 
				set name 	 	  	= :name,
				gender 				= :gender,
				image 				= :image
				where id_tag_super 	= :id_tag_super ");

			$stmt->execute( array( 
				":name"				=> $this->name,
				":gender"			=> $this->gender,
				":image"			=> $this->image,
				":id_tag_super" 	=> $this->id_tag_super ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - tag_super.php|update: ' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_tag_super  	= $obj->id_tag_super;
		$this->name 			= $obj->name;
		$this->gender 			= $obj->gender;
		$this->image 			= $obj->image;

	}

	public function map($id_tag_super){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from tag_super 
				where id_tag_super = :id_tag_super ");

			$stmt->execute( array( ":id_tag_super" => $id_tag_super ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - tag_super.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from tag_super 
				where id_tag_super = :id_tag_super");

			$stmt->execute( array( ":id_tag_super"  => $this->id_tag_super ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - tag_super.php|delete: ' . $e->getMessage();
		}
	}

	//EXISTS?
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from tag_super 
				where id_tag_super = :id_tag_super ");

			$stmt->execute( array( ":id_tag_super" => $this->id_tag_super ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - tag_super.php|exists' . $e->getMessage();
		}
	}

	//LIST
	public function get_all($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from tag_super ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$tag_super = new tag_super();
				$tag_super->mapea($reg);

				array_push($list, $tag_super);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - tag_super.php|get_all' . $e->getMessage();
		}

		return $list;

	}

	public function get_all_by_gender($gender, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from tag_super
				where gender = :gender ".$order);

			$stmt->execute(array(
				"gender" => $gender
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$tag_super = new tag_super();
				$tag_super->mapea($reg);

				array_push($list, $tag_super);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - tag_super.php|get_all' . $e->getMessage();
		}

		return $list;

	}

}

?>
