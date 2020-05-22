<?php
class ControllerExtensionAddress extends Controller {
	public function index(){
		$this->load->language('extension/checkout');

		$data['text_address_existing'] = $this->language->get('text_address_existing');
		$data['text_address_new'] = $this->language->get('text_address_new');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_loading'] = $this->language->get('text_loading');

		$data['entry_firstname'] = $this->language->get('entry_firstname');
		$data['entry_lastname'] = $this->language->get('entry_lastname');
		$data['entry_company'] = $this->language->get('entry_company');
		$data['entry_address_1'] = $this->language->get('entry_address_1');
		$data['entry_address_2'] = $this->language->get('entry_address_2');
		$data['entry_postcode'] = $this->language->get('entry_postcode');
		$data['entry_city'] = $this->language->get('entry_city');
		$data['entry_country'] = $this->language->get('entry_country');
		$data['entry_zone'] = $this->language->get('entry_zone');

		$data['button_continue'] = $this->language->get('button_continue');
		$data['button_upload'] = $this->language->get('button_upload');
		
		if(isset($this->request->get['address_type'])){
			$address_type = $this->request->get['address_type'];
		}else{
			$address_type = 'payment_address';
		}
		
		if(!empty($this->request->get['address_same'])){
			$shipping_status = $this->request->get['address_same'];
		}else{
			$shipping_status = '';
		}
		
		if(!empty($this->request->get['guest'])){
			$guest = $this->request->get['guest'];
		}else{
			$guest = '';
		}
		
		$data['address_type']= $address_type;
		$data['shipping_status']= $shipping_status;
		$data['guest']= $guest;

		if (isset($this->session->data[$address_type]['address_id'])) {
			$data['address_id'] = $this->session->data[$address_type]['address_id'];
		} else {
			$data['address_id'] = $this->customer->getAddressId();
		}

		$this->load->model('account/address');
		
		
		if (isset($this->session->data[$address_type]['firstname'])) {
			$data['firstname'] = $this->session->data[$address_type]['firstname'];
		} else {
			$data['firstname'] = '';
		}
		
		if (isset($this->session->data[$address_type]['lastname'])) {
			$data['lastname'] = $this->session->data[$address_type]['lastname'];
		} else {
			$data['lastname'] = '';
		}
		
		if (isset($this->session->data[$address_type]['company'])) {
			$data['company'] = $this->session->data[$address_type]['company'];
		} else {
			$data['company'] = '';
		}
		
		if (isset($this->session->data[$address_type]['address_1'])) {
			$data['address_1'] = $this->session->data[$address_type]['address_1'];
		} else {
			$data['address_1'] = '';
		}

		if (isset($this->session->data[$address_type]['address_2'])) {
			$data['address_2'] = $this->session->data[$address_type]['address_2'];
		} else {
			$data['address_2'] = '';
		}

		if (isset($this->session->data[$address_type]['postcode'])) {
			$data['postcode'] = $this->session->data[$address_type]['postcode'];
		} elseif (isset($this->session->data[$address_type]['postcode'])) {
			$data['postcode'] = $this->session->data[$address_type]['postcode'];
		} else {
			$data['postcode'] = '';
		}

		if (isset($this->session->data[$address_type]['city'])) {
			$data['city'] = $this->session->data[$address_type]['city'];
		} else {
			$data['city'] = '';
		}

		if (isset($this->session->data[$address_type]['country_id'])) {
			$data['country_id'] = $this->session->data[$address_type]['country_id'];
		} else {
			$data['country_id'] = $this->config->get('config_country_id');
		}

		if (isset($this->session->data[$address_type]['zone_id'])) {
			$data['zone_id'] = $this->session->data[$address_type]['zone_id'];
		} else {
			$data['zone_id'] = '';
		}

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		// Custom Fields
		$this->load->model('account/custom_field');

		$data['custom_fields'] = $this->model_account_custom_field->getCustomFields();

		if (isset($this->session->data['guest']['custom_field'])) {
			if (isset($this->session->data['guest']['custom_field'])) {
				$guest_custom_field = $this->session->data['guest']['custom_field'];
			} else {
				$guest_custom_field = array();
			}

			if (isset($this->session->data[$address_type]['custom_field'])) {
				$address_custom_field = $this->session->data[$address_type]['custom_field'];
			} else {
				$address_custom_field = array();
			}

			$data['guest_custom_field'] = $guest_custom_field + $address_custom_field;
		} else {
			$data['guest_custom_field'] = array();
		}
		

		$data['addresses'] = $this->model_account_address->getAddresses();

		if (isset($this->session->data[$address_type]['country_id'])) {
			$data['country_id'] = $this->session->data[$address_type]['country_id'];
		} else {
			$data['country_id'] = $this->config->get('config_country_id');
		}

		if (isset($this->session->data[$address_type]['zone_id'])) {
			$data['zone_id'] = $this->session->data[$address_type]['zone_id'];
		} else {
			$data['zone_id'] = '';
		}

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		// Custom Fields
		$this->load->model('account/custom_field');

		$data['custom_fields'] = $this->model_account_custom_field->getCustomFields($this->config->get('config_customer_group_id'));

		if (isset($this->session->data[$address_type]['custom_field'])) {
			$data['payment_address_custom_field'] = $this->session->data[$address_type]['custom_field'];
		} else {
			$data['payment_address_custom_field'] = array();
		}

		$this->response->setOutput($this->load->view('extension/stepcheckout/address', $data));
	}

