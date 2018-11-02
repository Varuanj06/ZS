<?php 

	function get_the_price($id_product){
		$product 		= new product();
		$product_lang 	= new product_lang();
		
		$product->map($id_product);

		if($product->get_global() == 'yes'){
			$price 	= $product->get_global_price($product);
		}else{
			$price_db 	= (float)$product_lang->get_product_price($product->get_id_product_prestashop());
			$price 		= round( 1.05 * $price_db , 2);
		}

		return (float)$price;
	}

	function get_the_discount($id_product, $price){
		$product 	= new product();
		$product->map($id_product);

		$discount 	= 0;

		if($product->get_global() == 'yes'){ 
			// global products don't have discount
		}else{

			if($product->get_discount_type() == "percentage" ){
				$discount = round( (float)$product->get_discount() * (float)0.01 * $price, 2);
			}else{
				$discount = round( (float)$product->get_discount(), 2);
			}

			if($discount==0){
				$product_keyword = str_replace("/", "", $product->get_keywords());
				
				if($product->get_keyword_discount_type($product_keyword) == "percentage" ){
					$discount = round( (float)$product->get_keyword_discount($product_keyword) * (float)0.01 * $price, 2);
				}else{
					$discount = round( (float)$product->get_keyword_discount($product_keyword), 2);
				}

				if($discount>$price){
					$discount = $price;
				}
			}

		}

		return $discount;
	}

?>
