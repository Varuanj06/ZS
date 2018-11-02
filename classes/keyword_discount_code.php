<?php

class keyword_discount_code{

	//VARIABLES
	private $code;
	private $id_keyword_discount;
	private $keyword;
	private $id_order;
	private $keyword_url;

	//CONSTRUCTOR
	public function __construct(){
		$this->code 					= "";
		$this->id_keyword_discount 		= "";
		$this->keyword 					= "";
		$this->id_order 				= "";
		$this->keyword_url 				= "";
	}

	//GETTERS AND SETTERS
	public function get_code(){
		return $this->code;
	}

	public function set_code($code){
		$this->code = $code;
	}

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

	public function get_id_order(){
		return $this->id_order;
	}

	public function set_id_order($id_order){
		$this->id_order = $id_order;
	}

	public function get_keyword_url(){
		return $this->keyword_url;
	}

	public function set_keyword_url($keyword_url){
		$this->keyword_url = $keyword_url;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into keyword_discount_code(code, id_keyword_discount, keyword, id_order, keyword_url) 
				values (:code, :id_keyword_discount, :keyword, :id_order, :keyword_url) ");

			$stmt->execute(array( 
				":code"						=> $this->code,
				":id_keyword_discount" 		=> $this->id_keyword_discount,
				":keyword" 					=> $this->keyword,
				":id_order"					=> $this->id_order,
				":keyword_url"				=> $this->keyword_url
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - keyword_discount_code.php|insert: ' . $e->getMessage();
		}
	}

	//UPDATE
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update keyword_discount_code
				set
					keyword 				= :keyword,
					id_keyword_discount 	= :id_keyword_discount,
					id_order 				= :id_order,
					keyword_url 			= :keyword_url
				where code 					= :code ");

			$stmt->execute(array( 
				":keyword"					=> $this->keyword,
				":id_keyword_discount"		=> $this->id_keyword_discount,
				":id_order"					=> $this->id_order,
				":code"						=> $this->code,
			));

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - keyword_discount_code.php|update: ' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->code  					= $obj->code;
		$this->id_keyword_discount  	= $obj->id_keyword_discount;
		$this->keyword  				= $obj->keyword;
		$this->id_order  				= $obj->id_order;
		$this->keyword_url 				= $obj->keyword_url;

	}

	public function map($code){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from keyword_discount_code
				where code = :code ");

			$stmt->execute( array( ":code" => $code ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - keyword_discount_code.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from keyword_discount_code 
				where code = :code");

			$stmt->execute( array( ":code"  => $this->code ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - keyword_discount_code.php|delete: ' . $e->getMessage();
		}
	}

	//EXISTS?
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from keyword_discount_code 
				where code = :code ");

			$stmt->execute( array( ":code" => $this->code ) );

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - keyword_discount_code.php|exists' . $e->getMessage();
		}
	}

	public function exists_with_keyword($code, $keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from keyword_discount_code 
				where code = :code
				and keyword = :keyword ");

			$stmt->execute(array( 
				":code" 		=> $code,
				":keyword" 		=> $keyword
			));

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - keyword_discount_code.php|exists_with_keyword' . $e->getMessage();
		}
	}

	public function exists_with_id_keyword_discount($code, $id_keyword_discount){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from keyword_discount_code 
				where code = :code
				and id_keyword_discount = :id_keyword_discount ");

			$stmt->execute(array( 
				":code" 					=> $code,
				":id_keyword_discount" 		=> $id_keyword_discount
			));

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - keyword_discount_code.php|exists_with_id_keyword_discount' . $e->getMessage();
		}
	}

	public function get_code_by_keyword_and_order($keyword, $id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select code
				from keyword_discount_code
				where keyword = :keyword
				and id_order = :id_order ");

			$stmt->execute(array(
					":keyword" => $keyword,
					":id_order" => $id_order
				));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->code;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
	    	return false;
		    echo 'ERROR: - keyword_discount_code.php|get_code_by_keyword_and_order' . $e->getMessage();
		}
	}

	public function get_url_by_keyword_and_order($keyword, $id_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select keyword_url
				from keyword_discount_code
				where keyword = :keyword
				and id_order = :id_order ");

			$stmt->execute(array(
					":keyword" => $keyword,
					":id_order" => $id_order
				));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->keyword_url;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
	    	return false;
		    echo 'ERROR: - keyword_discount_code.php|get_url_by_keyword_and_order' . $e->getMessage();
		}
	}

	//LIST
	public function get_list($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from keyword_discount_code ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$keyword_discount_code = new keyword_discount_code();
				$keyword_discount_code->mapea($reg);

				array_push($list, $keyword_discount_code);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - keyword_discount_code.php|get_list' . $e->getMessage();
		}

		return $list;

	}

	public function get_list_by_id_order($id_order, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from keyword_discount_code
				where id_order = :id_order
				or code in (select keyword_discount_code from order_detail where id_order = :id_order2) ".$order);

			$stmt->execute(array(
				":id_order" => $id_order,
				":id_order2" => $id_order
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$keyword_discount_code = new keyword_discount_code();
				$keyword_discount_code->mapea($reg);

				array_push($list, $keyword_discount_code);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - keyword_discount_code.php|get_list' . $e->getMessage();
		}

		return $list;

	}


	public function generate_code(){
   		$new_code 	= strtoupper( substr(md5(uniqid(rand(), true)),0,6) );  // creates a 6 digit token
   		$results 	= array();

   		global $conn;

   		try {
   		
	   		$stmt = $conn->prepare("
				select * 
				from keyword_discount_code 
				where code = '$new_code' ");
			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

		} catch(PDOException $e) {
		    echo 'ERROR: - keyword_discount_code.php|get_list' . $e->getMessage();
		}	

   		if (count($results) !== 0) {
      		generateUniqueID();
   		} else {
      		return $new_code;
   		}
	}

	public function get_users_that_used_code($code){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select id_fb_user, date_placed
				from `order`
				where id_order in (select id_order from order_detail where keyword_discount_code = :code) 
				/*group by id_fb_user*/
				order by 1 desc ");

			$stmt->execute(array(
				":code" 	=> $code
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$obj = array();

				$obj['id_fb_user'] 		= $reg->id_fb_user;
				$obj['date_placed'] 	= $reg->date_placed;

				array_push($list, $obj);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - keyword_discount_code.php|get_list' . $e->getMessage();
		}

		return $list;

	}

	public function get_list_by_keyword($keyword, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from keyword_discount_code 
				where id_keyword_discount = (
					select id_keyword_discount
					from keyword_discount
					where keyword = :keyword
					and status = 'active'
					and now() >= start_date 
					and now() <= end_date
					order by id_keyword_discount 
					limit 1
				) ".$order);

			$stmt->execute(array(
				":keyword" => $keyword
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$keyword_discount_code = new keyword_discount_code();
				$keyword_discount_code->mapea($reg);

				array_push($list, $keyword_discount_code);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - keyword_discount_code.php|get_list' . $e->getMessage();
		}

		return $list;

	}

}

?>