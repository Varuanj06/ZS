<?php

class product{
	//VARIABLES
	private $id_product;
	private $id_product_prestashop;
	private $name;
	private $link;
	private $image_link;
	private $keywords;
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
		$this->id_product_prestashop 	= "";
		$this->name 					= "";
		$this->link 					= "";
		$this->image_link 				= "";
		$this->keywords 				= "";
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

	public function get_keywords(){
		return $this->keywords;
	}

	public function set_keywords($keywords){
		$this->keywords = $keywords;
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
				insert into product(id_product, id_product_prestashop, name, link, image_link, keywords, genders, ages, like_count, share_count, love_count, discount, discount_type) 
				values (:id_product, :id_product_prestashop, :name, :link, :image_link, :keywords, :genders, :ages, :like_count, :share_count, :love_count, :discount, :discount_type) ");

			$stmt->execute( array( 
				":id_product"				=> $this->id_product,
				":id_product_prestashop"	=> $this->id_product_prestashop,
				":name"						=> $this->name,
				":link"						=> $this->link,
				":image_link"				=> $this->image_link,
				":keywords"					=> $this->keywords,
				":genders"					=> $this->genders,
				":ages"						=> $this->ages,
				":like_count"				=> $this->like_count,
				":share_count"				=> $this->share_count,
				":love_count"				=> $this->love_count,
				":discount"					=> $this->discount,
				":discount_type"			=> $this->discount_type ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - product.php|insert: ' . $e->getMessage();
		}
	}

	//UPDATE
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update product
				set
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

			$stmt->execute( array( 
				":id_product_prestashop"	=> $this->id_product_prestashop,
				":name"						=> $this->name,
				":link"						=> $this->link,
				":image_link"				=> $this->image_link,
				":like_count"				=> $this->like_count,
				":share_count"				=> $this->share_count,
				":love_count"				=> $this->love_count,
				":discount"					=> $this->discount,
				":discount_type"			=> $this->discount_type,
				":id_product" 				=> $this->id_product ) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - product.php|update: ' . $e->getMessage();
		}
	}

	public function update_social_count(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update product
				set
					like_count  			= :like_count,
					share_count  			= :share_count 
				where link 					= :link ");

			$stmt->execute( array( 
				":like_count"				=> $this->like_count,
				":share_count"				=> $this->share_count,
				":link"						=> $this->link ) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - update_social_count.php|update: ' . $e->getMessage();
		}
	}

	public function update_love_count(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update product
				set
					love_count  			= :love_count
				where id_product 			= :id_product ");

			$stmt->execute( array( 
				":love_count"				=> $this->love_count,
				":id_product"				=> $this->id_product ) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - update_love_count.php|update: ' . $e->getMessage();
		}
	}

