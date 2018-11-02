<?php

class purchase_order_row{
	//VARIABLES
	private $id_row;
	private $id_purchase_order;
	private $id_orders;
	private $id_order_details;
	private $payment;
	private $qty;
	private $image_url;
	private $product_link;
	private $item;
	private $color;
	private $size;
	private $asian_color;
	private $asian_size;
	private $comment;
	private $comment_agent;
	private $comment_qc;
	private $price;
	private $row_added;
	private $id_vendor_product;
	private $id_product_lang;
	private $out_of_stock;
	private $discontinued;

	//CONSTRUCTOR
	public function __construct(){
		$this->id_row 				= "";
		$this->id_purchase_order 	= "";
		$this->id_orders 			= "";
		$this->id_order_details 	= "";
		$this->payment 				= "";
		$this->qty 					= "";
		$this->image_url 			= "";
		$this->product_link 		= "";
		$this->item 				= "";
		$this->color 				= "";
		$this->size 				= "";
		$this->asian_color 			= "";
		$this->asian_size 			= "";
		$this->comment 	 			= "";
		$this->comment_agent		= "";
		$this->comment_qc 			= "";
		$this->price 	 			= "";
		$this->row_added 			= "N";
		$this->id_vendor_product 	= "0";
		$this->id_product_lang 		= "0";
		$this->out_of_stock 		= "0";
		$this->discontinued 		= "0";
	}

	//GETTERS AND SETTERS
	public function get_id_row(){
		return $this->id_row;
	}

	public function set_id_row($id_row){
		$this->id_row = $id_row;
	}

	public function get_id_purchase_order(){
		return $this->id_purchase_order;
	}

	public function set_id_purchase_order($id_purchase_order){
		$this->id_purchase_order = $id_purchase_order;
	}

	public function get_id_orders(){
		return $this->id_orders;
	}

	public function set_id_orders($id_orders){
		$this->id_orders = $id_orders;
	}

	public function get_id_order_details(){
		return $this->id_order_details;
	}

	public function set_id_order_details($id_order_details){
		$this->id_order_details = $id_order_details;
	}

	public function get_payment(){
		return $this->payment;
	}

	public function set_payment($payment){
		$this->payment = $payment;
	}

	public function get_qty(){
		return $this->qty;
	}

	public function set_qty($qty){
		$this->qty = $qty;
	}

	public function get_image_url(){
		return $this->image_url;
	}

	public function set_image_url($image_url){
		$this->image_url = $image_url;
	}

	public function get_product_link(){
		return $this->product_link;
	}

	public function set_product_link($product_link){
		$this->product_link = $product_link;
	}

	public function get_item(){
		return $this->item;
	}

	public function set_item($item){
		$this->item = $item;
	}

	public function get_color(){
		return $this->color;
	}

	public function set_color($color){
		$this->color = $color;
	}
	
	public function get_size(){
		return $this->size;
	}

	public function set_size($size){
		$this->size = $size;
	}

	public function get_asian_color(){
		return $this->asian_color;
	}

	public function set_asian_color($asian_color){
		$this->asian_color = $asian_color;
	}

	public function get_asian_size(){
		return $this->asian_size;
	}

	public function set_asian_size($asian_size){
		$this->asian_size = $asian_size;
	}

	public function get_comment(){
		return $this->comment;
	}

	public function set_comment($comment){
		$this->comment = $comment;
	}

	public function get_comment_agent(){
		return $this->comment_agent;
	}

	public function set_comment_agent($comment_agent){
		$this->comment_agent = $comment_agent;
	}

	public function get_comment_qc(){
		return $this->comment_qc;
	}

	public function set_comment_qc($comment_qc){
		$this->comment_qc = $comment_qc;
	}

	public function get_price(){
		return $this->price;
	}

	public function set_price($price){
		$this->price = $price;
	}

	public function get_row_added(){
		return $this->row_added;
	}

	public function set_row_added($row_added){
		$this->row_added = $row_added;
	}

	public function get_id_vendor_product(){
		return $this->id_vendor_product;
	}

