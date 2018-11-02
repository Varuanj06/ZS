<?php

class fb_user_profile{

	//VARIABLES
	private $id_fb_user;
	private $id_fb_user_profile;
	private $age;
	private $gender; 
	private $price_range; 
	private $profile; 

	//CONSTRUCTOR
	public function __construct(){
		$this->id_fb_user 				= "";
		$this->id_fb_user_profile 		= "";
		$this->age 						= "";
		$this->gender 					= "";
		$this->price_range 				= "";
		$this->profile 					= "";
	}

	//GETTERS AND SETTERS
	public function get_id_fb_user(){
		return $this->id_fb_user;
	}

	public function set_id_fb_user($id_fb_user){
		$this->id_fb_user = $id_fb_user;
	}

	public function get_id_fb_user_profile(){
		return $this->id_fb_user_profile;
	}

	public function set_id_fb_user_profile($id_fb_user_profile){
		$this->id_fb_user_profile = $id_fb_user_profile;
	}

	public function get_age(){
		return $this->age;
	}

	public function set_age($age){
		$this->age = $age;
	}

	public function get_gender(){
		return $this->gender;
	}

	public function set_gender($gender){
		$this->gender = $gender;
	}

	public function get_price_range(){
		return $this->price_range;
	}

	public function set_price_range($price_range){
		$this->price_range = $price_range;
	}

	public function get_profile(){
		return $this->profile;
	}

