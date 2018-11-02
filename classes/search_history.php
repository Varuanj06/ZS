<?php

class search_history{
	//VARIABLES
	private $id_search_history;
	private $id_fb_user;
	private $keyword;
	private $results_count;
	private $date;
	private $name_fb_user;
	private $last_name_fb_user;
	private $gender_fb_user;
	private $birthday_fb_user;

	//CONSTRUCTOR
	public function search_history(){
		$this->id_search_history 		= "";
		$this->id_fb_user 				= "";
		$this->keyword 					= "";
		$this->results_count 			= "";
		$this->date 					= "";
		$this->name_fb_user 			= "";
		$this->last_name_fb_user 		= "";
		$this->gender_fb_user 			= "";
		$this->birthday_fb_user 		= "";
	}

	//GETTERS AND SETTERS
	public function get_id_search_history(){
		return $this->id_search_history;
	}

	public function set_id_search_history($id_search_history){
		$this->id_search_history = $id_search_history;
	}

	public function get_id_fb_user(){
		return $this->id_fb_user;
	}

	public function set_id_fb_user($id_fb_user){
		$this->id_fb_user = $id_fb_user;
	}

	public function get_keyword(){
		return $this->keyword;
	}

	public function set_keyword($keyword){
		$this->keyword = $keyword;
	}

	public function get_results_count(){
		return $this->results_count;
	}

	public function set_results_count($results_count){
		$this->results_count = $results_count;
	}

	public function get_date(){
		return $this->date;
	}

	public function set_date($date){
		$this->date = $date;
	}

	public function get_name_fb_user(){
		return $this->name_fb_user;
	}

	public function set_name_fb_user($name_fb_user){
		$this->name_fb_user = $name_fb_user;
	}

	public function get_last_name_fb_user(){
		return $this->last_name_fb_user;
	}

	public function set_last_name_fb_user($last_name_fb_user){
		$this->last_name_fb_user = $last_name_fb_user;
	}

	public function get_gender_fb_user(){
		return $this->gender_fb_user;
	}

	public function set_gender_fb_user($gender_fb_user){
		$this->gender_fb_user = $gender_fb_user;
	}

	public function get_birthday_fb_user(){
		return $this->birthday_fb_user;
	}

	public function set_birthday_fb_user($birthday_fb_user){
		$this->birthday_fb_user = $birthday_fb_user;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into search_history(id_search_history, id_fb_user, keyword, results_count, date, name_fb_user, last_name_fb_user, gender_fb_user, birthday_fb_user) 
				values (:id_search_history, :id_fb_user, :keyword, :results_count, now(), :name_fb_user, :last_name_fb_user, :gender_fb_user, :birthday_fb_user) ");

			$stmt->execute( array( 
				":id_search_history"	=> $this->id_search_history,
				":id_fb_user"			=> $this->id_fb_user,
				":keyword"				=> $this->keyword,
				":results_count"		=> $this->results_count,
				":name_fb_user"			=> $this->name_fb_user,
				":last_name_fb_user"	=> $this->last_name_fb_user,
				":gender_fb_user"		=> $this->gender_fb_user,
				":birthday_fb_user"		=> $this->birthday_fb_user
			) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - search_history.php|insert: ' . $e->getMessage();
		    return false;
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_search_history  	= $obj->id_search_history;
		$this->id_fb_user  			= $obj->id_fb_user;
		$this->keyword  			= $obj->keyword;
		$this->results_count  		= $obj->results_count;
		$this->date  				= $obj->date;
		$this->name_fb_user  		= $obj->name_fb_user;
		$this->last_name_fb_user  	= $obj->last_name_fb_user;
		$this->gender_fb_user  		= $obj->gender_fb_user;
		$this->birthday_fb_user 	= $obj->birthday_fb_user;

	}

	public function map($id_search_history){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from search_history
				where id_search_history 	= :id_search_history ");

			$stmt->execute( array( 
				":id_search_history" => $id_search_history
			) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - search_history.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from search_history 
				where id_search_history 	= :id_search_history ");

			$stmt->execute( array( 
				":id_search_history"  	=> $this->id_search_history
			) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - search_history.php|delete: ' . $e->getMessage();
		    return false;
		}
	}

	//EXISTS?
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from search_history 
				where id_search_history 	= :id_search_history");

			$stmt->execute( array( 
					":id_search_history" => $this->id_search_history
			) );

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - search_history.php|exists' . $e->getMessage();
		}
	}

	//MAXIMUM
	public function max_id_search_history(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select COALESCE(MAX(id_search_history)+1,1) AS max 
				from search_history ");

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->max;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - search_history.php|max_id_search_history' . $e->getMessage();
		}
	}

	//MAXIMUM
	public function get_id_by_keyword_and_user($keyword, $id_fb_user){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select *
				from search_history
				where keyword 	= :keyword
				and id_fb_user 	= :id_fb_user ");

			$stmt->execute(array(
				":keyword" 		=> $keyword,
				":id_fb_user" 	=> $id_fb_user
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->id_search_history;
			}else{
				return "";
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - search_history.php|get_id_by_keyword_and_user' . $e->getMessage();
		}
	}

	//LIST
	public function get_list( $current_id_fb_user, $current_gender, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from search_history
				where results_count > 0
				and id_fb_user != :current_id_fb_user
				and gender_fb_user = :current_gender ".$order);

			$stmt->execute(array( 
				"current_id_fb_user" 	=> $current_id_fb_user,
				"current_gender" 		=> $current_gender 
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$search_history = new search_history();
				$search_history->mapea($reg);

				array_push($list, $search_history);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - search_history.php|get_list' . $e->getMessage();
		}

		return $list;

	}

	//LIST
	public function get_last_keywords( $current_id_fb_user, $current_gender, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from search_history
				where results_count > 0
				and id_fb_user != :current_id_fb_user
				and gender_fb_user = :current_gender ".$order);

			$stmt->execute(array( 
				"current_id_fb_user" 	=> $current_id_fb_user,
				"current_gender" 		=> $current_gender 
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$search_history = new search_history();
				$search_history->mapea($reg);

				array_push($list, $search_history);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - search_history.php|get_last_keywords' . $e->getMessage();
		}

		return $list;
	}

	public function get_all_last_keywords($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from search_history
				where results_count > 0 ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$search_history = new search_history();
				$search_history->mapea($reg);

				array_push($list, $search_history);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - search_history.php|get_all_last_keywords' . $e->getMessage();
		}

		return $list;
	}
}

?>






