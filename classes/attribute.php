<?php

class attribute{
	//VARIABLES
	private $id_attribute;
	private $id_attribute_group;
	private $color;


	//CONSTRUCTOR
	public function attribute(){
		$this->id_attribute 			= "";
		$this->id_attribute_group 		= "";
		$this->color 			 		= "";
	}

	//GETTERS AND SETTERS
	public function get_id_attribute(){
		return $this->id_attribute;
	}

	public function set_id_attribute($id_attribute){
		$this->id_attribute = $id_attribute;
	}

	public function get_id_attribute_group(){
		return $this->id_attribute_group;
	}

	public function set_id_attribute_group($id_attribute_group){
		$this->id_attribute_group = $id_attribute_group;
	}

	public function get_color(){
		return $this->color;
	}

	public function set_color($color){
		$this->color = $color;
	}

	//SELECT
	public function mapea($obj){

		$this->id_attribute  			= $obj->id_attribute;
		$this->id_attribute_group  		= $obj->id_attribute_group;
		$this->color  					= $obj->color;

	}

	public function map($id_attribute){
		global $conn_prestashop;

		try {

			$stmt = $conn_prestashop->prepare("
				select * 
				from miracas_attribute
				where id_attribute = :id_attribute ");

			$stmt->execute( array( ":id_attribute" => $id_attribute ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			if($results){

				$this->mapea($results[0]);

				return true;
			}else{
				return false;
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - attribute.php|map' . $e->getMessage();
		}
	}
	
	public function get_sizes_of_product($id_product){

		global $conn_prestashop;

		$list = array();

		try {

			$stmt = $conn_prestashop->prepare("
				select * 
				from miracas_attribute_lang 
				where id_lang = '1'
				and id_attribute in (
					select id_attribute 
					from miracas_attribute 
					where id_attribute_group = '1' 
					and id_attribute in (
						select distinct id_attribute 
						from miracas_product_attribute_combination
						where id_product_attribute in(
							select id_product_attribute 
							from miracas_product_attribute
							where id_product = :id_product
						)
					)
				) ");

			$stmt->execute( array(':id_product' => $id_product ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				array_push($list, $reg->name);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - attribute.php|get_sizes_of_product' . $e->getMessage();
		}

		return $list;

	}

	public function get_colors_of_product($id_product){

		global $conn_prestashop;

		$list = array();

		try {

			$stmt = $conn_prestashop->prepare("
				select * 
				from miracas_attribute 
				where id_attribute_group = '2' 
				and id_attribute in (
					select distinct id_attribute 
					from miracas_product_attribute_combination
					where id_product_attribute in(
				        select id_product_attribute 
						from miracas_product_attribute
						where id_product = :id_product
					)
				) ");

			$stmt->execute( array(':id_product' => $id_product ) );

			$results = $stmt->fetchAll( PDO::FETCH_OBJ );

			foreach ($results as $reg) {
				array_push($list, $reg->color);
			}

	    } catch(PDOException $e) {
		    echo 'ERROR: - attribute.php|get_colors_of_product' . $e->getMessage();
		}

		return $list;

	}

}

?>






