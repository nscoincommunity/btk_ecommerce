<?php
class ModelExtensionShippingJneTrucking extends Model {
	function getQuote($address) {
		$this->load->language('extension/shipping/jne_trucking');
   $costs = array();
   $berat = $this->cart->getWeight();
   $unit=$this->weight->getUnit($this->config->get('config_weight_class_id'));
      
// format in KG
    if($unit == 'g') {
   $berat = round($berat / 1000,1,PHP_ROUND_HALF_UP);
   } else if($unit == 'kg') {
    $berat = round($berat,1,PHP_ROUND_HALF_UP);
   }
        
    $title = "JNE Trucking";        
        
    $costs = array();
    if(!empty($address['sub_district_id']) && ($berat >= $this->config->get('module_bundle_trucking_weight'))) {
        
        $costs = $this->cart->getCosts($this->config->get('module_bundle_city_id'),$address['sub_district_id'],$berat*1000,"JTR","JNE");

    }
		if ($costs && $costs['cost']) {
			$status = true;

                if ($this->cart->getSubTotal() < $this->config->get('module_bundle_trucking_total')) {
			$status = false;
		}
		
		} else {
			$status = false;
		} // else of address selection query
		

		
		$method_data = array();

        
		if ($status) {
            
		$text=false;
		$query_filter=$this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_shipping_filtered WHERE shipping_code='module_bundle_trucking.jne_trucking'");
		if((int)$query_filter->row['total'] > 0) {
		
		$products_data=$this->getProducts();
		if(!empty($products_data))
		{
				/*create array from product_data*/
			$products_id=array();
			foreach($products_data as $k => $v) {
			array_push($products_id,$v['product_id']);
			}

		/*select product notification*/
		$arr_error=array();
		$arr_test=array();
	
		$products=$this->getProductFiltered('module_bundle_trucking.jne_trucking');
	    foreach ($products as $product_id) {
			array_push($arr_test,$product_id);
		}

		  $product_error=$this->is_in_array($arr_test,$products_id);
		 if($product_error || gettype($product_error) == 'array') {
				$text=$this->language->get('text_method_failed')."<br/> <ol>";
				foreach($products_data as $k => $v) {
					if(in_array($v['product_id'],$product_error))
						$text.="<li>".$v['name']."</li>";
				}  
				$text.="</ol>";
			} 
		else {
			$text=false;
			} 
		}
	} // end of filter products 
        $cost = $costs['cost'];

            /*total shipping*/
		$h_fee=(float)$this->config->get('module_bundle_handling_fee');
		if($this->config->get('module_bundle_handling_fee_mode') == "perweight") {
		  $cost += ($h_fee*(int)$berat);
		} else {
		  $cost += $h_fee;
		}
      
            
			$quote_data = array();
			
      		$quote_data['module_bundle_trucking'] = array(
        		'code'         => 'module_bundle_trucking.jne_trucking',
        		'title'        => sprintf($this->language->get('text_description')),
        		'cost'         => $cost,
                'text_kg'      => $this->language->get('text_kg'),
        		'text_day'     => $this->language->get('text_day'),
        		'weight'       => $berat,
                 'image'       => 'catalog/view/theme/default/image/shipping/jne.png',
        		'etd'          => $costs['etd'],
        		'tax_class_id' => $this->config->get('module_bundle_trucking_tax_class_id'),
                'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('shipping_jne_trucking_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'])
      		);

      		$method_data = array(
        		'code'       => 'module_bundle_trucking.jne_trucking',
        		'title'      => sprintf($this->language->get('text_title'),$title),
        		'quote'      => $quote_data,
				'sort_order' => $this->config->get('shipping_jne_trucking_sort_order'),
        		'error'      => $text
      		);
		}
	
		return $method_data;
	}