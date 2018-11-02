<?php

class address{
	//VARIABLES
	private $id_address;
	private $id_fb_user;
	private $name;
	private $mobile_number;
	private $address;
	private $landmark;
	private $city;
	private $state;
	private $pin_code;
	private $email;
	private $date_add;
	private $date_update;

	//CONSTRUCTOR
	public function address(){
		$this->id_address 		= "";
		$this->id_fb_user 		= "";
		$this->name 			= "";
		$this->mobile_number 	= "";
		$this->address 			= "";
		$this->landmark 		= "";
		$this->city 			= "";
		$this->state 			= "";
		$this->pin_code 		= "";
		$this->email 			= "";
		$this->date_add 		= "";
		$this->date_update 		= "";
	}

	//GETTERS AND SETTERS
	public function get_id_address(){
		return $this->id_address;
	}

	public function set_id_address($id_address){
		$this->id_address = $id_address;
	}

	public function get_id_fb_user(){
		return $this->id_fb_user;
	}

	public function set_id_fb_user($id_fb_user){
		$this->id_fb_user = $id_fb_user;
	}

	public function get_name(){
		return $this->name;
	}

	public function set_name($name){
		$this->name = $name;
	}

	public function get_mobile_number(){
		return $this->mobile_number;
	}

	public function set_mobile_number($mobile_number){
		$this->mobile_number = $mobile_number;
	}

	public function get_address(){
		return $this->address;
	}

	public function set_address($address){
		$this->address = $address;
	}

	public function get_landmark(){
		return $this->landmark;
	}

	public function set_landmark($landmark){
		$this->landmark = $landmark;
	}

	public function get_city(){
		return $this->city;
	}

	public function set_city($city){
		$this->city = $city;
	}

	public function get_state(){
		return $this->state;
	}

	public function set_state($state){
		$this->state = $state;
	}

	public function get_pin_code(){
		return $this->pin_code;
	}

	public function set_pin_code($pin_code){
		$this->pin_code = $pin_code;
	}

	public function get_email(){
		return $this->email;
	}

	public function set_email($email){
		$this->email = $email;
	}

	public function get_date_add(){
		return $this->date_add;
	}

	public function set_date_add($date_add){
		$this->date_add = $date_add;
	}

	public function get_date_update(){
		return $this->date_update;
	}

	public function set_date_update($date_update){
		$this->date_update = $date_update;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into address(id_address, id_fb_user, name, mobile_number, address, landmark, city, state, pin_code, email, date_add, date_update) 
				values (:id_address, :id_fb_user, :name, :mobile_number, :address, :landmark, :city, :state, :pin_code, :email, now(), now()) ");

			$stmt->execute( array( 
				":id_address"			=> $this->id_address,
				":id_fb_user"			=> $this->id_fb_user,
				":name"					=> $this->name,
				":mobile_number"		=> $this->mobile_number,
				":address"				=> $this->address,
				":landmark"				=> $this->landmark,
				":city"					=> $this->city,
				":state"				=> $this->state,
				":pin_code"				=> $this->pin_code,
				":email"				=> $this->email
			) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - address.php|insert: ' . $e->getMessage();
		    return false;
		}
	}

	//UPDATE
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update address
				set
					name 					= :name,
					mobile_number 			= :mobile_number,
					address 				= :address,
					landmark  				= :landmark,
					city  					= :city, 
					state  					= :state,
					pin_code  				= :pin_code,
					email  					= :email,
					date_update 			= now()
				where id_address 			= :id_address
				and id_fb_user 				= :id_fb_user ");

			$stmt->execute( array( 
				":name"						=> $this->name,
				":mobile_number"			=> $this->mobile_number,
				":address"					=> $this->address,
				":landmark"					=> $this->landmark,
				":city"						=> $this->city,
				":state"					=> $this->state,
				":pin_code"					=> $this->pin_code,
				":email"					=> $this->email,
				":id_address" 				=> $this->id_address,
				":id_fb_user" 				=> $this->id_fb_user ) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - address.php|update: ' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_address  			= $obj->id_address;
		$this->id_fb_user  			= $obj->id_fb_user;
		$this->name  				= $obj->name;
		$this->mobile_number  		= $obj->mobile_number;
		$this->address  			= $obj->address;
		$this->landmark  			= $obj->landmark;
		$this->city  				= $obj->city;
		$this->state  				= $obj->state;
		$this->pin_code  			= $obj->pin_code;
		$this->email  				= $obj->email;
		$this->date_add  			= $obj->date_add;
		$this->date_update  		= $obj->date_update;

	}

	public function map($id_address, $id_fb_user){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from address
				where id_address 	= :id_address
				and id_fb_user 		= :id_fb_user ");

			$stmt->execute( array( 
				":id_address" => $id_address,
				":id_fb_user" => $id_fb_user
			) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - address.php|map' . $e->getMessage();
		    return false;
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from address 
				where id_address 	= :id_address
				and id_fb_user 		= :id_fb_user ");

			$stmt->execute( array( 
				":id_address"  	=> $this->id_address,
				":id_fb_user"  	=> $this->id_fb_user
			) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - address.php|delete: ' . $e->getMessage();
		    return false;
		}
	}

	//MAXIMUM
	public function max_id_address($id_fb_user){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select COALESCE(MAX(id_address)+1,1) AS max 
				from address
				where id_fb_user = :id_fb_user ");

			$stmt->execute(array(
					":id_fb_user" => $id_fb_user
				));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->max;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - address.php|max_id_address' . $e->getMessage();
		}
	}

	//LIST
	public function get_list($id_fb_user, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from address
				where id_fb_user = :id_fb_user ".$order);

			$stmt->execute(array(
				":id_fb_user" => $id_fb_user
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$address = new address();
				$address->mapea($reg);

				array_push($list, $address);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - address.php|get_list' . $e->getMessage();
		}

		return $list;

	}
}

?>






