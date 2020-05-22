<?php
class ControllerExtensionStep1 extends Controller {
	public function index(){
		
		$this->load->language('extension/checkout');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');
		
		$data['stepcheckout_account'] = $this->config->get('module_stepcheckout_account');
		
		$data['text_account']=  (!empty($data['stepcheckout_account'][$this->config->get('config_language_id')]['heading']) ? $data['stepcheckout_account'][$this->config->get('config_language_id')]['heading'] : '');
		
		$data['text_login']=  (!empty($data['stepcheckout_account'][$this->config->get('config_language_id')]['login']) ? $data['stepcheckout_account'][$this->config->get('config_language_id')]['login'] : '');
		
		$data['text_register']=  (!empty($data['stepcheckout_account'][$this->config->get('config_language_id')]['register']) ? $data['stepcheckout_account'][$this->config->get('config_language_id')]['register'] : '');
		
		$data['text_guest']=  (!empty($data['stepcheckout_account'][$this->config->get('config_language_id')]['guest']) ? $data['stepcheckout_account'][$this->config->get('config_language_id')]['guest'] : '');
		
		$data['stepcheckout_address'] = $this->config->get('module_stepcheckout_address');
		
		$data['text_address']=  (!empty($data['stepcheckout_address'][$this->config->get('config_language_id')]['heading']) ? $data['stepcheckout_address'][$this->config->get('config_language_id')]['heading'] : '');
		
		$data['stepcheckout_paymentt'] = $this->config->get('module_stepcheckout_paymentt');
		
		$data['text_payment']=  (!empty($data['stepcheckout_paymentt'][$this->config->get('config_language_id')]['heading']) ? $data['stepcheckout_paymentt'][$this->config->get('config_language_id')]['heading'] : '');

		
		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} else {
			$data['error_warning'] = '';
		}

		$data['logged'] = $this->customer->isLogged();

		if (isset($this->session->data['account'])) {
			$data['account'] = $this->session->data['account'];
		} else {
			$data['account'] = '';
		}
		
		$data['cart_status'] = $this->config->get('module_stepcheckout_shoppingcart');
		
		$data['stepcheckout_displaypanel'] = $this->config->get('module_stepcheckout_displaypanel');
		$data['stepcheckout_register'] = $this->config->get('module_stepcheckout_register');
		$data['stepcheckout_guest'] = $this->config->get('module_stepcheckout_guest');
		$data['stepcheckout_login'] = $this->config->get('module_stepcheckout_login');
		
		
		if(isset($this->session->data['guest'])){
		 $data['stepcheckout_displaypanel'] = 1;	
		}
		
		$data['shipping_required'] = $this->cart->hasShipping();
		$data['login'] = $this->load->controller('extension/login');
		$data['register'] = $this->load->controller('extension/register');
		$data['guest'] = $this->load->controller('extension/guest');
		$data['wlogin'] = $this->load->controller('extension/wlogin');
		$data['cart'] = $this->load->controller('extension/cart');
		//$data['address'] = $this->load->controller('stepcheckout/address');
	
		$this->response->setOutput($this->load->view('extension/stepcheckout/step1', $data));
	}
}