	public function set_profile($profile){
		$this->profile = $profile;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into fb_user_profile(id_fb_user, id_fb_user_profile, `age`, gender, price_range, profile) 
				values (:id_fb_user, :id_fb_user_profile, :age, :gender, :price_range, :profile) ");

			$stmt->execute(array( 
				":id_fb_user" 			=> $this->id_fb_user, 
				":id_fb_user_profile" 	=> $this->id_fb_user_profile,
				":age" 					=> $this->age,
				":gender" 				=> $this->gender,
				":price_range" 			=> $this->price_range,
				":profile" 				=> $this->profile
			 ));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - fb_user_profile.php|insert: ' . $e->getMessage();
		}
	}

	//UPDATE
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update fb_user_profile 
					set `age` 	 	 	= :age,
					`gender` 	 	 	= :gender,
					`price_range` 	 	= :price_range,
					`profile` 	 	 	= :profile
				where id_fb_user 		= :id_fb_user
				and id_fb_user_profile 	= :id_fb_user_profile ");

			$stmt->execute(array( 
				":age"					=> $age,
				":gender"				=> $gender,
				":price_range"			=> $price_range,
				":profile"				=> $profile,
				":id_fb_user" 			=> $id_fb_user,
				":id_fb_user_profile" 	=> $id_fb_user_profile 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - fb_user_profile.php|update: ' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_fb_user 			= $obj->id_fb_user;
		$this->id_fb_user_profile 	= $obj->id_fb_user_profile;
		$this->age 					= $obj->age;
		$this->gender 				= $obj->gender;
		$this->price_range 			= $obj->price_range;
		$this->profile 				= $obj->profile;

	}

	public function map($id_fb_user, $id_fb_user_profile){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from fb_user_profile 
				where id_fb_user			= :id_fb_user
				and id_fb_user_profile 		= :id_fb_user_profile ");

			$stmt->execute(array( 
				":id_fb_user" 			=> $id_fb_user,
				":id_fb_user_profile" 	=> $id_fb_user_profile
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - fb_user_profile.php|map' . $e->getMessage();
		}
	}

	public function map_last_profile($id_fb_user){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from fb_user_profile 
				where id_fb_user			= :id_fb_user
				order by id_fb_user_profile desc
				limit 1 ");

			$stmt->execute(array( 
				":id_fb_user" 			=> $id_fb_user
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - fb_user_profile.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from fb_user_profile 
				where id_fb_user 			= :id_fb_user,
				and id_fb_user_profile 		= :id_fb_user_profile ");

			$stmt->execute(array( 
				":id_fb_user"  			=> $this->id_fb_user,
				":id_fb_user_profile"  	=> $this->id_fb_user_profile 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - fb_user_profile.php|delete: ' . $e->getMessage();
		}
	}


	//EXIST
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from fb_user_profile 
				where id_fb_user 			= :id_fb_user,
				and id_fb_user_profile 		= :id_fb_user_profile ");

			$stmt->execute(array( 
				":id_fb_user"  			=> $this->id_fb_user,
				":id_fb_user_profile"  	=> $this->id_fb_user_profile 
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - fb_user_profile.php|exists' . $e->getMessage();
		}
	}

	//MAXIMUM
	public function max_id_fb_user_profile($id_fb_user){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select COALESCE(MAX(id_fb_user_profile)+1,1) AS maximo 
				from fb_user_profile
				where id_fb_user = :id_fb_user ");

			$stmt->execute(array( 
				":id_fb_user"  => $id_fb_user
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->maximo;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - fb_user_profile.php|max_id_fb_user_profile' . $e->getMessage();
		}
	}

	public function get_last_profile($id_fb_user){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select profile
				from fb_user_profile
				where id_fb_user = :id_fb_user
				order by id_fb_user_profile desc
				limit 1 ");

			$stmt->execute(array( 
				":id_fb_user"  => $id_fb_user
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->profile;
			}else{
				return '';
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - fb_user_profile.php|get_last_profile' . $e->getMessage();
		}
	}

	public function get_list($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from `fb_user_profile` ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$fb_user_profile = new fb_user_profile();
				$fb_user_profile->mapea($reg);

				array_push($list, $fb_user_profile);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - fb_user_profile.php|get_list' . $e->getMessage();
		}

		return $list;

	}

	public function save_profile($user, $price_range){

		$fb_user_profile = new fb_user_profile();

		/* ### CALCULATE AGE ## */

		$age = '';
		if( isset($user['birthday']) ){
			$from = new DateTime($user['birthday']);
			$to   = new DateTime('today');
			$age  = $from->diff($to)->y;
		} 

		/* ### GET GENDER ### */

		$gender = $user['gender'];

		/* ### GET PRICE RANGE ### */

		$price_range_min 	= explode(" - ", $price_range)[0];
		$price_range_max 	= explode(" - ", $price_range)[1];

		/* ### GENERATE PROFILE ### */

		$new_profile = '';
		
		if($gender === 'female'){
		  $new_profile .= 'f';
		}else{
		  $new_profile .= 'm';
		}

		if($age === ''){
			$new_profile .= 'x';
		}else if($age < 21){
		  $new_profile .= 'a';
		}else if($age >= 21 && $age <=29){
		  $new_profile .= 'b';
		}else if($age >= 30 && $age <=39){
		  $new_profile .= 'c';
		}else if($age >= 40){
		  $new_profile .= 'd';
		}

		if($price_range_max <= 1000){
			$new_profile .= '1k';
		}else if($price_range_max > 1000 && $price_range_max <= 2000){
			$new_profile .= '2k';
		}else if($price_range_max > 2000 && $price_range_max <= 3000){
			$new_profile .= '3k';
		}else if($price_range_max > 3000 && $price_range_max <= 4000){
			$new_profile .= '4k';
		}else if($price_range_max > 4000){
			$new_profile .= '4k+';
		}

		/* ### CHECK IF THERE WAS A CHANGE ### */

		if($fb_user_profile->get_last_profile($user['id']) != $new_profile){

			/* ### SAVE PROFILE ### */

			$fb_user_profile->set_id_fb_user( $user['id'] );
			$fb_user_profile->set_id_fb_user_profile( $fb_user_profile->max_id_fb_user_profile($user['id']) );
			$fb_user_profile->set_age($age);
			$fb_user_profile->set_gender($gender);
			$fb_user_profile->set_price_range( $price_range );
			$fb_user_profile->set_profile($new_profile);

			$fb_user_profile->insert();

		}

	}

	public function get_next_profile($current_profile){

		$gender 			= substr($current_profile, 0, 1); 	// get the first char
		$current_profile 	= substr($current_profile, 1); 		// remove the first char from the profile

		$all_profiles = [
			'x4k+', 'd4k+', 'c4k+', 'b4k+', 'a4k+', 	// 4k+
			'x4k', 'd4k', 'c4k', 'b4k', 'a4k', 			// 4k
			'x3k', 'd3k', 'c3k', 'b3k', 'a3k',  		// 3k
			'x2k', 'd2k', 'c2k', 'b2k', 'a2k',  		// 2k
			'x1k', 'd1k', 'c1k', 'b1k', 'a1k',  		// 1k
		];

		$current_index 	= array_search($current_profile, $all_profiles);
		$next_index 	= $current_index+1;

		if($current_index === false){
			return false;
		}else if($next_index >= count($all_profiles)){
			return false;
		}else{
			return $gender . $all_profiles[$next_index];
		}

	}

}

?>