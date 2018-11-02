<?php 

	function get_the_price_espresso($id_product){
		$espresso_products 		= new espresso_products();
		$product_lang 			= new product_lang();
		
		$espresso_products->map($id_product);

		$price_db 	= (float)$product_lang->get_product_price($espresso_products->get_id_product_prestashop());
		$price 		= round( 1.05 * $price_db , 2);

		return (float)$price;
	}

	function get_the_discount_espresso($id_product, $price){
		$espresso_products 	= new espresso_products();
		$espresso_products->map($id_product);

		$discount 	= 0;

		if($espresso_products->get_discount_type() == "percentage" ){
			$discount = round( (float)$espresso_products->get_discount() * (float)0.01 * $price, 2);
		}else{
			$discount = round( (float)$espresso_products->get_discount(), 2);
		}

		if($discount==0){
			
			if($espresso_products->get_keyword_discount_type($espresso_products->get_id_keyword()) == "percentage" ){
				$discount = round( (float)$espresso_products->get_keyword_discount($espresso_products->get_id_keyword()) * (float)0.01 * $price, 2);
			}else{
				$discount = round( (float)$espresso_products->get_keyword_discount($espresso_products->get_id_keyword()), 2);
			}

			if($discount>$price){
				$discount = $price;
			}
		}

		return $discount;
	}

?>
