<?php

class pixel{
	//VARIABLES
	private $id_pixel;
	private $id_vendor;
	private $name;
	private $image_link;
	private $keywords;
	private $price;
	private $discount;
	private $discount_type;
	private $type;
	private $pixel_count;
	private $product_link;
	private $message;
	private $pixel_keywords;
	private $vendor_link;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_pixel 				= "";
		$this->id_vendor 				= "";
		$this->name 					= "";
		$this->image_link 				= "";
		$this->keywords 				= "";
		$this->price 					= "";
		$this->discount 				= "";
		$this->discount_type 			= "";
		$this->type 					= "";
		$this->pixel_count 				= "";
		$this->product_link 			= "";
		$this->message 					= "";
		$this->pixel_keywords 			= "";
		$this->vendor_link 				= "";
	}

	//GETTERS AND SETTERS
	public function get_id_pixel(){
		return $this->id_pixel;
	}

	public function set_id_pixel($id_pixel){
		$this->id_pixel = $id_pixel;
	}

	public function get_id_vendor(){
		return $this->id_vendor;
	}

	public function set_id_vendor($id_vendor){
		$this->id_vendor = $id_vendor;
	}

	public function get_name(){
		return $this->name;
	}

	public function set_name($name){
		$this->name = $name;
	}

	public function get_image_link(){
		return $this->image_link;
	}

	public function set_image_link($image_link){
		$this->image_link = $image_link;
	}

	public function get_keywords(){
		return $this->keywords;
	}

	public function set_keywords($keywords){
		$this->keywords = $keywords;
	}

	public function get_price(){
		return $this->price;
	}

	public function set_price($price){
		$this->price = $price;
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

	public function get_type(){
		return $this->type;
	}

	public function set_type($type){
		$this->type = $type;
	}

	public function get_pixel_count(){
		return $this->pixel_count;
	}

	public function set_pixel_count($pixel_count){
		$this->pixel_count = $pixel_count;
	}

	public function get_product_link(){
		return $this->product_link;
	}

	public function set_product_link($product_link){
		$this->product_link = $product_link;
	}

	public function get_message(){
		return $this->message;
	}

	public function set_message($message){
		$this->message = $message;
	}

	public function get_pixel_keywords(){
		return $this->pixel_keywords;
	}

	public function set_pixel_keywords($pixel_keywords){
		$this->pixel_keywords = $pixel_keywords;
	}

	public function get_vendor_link(){
		return $this->vendor_link;
	}

	public function set_vendor_link($vendor_link){
		$this->vendor_link = $vendor_link;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into pixel(id_vendor, name, image_link, keywords, price, discount, discount_type, type, product_link, message, pixel_keywords, vendor_link) 
				values (:id_vendor, :name, :image_link, :keywords, :price, :discount, :discount_type, :type, :product_link, :message, :pixel_keywords, :vendor_link) ");

			$stmt->execute( array( 
				":id_vendor"				=> $this->id_vendor,
				":name"						=> $this->name,
				":image_link"				=> $this->image_link,
				":keywords"					=> $this->keywords,
				":price"					=> $this->price,
				":discount"					=> $this->discount,
				":discount_type"			=> $this->discount_type,
				":type"						=> $this->type,
				":product_link"				=> $this->product_link,
				":message"					=> $this->message,
				":pixel_keywords"			=> $this->pixel_keywords,
				":vendor_link"				=> $this->vendor_link
			) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - pixel.php|insert: ' . $e->getMessage();
		}
	}

	//UPDATE
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update pixel
				set
					name 					= :name,
					image_link 				= :image_link,
					keywords  				= :keywords,
					price  					= :price,
					discount  				= :discount,
					discount_type  			= :discount_type,
					pixel_keywords  		= :pixel_keywords,
					vendor_link  			= :vendor_link
				where id_pixel 				= :id_pixel ");

			$stmt->execute( array( 
				":name"						=> $this->name,
				":image_link"				=> $this->image_link,
				":keywords"					=> $this->keywords,
				":price"					=> $this->price,
				":discount"					=> $this->discount,
				":discount_type"			=> $this->discount_type,
				":pixel_keywords"			=> $this->pixel_keywords,
				":vendor_link"				=> $this->vendor_link,
				":id_pixel" 				=> $this->id_pixel ) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - pixel.php|update: ' . $e->getMessage();
		}
	}

	public function update_type($id_pixel, $type, $product_link, $message){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update pixel
				set
					type 					= :type,
					product_link 			= :product_link,
					message 				= :message
				where id_pixel 				= :id_pixel ");

			$stmt->execute( array( 
				":type" 				=> $type,
				":product_link" 		=> $product_link,
				":message" 				=> $message,
				":id_pixel" 			=> $id_pixel
			));

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
	    	return false;
		    echo 'ERROR - pixel.php|update_type: ' . $e->getMessage();
		}
	}

	public function update_pixel_count($id_pixel){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update pixel
				set
					pixel_count 			= (select count(id_pixel) from pixel_count where id_pixel=:id_pixel_a)
				where id_pixel 				= :id_pixel_b ");

			$stmt->execute( array( 
				":id_pixel_a" 			=> $id_pixel, 
				":id_pixel_b" 			=> $id_pixel 
			));

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - pixel.php|update_pixel_count: ' . $e->getMessage();
		}
	}

	public function update_pixel_keyword($pixel_keyword, $old_pixel_keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update pixel
				set
					pixel_keywords		=  REPLACE(pixel_keywords, '/$old_pixel_keyword/', '/$pixel_keyword/')
				where pixel_keywords like '%/$old_pixel_keyword/%' ");

			$stmt->execute();
			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - pixel.php|update_pixel_keyword: ' . $e->getMessage();
		}
	}

	public function update_keyword($keyword, $old_keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update pixel
				set
					keywords		=  REPLACE(keywords, '/$old_keyword/', '/$keyword/')
				where keywords like '%/$old_keyword/%' ");

			$stmt->execute();
			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - pixel.php|update_keyword: ' . $e->getMessage();
		}
	}

	//GET EXPIRY DATE
	public function get_closest_expiry_date_from_pixel_keyword($id_pixel){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select expiry_date as expiry_date 
				from pixel_keyword
				where (select pixel_keywords from pixel where id_pixel = :id_pixel) like CONCAT('%', pixel_keyword, '%')
				and expiry_date >= now()
				order by expiry_date
				limit 1  ");

			$stmt->execute(array( 
				":id_pixel"  => $id_pixel
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->expiry_date;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - pixel.php|get_closest_expiry_date_from_pixel_keyword' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_pixel  				= $obj->id_pixel;
		$this->id_vendor  				= $obj->id_vendor;
		$this->name  					= $obj->name;
		$this->image_link 				= $obj->image_link;
		$this->keywords  				= $obj->keywords;
		$this->price  					= $obj->price;
		$this->discount  				= $obj->discount;
		$this->discount_type  			= $obj->discount_type;
		$this->type  					= $obj->type;
		$this->pixel_count  			= $obj->pixel_count;
		$this->product_link  			= $obj->product_link;
		$this->message  				= $obj->message;
		$this->pixel_keywords  			= $obj->pixel_keywords;
		$this->vendor_link  			= $obj->vendor_link;

	}

	public function map($id_pixel){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from pixel
				where id_pixel = :id_pixel ");

			$stmt->execute( array( ":id_pixel" => $id_pixel ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - pixel.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from pixel 
				where id_pixel = :id_pixel");

			$stmt->execute( array( ":id_pixel"  => $this->id_pixel ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - pixel.php|delete: ' . $e->getMessage();
		}
	}

	//EXISTS?
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from pixel 
				where id_pixel = :id_pixel ");

			$stmt->execute( array( ":id_pixel" => $this->id_pixel ) );

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - pixel.php|exists' . $e->getMessage();
		}
	}

	public function pixel_keyword_exists($pixel_keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from pixel 
				where pixel_keywords like '%/$pixel_keyword/%' ");

			$stmt->execute();

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - pixel.php|pixel_keyword_exists' . $e->getMessage();
		}
	}

	//LIST
	public function get_pixels($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from pixel
				where type = 'pixel' ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$pixel = new pixel();
				$pixel->mapea($reg);

				array_push($list, $pixel);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - pixel.php|get_list' . $e->getMessage();
		}

		return $list;

	}

	public function get_pixels_by_vendor($id_vendor, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from pixel
				where type = 'pixel'
				and id_vendor = :id_vendor ".$order);

			$stmt->execute( array( ":id_vendor" => $id_vendor ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$pixel = new pixel();
				$pixel->mapea($reg);

				array_push($list, $pixel);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - pixel.php|get_pixels_by_vendor' . $e->getMessage();
		}

		return $list;

	}

	public function get_converted_pixels_by_vendor($id_vendor, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from pixel
				where type = 'product'
				and id_vendor = :id_vendor ".$order);

			$stmt->execute( array( ":id_vendor" => $id_vendor ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$pixel = new pixel();
				$pixel->mapea($reg);

				array_push($list, $pixel);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - pixel.php|get_pixels_by_vendor' . $e->getMessage();
		}

		return $list;

	}

	public function get_list_by_keyword($keyword, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from pixel
				where type in ('pixel')
				and keywords like '%/$keyword/%' ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$pixel = new pixel();
				$pixel->mapea($reg);

				array_push($list, $pixel);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - pixel.php|get_list_by_keyword' . $e->getMessage();
		}

		return $list;

	}	

	public function get_list_by_pixel_keyword($pixel_keyword, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from pixel
				where type in ('pixel', 'product')
				and pixel_keywords like '%/$pixel_keyword/%' ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$pixel = new pixel();
				$pixel->mapea($reg);

				array_push($list, $pixel);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - pixel.php|get_list_by_pixel_keyword' . $e->getMessage();
		}

		return $list;

	}	

}

?>






