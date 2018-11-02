<?php

class pixel_count{
	//VARIABLES
	private $id_pixel;
	private $id_fb_user;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_pixel 				= "";
		$this->id_fb_user 				= "";
	}

	//GETTERS AND SETTERS
	public function get_id_pixel(){
		return $this->id_pixel;
	}

	public function set_id_pixel($id_pixel){
		$this->id_pixel = $id_pixel;
	}

	public function get_id_fb_user(){
		return $this->id_fb_user;
	}

	public function set_id_fb_user($id_fb_user){
		$this->id_fb_user = $id_fb_user;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into pixel_count(id_pixel, id_fb_user) 
				values (:id_pixel, :id_fb_user) ");

			$stmt->execute( array( 
				":id_pixel"			=> $this->id_pixel,
				":id_fb_user"		=> $this->id_fb_user ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - pixel_count.php|insert: ' . $e->getMessage();
		    return false;
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_pixel  	= $obj->id_pixel;
		$this->id_fb_user  	= $obj->id_fb_user;

	}

	public function map($id_pixel, $id_fb_user){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from pixel_count
				where id_pixel 	= :id_pixel
				and id_fb_user 		= :id_fb_user ");

			$stmt->execute( array( 
				":id_pixel" => $id_pixel,
				":id_fb_user" => $id_fb_user ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - pixel_count.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from pixel_count 
				where id_pixel 		= :id_pixel
				and id_fb_user 		= :id_fb_user ");

			$stmt->execute( array( 
				":id_pixel"  	=> $this->id_pixel,
				":id_fb_user" 	=> $this->id_fb_user ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - pixel_count.php|delete: ' . $e->getMessage();
		    return false;
		}
	}

	//EXISTS?
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from pixel_count 
				where id_pixel 		= :id_pixel
				and id_fb_user 		= :id_fb_user ");

			$stmt->execute( array( 
					":id_pixel" 	=> $this->id_pixel,
					":id_fb_user" 	=> $this->id_fb_user ) );

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - pixel_count.php|exists' . $e->getMessage();
		}
	}

	//LIST
	public function get_list($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from pixel_count ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$pixel_count = new pixel_count();
				$pixel_count->mapea($reg);

				array_push($list, $pixel_count);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - pixel_count.php|get_list' . $e->getMessage();
		}

		return $list;

	}

	public function get_users_by_pixel($id_pixel, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from pixel_count
				where id_pixel = :id_pixel ".$order);

			$stmt->execute(array( 
					":id_pixel" 	=> $id_pixel
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$pixel_count = new pixel_count();
				$pixel_count->mapea($reg);

				array_push($list, $pixel_count);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - pixel_count.php|get_users_by_pixel' . $e->getMessage();
		}

		return $list;

	}

}

?>






