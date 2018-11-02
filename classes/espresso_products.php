<?php

class espresso_products{

	//VARIABLES
	private $id_product;
	private $id_keyword;
	private $id_product_prestashop;
	private $name;
	private $link;
	private $image_link;
	private $genders;
	private $ages;
	private $like_count;
	private $share_count;
	private $love_count;
	private $discount;
	private $discount_type;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_product 				= "";
		$this->id_keyword 				= "";
		$this->id_product_prestashop 	= "";
		$this->name 					= "";
		$this->link 					= "";
		$this->image_link 				= "";
		$this->genders 					= "";
		$this->ages 					= "";
		$this->like_count 				= "";
		$this->share_count 				= "";
		$this->love_count 				= "";
		$this->discount 				= "";
		$this->discount_type 			= "";
	}

	//GETTERS AND SETTERS
	public function get_id_product(){
		return $this->id_product;
	}

	public function set_id_product($id_product){
		$this->id_product = $id_product;
	}

	public function get_id_keyword(){
		return $this->id_keyword;
	}

	public function set_id_keyword($id_keyword){
		$this->id_keyword = $id_keyword;
	}

	public function get_id_product_prestashop(){
		return $this->id_product_prestashop;
	}

	public function set_id_product_prestashop($id_product_prestashop){
		$this->id_product_prestashop = $id_product_prestashop;
	}

	public function get_name(){
		return $this->name;
	}

	public function set_name($name){
		$this->name = $name;
	}

	public function get_link(){
		return $this->link;
	}

	public function set_link($link){
		$this->link = $link;
	}

	public function get_image_link(){
		return $this->image_link;
	}

	public function set_image_link($image_link){
		$this->image_link = $image_link;
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

	public function get_like_count(){
		return $this->like_count;
	}

	public function set_like_count($like_count){
		$this->like_count = $like_count;
	}

	public function get_share_count(){
		return $this->share_count;
	}

	public function set_share_count($share_count){
		$this->share_count = $share_count;
	}

	public function get_love_count(){
		return $this->love_count;
	}

	public function set_love_count($love_count){
		$this->love_count = $love_count;
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

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into espresso_products(id_product, id_keyword, id_product_prestashop, name, link, image_link, genders, ages, like_count, share_count, love_count, discount, discount_type) 
				values (:id_product, :id_keyword, :id_product_prestashop, :name, :link, :image_link, :genders, :ages, :like_count, :share_count, :love_count, :discount, :discount_type) ");

			$stmt->execute(array( 
				":id_product"				=> $this->id_product,
				":id_keyword" 				=> $this->id_keyword,
				":id_product_prestashop"	=> $this->id_product_prestashop,
				":name"						=> $this->name,
				":link"						=> $this->link,
				":image_link"				=> $this->image_link,
				":genders"					=> $this->genders,
				":ages"						=> $this->ages,
				":like_count"				=> $this->like_count,
				":share_count"				=> $this->share_count,
				":love_count"				=> $this->love_count,
				":discount"					=> $this->discount,
				":discount_type"			=> $this->discount_type
			));

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - espresso_products.php|insert: ' . $e->getMessage();
		}
	}

	//UPDATE
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update espresso_products
				set
					id_keyword 				= :id_keyword,
					id_product_prestashop 	= :id_product_prestashop,
					name 					= :name,
					link 					= :link,
					image_link 				= :image_link,
					like_count  			= :like_count,
					share_count  			= :share_count,
					love_count  			= :love_count,
					discount  				= :discount,
					discount_type  			= :discount_type
				where id_product 			= :id_product ");

			$stmt->execute(array( 
				":id_keyword"				=> $this->id_keyword,
				":id_product_prestashop"	=> $this->id_product_prestashop,
				":name"						=> $this->name,
				":link"						=> $this->link,
				":image_link"				=> $this->image_link,
				":like_count"				=> $this->like_count,
				":share_count"				=> $this->share_count,
				":love_count"				=> $this->love_count,
				":discount"					=> $this->discount,
				":discount_type"			=> $this->discount_type,
				":id_product" 				=> $this->id_product 
			));

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - espresso_products.php|update: ' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_product  				= $obj->id_product;
		$this->id_keyword  				= $obj->id_keyword;
		$this->id_product_prestashop  	= $obj->id_product_prestashop;
		$this->name  					= $obj->name;
		$this->link  					= $obj->link;
		$this->image_link 				= $obj->image_link;
		$this->genders  				= $obj->genders;
		$this->ages  					= $obj->ages;
		$this->like_count  				= $obj->like_count;
		$this->share_count  			= $obj->share_count;
		$this->love_count  				= $obj->love_count;
		$this->discount  				= $obj->discount;
		$this->discount_type  			= $obj->discount_type;

	}

	public function map($id_product){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from espresso_products
				where id_product = :id_product ");

			$stmt->execute( array( ":id_product" => $id_product ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - espresso_products.php|map' . $e->getMessage();
		}
	}

	public function get_keyword_discount_type($id_keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from espresso_keywords 
				where id_keyword = :id_keyword");

			$stmt->execute(array(
				":id_keyword" => $id_keyword
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->discount_type;
			}else{
				return "";
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - espresso_products.php|get_keyword_discount_type' . $e->getMessage();
		}
	}

	public function get_keyword_discount($id_keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from espresso_keywords 
				where id_keyword = :id_keyword");

			$stmt->execute(array(
				":id_keyword" => $id_keyword
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->discount;
			}else{
				return "";
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - espresso_products.php|get_keyword_discount' . $e->getMessage();
		}
	}

	public function max_id_product(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select COALESCE(MAX(id_product)+1,1) AS maximo 
				from espresso_products ");

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->maximo;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - espresso_products.php|max_id_product' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from espresso_products 
				where id_product = :id_product");

			$stmt->execute( array( ":id_product"  => $this->id_product ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - espresso_products.php|delete: ' . $e->getMessage();
		}
	}

	//EXISTS?
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from espresso_products 
				where id_product = :id_product ");

			$stmt->execute( array( ":id_product" => $this->id_product ) );

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - espresso_products.php|exists' . $e->getMessage();
		}
	}

	public function keyword_exists($id_keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from espresso_products 
				where id_keyword = :id_keyword ");

			$stmt->execute(array( ":id_keyword" => $id_keyword ));

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - espresso_products.php|keyword_exists' . $e->getMessage();
		}
	}

	//LIST
	public function get_list($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from espresso_products ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$product = new product();
				$product->mapea($reg);

				array_push($list, $product);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - espresso_products.php|get_list' . $e->getMessage();
		}

		return $list;

	}

	public function get_list_by_keyword($id_keyword, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from espresso_products
				where id_keyword = :id_keyword ".$order);

			$stmt->execute(array(":id_keyword" => $id_keyword));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$espresso_products = new espresso_products();
				$espresso_products->mapea($reg);

				array_push($list, $espresso_products);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - espresso_products.php|get_list_by_keyword' . $e->getMessage();
		}

		return $list;

	}	

	public function get_search_limit($keyword, $gender, $age, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select *
				from espresso_products 
				where id_keyword = (select id_keyword from espresso_keywords where keyword = :keyword)
				and genders like :gender
				and (ages like :age or ages like '%/all/%') 
				$order
			");

			$stmt->execute(array( 
				":keyword" 		=> $keyword,
				":gender" 		=> "%".$gender."%", 
				":age" 			=> "%".$age."%"  
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$espresso_products = new espresso_products();
				$espresso_products->mapea($reg);

				array_push($list, $espresso_products);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - espresso_products.php|get_list' . $e->getMessage();
		}

		return $list;

	}

	public function get_search($id_keyword, $gender, $age, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select 
					id_keyword,
					id_product,
					id_product_prestashop, 
					name, 
					link, 
					image_link, 
					genders, 
					ages, 
					like_count, 
					share_count, 
					love_count, 
					(love_count) as final_count,
					discount,
					discount_type
				from espresso_products 
				where genders like :gender
				and (ages like :age or ages like '%/all/%') 
				and id_keyword = :id_keyword
			".$order);

			$stmt->execute(array( 
				":gender" 		=> "%$gender%", 
				":age" 			=> "%$age%",
				":id_keyword" 	=> $id_keyword  
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$espresso_products = new espresso_products();
				$espresso_products->mapea($reg);

				array_push($list, $espresso_products);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - espresso_products.php|get_list' . $e->getMessage();
		}

		return $list;

	}

}

?>