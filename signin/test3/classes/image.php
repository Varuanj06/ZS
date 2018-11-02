<?php

class image{
	//VARIABLES
	private $id_image;
	private $id_product;

	//CONSTRUCTOR
	public function image(){
		$this->id_image 		= "";
		$this->id_product 		= "";
	}

	//GETTERS AND SETTERS
	public function get_id_image(){
		return $this->id_image;
	}

	public function set_id_image($id_image){
		$this->id_image = $id_image;
	}

	public function get_id_product(){
		return $this->id_product;
	}

	public function set_id_product($id_product){
		$this->id_product = $id_product;
	}

	//SELECT
	public function mapea($obj){

		$this->id_image  	= $obj->id_image;
		$this->id_product  	= $obj->id_product;

	}

	public function map($id_image){
		global $conn_prestashop;

		try {

			$stmt = $conn_prestashop->prepare("
				select * 
				from miracas_image
				where id_image = :id_image ");

			$stmt->execute( array( ":id_image" => $id_image ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - image.php|map' . $e->getMessage();
		}
	}

	
	public function get_images($id_product){

		global $conn_prestashop;

		$list = array();

		try {

			$stmt = $conn_prestashop->prepare("
				select * 
				from miracas_image 
				where id_product = :id_product order by position ");

			$stmt->execute( array(':id_product' => $id_product ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				array_push($list, $reg->id_image);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - image.php|get_images' . $e->getMessage();
		}

		return $list;

	}

}

?>






