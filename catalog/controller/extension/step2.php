<?php
class ControllerExtensionStep2 extends Controller {
	public function index(){
		$this->load->language('extension/checkout');
		
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['stepcheckout_address'] = $this->config->get('module_stepcheckout_address');
		
		$data['text_address']=  (!empty($data['stepcheckout_address'][$this->config->get('config_language_id')]['heading']) ? $data['stepcheckout_address'][$this->config->get('config_language_id')]['heading'] : '');
		
		$data['text_paymentadd']=  (!empty($data['stepcheckout_address'][$this->config->get('config_language_id')]['payment_address']) ? $data['stepcheckout_address'][$this->config->get('config_language_id')]['payment_address'] : '');
		
		$data['text_shippingadd']=  (!empty($data['stepcheckout_address'][$this->config->get('config_language_id')]['shipping_address']) ? $data['stepcheckout_address'][$this->config->get('config_language_id')]['shipping_address'] : '');
		
		$data['text_comments']=  (!empty($data['stepcheckout_address'][$this->config->get('config_language_id')]['commentlabel']) ? $data['stepcheckout_address'][$this->config->get('config_language_id')]['commentlabel'] : '');
		
		$data['text_comment_placeholder']=  (!empty($data['stepcheckout_address'][$this->config->get('config_language_id')]['commentplaceholder']) ? $data['stepcheckout_address'][$this->config->get('config_language_id')]['commentplaceholder'] : '');
		
		$data['stepcheckout_description'] = $this->config->get('module_stepcheckout_description');
		
		$data['button_save_continue']= $data['stepcheckout_description'][$this->config->get('config_language_id')]['continueshipping'];
		
		$data['stepcheckout_account'] = $this->config->get('module_stepcheckout_account');
		
		$data['text_account']=  (!empty($data['stepcheckout_account'][$this->config->get('config_language_id')]['heading']) ? $data['stepcheckout_account'][$this->config->get('config_language_id')]['heading'] : '');
		
		$data['text_login']=  (!empty($data['stepcheckout_account'][$this->config->get('config_language_id')]['login']) ? $data['stepcheckout_account'][$this->config->get('config_language_id')]['login'] : '');
		
		$data['text_register']=  (!empty($data['stepcheckout_account'][$this->config->get('config_language_id')]['register']) ? $data['stepcheckout_account'][$this->config->get('config_language_id')]['register'] : '');
		
		$data['text_guest']=  (!empty($data['stepcheckout_account'][$this->config->get('config_language_id')]['guest']) ? $data['stepcheckout_account'][$this->config->get('config_language_id')]['guest'] : '');
		
		$data['stepcheckout_paymentt'] = $this->config->get('module_stepcheckout_paymentt');
		
		$data['text_payment']=  (!empty($data['stepcheckout_paymentt'][$this->config->get('config_language_id')]['heading']) ? $data['stepcheckout_paymentt'][$this->config->get('config_language_id')]['heading'] : '');
		$data['sessionmissng'] = true;
		
		if (!empty($this->session->data['payment_address']['address_id'])) {
			$data['address_id'] = $this->session->data['payment_address']['address_id'];
		} else {
			$data['address_id'] = $this->customer->getAddressId();
		}
		
		
		if (!$this->cart->hasShipping()) {
			if(!empty($this->session->data['payment_address']['address_id'])){
			 $data['sessionmissng'] = false;
			}
		}else{
			if(!isset($this->session->data['payment_address']['address_id']) && !isset($this->session->data['shipping_address']['address_id'])){
			 $data['sessionmissng'] = false;
			}
		}
		
		
		$this->load->model('account/address');

		$data['payment_address'] = $this->model_account_address->getAddress($data['address_id']);
		if (!empty($this->session->data['shipping_address']['address_id'])) {
			$data['address_id'] = $this->session->data['shipping_address']['address_id'];
		} else {
			$data['address_id'] = $this->customer->getAddressId();
		}
		
		$data['shipping_address'] = $this->model_account_address->getAddress($data['address_id']);
		
		$data['telephone'] = $this->customer->getTelephone();
		
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
		
		$data['cart_status'] = $this->config->get('module_stepcheckout_shoppingcart');
		
		$data['shipping_status']=true;
		if (!$this->cart->hasShipping()) {
			$data['shipping_status']=false;
		}
			
		$data['cart'] = $this->load->controller('extension/cart');
	
		$this->response->setOutput($this->load->view('extension/stepcheckout/step2', $data));
	}
}