	public function increase_love_count($id_product_prestashop, $qty){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update product
				set
					love_count  				= love_count + :qty
				where id_product_prestashop 	= :id_product_prestashop ");

			$stmt->execute( array( 
				":qty"						=> $qty,
				":id_product_prestashop"	=> $id_product_prestashop ) );

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - update_love_count.php|update: ' . $e->getMessage();
		}
	}

	public function update_keyword_data($keyword, $genders, $ages, $old_keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update product
				set
					keywords		= :keyword,
					genders			= :genders,
					ages 			= :ages
				where keywords 		= :old_keyword ");

			$stmt->execute(array( 
				":keyword"			=> "/$keyword/",
				":genders"			=> $genders,
				":ages"				=> $ages,
				":old_keyword" 		=> "/$old_keyword/"
			));

			
			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - product.php|update_keyword_data: ' . $e->getMessage();
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_product  				= $obj->id_product;
		$this->id_product_prestashop  	= $obj->id_product_prestashop;
		$this->name  					= $obj->name;
		$this->link  					= $obj->link;
		$this->image_link 				= $obj->image_link;
		$this->keywords  				= $obj->keywords;
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
				from product
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
		    echo 'ERROR: - product.php|map' . $e->getMessage();
		}
	}

	public function max_id_product(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select COALESCE(MAX(id_product)+1,1) AS maximo 
				from product ");

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->maximo;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product.php|max_id_product' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from product 
				where id_product = :id_product");

			$stmt->execute( array( ":id_product"  => $this->id_product ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - product.php|delete: ' . $e->getMessage();
		}
	}

	//EXISTS?
	public function exists(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from product 
				where id_product = :id_product ");

			$stmt->execute( array( ":id_product" => $this->id_product ) );

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product.php|exists' . $e->getMessage();
		}
	}

	public function keyword_exists($keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from product 
				where keywords = :keyword ");

			$stmt->execute( array( ":keyword" => "/$keyword/" ) );

			$count = $stmt->rowCount();
			
			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product.php|exists' . $e->getMessage();
		}
	}

	//LIST
	public function get_list($order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from product ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$product = new product();
				$product->mapea($reg);

				array_push($list, $product);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product.php|get_list' . $e->getMessage();
		}

		return $list;

	}

	public function get_list_by_keyword($keyword, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from product
				where keywords = :keyword ".$order);

			$stmt->execute(array(":keyword" => "/$keyword/"));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$product = new product();
				$product->mapea($reg);

				array_push($list, $product);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product.php|get_list' . $e->getMessage();
		}

		return $list;

	}	

	//SEARCH
	public function get_all_keywords($gender, $age, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from product 
				where genders like :gender
				and (ages like :age or ages like '%/all/%')
				and (select status from keyword where keyword = REPLACE(product.keywords, '/','')) = 'active' ".$order);

			$stmt->execute( array( ":gender" => "%$gender%", ":age" => "%$age%"  ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$product = new product();
				$product->mapea($reg);

				array_push($list, $product);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product.php|get_all_keywords' . $e->getMessage();
		}

		return $list;

	}

	public function get_brand_keywords_per_gender($gender, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from product 
				where genders like :gender
				and (select status from keyword where keyword = REPLACE(product.keywords, '/','')) = 'active'
				and keywords like '/BRAND%' ".$order);

			$stmt->execute(array( 
				":gender" => "%$gender%"  
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$product = new product();
				$product->mapea($reg);

				array_push($list, $product);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product.php|get_all_keywords_per_gender' . $e->getMessage();
		}

		return $list;

	}

	//SEARCH
	public function get_search($keyword, $gender, $age, $order){

		global $conn;

		$list = array();

		$keyword_likes 	= "";
		$arr 			= explode(',', $keyword) ;
		for ($i=0; $i < count($arr); $i++) {
			if($i>0){
				$keyword_likes .= " or ";
			}
		 	$keyword_likes .= " keywords like '%/".$arr[$i]."/%' ";
		}
		if($keyword_likes!=""){
			$keyword_likes = "and ($keyword_likes)";	
		} 

		try {

			$stmt = $conn->prepare("
				select 
					id_product,
					id_product_prestashop, 
					name, 
					link, 
					image_link, 
					keywords, 
					genders, 
					ages, 
					(select coalesce(sum(qty),0) from fb_user_product_view where id_product = product.id_product) as like_count, 
					share_count, 
					love_count, 
					(love_count) as final_count,
					discount,
					discount_type
				from product 
				where genders like :gender
				and (ages like :age or ages like '%/all/%') ".$keyword_likes." ".$order);

			$stmt->execute( array( ":gender" => "%".$gender."%", ":age" => "%".$age."%"  ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$product = new product();
				$product->mapea($reg);

				array_push($list, $product);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product.php|get_list' . $e->getMessage();
		}

		return $list;

	}

	public function get_search_by_lucky_size($size, $gender, $age, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select 
					id_product,
					id_product_prestashop, 
					name, 
					link, 
					image_link, 
					keywords, 
					genders, 
					ages, 
					(select coalesce(sum(qty),0) from fb_user_product_view where id_product = product.id_product) as like_count,  
					share_count, 
					love_count, 
					(love_count) as final_count,
					discount,
					discount_type
				from product 
				where genders like :gender
				and (ages like :age or ages like '%/all/%')
				and id_product in (select id_product from product_stock where (size = :size or size = 'Empty') and stock > 0) ".$order);

			$stmt->execute(array( 
					':gender' 	=> "%$gender%", 
					':age' 		=> "%$age%",
					':size' 	=> $size  
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$product = new product();
				$product->mapea($reg);

				array_push($list, $product);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product.php|get_search_by_lucky_size' . $e->getMessage();
		}

		return $list;

	}

	public function get_search_by_tag($id_tag, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select 
					id_product,
					id_product_prestashop, 
					name, 
					link, 
					image_link, 
					keywords, 
					genders, 
					ages, 
					(select coalesce(sum(qty),0) from fb_user_product_view where id_product = product.id_product) as like_count,  
					share_count, 
					love_count, 
					(love_count) as final_count,
					discount,
					discount_type
				from product 
				where id_product in (select id_product from product_tag where id_tag = :id_tag) ".$order);

			$stmt->execute(array( 
					':id_tag' 	=> $id_tag  
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$product = new product();
				$product->mapea($reg);

				array_push($list, $product);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product.php|get_search_by_tag' . $e->getMessage();
		}

		return $list;

	}

	public function get_search_by_fb_user($id_fb_user, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select 
					id_product,
					id_product_prestashop, 
					name, 
					link, 
					image_link, 
					keywords, 
					genders, 
					ages, 
					(select coalesce(sum(qty),0) from fb_user_product_view where id_product = product.id_product) as like_count,  
					share_count, 
					love_count, 
					(love_count) as final_count,
					discount,
					discount_type
				from product 
				where id_product in (select id_product from fb_user_product_save where id_fb_user = :id_fb_user) ".$order);

			$stmt->execute(array( 
					':id_fb_user' 	=> $id_fb_user  
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$product = new product();
				$product->mapea($reg);

				array_push($list, $product);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product.php|get_search_by_lucky_size' . $e->getMessage();
		}

		return $list;

	}

	public function get_search_limit($keyword, $gender, $age, $limit_start, $order){

		global $conn;

		$list = array();

		$keyword_likes 	= "";
		$arr 			= explode(',', $keyword) ;
		for ($i=0; $i < count($arr); $i++) {
			if($i>0){
				$keyword_likes .= " or ";
			}
		 	$keyword_likes .= " keywords like '%/".$arr[$i]."/%' ";
		}
		if($keyword_likes!=""){
			$keyword_likes = "and ($keyword_likes)";	
		} 

		try {

			$stmt = $conn->prepare("
				select 
					id_product,
					id_product_prestashop, 
					name, 
					link, 
					image_link, 
					keywords, 
					genders, 
					ages, 
					like_count, 
					share_count, 
					love_count, 
					(love_count) as final_count,
					discount,
					discount_type
				from product 
				where genders like :gender
				and (ages like :age or ages like '%/all/%') ".$keyword_likes." ".$order." /*limit ".$limit_start.", 2 this was trasnfer to the code because of the 'active' new filter on the products result */ ");

			$stmt->execute( array( ":gender" => "%".$gender."%", ":age" => "%".$age."%"  ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$product = new product();
				$product->mapea($reg);

				array_push($list, $product);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product.php|get_list' . $e->getMessage();
		}

		return $list;

	}

	//PRODUCT PRICE
	public function get_id_product_from_id_product_prestashop($id_product_prestashop){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from product 
				where id_product_prestashop = :id_product_prestashop");

			$stmt->execute( array(":id_product_prestashop" => $id_product_prestashop) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->id_product;
			}else{
				return "";
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product.php|get_id_product_from_id_product_prestashop' . $e->getMessage();
		}
	}

	public function get_keyword_discount_type($keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from keyword 
				where keyword = :keyword");

			$stmt->execute(array(
				":keyword" => $keyword
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->discount_type;
			}else{
				return "";
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product.php|get_keyword_discount_type' . $e->getMessage();
		}
	}

	public function get_keyword_discount($keyword){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from keyword 
				where keyword = :keyword");

			$stmt->execute(array(
				":keyword" => $keyword
			));

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->discount;
			}else{
				return "";
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - product.php|get_keyword_discount_type' . $e->getMessage();
		}
	}


}

?>