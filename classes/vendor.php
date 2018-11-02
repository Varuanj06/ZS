<?php

class vendor{
	//VARIABLES
	private $id_vendor;
	private $name;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_vendor 	= "";
		$this->name 		= "";
	}

	//GETTERS AND SETTERS
	public function get_id_vendor(){
		return $this->id_vendor;
	}

	public function set_id_vendor($id_vendor){
		$this->id_vendor = $id_vendor;
	}

	public function get_name(){
		return $this->name;
	}

	public function set_name($name){
		$this->name = $name;
	}

	//INSERT
	public function insert(){
		global $conn_reports;

		try {

			$stmt = $conn_reports->prepare("
				insert into vendor(name) 
				values (:name) ");

			$stmt->execute( array( 
				":name"		=> $this->name ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo "string"; 'ERROR - vendor.php|insert: ' . $e->getMessage();
		}
	}

	//UPDATE
	public function update(){
		global $conn_reports;

		try {

			$stmt = $conn_reports->prepare("
				update vendor 
				set name 	 	  	= :name
				where id_vendor 	= :id_vendor ");

			$stmt->execute( array( 
				":name"			=> $this->name,
				":id_vendor" 	=> $this->id_vendor ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - vendor.php|update: ' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_vendor  	= $obj->id_vendor;
		$this->name 		= $obj->name;

	}

	public function map($id_vendor){
		global $conn_reports;

		try {

			$stmt = $conn_reports->prepare("
				select * 
				from vendor 
				where id_vendor = :id_vendor ");

			$stmt->execute( array( ":id_vendor" => $id_vendor ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - vendor.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn_reports;

		try {

			$stmt = $conn_reports->prepare("
				delete from vendor 
				where id_vendor = :id_vendor");

			$stmt->execute( array( ":id_vendor"  => $this->id_vendor ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - vendor.php|delete: ' . $e->getMessage();
		}
	}

	//EXISTS?
	public function exists(){
		global $conn_reports;

		try {

			$stmt = $conn_reports->prepare("
				select * 
				from vendor 
				where id_vendor = :id_vendor ");

			$stmt->execute( array( ":id_vendor" => $this->id_vendor ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - vendor.php|exists' . $e->getMessage();
		}
	}

	public function exists_by_name($name, $id_vendor){
		global $conn_reports;

		try {

			$stmt = $conn_reports->prepare("
				select * 
				from vendor 
				where name = :name and id_vendor != :id_vendor ");

			$stmt->execute( array( ":name" => $name, 
								   ":id_vendor" => $id_vendor ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - vendor.php|exists_by_name' . $e->getMessage();
		}
	}

	//LIST
	public function get_all($order){

		global $conn_reports;

		$list = array();

		try {

			$stmt = $conn_reports->prepare("
				select * 
				from vendor ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$vendor = new vendor();
				$vendor->mapea($reg);

				array_push($list, $vendor);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - vendor.php|get_all' . $e->getMessage();
		}

		return $list;

	}

}

?>






