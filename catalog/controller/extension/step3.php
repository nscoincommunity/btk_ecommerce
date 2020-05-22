<?php
class ControllerExtensionStep3 extends Controller {
	public function index(){
		$this->load->language('extension/checkout');
		
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['stepcheckout_account'] = $this->config->get('module_stepcheckout_account');
		
		$data['text_account']=  (!empty($data['stepcheckout_account'][$this->config->get('config_language_id')]['heading']) ? $data['stepcheckout_account'][$this->config->get('config_language_id')]['heading'] : '');
		
		$data['text_login']=  (!empty($data['stepcheckout_account'][$this->config->get('config_language_id')]['login']) ? $data['stepcheckout_account'][$this->config->get('config_language_id')]['login'] : '');
		
		$data['text_register']=  (!empty($data['stepcheckout_account'][$this->config->get('config_language_id')]['register']) ? $data['stepcheckout_account'][$this->config->get('config_language_id')]['register'] : '');
		
		$data['text_guest']=  (!empty($data['stepcheckout_account'][$this->config->get('config_language_id')]['guest']) ? $data['stepcheckout_account'][$this->config->get('config_language_id')]['guest'] : '');
		
		$data['stepcheckout_paymentt'] = $this->config->get('module_stepcheckout_paymentt');
		
		$data['text_payment']=  (!empty($data['stepcheckout_paymentt'][$this->config->get('config_language_id')]['heading']) ? $data['stepcheckout_paymentt'][$this->config->get('config_language_id')]['heading'] : '');
		
		$data['text_payment_method']=  (!empty($data['stepcheckout_paymentt'][$this->config->get('config_language_id')]['payment_method']) ? $data['stepcheckout_paymentt'][$this->config->get('config_language_id')]['payment_method'] : '');
		
		$data['stepcheckout_address'] = $this->config->get('module_stepcheckout_address');
		
		$data['text_address']=  (!empty($data['stepcheckout_address'][$this->config->get('config_language_id')]['heading']) ? $data['stepcheckout_address'][$this->config->get('config_language_id')]['heading'] : '');
		
		$data['prestep'] =  $this->customer->islogged();
		
		if (isset($this->session->data['payment_address'])) {
			// Totals
			$totals = array();
			$taxes = $this->cart->getTaxes();
			$total = 0;

			// Because __call can not keep var references so we put them into an array.
			$total_data = array(
				'totals' => &$totals,
				'taxes'  => &$taxes,
				'total'  => &$total
			);
			
			$this->load->model('setting/extension');

			$sort_order = array();

			$results = $this->model_setting_extension->getExtensions('total');

			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get('total_'.$value['code'] . '_sort_order');
			}

			array_multisort($sort_order, SORT_ASC, $results);

			foreach ($results as $result) {
				if ($this->config->get('total_'.$result['code'] . '_status')) {
					$this->load->model('extension/total/' . $result['code']);
					
					// We have to put the totals in an array so that they pass by reference.
					$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
				}
			}

			// Payment Methods
			$method_data = array();

			$this->load->model('setting/extension');

			$results = $this->model_setting_extension->getExtensions('payment');

			$recurring = $this->cart->hasRecurringProducts();

			foreach ($results as $result) {
				if ($this->config->get('payment_'.$result['code'] . '_status')) {
					$this->load->model('extension/payment/' . $result['code']);

					$method = $this->{'model_extension_payment_' . $result['code']}->getMethod($this->session->data['payment_address'], $total);

					if ($method) {
						if ($recurring) {
							if (property_exists($this->{'model_extension_payment_' . $result['code']}, 'recurringPayments') && $this->{'model_extension_payment_' . $result['code']}->recurringPayments()) {
								$method_data[$result['code']] = $method;
							}
						} else {
							$method_data[$result['code']] = $method;
						}
					}
				}
			}

			$sort_order = array();

			foreach ($method_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $method_data);

			$this->session->data['payment_methods'] = $method_data;
		}
		
		$data['allpayment_methods']=array();
		if(isset($this->session->data['payment_methods'])){
			foreach($this->session->data['payment_methods'] as $payment_method){
				$data['allpayment_methods'][]=array(
				 'code'					=> $payment_method['code'],
				 'title'					=> $payment_method['title'],
				 'terms'					=> $payment_method['terms'],
				 'sort_order'					=> $payment_method['sort_order'],
				 'payment_description'  => $this->load->controller('extension/payment/' . $payment_method['code']),
				);
			}
		}
		
	
		if (!empty($this->session->data['payment_methods'])) {
			$data['payment_methods'] = $this->session->data['payment_methods'];
		} else {
			$data['payment_methods'] = array();
		}

