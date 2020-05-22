<?php
class ModelExtensionShippingStarMotor extends Model {
	function getQuote($address) {
		$this->load->language('extension/shipping/hp_shipping_bundle');
 
       $berat = $this->cart->getWeight();

       $unit=$this->weight->getUnit($this->config->get('config_weight_class_id'));

       if($unit == 'g') {
            $berat = $berat;
       } else if($unit == 'kg') {
            $berat = $berat * 1000;
       }

        // normalization
        if($berat == 0) {
            $berat = 1;
         }  
        
        $title = "Star MOTOR";
        
    $costs = array();
        
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('module_bundle_geo_zone_id_star') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

    if (!$this->config->get('module_bundle_geo_zone_id_star')) {
        $status = true;
    } elseif ($query->num_rows) {
        $status = true;
    } else {
        $status = false;
    }

    if(!empty($address['sub_district_id'])) {
        $costs = $this->cart->getCosts($this->config->get('module_bundle_city_id'),$address['sub_district_id'],$berat,"MOTOR","STAR");
        
                
        if ($costs && $costs['cost']) {
            $status = true;

        if ($this->cart->getSubTotal() < $this->config->get('module_bundle_total_shipping_star')) {
            $status = false;
        }

        } else {
            $status = false;
        } 

    } else {
        $status = false;
    }
	
		$method_data = array();

        
		if ($status) {
        $cost = $costs['cost'];
       
        $berat = explode('.',$berat);
		
        if(empty($berat[1]))
		{ 
			$berat[1]=0;
		   }
			/* penentuan harga */
		
        if ((float)$berat[0] < 1){
		$berat=$berat[0].".".$berat[1];	
		}
		elseif((float)$berat[1] <= 0.3){
		$berat=$berat[0];
		}
		else{
		$berat=$berat[0].".".$berat[1];
		}
            
            /*total shipping*/
		$h_fee=(float)$this->config->get('module_bundle_handling_fee');
		if($this->config->get('module_bundle_handling_fee_mode') == "perweight") {
		  $cost += ($h_fee*(int)$berat);
		} else {
		  $cost += $h_fee;
		}
            
        $quote_data = array();
            
        $cost = ($this->config->get('config_currency') == 'IDR') ? $cost : $this->currency->convert($cost,'IDR','USD');

        $quote_data['star_motor'] = array(
            'code'         => 'star_motor.star_motor',
            'title'        => sprintf($this->language->get('text_description'),$title),
            'cost'         => $cost,
            'text_kg'      => $this->language->get('text_kg'),
            'text_day'     => $this->language->get('text_day'),
            'weight'       => $berat/1000,
            'image'        => 'catalog/view/theme/default/image/shipping/star.png',
            'etd'          => $costs['etd'],
            'tax_class_id' => $this->config->get('module_bundle_tax_class_id_star'),
            'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('module_bundle_tax_class_id_star'), $this->config->get('config_tax')), $this->session->data['currency'])
        );

      		$method_data = array(
        		'code'       => 'star_motor.star_motor',
        		'title'      => sprintf($this->language->get('text_title'),$title),
        		'quote'      => $quote_data,
				'sort_order' => $this->config->get('shipping_star_motor_sort_order'),
        		'error'      => false
      		);
		}
	
		return $method_data;
	}
}