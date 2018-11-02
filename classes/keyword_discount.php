<?php

class keyword_discount{
	//VARIABLES
	private $id_keyword_discount;
	private $keyword;
	private $discount;
	private $discount_type;
	private $status;
	private $start_date;
	private $end_date;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_keyword_discount 		= "";
		$this->keyword 					= "";
		$this->discount 				= "";
		$this->discount_type 			= "";
		$this->status 					= "";
		$this->start_date 				= "";
		$this->end_date 				= "";
	}

	//GETTERS AND SETTERS
	public function get_id_keyword_discount(){
		return $this->id_keyword_discount;
	}

	public function set_id_keyword_discount($id_keyword_discount){
		$this->id_keyword_discount = $id_keyword_discount;
	}

	public function get_keyword(){
		return $this->keyword;
	}

	public function set_keyword($keyword){
		$this->keyword = $keyword;
	}

	public function get_discount(){
		return $this->discount;
	}

	public function set_discount($discount){
		$this->discount = $discount;
	}

	public function get_discount_type(){
		return $this->discount_type;
	}

	public function set_discount_type($discount_type){
		$this->discount_type = $discount_type;
	}

	public function get_status(){
		return $this->status;
	}

	public function set_status($status){
		$this->status = $status;
	}

	public function get_start_date(){
		return $this->start_date;
	}

	public function set_start_date($start_date){
		$this->start_date = $start_date;
	}

	public function get_end_date(){
		return $this->end_date;
	}

	public function set_end_date($end_date){
		$this->end_date = $end_date;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into keyword_discount(id_keyword_discount, keyword, discount, discount_type, status, start_date, end_date) 
				values (:id_keyword_discount, :keyword, :discount, :discount_type, :status, : start_date, :end_date) ");

			$stmt->execute(array( 
				":id_keyword_discount"		=> $this->id_keyword_discount,
				":keyword"					=> $this->keyword,
				":discount"					=> $this->discount,
				":discount_type"			=> $this->discount_type,
				":status"					=> $this->status,
				":start_date"				=> $this->start_date,
				":end_date"					=> $this->end_date
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - keyword_discount.php|insert: ' . $e->getMessage();
		    return false;
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_keyword_discount  	= $obj->id_keyword_discount;
		$this->keyword  				= $obj->keyword;
		$this->discount  				= $obj->discount;
		$this->discount_type  			= $obj->discount_type;
		$this->status  					= $obj->status;
		$this->start_date  				= $obj->start_date;
		$this->end_date  				= $obj->end_date;

	}

	public function map($id_keyword_discount){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from keyword_discount
				where id_keyword_discount 	= :id_keyword_discount ");

			$stmt->execute( array( 
				":id_keyword_discount" => $id_keyword_discount
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - keyword_discount.php|map' . $e->getMessage();
		}
	}

	public function map_active_by_keyword($keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from keyword_discount
				where keyword 	= :keyword 
				and status = 'active'
				and now() >= start_date 
				and now() <= end_date ");

			$stmt->execute( array( 
				":keyword" => $keyword
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - keyword_discount.php|map_active_by_keyword' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from keyword_discount 
				where id_keyword_discount 	= :id_keyword_discount ");

			$stmt->execute( array( 
				":id_keyword_discount"  	=> $this->id_keyword_discount
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - keyword_discount.php|delete: ' . $e->getMessage();
		    return false;
		}
	}

	//EXISTS?
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from keyword_discount 
				where id_keyword_discount 	= :id_keyword_discount ");

			$stmt->execute( array( 
					":id_keyword_discount" => $this->id_keyword_discount
			));

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - keyword_discount.php|exists' . $e->getMessage();
		}
	}

	//LIST
	public function get_list($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from keyword_discount ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$keyword_discount = new keyword_discount();
				$keyword_discount->mapea($reg);

				array_push($list, $keyword_discount);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - keyword_discount.php|get_list' . $e->getMessage();
		}

		return $list;

	}
}

?>