	public function set_id_vendor_product($id_vendor_product){
		$this->id_vendor_product = $id_vendor_product;
	}

	public function get_id_product_lang(){
		return $this->id_product_lang;
	}

	public function set_id_product_lang($id_product_lang){
		$this->id_product_lang = $id_product_lang;
	}

	public function get_out_of_stock(){
		return $this->out_of_stock;
	}

	public function set_out_of_stock($out_of_stock){
		$this->out_of_stock = $out_of_stock;
	}

	public function get_discontinued(){
		return $this->discontinued;
	}

	public function set_discontinued($discontinued){
		$this->discontinued = $discontinued;
	}

	//INSERT
	public function insert(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				insert into purchase_order_row(id_purchase_order, id_orders, id_order_details, payment, qty, image_url, product_link, item, color, size, asian_color, asian_size, comment, comment_agent, comment_qc, price, row_added, id_vendor_product, id_product_lang, out_of_stock, discontinued) 
				values (:id_purchase_order, :id_orders, :id_order_details, :payment, :qty, :image_url, :product_link, :item, :color, :size, :asian_color, :asian_size, :comment, :comment_agent, :comment_qc, :price, :row_added, :id_vendor_product, :id_product_lang, '', '') ");

			$stmt->execute( array( 
				":id_purchase_order"	=> $this->id_purchase_order,
				":id_orders"			=> $this->id_orders,
				":id_order_details"		=> $this->id_order_details,
				":payment"				=> $this->payment,
				":qty"					=> $this->qty,
				":image_url"			=> $this->image_url,
				":product_link"			=> $this->product_link,
				":item"					=> $this->item,
				":color"				=> $this->color,
				":size"					=> $this->size,
				":asian_color"			=> $this->asian_color,
				":asian_size"			=> $this->asian_size,
				":comment"				=> $this->comment,
				":comment_agent"		=> $this->comment_agent,
				":comment_qc"			=> $this->comment_qc,
				":price"				=> $this->price,
				":row_added"			=> $this->row_added,
				":id_vendor_product"	=> $this->id_vendor_product,
				":id_product_lang"		=> $this->id_product_lang ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - purchase_order_row.php|insert: ' . $e->getMessage();
		    return false;
		}
	}

	//UPDATE
	public function update(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				update purchase_order_row
				set
					id_purchase_order 		= :id_purchase_order,
					id_orders				= :id_orders,
					id_order_details		= :id_order_details,
					payment					= :payment,
					qty						= :qty,
					image_url				= :image_url,
					product_link			= :product_link,
					item					= :item,
					color					= :color,
					size					= :size,
					asian_color				= :asian_color,
					asian_size				= :asian_size,
					comment					= :comment,
					comment_agent			= :comment_agent,
					comment_qc				= :comment_qc,
					price					= :price,
					row_added				= :row_added,
					id_vendor_product		= :id_vendor_product,
					id_product_lang			= :id_product_lang,
					out_of_stock			= :out_of_stock,
					discontinued			= :discontinued
				where id_row 	= :id_row ");

			$stmt->execute( array( 
				":id_purchase_order"	=> $this->id_purchase_order,
				":id_orders"			=> $this->id_orders,
				":id_order_details"		=> $this->id_order_details,
				":payment"				=> $this->payment,
				":qty"					=> $this->qty,
				":image_url"			=> $this->image_url,
				":product_link"			=> $this->product_link,
				":item"					=> $this->item,
				":color"				=> $this->color,
				":size"					=> $this->size,
				":asian_color"			=> $this->asian_color,
				":asian_size"			=> $this->asian_size,
				":comment"				=> $this->comment,
				":comment_agent"		=> $this->comment_agent,
				":comment_qc"			=> $this->comment_qc,
				":price"				=> $this->price,
				":row_added"			=> $this->row_added,
				":id_vendor_product"	=> $this->id_vendor_product,
				":id_product_lang"		=> $this->id_product_lang,
				":out_of_stock"			=> $this->out_of_stock,
				":discontinued"			=> $this->discontinued,
				":id_row"				=> $this->id_row, 
			) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return true;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - purchase_order_row.php|update: ' . $e->getMessage();
		    return false;
		}
	}

	//SELECT
	public function mapea($obj){

		$this->id_row  					= $obj->id_row;
		$this->id_purchase_order  		= $obj->id_purchase_order;
		$this->id_orders				= $obj->id_orders;
		$this->id_order_details			= $obj->id_order_details;
		$this->payment					= $obj->payment;
		$this->qty						= $obj->qty;
		$this->image_url				= $obj->image_url;
		$this->product_link				= $obj->product_link;
		$this->item						= $obj->item;
		$this->color					= $obj->color;
		$this->size						= $obj->size;
		$this->asian_color				= $obj->asian_color;
		$this->asian_size				= $obj->asian_size;
		$this->comment					= $obj->comment;
		$this->comment_agent			= $obj->comment_agent;
		$this->comment_qc				= $obj->comment_qc;
		$this->price					= $obj->price;
		$this->row_added				= $obj->row_added;
		$this->id_vendor_product		= $obj->id_vendor_product;
		$this->id_product_lang  		= $obj->id_product_lang;
		$this->out_of_stock  			= $obj->out_of_stock;
		$this->discontinued  			= $obj->discontinued;

	}

	public function map($id_row){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select * 
				from purchase_order_row
				where id_row = :id_row ");

			$stmt->execute( array( ":id_row" => $id_row ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - purchase_order_row.php|map' . $e->getMessage();
		}
	}

	//DELETE
	public function delete(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from purchase_order_row 
				where id_row = :id_row");

			$stmt->execute( array( ":id_row"  => $this->id_row ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - purchase_order_row.php|delete: ' . $e->getMessage();
		}
	}

	//DELETE
	public function delete_by_id_purchase_order($id_purchase_order){
		global $conn;

		try {

			$stmt = $conn->prepare("
				delete from purchase_order_row 
				where id_purchase_order = :id_purchase_order");

			$stmt->execute( array( ":id_purchase_order"  => $id_purchase_order ) );

			$count = $stmt->rowCount();

			if($count>0){
				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR - purchase_order_row.php|delete_by_id_purchase_order: ' . $e->getMessage();
		}
	}

	//LIST
	public function get_all_from_id_purchase_order($id_purchase_order, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from purchase_order_row where id_purchase_order = :id_purchase_order ".$order);

			$stmt->execute( array( ":id_purchase_order" => $id_purchase_order ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$purchase_order_row = new purchase_order_row();
				$purchase_order_row->mapea($reg);

				array_push($list, $purchase_order_row);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - purchase_order_row.php|get_all_from_id_purchase_order' . $e->getMessage();
		}

		return $list;

	}

	public function get_all_from_url($url, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from purchase_order_row where id_purchase_order = (select id_purchase_order from purchase_order where url = :url) ".$order);

			$stmt->execute( array( ":url" => $url ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$purchase_order_row = new purchase_order_row();
				$purchase_order_row->mapea($reg);

				array_push($list, $purchase_order_row);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - purchase_order_row.php|get_all_from_url' . $e->getMessage();
		}

		return $list;

	}

	public function get_all_unavailable_products($column, $order){

		global $conn;

		$list = array();

		try {

			$stmt = $conn->prepare("
				select * 
				from purchase_order_row 
				where $column = 'yes' ".$order);

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				$purchase_order_row = new purchase_order_row();
				$purchase_order_row->mapea($reg);

				array_push($list, $purchase_order_row);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - purchase_order_row.php|get_all_unavailable_products' . $e->getMessage();
		}

		return $list;

	}

	//MAXIMUM
	public function max_id_row(){
		global $conn;

		try {

			$stmt = $conn->prepare("
				select COALESCE(MAX(id_row)+1,1) AS max 
				from purchase_order_row ");

			$stmt->execute();

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){
				return $results[0]->max;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - purchase_order_row.php|max_id_row' . $e->getMessage();
		}
	}


}

?>






