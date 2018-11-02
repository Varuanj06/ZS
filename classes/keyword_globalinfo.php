<?php

class keyword_globalinfo{
	//VARIABLES
	private $keyword;
	private $international_shipping_cost;
	private $domestic_shipping_cost;
	private $vendor_commission_percentage;
	private $shipping_days;
	private $custom_duty_percentage;
	private $expiry_date;

	//CONSTRUCTOR
	public function __construct(){
		$this->keyword 							= "";
		$this->international_shipping_cost 		= "";
		$this->domestic_shipping_cost 			= "";
		$this->vendor_commission_percentage 	= "";
		$this->shipping_days 					= "";
		$this->custom_duty_percentage 			= "";
		$this->expiry_date 						= "";
	}

	//GETTERS AND SETTERS
	public function get_keyword(){
		return $this->keyword;
	}

	public function set_keyword($keyword){
		$this->keyword = $keyword;
	}

	public function get_international_shipping_cost(){
		return $this->international_shipping_cost;
	}

	public function set_international_shipping_cost($international_shipping_cost){
		$this->international_shipping_cost = $international_shipping_cost;
	}

	public function get_domestic_shipping_cost(){
		return $this->domestic_shipping_cost;
	}

	public function set_domestic_shipping_cost($domestic_shipping_cost){
		$this->domestic_shipping_cost = $domestic_shipping_cost;
	}

	public function get_vendor_commission_percentage(){
		return $this->vendor_commission_percentage;
	}

	public function set_vendor_commission_percentage($vendor_commission_percentage){
		$this->vendor_commission_percentage = $vendor_commission_percentage;
	}

	public function get_shipping_days(){
		return $this->shipping_days;
	}

	public function set_shipping_days($shipping_days){
		$this->shipping_days = $shipping_days;
	}

	public function get_custom_duty_percentage(){
		return $this->custom_duty_percentage;
	}

	public function set_custom_duty_percentage($custom_duty_percentage){
		$this->custom_duty_percentage = $custom_duty_percentage;
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
				insert into keyword_globalinfo(keyword, international_shipping_cost, domestic_shipping_cost, vendor_commission_percentage, shipping_days, custom_duty_percentage, expiry_date) 
				values (:keyword, :international_shipping_cost, :domestic_shipping_cost, :vendor_commission_percentage, :shipping_days, :custom_duty_percentage, :expiry_date) ");
			$stmt->execute(array( 
				":keyword"							=> $this->keyword,
				":international_shipping_cost"		=> $this->international_shipping_cost,
				":domestic_shipping_cost"			=> $this->domestic_shipping_cost,
				":vendor_commission_percentage"		=> $this->vendor_commission_percentage,
				":shipping_days"					=> $this->shipping_days, 
				":custom_duty_percentage"			=> $this->custom_duty_percentage,
				":expiry_date"						=> $this->expiry_date,
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - keyword_globalinfo.php|insert: ' . $e->getMessage();
		    return false;
		}
	}

	//UPDATE
	public function update($old_keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update keyword_globalinfo
				set
					keyword 						= :keyword,
					international_shipping_cost  	= :international_shipping_cost,
					domestic_shipping_cost  		= :domestic_shipping_cost,
					vendor_commission_percentage  	= :vendor_commission_percentage,
					shipping_days  					= :shipping_days,
					custom_duty_percentage  		= :custom_duty_percentage,
					expiry_date  					= :expiry_date
				where keyword 						= :old_keyword ");

			$stmt->execute(array( 
				":keyword"							=> $this->keyword,
				":international_shipping_cost"		=> $this->international_shipping_cost,
				":domestic_shipping_cost"			=> $this->domestic_shipping_cost,
				":vendor_commission_percentage"		=> $this->vendor_commission_percentage,
				":shipping_days"					=> $this->shipping_days,
				":custom_duty_percentage"			=> $this->custom_duty_percentage,
				":expiry_date"						=> $this->expiry_date,
				":old_keyword"						=> $old_keyword 
			));

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - keyword_globalinfo.php|update: ' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->keyword  					= $obj->keyword;
		$this->international_shipping_cost  = $obj->international_shipping_cost;
		$this->domestic_shipping_cost  		= $obj->domestic_shipping_cost;
		$this->vendor_commission_percentage = $obj->vendor_commission_percentage;
		$this->shipping_days  				= $obj->shipping_days;
		$this->custom_duty_percentage  		= $obj->custom_duty_percentage;
		$this->expiry_date  				= $obj->expiry_date;

	}

	public function map($keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from keyword_globalinfo
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
		    echo 'ERROR: - keyword_globalinfo.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from keyword_globalinfo 
				where keyword 	= :keyword");

			$stmt->execute( array( ":keyword"  	=> $this->keyword) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - keyword_globalinfo.php|delete: ' . $e->getMessage();
		    return false;
		}
	}

	//EXISTS?
	public function exists($keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from keyword_globalinfo 
				where keyword 	= :keyword ");

			$stmt->execute( array( ":keyword" => $keyword ) );

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - keyword_globalinfo.php|exists' . $e->getMessage();
		}
	}

	//LIST
	public function get_list($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from keyword_globalinfo ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$keyword_globalinfo = new keyword_globalinfo();
				$keyword_globalinfo->mapea($reg);

				array_push($list, $keyword_globalinfo);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - keyword_globalinfo.php|get_list' . $e->getMessage();
		}

		return $list;

	}
}

?>






