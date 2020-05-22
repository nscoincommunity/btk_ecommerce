<?php
class ControllerExtensionGuestStep2 extends Controller {
	public function index(){
		$this->load->language('extension/checkout');
		
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_login'] = $this->language->get('text_login');
		$data['text_register'] = $this->language->get('text_register');
		$data['text_guest'] = $this->language->get('text_guest');
		$data['text_account'] = $this->language->get('text_account');
		$data['text_payment'] = $this->language->get('text_payment');
		$data['stepcheckout_address'] = $this->config->get('module_stepcheckout_address');

		

		$data['text_address']=  (!empty($data['stepcheckout_address'][$this->config->get('config_language_id')]['heading']) ? $data['stepcheckout_address'][$this->config->get('config_language_id')]['heading'] : '');

		

		$data['text_paymentadd']=  (!empty($data['stepcheckout_address'][$this->config->get('config_language_id')]['payment_address']) ? $data['stepcheckout_address'][$this->config->get('config_language_id')]['payment_address'] : '');

		

		$data['text_shippingadd']=  (!empty($data['stepcheckout_address'][$this->config->get('config_language_id')]['shipping_address']) ? $data['stepcheckout_address'][$this->config->get('config_language_id')]['shipping_address'] : '');

		

		$data['text_comments']=  (!empty($data['stepcheckout_address'][$this->config->get('config_language_id')]['commentlabel']) ? $data['stepcheckout_address'][$this->config->get('config_language_id')]['commentlabel'] : '');
		
		$data['stepcheckout_description'] = $this->config->get('module_stepcheckout_description');

		$data['button_save_continue']= $data['stepcheckout_description'][$this->config->get('config_language_id')]['continueshipping'];
		
		$data['sessionmissng'] = false;
		

		$data['payment_session_status'] = false;
		if(isset($this->session->data['payment_address'])){
		$data['payment_session_status'] = true;	
		}
		
		if (isset($this->session->data['guest']['telephone'])) {
			$data['telephone'] = $this->session->data['guest']['telephone'];
		} else {
			$data['telephone'] = '';
		}
		
		if(!empty($this->session->data['address_same'])){
			$data['sameaddress'] = $this->session->data['address_same'];
		}else{
			$data['sameaddress'] = '';
		}
		
		if (isset($this->session->data['comment'])) {
			$data['comment'] = $this->session->data['comment'];
		} else {
			$data['comment'] = '';
		}
		
		if (isset($this->session->data['payment_address']['firstname'])) {
			$payment_address['firstname'] = $this->session->data['payment_address']['firstname'];
		} else {
			$payment_address['firstname'] = '';
		}
		
		if (isset($this->session->data['payment_address']['lastname'])) {
			$payment_address['lastname'] = $this->session->data['payment_address']['lastname'];
		} else {
			$payment_address['lastname'] = '';
		}
		
		if (isset($this->session->data['payment_address']['company'])) {
			$payment_address['company'] = $this->session->data['payment_address']['company'];
		} else {
			$payment_address['company'] = '';
		}

		if (isset($this->session->data['payment_address']['address_1'])) {
			$payment_address['address_1'] = $this->session->data['payment_address']['address_1'];
		} else {
			$payment_address['address_1'] = '';
		}

		if (isset($this->session->data['payment_address']['address_2'])) {
			$payment_address['address_2'] = $this->session->data['payment_address']['address_2'];
		} else {
			$payment_address['address_2'] = '';
		}

		if (isset($this->session->data['payment_address']['postcode'])) {
			$payment_address['postcode'] = $this->session->data['payment_address']['postcode'];
		} elseif (isset($this->session->data['shipping_address']['postcode'])) {
			$payment_address['postcode'] = $this->session->data['shipping_address']['postcode'];
		} else {
			$payment_address['postcode'] = '';
		}

		if (isset($this->session->data['payment_address']['city'])) {
			$payment_address['city'] = $this->session->data['payment_address']['city'];
		} else {
			$payment_address['city'] = '';
		}

		if (isset($this->session->data['payment_address']['country_id'])) {
			$data['country_id'] = $this->session->data['payment_address']['country_id'];
		} elseif (isset($this->session->data['shipping_address']['country_id'])) {
			$data['country_id'] = $this->session->data['shipping_address']['country_id'];
		} else {
			$data['country_id'] = $this->config->get('config_country_id');
		}
		
		$this->load->model('localisation/country');
		
		$countries = $this->model_localisation_country->getCountry($data['country_id']);
		if(!empty($countries['name'])){
			$payment_address['country']= $countries['name'];
		}else{
			$payment_address['country'] = '';
		}
		
		if (isset($this->session->data['payment_address']['zone_id'])) {
			$data['zone_id'] = $this->session->data['payment_address']['zone_id'];
		} elseif (isset($this->session->data['shipping_address']['zone_id'])) {
			$data['zone_id'] = $this->session->data['shipping_address']['zone_id'];
		} else {
			$data['zone_id'] = '';
		}
		
		$this->load->model('localisation/zone');
		
		$zone = $this->model_localisation_zone->getZone($data['zone_id']);
		if(!empty($zone['name'])){
			$payment_address['zone']= $zone['name'];
		}else{
			$payment_address['zone'] = '';
		}
		
		$data['payment_address'] = $payment_address;
		
		
		/*shipping address*/
		$data['shpping_session_status'] = false;
		if(isset($this->session->data['shipping_address'])){
		$data['shpping_session_status'] = true;	
		}
		if (isset($this->session->data['shipping_address']['firstname'])) {
			$shipping_address['firstname'] = $this->session->data['shipping_address']['firstname'];
		} else {
			$shipping_address['firstname'] = '';
		}
		
		if (isset($this->session->data['shipping_address']['lastname'])) {
			$shipping_address['lastname'] = $this->session->data['shipping_address']['lastname'];
		} else {
			$shipping_address['lastname'] = '';
		}
		
		if (isset($this->session->data['shipping_address']['company'])) {
			$shipping_address['company'] = $this->session->data['shipping_address']['company'];
		} else {
			$shipping_address['company'] = '';
		}

		if (isset($this->session->data['shipping_address']['address_1'])) {
			$shipping_address['address_1'] = $this->session->data['shipping_address']['address_1'];
		} else {
			$shipping_address['address_1'] = '';
		}

		if (isset($this->session->data['shipping_address']['address_2'])) {
			$shipping_address['address_2'] = $this->session->data['shipping_address']['address_2'];
		} else {
			$shipping_address['address_2'] = '';
		}

		if (isset($this->session->data['shipping_address']['postcode'])) {
			$shipping_address['postcode'] = $this->session->data['shipping_address']['postcode'];
		} elseif (isset($this->session->data['shipping_address']['postcode'])) {
			$shipping_address['postcode'] = $this->session->data['shipping_address']['postcode'];
		} else {
			$shipping_address['postcode'] = '';
		}

		if (isset($this->session->data['shipping_address']['city'])) {
			$shipping_address['city'] = $this->session->data['shipping_address']['city'];
		} else {
			$shipping_address['city'] = '';
		}

		if (isset($this->session->data['shipping_address']['country_id'])) {
			$data['country_id'] = $this->session->data['shipping_address']['country_id'];
		} elseif (isset($this->session->data['shipping_address']['country_id'])) {
			$data['country_id'] = $this->session->data['shipping_address']['country_id'];
		} else {
			$data['country_id'] = '';
		}
		
		$this->load->model('localisation/country');
		
		$countries = $this->model_localisation_country->getCountry($data['country_id']);
		if(!empty($countries['name'])){
			$shipping_address['country']= $countries['name'];
		}else{
			$shipping_address['country'] = '';
		}
		

		if (isset($this->session->data['shipping_address']['zone_id'])) {
			$data['zone_id'] = $this->session->data['shipping_address']['zone_id'];
		} elseif (isset($this->session->data['shipping_address']['zone_id'])) {
			$data['zone_id'] = $this->session->data['shipping_address']['zone_id'];
		} else {
			$data['zone_id'] = '';
		}
		
		$this->load->model('localisation/zone');
		
		$zone = $this->model_localisation_zone->getZone($data['zone_id']);
		if(!empty($zone['name'])){
			$shipping_address['zone']= $zone['name'];
		}else{
			$shipping_address['zone'] = '';
		}
		
		$data['shipping_address'] = $shipping_address;
		$data['term_status'] = $this->config->get('module_stepcheckout_termcondition');
		if ($this->config->get('module_stepcheckout_termcondition')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('module_stepcheckout_termcondition'));

			if ($information_info) {
				$data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('module_stepcheckout_termcondition'), true), $information_info['title'], $information_info['title']);
			} else {
				$data['text_agree'] = '';
			}
		} else {
			$data['text_agree'] = '';
		}

		if (isset($this->session->data['agree'])) {
			$data['agree'] = $this->session->data['agree'];
		} else {
			$data['agree'] = $this->config->get('module_stepcheckout_autotermcondition');
		}
		
		$data['stepcheckout_comment'] = $this->config->get('module_stepcheckout_comment');
		
		$data['shipping_status']=true;
		if (!$this->cart->hasShipping()) {
			$data['shipping_status']=false;
		}
		
		$data['cart_status'] = $this->config->get('module_stepcheckout_shoppingcart');
			
		$data['cart'] = $this->load->controller('extension/cart');
	
		$this->response->setOutput($this->load->view('extension/stepcheckout/gueststep2', $data));
	}
}