		if (isset($this->session->data['payment_method']['code'])) {
			$data['code'] = $this->session->data['payment_method']['code'];
		} else {
			$data['code'] = $this->config->get('module_stepcheckout_payment');
		}
		
		
		$data['text_recurring_item'] = $this->language->get('text_recurring_item');
		$data['text_payment_recurring'] = $this->language->get('text_payment_recurring');

		$data['column_image'] = $this->language->get('column_image');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_total'] = $this->language->get('column_total');
		$this->load->model('tool/image');
		//confirmation Carts
		$order_data = array();

			$totals = array();
			$taxes = $this->cart->getTaxes();
			$total = 0;

			// Because __call can not keep var references so we put them into an array.
			$total_data = array(
				'totals' => &$totals,
				'taxes'  => &$taxes,
				'total'  => &$total
			);

			$this->load->model('setting/extension');

			$sort_order = array();

			$results = $this->model_setting_extension->getExtensions('total');

			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get('total_'.$value['code'] . '_sort_order');
			}

			array_multisort($sort_order, SORT_ASC, $results);

			foreach ($results as $result) {
				if ($this->config->get('total_'.$result['code'] . '_status')) {
					$this->load->model('extension/total/' . $result['code']);

					// We have to put the totals in an array so that they pass by reference.
					$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
				}
			}

			$sort_order = array();

			foreach ($totals as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $totals);

			$order_data['totals'] = $totals;
		
		$data['products'] = array();

			foreach ($this->cart->getProducts() as $product) {
				$option_data = array();

				foreach ($product['option'] as $option) {
					if ($option['type'] != 'file') {
						$value = $option['value'];
					} else {
						$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

						if ($upload_info) {
							$value = $upload_info['name'];
						} else {
							$value = '';
						}
					}

					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
					);
				}

				$recurring = '';

				if ($product['recurring']) {
					$frequencies = array(
						'day'        => $this->language->get('text_day'),
						'week'       => $this->language->get('text_week'),
						'semi_month' => $this->language->get('text_semi_month'),
						'month'      => $this->language->get('text_month'),
						'year'       => $this->language->get('text_year'),
					);

					if ($product['recurring']['trial']) {
						$recurring = sprintf($this->language->get('text_trial_description'), $this->currency->format($this->tax->calculate($product['recurring']['trial_price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['trial_cycle'], $frequencies[$product['recurring']['trial_frequency']], $product['recurring']['trial_duration']) . ' ';
					}

					if ($product['recurring']['duration']) {
						$recurring .= sprintf($this->language->get('text_payment_description'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
					} else {
						$recurring .= sprintf($this->language->get('text_payment_cancel'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
					}
				}
				
				if ($product['image']) {
					$image = $this->model_tool_image->resize($product['image'], 60,60);
				} else {
					$image = '';
				}

				$data['products'][] = array(
					'cart_id'    => $product['cart_id'],
					'thumb'     => $image,
					'product_id' => $product['product_id'],
					'name'       => $product['name'],
					'model'      => $product['model'],
					'option'     => $option_data,
					'recurring'  => $recurring,
					'quantity'   => $product['quantity'],
					'subtract'   => $product['subtract'],
					'price'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
					'total'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'], $this->session->data['currency']),
					'href'       => $this->url->link('product/product', 'product_id=' . $product['product_id'])
				);
			}

			// Gift Voucher
			$data['vouchers'] = array();

			if (!empty($this->session->data['vouchers'])) {
				foreach ($this->session->data['vouchers'] as $voucher) {
					$data['vouchers'][] = array(
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'], $this->session->data['currency'])
					);
				}
			}

			$data['totals'] = array();

			foreach ($order_data['totals'] as $total) {
				$data['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'], $this->session->data['currency'])
				);
			}
		
		
		
		$data['scripts'] = $this->document->getScripts();
		
		$data['cart'] = $this->load->controller('extension/cart');
	
		$this->response->setOutput($this->load->view('extension/stepcheckout/step3', $data));
	}
	
	public function loadpayment(){
	$json=array();	
	$json['payment'] = $this->load->controller('extension/payment/' . $this->request->get['code']);	
	$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->get['code']];
	print_r(json_encode($json));
	}
	
	public function save() {
		$this->load->language('extension/checkout');
		$this->load->model('extension/checkout');

		$json = array();

		// Validate if payment address has been set.
		if (!isset($this->session->data['payment_address'])) {
			$json['step2'] = true;
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

		if (!isset($this->request->get['code'])) {
			$json['step3'] = true;
		} elseif (!isset($this->session->data['payment_methods'][$this->request->get['code']])) {
			$json['step3'] = true;
		}

		if (!$json) {
			
			$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->get['code']];
			$this->model_extension_checkout->addOrder();
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}