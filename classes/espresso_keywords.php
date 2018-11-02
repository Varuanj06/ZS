<?php

class espresso_keywords{
	//VARIABLES
	private $id_keyword;
	private $keyword;
	private $image;
	private $genders;
	private $ages;
	private $status;
	private $description;
	private $discount;
	private $discount_type;
	private $popular;
	private $booking_count;
	private $booking_threshold;
	private $brand_link;
	private $created_at;
	private $updated_at;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_keyword 			= ""; 
		$this->keyword 				= ""; 
		$this->image 				= ""; 
		$this->genders 				= ""; 
		$this->ages 				= ""; 
		$this->status 				= ""; 
		$this->description 			= ""; 
		$this->discount 			= ""; 
		$this->discount_type 		= ""; 
		$this->popular 				= ""; 
		$this->booking_count 		= ""; 
		$this->booking_threshold 	= ""; 
		$this->brand_link 			= ""; 
		$this->created_at 			= ""; 
		$this->updated_at 			= ""; 
	}

	//GETTERS AND SETTERS
	public function get_id_keyword(){
		return $this->id_keyword;
	}

	public function set_id_keyword($id_keyword){
		$this->id_keyword = $id_keyword;
	}

	public function get_keyword(){
		return $this->keyword;
	}

	public function set_keyword($keyword){
		$this->keyword = $keyword;
	}

	public function get_image(){
		return $this->image;
	}

	public function set_image($image){
		$this->image = $image;
	}

	public function get_genders(){
		return $this->genders;
	}

	public function set_genders($genders){
		$this->genders = $genders;
	}

	public function get_ages(){
		return $this->ages;
	}

	public function set_ages($ages){
		$this->ages = $ages;
	}

	public function get_status(){
		return $this->status;
	}

	public function set_status($status){
		$this->status = $status;
	}

	public function get_description(){
		return $this->description;
	}

	public function set_description($description){
		$this->description = $description;
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

	public function get_popular(){
		return $this->popular;
	}

	public function set_popular($popular){
		$this->popular = $popular;
	}

	public function get_booking_count(){
		return $this->booking_count;
	}

	public function set_booking_count($booking_count){
		$this->booking_count = $booking_count;
	}

	public function get_booking_threshold(){
		return $this->booking_threshold;
	}

	public function set_booking_threshold($booking_threshold){
		$this->booking_threshold = $booking_threshold;
	}

	public function get_brand_link(){
		return $this->brand_link;
	}

	public function set_brand_link($brand_link){
		$this->brand_link = $brand_link;
	}

	public function get_created_at(){
		return $this->created_at;
	}

	public function set_created_at($created_at){
		$this->created_at = $created_at;
	}

	public function get_updated_at(){
		return $this->updated_at;
	}

	public function set_updated_at($updated_at){
		$this->updated_at = $updated_at;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into espresso_keywords(keyword, image, genders, ages, status, description, discount, discount_type, popular, booking_count, booking_threshold, brand_link, created_at, updated_at) 
				values (:keyword, :image, :genders, :ages, :status, :description, :discount, :discount_type, :popular, '', :booking_threshold, :brand_link, now(), '') ");

			$stmt->execute(array( 
				":keyword"			=> $this->keyword,
				":image"			=> $this->image,
				":genders"			=> $this->genders,
				":ages"				=> $this->ages,
				":status"			=> $this->status, 
				":description"		=> $this->description,
				":discount"			=> $this->discount,
				":discount_type"	=> $this->discount_type,
				":popular"			=> $this->popular,
				":booking_threshold"=> $this->booking_threshold,
				":brand_link"		=> $this->brand_link
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - espresso_keywords.php|insert: ' . $e->getMessage();
		    return false;
		}
	}

	//UPDATE
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update espresso_keywords
				set
					keyword 			= :keyword,
					image  				= :image,
					genders  			= :genders,
					ages  				= :ages,
					status  			= :status,
					description  		= :description,
					discount  			= :discount,
					discount_type  		= :discount_type,
					popular  			= :popular,
					booking_threshold  	= :booking_threshold,
					brand_link  		= :brand_link
				where id_keyword 		= :id_keyword ");

			$stmt->execute(array( 
				":keyword"				=> $this->keyword,
				":image"				=> $this->image,
				":genders"				=> $this->genders,
				":ages"					=> $this->ages,
				":status"				=> $this->status,
				":description"			=> $this->description,
				":discount"				=> $this->discount,
				":discount_type"		=> $this->discount_type,
				":popular"				=> $this->popular,
				":booking_threshold"	=> $this->booking_threshold,
				":brand_link"			=> $this->brand_link,
				":id_keyword"			=> $this->id_keyword 
			));

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - espresso_keywords.php|update: ' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_keyword  			= $obj->id_keyword;
		$this->keyword  			= $obj->keyword;
		$this->image  				= $obj->image;
		$this->genders  			= $obj->genders;
		$this->ages  				= $obj->ages;
		$this->status  				= $obj->status;
		$this->description  		= $obj->description;
		$this->discount  			= $obj->discount;
		$this->discount_type 		= $obj->discount_type;
		$this->popular  			= $obj->popular;
		$this->booking_count  		= $obj->booking_count;
		$this->booking_threshold  	= $obj->booking_threshold;
		$this->brand_link  			= $obj->brand_link;
		$this->created_at  			= $obj->created_at;
		$this->updated_at  			= $obj->updated_at;

	}

	public function map($id_keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from espresso_keywords
				where id_keyword 	= :id_keyword");

			$stmt->execute(array( ":id_keyword" => $id_keyword ));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - espresso_keywords.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from espresso_keywords 
				where id_keyword 	= :id_keyword ");

			$stmt->execute(array( ":id_keyword"  	=> $this->id_keyword ));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - espresso_keywords.php|delete: ' . $e->getMessage();
		    return false;
		}
	}

	//EXISTS?
	public function exists($id_keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from espresso_keywords 
				where id_keyword 	= :id_keyword ");

			$stmt->execute( array( ":id_keyword" => $id_keyword ) );

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - espresso_keywords.php|exists' . $e->getMessage();
		}
	}

	//LIST
	public function get_list($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from espresso_keywords ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$espresso_keywords = new espresso_keywords();
				$espresso_keywords->mapea($reg);

				array_push($list, $espresso_keywords);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - espresso_keywords.php|get_list' . $e->getMessage();
		}

		return $list;

	}

	public function get_lastest_3_active($gender, $age){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from espresso_keywords
				where status in ('active', 'inactive', 'BREWED')
				and genders like :gender
				and (ages like :age or ages like '%/all/%')
				order by created_at desc
				limit 3 ");

			$stmt->execute(array( ":gender" => "%$gender%", ":age" => "%$age%" ));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$espresso_keywords = new espresso_keywords();
				$espresso_keywords->mapea($reg);

				array_push($list, $espresso_keywords);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - espresso_keywords.php|get_list' . $e->getMessage();
		}

		return $list;

	}

	public function get_lastest_3_created($gender, $age){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from espresso_keywords
				where status in ('daily')
				and genders like :gender
				and (ages like :age or ages like '%/all/%')
				order by created_at desc
				limit 3 ");

			$stmt->execute(array( ":gender" => "%$gender%", ":age" => "%$age%" ));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$espresso_keywords = new espresso_keywords();
				$espresso_keywords->mapea($reg);

				array_push($list, $espresso_keywords);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - espresso_keywords.php|get_list' . $e->getMessage();
		}

		return $list;

	}

	public function get_lastest_7_created($gender, $age){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from espresso_keywords
				where status in ('daily')
				and genders like :gender
				and (ages like :age or ages like '%/all/%')
				order by created_at desc
				limit 7 ");

			$stmt->execute(array( ":gender" => "%$gender%", ":age" => "%$age%" ));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$espresso_keywords = new espresso_keywords();
				$espresso_keywords->mapea($reg);

				array_push($list, $espresso_keywords);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - espresso_keywords.php|get_list' . $e->getMessage();
		}

		return $list;

	}

	public function get_lastest_60_created($gender, $age){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from espresso_keywords
				where status in ('daily')
				and genders like :gender
				and (ages like :age or ages like '%/all/%')
				order by created_at desc
				limit 60 ");

			$stmt->execute(array( ":gender" => "%$gender%", ":age" => "%$age%" ));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$espresso_keywords = new espresso_keywords();
				$espresso_keywords->mapea($reg);

				array_push($list, $espresso_keywords);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - espresso_keywords.php|get_list' . $e->getMessage();
		}

		return $list;

	}

}

?>