	public function save(){
		$this->load->language('checkout/checkout');

		$json = array();

		// Validate if customer is logged in.
		if (!$this->customer->isLogged() && !isset($this->request->post['guest'])){
			$json['redirect'] = $this->url->link('checkout/checkout', '', true);
		}

		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
		}

		// Validate minimum quantity requirements.
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$json['redirect'] = $this->url->link('checkout/cart');

				break;
			}
		}

		if (!$json) {
			if(!empty($this->request->post['address_same'])){
			 $address_same	= $this->request->post['address_same'];
			 $this->session->data['address_same'] = $address_same;
			}else{
			 $address_same	= '';
			 $this->session->data['address_same'] = '';
			}
			
			if(!empty($this->request->post['address_type'])){
			 $address_type	= $this->request->post['address_type'];
			}else{
			 $address_type	= '';
			}
	
			
			if(!empty($this->request->post['guest'])){
			 $guest	= $this->request->post['guest'];
			}else{
			 $guest	= '';
			}
			
			if($guest){
				
				if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
					$json['error']['firstname'] = $this->language->get('error_firstname');
				}

				if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
					$json['error']['lastname'] = $this->language->get('error_lastname');
				}

				if ((utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128)) {
					$json['error']['address_1'] = $this->language->get('error_address_1');
				}

				if ((utf8_strlen(trim($this->request->post['city'])) < 2) || (utf8_strlen(trim($this->request->post['city'])) > 128)) {
					$json['error']['city'] = $this->language->get('error_city');
				}

				$this->load->model('localisation/country');

				$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

				if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
					$json['error']['postcode'] = $this->language->get('error_postcode');
				}

				if ($this->request->post['country_id'] == '') {
					$json['error']['country'] = $this->language->get('error_country');
				}

				if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '' || !is_numeric($this->request->post['zone_id'])) {
					$json['error']['zone'] = $this->language->get('error_zone');
				}

				// Custom field validation
				$this->load->model('account/custom_field');

				$custom_fields = $this->model_account_custom_field->getCustomFields($this->session->data['guest']['customer_group_id']);

				foreach ($custom_fields as $custom_field) {
					if (($custom_field['location'] == 'address') && $custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['custom_field_id']])) {
						$json['error']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
					} elseif (($custom_field['location'] == 'address') && ($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
						$json['error']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
					}
				}
			
			
				if (!$json) {
					if(!$address_same){
						$this->session->data[$address_type]['firstname'] = $this->request->post['firstname'];
						$this->session->data[$address_type]['lastname'] = $this->request->post['lastname'];
						$this->session->data[$address_type]['company'] = $this->request->post['company'];
						$this->session->data[$address_type]['address_1'] = $this->request->post['address_1'];
						$this->session->data[$address_type]['address_2'] = $this->request->post['address_2'];
						$this->session->data[$address_type]['postcode'] = $this->request->post['postcode'];
						$this->session->data[$address_type]['city'] = $this->request->post['city'];
						$this->session->data[$address_type]['country_id'] = $this->request->post['country_id'];
						$this->session->data[$address_type]['zone_id'] = $this->request->post['zone_id'];

						$this->load->model('localisation/country');

						$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

						if ($country_info) {
							$this->session->data[$address_type]['country'] = $country_info['name'];
							$this->session->data[$address_type]['iso_code_2'] = $country_info['iso_code_2'];
							$this->session->data[$address_type]['iso_code_3'] = $country_info['iso_code_3'];
							$this->session->data[$address_type]['address_format'] = $country_info['address_format'];
						} else {
							$this->session->data[$address_type]['country'] = '';
							$this->session->data[$address_type]['iso_code_2'] = '';
							$this->session->data[$address_type]['iso_code_3'] = '';
							$this->session->data[$address_type]['address_format'] = '';
						}

						$this->load->model('localisation/zone');

						$zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);
						
						if ($zone_info) {
							$this->session->data[$address_type]['zone'] = $zone_info['name'];
							$this->session->data[$address_type]['zone_code'] = $zone_info['code'];
						} else {
							$this->session->data[$address_type]['zone'] = '';
							$this->session->data[$address_type]['zone_code'] = '';
						}

						if (isset($this->request->post['custom_field'])) {
							$this->session->data[$address_type]['custom_field'] = $this->request->post['custom_field'];
						} else {
							$this->session->data[$address_type]['custom_field'] = array();
						}
						
						
						if($address_type=='payment_address'){
							unset($this->session->data['payment_method']);
							unset($this->session->data['payment_methods']);
						}else{
							unset($this->session->data['shipping_method']);
							unset($this->session->data['shipping_methods']);
						}
					}else{
						//SET PAYMENT ADDRESS
						$this->session->data['payment_address']['firstname'] = $this->request->post['firstname'];
						$this->session->data['payment_address']['lastname'] = $this->request->post['lastname'];
						$this->session->data['payment_address']['company'] = $this->request->post['company'];
						$this->session->data['payment_address']['address_1'] = $this->request->post['address_1'];
						$this->session->data['payment_address']['address_2'] = $this->request->post['address_2'];
						$this->session->data['payment_address']['postcode'] = $this->request->post['postcode'];
						$this->session->data['payment_address']['city'] = $this->request->post['city'];
						$this->session->data['payment_address']['country_id'] = $this->request->post['country_id'];
						$this->session->data['payment_address']['zone_id'] = $this->request->post['zone_id'];

						$this->load->model('localisation/country');

						$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

						if ($country_info) {
							$this->session->data['payment_address']['country'] = $country_info['name'];
							$this->session->data['payment_address']['iso_code_2'] = $country_info['iso_code_2'];
							$this->session->data['payment_address']['iso_code_3'] = $country_info['iso_code_3'];
							$this->session->data['payment_address']['address_format'] = $country_info['address_format'];
						} else {
							$this->session->data['payment_address']['country'] = '';
							$this->session->data['payment_address']['iso_code_2'] = '';
							$this->session->data['payment_address']['iso_code_3'] = '';
							$this->session->data['payment_address']['address_format'] = '';
						}

						if (isset($this->request->post['custom_field']['address'])) {
							$this->session->data['payment_address']['custom_field'] = $this->request->post['custom_field']['address'];
						} else {
							$this->session->data['payment_address']['custom_field'] = array();
						}

						$this->load->model('localisation/zone');

						$zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);

						if ($zone_info) {
							$this->session->data['payment_address']['zone'] = $zone_info['name'];
							$this->session->data['payment_address']['zone_code'] = $zone_info['code'];
						} else {
							$this->session->data['payment_address']['zone'] = '';
							$this->session->data['payment_address']['zone_code'] = '';
						}
						
						
						
						unset($this->session->data['payment_method']);
						unset($this->session->data['payment_methods']);
						
						
						//SET SHIPPING ADDRESS
						if ($this->cart->hasShipping()){
						$this->session->data['shipping_address']['firstname'] = $this->request->post['firstname'];
						$this->session->data['shipping_address']['lastname'] = $this->request->post['lastname'];
						$this->session->data['shipping_address']['company'] = $this->request->post['company'];
						$this->session->data['shipping_address']['address_1'] = $this->request->post['address_1'];
						$this->session->data['shipping_address']['address_2'] = $this->request->post['address_2'];
						$this->session->data['shipping_address']['postcode'] = $this->request->post['postcode'];
						$this->session->data['shipping_address']['city'] = $this->request->post['city'];
						$this->session->data['shipping_address']['country_id'] = $this->request->post['country_id'];
						$this->session->data['shipping_address']['zone_id'] = $this->request->post['zone_id'];

						$this->load->model('localisation/country');

						$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

						if ($country_info) {
							$this->session->data['shipping_address']['country'] = $country_info['name'];
							$this->session->data['shipping_address']['iso_code_2'] = $country_info['iso_code_2'];
							$this->session->data['shipping_address']['iso_code_3'] = $country_info['iso_code_3'];
							$this->session->data['shipping_address']['address_format'] = $country_info['address_format'];
						} else {
							$this->session->data['shipping_address']['country'] = '';
							$this->session->data['shipping_address']['iso_code_2'] = '';
							$this->session->data['shipping_address']['iso_code_3'] = '';
							$this->session->data['shipping_address']['address_format'] = '';
						}

						$this->load->model('localisation/zone');

						$zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);

						if ($zone_info) {
							$this->session->data['shipping_address']['zone'] = $zone_info['name'];
							$this->session->data['shipping_address']['zone_code'] = $zone_info['code'];
						} else {
							$this->session->data['shipping_address']['zone'] = '';
							$this->session->data['shipping_address']['zone_code'] = '';
						}

						if (isset($this->request->post['custom_field'])) {
							$this->session->data['shipping_address']['custom_field'] = $this->request->post['custom_field'];
						} else {
							$this->session->data['shipping_address']['custom_field'] = array();
						}
						
						unset($this->session->data['shipping_method']);
						unset($this->session->data['shipping_methods']);

						}
						
					}	
				}
				
			}else{
			
				if (isset($this->request->post[$address_type]) && $this->request->post[$address_type] == 'existing' && (!empty($this->request->post['address_id']) && $this->request->post['address_id'] != 'new')){
					// Default Payment Address
					$this->load->model('account/address');
					
					
					if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
						$json['error']['firstname'] = $this->language->get('error_firstname');
					}

					if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
						$json['error']['lastname'] = $this->language->get('error_lastname');
					}

					if ((utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128)) {
						$json['error']['address_1'] = $this->language->get('error_address_1');
					}

					if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 32)) {
						$json['error']['city'] = $this->language->get('error_city');
					}

					$this->load->model('localisation/country');

					$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

					if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
						$json['error']['postcode'] = $this->language->get('error_postcode');
					}

					if ($this->request->post['country_id'] == '') {
						$json['error']['country'] = $this->language->get('error_country');
					}

					if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '' || !is_numeric($this->request->post['zone_id'])) {
						$json['error']['zone'] = $this->language->get('error_zone');
					}

					// Custom field validation
					$this->load->model('account/custom_field');

					$custom_fields = $this->model_account_custom_field->getCustomFields($this->config->get('config_customer_group_id'));

					foreach ($custom_fields as $custom_field) {
						if (($custom_field['location'] == 'address') && $custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['custom_field_id']])) {
							$json['error']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
						} elseif (($custom_field['location'] == 'address') && ($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
							$json['error']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
						}
					}
						
						
					if (!$json){
						$this->model_account_address->editAddress($this->request->post['address_id'],$this->request->post);
						if($address_same){
							$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->request->post['address_id']);
							unset($this->session->data['payment_method']);
							unset($this->session->data['payment_methods']);
							
							if ($this->cart->hasShipping()){
								$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->request->post['address_id']);
								unset($this->session->data['shipping_method']);
								unset($this->session->data['shipping_methods']);
							}
						}else{
							$this->session->data[$address_type] = $this->model_account_address->getAddress($this->request->post['address_id']);
							if($address_type=='payment_address'){
								unset($this->session->data['payment_method']);
								unset($this->session->data['payment_methods']);
							}else{
								unset($this->session->data['shipping_method']);
								unset($this->session->data['shipping_methods']);
							}
						}
					}
					
				} else {
					if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
						$json['error']['firstname'] = $this->language->get('error_firstname');
					}

					if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
						$json['error']['lastname'] = $this->language->get('error_lastname');
					}

					if ((utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128)) {
						$json['error']['address_1'] = $this->language->get('error_address_1');
					}

					if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 32)) {
						$json['error']['city'] = $this->language->get('error_city');
					}

					$this->load->model('localisation/country');

					$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

					if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
						$json['error']['postcode'] = $this->language->get('error_postcode');
					}

					if ($this->request->post['country_id'] == '') {
						$json['error']['country'] = $this->language->get('error_country');
					}

					if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '' || !is_numeric($this->request->post['zone_id'])) {
						$json['error']['zone'] = $this->language->get('error_zone');
					}

					// Custom field validation
					$this->load->model('account/custom_field');

					$custom_fields = $this->model_account_custom_field->getCustomFields($this->config->get('config_customer_group_id'));

					foreach ($custom_fields as $custom_field) {
						if (($custom_field['location'] == 'address') && $custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['custom_field_id']])) {
							$json['error']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
						} elseif (($custom_field['location'] == 'address') && ($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
							$json['error']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
						}
					}

					if (!$json) {
						// Default Payment Address
						$this->load->model('account/address');

						$address_id = $this->model_account_address->addAddress($this->customer->getId(),$this->request->post);
		
						if($address_same){
							$this->session->data['payment_address'] = $this->model_account_address->getAddress($address_id);
							if ($this->cart->hasShipping()){
							$this->session->data['shipping_address'] = $this->model_account_address->getAddress($address_id);
							
							unset($this->session->data['shipping_method']);
							unset($this->session->data['shipping_methods']);
							}

							unset($this->session->data['payment_method']);
							unset($this->session->data['payment_methods']);
						
						}else{
							$this->session->data[$address_type] = $this->model_account_address->getAddress($address_id);
							
							
							if($address_type=='payment_address'){
								unset($this->session->data['payment_method']);
								unset($this->session->data['payment_methods']);
							}else{
								unset($this->session->data['shipping_method']);
								unset($this->session->data['shipping_methods']);
							}
						}

						if ($this->config->get('config_customer_activity')) {
							$this->load->model('account/activity');

							$activity_data = array(
								'customer_id' => $this->customer->getId(),
								'name'        => $this->customer->getFirstName() . ' ' . $this->customer->getLastName()
							);

							$this->model_account_activity->addActivity('address_add', $activity_data);
						}
					}
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function selectaddress(){
		$json=array();
		$this->load->model('account/address');
		if(isset($this->request->get['address_id'])){
			 $address_id = $this->request->get['address_id'];
		
			
			$json['address'] = $this->model_account_address->getAddress($address_id);
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function guestautosave(){
		$this->load->language('stepcheckout/checkout');

		$json = array();

		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
		}

		// Validate minimum quantity requirements.
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$json['redirect'] = $this->url->link('checkout/cart');

				break;
			}
		}
		
		if(isset($this->session->data['address_same'])){
			unset($this->session->data['address_same']);
		}
		
		if(!empty($this->request->get['address_same'])){
			$this->session->data['address_same'] = $this->request->get['address_same'];
		}else{
			$this->session->data['address_same'] = '';
		}
		
		if (!$json){
			if(!empty($this->request->get['address_same'])){
				if(isset($this->session->data['payment_address'])){
				  $this->session->data['shipping_address'] =$this->session->data['payment_address'];
				}
			}else{
				if(isset($this->session->data['shipping_address'])){
				 unset($this->session->data['shipping_address']);
				}
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		
	}
	
	public function autosave(){
		$this->load->language('extension/checkout');

		$json = array();

		// Validate if customer is logged in.
		if (!$this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', true);
		}

		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
		}

		// Validate minimum quantity requirements.
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$json['redirect'] = $this->url->link('checkout/cart');

				break;
			}
		}
		
		if(isset($this->session->data['address_same'])){
			unset($this->session->data['address_same']);
		}
		
		if(!empty($this->request->get['address_same'])){
			$this->session->data['address_same'] = $this->request->get['address_same'];
		}else{
			$this->session->data['address_same'] = '';
		}

		if (!$json) {
			if(!empty($this->request->get['payment_address_id'])){
			 $payment_address_id	= $this->request->get['payment_address_id'];
			}else{
			 $payment_address_id	= 0;
			}
			
			if(!empty($this->request->get['shipping_address_id'])){
			 $shipping_address_id	= $this->request->get['shipping_address_id'];
			}else{
			 $shipping_address_id	= 0;
			}
			if ($payment_address_id && $shipping_address_id){
				$this->load->model('account/address');
				if (!$json) {
					$this->load->model('account/address');
					
						
						$this->session->data['payment_address'] = $this->model_account_address->getAddress($payment_address_id);
						$this->session->data['shipping_address'] = $this->model_account_address->getAddress($shipping_address_id);
						
						unset($this->session->data['shipping_method']);
						unset($this->session->data['shipping_methods']);

						unset($this->session->data['payment_method']);
						unset($this->session->data['payment_methods']);
				}
			}
		}
	

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}