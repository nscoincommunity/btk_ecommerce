<?php
class ControllerExtensionShippingMethod extends Controller {
	public function index() {
		$this->load->language('extension/checkout');
		if(isset($this->session->data['shipping_methods'])){
			unset($this->session->data['shipping_methods']);
		}
		if (isset($this->session->data['shipping_address'])) {
			// Shipping Methods
			$method_data = array();

			$this->load->model('setting/extension');

			$results = $this->model_setting_extension->getExtensions('shipping');

			foreach ($results as $result) {
				if ($this->config->get('shipping_' .$result['code'] . '_status')) {
					$this->load->model('extension/shipping/' . $result['code']);

					$quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);

					if ($quote) {
						$method_data[$result['code']] = array(
							'title'      => $quote['title'],
							'quote'      => $quote['quote'],
							'sort_order' => $quote['sort_order'],
							'error'      => $quote['error']
						);
					}
				}
			}
			
			$sort_order = array();

			foreach ($method_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $method_data);

			$this->session->data['shipping_methods'] = $method_data;
		}
		
		$data['stepcheckout_paymentt'] = $this->config->get('module_stepcheckout_paymentt');
		
		$data['text_payment']=  (!empty($data['stepcheckout_paymentt'][$this->config->get('config_language_id')]['heading']) ? $data['stepcheckout_paymentt'][$this->config->get('config_language_id')]['heading'] : '');
		
		$data['text_payment_method']=  (!empty($data['stepcheckout_paymentt'][$this->config->get('config_language_id')]['payment_method']) ? $data['stepcheckout_paymentt'][$this->config->get('config_language_id')]['payment_method'] : '');
		
		$data['text_shipping_method']=  (!empty($data['stepcheckout_paymentt'][$this->config->get('config_language_id')]['shipping_method']) ? $data['stepcheckout_paymentt'][$this->config->get('config_language_id')]['shipping_method'] : '');

		$data['text_comments'] = $this->language->get('text_comments');
		$data['text_loading'] = $this->language->get('text_loading');

		$data['button_continue'] = $this->language->get('button_continue');

		if (empty($this->session->data['shipping_methods'])) {
			$data['error_warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['shipping_methods'])) {
			$data['shipping_methods'] = $this->session->data['shipping_methods'];
		} else {
			$data['shipping_methods'] = array();
		}
		

		if (isset($this->session->data['shipping_method']['code'])) {
			$data['code'] = $this->session->data['shipping_method']['code'];
		} else {
			$data['code'] = $this->config->get('module_stepcheckout_shipping');
		}

		if (isset($this->session->data['comment'])) {
			$data['comment'] = $this->session->data['comment'];
		} else {
			$data['comment'] = '';
		}

		$this->response->setOutput($this->load->view('extension/stepcheckout/shipping_method', $data));
	}
	
	public function insteadsave(){
		$this->load->language('extension/checkout');

		$json = array();

		// Validate if shipping is required. If not the customer should not have reached this page.
		if (!$this->cart->hasShipping()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', true);
		}

		// Validate if shipping address has been set.
		if (!isset($this->session->data['shipping_address'])) {
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

		if (!isset($this->request->post['shipping_method'])) {
			$json['error']['warning'] = $this->language->get('error_shipping');
		} else {
			$shipping = explode('.', $this->request->post['shipping_method']);

			if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
				$json['error']['warning'] = $this->language->get('error_shipping');
			}
		}

		if (!$json) {
			$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];

			if($this->config->get('module_stepcheckout_comment')){
				$this->session->data['comment'] = strip_tags($this->request->post['comment']);
			}else{
				$this->session->data['comment'] = '';	
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function save(){
		$this->load->language('extension/checkout');
		$this->load->model('extension/checkout');

		$json = array();

		// Validate if shipping is required. If not the customer should not have reached this page.
		

		// Validate if shipping address has been set.
		if ($this->cart->hasShipping()){
			if (!isset($this->session->data['shipping_address'])) {
				$json['error']['warning'] = $this->language->get('error_address');
			}
		}
		
		if (!isset($this->session->data['payment_address'])) {
			$json['error']['warning'] = $this->language->get('error_payment_address');
		}

		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
		}

		// Validate minimum quantity requirements.
		$products = $this->cart->getProducts();

		foreach ($products as $product){
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

		if ($this->cart->hasShipping()) {
			if (!isset($this->request->post['shipping_method'])) {
				$json['error']['warning'] = $this->language->get('error_shipping');
			} else {
				$shipping = explode('.', $this->request->post['shipping_method']);

				if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
					$json['error']['warning'] = $this->language->get('error_shipping');
				}
			}
		}
		
		if ($this->config->get('module_stepcheckout_termcondition')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('module_stepcheckout_termcondition'));

			if ($information_info && !isset($this->request->post['agree'])) {
				$json['error']['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
			}
		}

		if (!$json) {
			if ($this->cart->hasShipping()) {
			  $this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
			}
			if($this->config->get('stepcheckout_comment')){
				$this->session->data['comment'] = strip_tags($this->request->post['comment']);
			}else{
				$this->session->data['comment'] = '';	
			}
			
			$this->model_extension_checkout->addOrder();
			
			$this->session->data['checkoutstep'] = 3;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}