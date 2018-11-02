<?php

class fb_user_blacklist{

	//VARIABLES
	private $id_fb_user;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_fb_user 		= "";
	}

	//GETTERS AND SETTERS
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
				insert into fb_user_blacklist(id_fb_user) 
				values (:id_fb_user) ");

			$stmt->execute( array( 
				":id_fb_user"			=> $this->id_fb_user
			) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - fb_user_blacklist.php|insert: ' . $e->getMessage();
		    return false;
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from fb_user_blacklist 
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
		    echo 'ERROR - fb_user_blacklist.php|delete: ' . $e->getMessage();
		    return false;
		}
	}

	//EXISTS?
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from fb_user_blacklist 
				where id_fb_user = :id_fb_user ");

			$stmt->execute( array( ":id_fb_user" => $this->id_fb_user ) );

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - fb_user_blacklist.php|exists' . $e->getMessage();
		}
	}

	//LIST
	public function get_list($id_fb_user, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from fb_user_blacklist ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$fb_user_blacklist = new fb_user_blacklist();
				$fb_user_blacklist->mapea($reg);

				array_push($list, $fb_user_blacklist);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - fb_user_blacklist.php|get_list' . $e->getMessage();
		}

		return $list;

	}

}

?>






