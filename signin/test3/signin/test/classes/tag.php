<?php

class tag{
	//VARIABLES
	private $id_tag;
	private $name;
	private $id_tag_super;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_tag 			= "";
		$this->name 			= "";
		$this->id_tag_super 	= "";
	}

	//GETTERS AND SETTERS
	public function get_id_tag(){
		return $this->id_tag;
	}

	public function set_id_tag($id_tag){
		$this->id_tag = $id_tag;
	}

	public function get_name(){
		return $this->name;
	}

	public function set_name($name){
		$this->name = $name;
	}

	public function get_id_tag_super(){
		return $this->id_tag_super;
	}

	public function set_id_tag_super($id_tag_super){
		$this->id_tag_super = $id_tag_super;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into tag(name, id_tag_super) 
				values (:name, :id_tag_super) ");

			$stmt->execute(array( 
				":name"				=> $this->name,
				":id_tag_super"		=> $this->id_tag_super 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo "string"; 'ERROR - tag.php|insert: ' . $e->getMessage();
		}
	}

	//UPDATE
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update tag 
					set name 	 	  	= :name,
					id_tag_super 	 	= :id_tag_super
				where id_tag 	= :id_tag ");

			$stmt->execute(array( 
				":name"				=> $this->name,
				":id_tag_super" 	=> $this->id_tag_super,
				":id_tag" 			=> $this->id_tag 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - tag.php|update: ' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_tag  			= $obj->id_tag;
		$this->name 			= $obj->name;
		$this->id_tag_super 	= $obj->id_tag_super;

	}

	public function map($id_tag){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from tag 
				where id_tag = :id_tag ");

			$stmt->execute( array( ":id_tag" => $id_tag ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - tag.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from tag 
				where id_tag = :id_tag");

			$stmt->execute(array( ":id_tag"  => $this->id_tag ));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - tag.php|delete: ' . $e->getMessage();
		}
	}

	//EXISTS?
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from tag 
				where id_tag = :id_tag ");

			$stmt->execute( array( ":id_tag" => $this->id_tag ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - tag.php|exists' . $e->getMessage();
		}
	}

	//LIST
	public function get_all($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from tag ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$tag = new tag();
				$tag->mapea($reg);

				array_push($list, $tag);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - tag.php|get_all' . $e->getMessage();
		}

		return $list;

	}

	public function get_list_by_tag_super($id_tag_super, $order){

	global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from tag
				where id_tag_super = '$id_tag_super' ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$tag = new tag();
				$tag->mapea($reg);

				array_push($list, $tag);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - tag.php|get_all' . $e->getMessage();
		}

		return $list;

	}

}

?>