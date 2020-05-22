<?php
class ControllerExtensionModuleStepcheckout extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/stepcheckout');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_stepcheckout', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_register'] = $this->language->get('text_register');
		$data['text_guest'] = $this->language->get('text_guest');
		$data['text_login'] = $this->language->get('text_login');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_country'] = $this->language->get('entry_country');
		$data['entry_zone'] = $this->language->get('entry_zone');
		$data['entry_cartpage'] = $this->language->get('entry_cartpage');
		$data['entry_panel'] = $this->language->get('entry_panel');
		$data['entry_defaultpanel'] = $this->language->get('entry_defaultpanel');
		$data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$data['entry_information'] = $this->language->get('entry_information');
		$data['entry_newsletter'] = $this->language->get('entry_newsletter');
		$data['entry_payment'] = $this->language->get('entry_payment');
		$data['entry_shipping'] = $this->language->get('entry_shipping');
		$data['entry_shoppingcart'] = $this->language->get('entry_shoppingcart');
		$data['entry_quantity'] = $this->language->get('entry_quantity');
		$data['entry_showreward'] = $this->language->get('entry_showreward');
		$data['entry_showimage'] = $this->language->get('entry_showimage');
		$data['entry_imagewidthheight'] = $this->language->get('entry_imagewidthheight');
		$data['entry_termcondition'] = $this->language->get('entry_termcondition');
		$data['entry_autochecked'] = $this->language->get('entry_autochecked');
		$data['entry_comment'] = $this->language->get('entry_comment');
		$data['entry_shippingbutton'] = $this->language->get('entry_shippingbutton');
		$data['entry_confirmbuttontrigger'] = $this->language->get('entry_confirmbuttontrigger');
		$data['entry_heading'] = $this->language->get('entry_heading');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_register'] = $this->language->get('entry_register');
		$data['entry_guest'] = $this->language->get('entry_guest');
		$data['entry_login'] = $this->language->get('entry_login');
		$data['entry_panelheading'] = $this->language->get('entry_panelheading');
		$data['entry_message'] = $this->language->get('entry_message');
		$data['entry_commentlabel'] = $this->language->get('entry_commentlabel');
		$data['entry_commentplaceholder'] = $this->language->get('entry_commentplaceholder');
		$data['entry_continueshipping'] = $this->language->get('entry_continueshipping');
		$data['entry_checkoutbutton'] = $this->language->get('entry_checkoutbutton');
		$data['entry_successpage'] = $this->language->get('entry_successpage');
		$data['entry_successheading'] = $this->language->get('entry_successheading');
		$data['entry_customcss'] = $this->language->get('entry_customcss');
		$data['entry_continue'] = $this->language->get('entry_continue');
		$data['entry_promoteproducts'] = $this->language->get('entry_promoteproducts');
		$data['entry_image_height'] = $this->language->get('entry_image_height');
		$data['entry_products'] = $this->language->get('entry_products');
		$data['entry_showcoupon'] = $this->language->get('entry_showcoupon');
		$data['entry_showvoucher'] = $this->language->get('entry_showvoucher');		
		$data['entry_paymentadd'] = $this->language->get('entry_paymentadd');		
		$data['entry_shippingadd'] = $this->language->get('entry_shippingadd');		
		$data['entry_hideregister'] = $this->language->get('entry_hideregister');		
		$data['entry_hideguest'] = $this->language->get('entry_hideguest');		
		$data['entry_hideguest'] = $this->language->get('entry_hideguest');		
		$data['entry_hidelogin'] = $this->language->get('entry_hidelogin');		
		$data['entry_fappid'] = $this->language->get('entry_fappid');		
		$data['entry_fappappsecret'] = $this->language->get('entry_fappappsecret');		
		$data['entry_fcallbackurl'] = $this->language->get('entry_fcallbackurl');		
		$data['entry_gappid'] = $this->language->get('entry_gappid');		
		$data['entry_gappappsecret'] = $this->language->get('entry_gappappsecret');		
		$data['entry_gcallbackurl'] = $this->language->get('entry_gcallbackurl');		
		$data['entry_lappid'] = $this->language->get('entry_lappid');		
		$data['entry_lappappsecret'] = $this->language->get('entry_lappappsecret');		
		$data['entry_lcallbackurl'] = $this->language->get('entry_lcallbackurl');		
		$data['entry_ftext'] = $this->language->get('entry_ftext');		
		$data['entry_gtext'] = $this->language->get('entry_gtext');		
		$data['entry_ltext'] = $this->language->get('entry_ltext');		
		$data['entry_shippingmethod'] = $this->language->get('entry_shippingmethod');		
		$data['entry_paymentmethod'] = $this->language->get('entry_paymentmethod');		
		$data['entry_confirmorder'] = $this->language->get('entry_confirmorder');		
		
		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_setting'] = $this->language->get('tab_setting');
		$data['tab_language'] = $this->language->get('tab_language');
		$data['tab_success'] = $this->language->get('tab_success');
		$data['tab_custom'] = $this->language->get('tab_custom');
		$data['tab_accbilling'] = $this->language->get('tab_accbilling');
		$data['tab_paymentshipping'] = $this->language->get('tab_paymentshipping');
		$data['tab_shoppingcart'] = $this->language->get('tab_shoppingcart');
		$data['tab_confirmorder'] = $this->language->get('tab_confirmorder');
		$data['tab_sociallogin'] = $this->language->get('tab_sociallogin');
		
		$data['help_defaultpanel'] = $this->language->get('help_defaultpanel');
		$data['help_products'] = $this->language->get('help_products');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		
		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['address'])) {
			$data['error_address'] = $this->error['address'];
		} else {
			$data['error_address'] = array();
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/stepcheckout', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/stepcheckout', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_stepcheckout_status'])) {
			$data['module_stepcheckout_status'] = $this->request->post['module_stepcheckout_status'];
		} else {
			$data['module_stepcheckout_status'] = $this->config->get('module_stepcheckout_status');
		}
		
		if ($this->request->server['HTTPS']) {
			$server = HTTPS_CATALOG;
		} else {
			$server = HTTP_CATALOG;
		}
		
		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		if (isset($this->request->post['module_stepcheckout_country'])) {
			$data['module_stepcheckout_country'] = $this->request->post['module_stepcheckout_country'];
		} else {
			$data['module_stepcheckout_country'] = $this->config->get('module_stepcheckout_country');
		}
		
		if (isset($this->request->post['module_stepcheckout_zone'])) {
			$data['module_stepcheckout_zone'] = $this->request->post['module_stepcheckout_country'];
		} else {
			$data['module_stepcheckout_zone'] = $this->config->get('module_stepcheckout_zone');
		}
		
		if (isset($this->request->post['module_stepcheckout_fstatus'])) {
			$data['module_stepcheckout_fstatus'] = $this->request->post['module_stepcheckout_fstatus'];
		} else {
			$data['module_stepcheckout_fstatus'] = $this->config->get('module_stepcheckout_fstatus');
		}
		
		if (isset($this->request->post['module_stepcheckout_gstatus'])) {
			$data['module_stepcheckout_gstatus'] = $this->request->post['module_stepcheckout_gstatus'];
		} else {
			$data['module_stepcheckout_gstatus'] = $this->config->get('module_stepcheckout_gstatus');
		}
		
		if (isset($this->request->post['module_stepcheckout_gappid'])) {
			$data['module_stepcheckout_gappid'] = $this->request->post['module_stepcheckout_gappid'];
		} else {
			$data['module_stepcheckout_gappid'] = $this->config->get('module_stepcheckout_gappid');
		}
		
		if (isset($this->request->post['module_stepcheckout_gsecretkey'])) {
			$data['module_stepcheckout_gsecretkey'] = $this->request->post['module_stepcheckout_gsecretkey'];
		} else {
			$data['module_stepcheckout_gsecretkey'] = $this->config->get('module_stepcheckout_gsecretkey');
		}
		
		if (isset($this->request->post['module_stepcheckout_fappid'])) {
			$data['module_stepcheckout_fappid'] = $this->request->post['module_stepcheckout_fappid'];
		} else {
			$data['module_stepcheckout_fappid'] = $this->config->get('module_stepcheckout_fappid');
		}
		
		$this->load->model('catalog/information');
		
		$data['informations'] = $this->model_catalog_information->getInformations(array());
		
		if (isset($this->request->post['module_stepcheckout_information'])) {
			$data['module_stepcheckout_information'] = $this->request->post['module_stepcheckout_information'];
		} else {
			$data['module_stepcheckout_information'] = $this->config->get('module_stepcheckout_information');
		}
		
		if (isset($this->request->post['module_stepcheckout_cartpage'])) {
			$data['module_stepcheckout_cartpage'] = $this->request->post['module_stepcheckout_cartpage'];
		} elseif ($this->config->get('module_stepcheckout_cartpage')) {
			$data['module_stepcheckout_cartpage'] = $this->config->get('module_stepcheckout_cartpage');
		} else {
			$data['module_stepcheckout_cartpage'] = '';
		}
		
		if (isset($this->request->post['module_stepcheckout_newsletter'])) {
			$data['module_stepcheckout_newsletter'] = $this->request->post['module_stepcheckout_newsletter'];
		} elseif($this->config->get('module_stepcheckout_newsletter')) {
			$data['module_stepcheckout_newsletter'] = $this->config->get('module_stepcheckout_newsletter');
		} else{
			$data['module_stepcheckout_newsletter'] = '';
		}
		
		if (isset($this->request->post['module_stepcheckout_register'])) {
			$data['module_stepcheckout_register'] = $this->request->post['module_stepcheckout_register'];
		} elseif($this->config->get('module_stepcheckout_register')) {
			$data['module_stepcheckout_register'] = $this->config->get('module_stepcheckout_register');
		} else{
			$data['module_stepcheckout_register'] = '';
		}
		
		if (isset($this->request->post['module_stepcheckout_guest'])) {
			$data['module_stepcheckout_guest'] = $this->request->post['module_stepcheckout_guest'];
		} elseif($this->config->get('module_stepcheckout_guest')) {
			$data['module_stepcheckout_guest'] = $this->config->get('module_stepcheckout_guest');
		} else{
			$data['module_stepcheckout_guest'] = '';
		}
		
		if (isset($this->request->post['module_stepcheckout_login'])) {
			$data['module_stepcheckout_login'] = $this->request->post['module_stepcheckout_login'];
		} elseif($this->config->get('module_stepcheckout_login')) {
			$data['module_stepcheckout_login'] = $this->config->get('module_stepcheckout_login');
		} else{
			$data['module_stepcheckout_login'] = '';
		}
		
		if (isset($this->request->post['module_stepcheckout_fsecretkey'])) {
			$data['module_stepcheckout_fsecretkey'] = $this->request->post['module_stepcheckout_fsecretkey'];
		} else{
			$data['module_stepcheckout_fsecretkey'] = $this->config->get('module_stepcheckout_fsecretkey');
		}
		
		if (isset($this->request->post['module_stepcheckout_lstatus'])) {
			$data['module_stepcheckout_lstatus'] = $this->request->post['module_stepcheckout_lstatus'];
		} else{
			$data['module_stepcheckout_lstatus'] = $this->config->get('module_stepcheckout_lstatus');
		}
		
		if (isset($this->request->post['module_stepcheckout_lappid'])) {
			$data['module_stepcheckout_lappid'] = $this->request->post['module_stepcheckout_lappid'];
		} else{
			$data['module_stepcheckout_lappid'] = $this->config->get('module_stepcheckout_lappid');
		}
		
		if (isset($this->request->post['module_stepcheckout_lsecretkey'])) {
			$data['module_stepcheckout_lsecretkey'] = $this->request->post['module_stepcheckout_lsecretkey'];
		} else{
			$data['module_stepcheckout_lsecretkey'] = $this->config->get('module_stepcheckout_lsecretkey');
		}
		
		$data['module_stepcheckout_fcallback'] = $server.'index.php?route=extension/module/stepcheckout/fcallback';
		
		$data['module_stepcheckout_gcallback'] = $server.'index.php?route=extension/module/stepcheckout/gcallback';
		
		$data['module_stepcheckout_lcallback'] = $server.'index.php?route=extension/module/stepcheckout/lcallback';

		if (isset($this->request->post['module_stepcheckout_shoppingcart'])) {
			$data['module_stepcheckout_shoppingcart'] = $this->request->post['module_stepcheckout_shoppingcart'];
		} else{
			$data['module_stepcheckout_shoppingcart'] = $this->config->get('module_stepcheckout_shoppingcart');
		} 
		
		if (isset($this->request->post['module_stepcheckout_reward'])) {
			$data['module_stepcheckout_reward'] = $this->request->post['module_stepcheckout_reward'];
		} else{
			$data['module_stepcheckout_reward'] = $this->config->get('module_stepcheckout_reward');
		} 
		
		if (isset($this->request->post['module_stepcheckout_quantity'])) {
			$data['module_stepcheckout_quantity'] = $this->request->post['module_stepcheckout_quantity'];
		} else{
			$data['module_stepcheckout_quantity'] = $this->config->get('module_stepcheckout_quantity');
		} 
		
		if (isset($this->request->post['module_stepcheckout_coupon'])) {
			$data['module_stepcheckout_coupon'] = $this->request->post['module_stepcheckout_coupon'];
		} else{
			$data['module_stepcheckout_coupon'] = $this->config->get('module_stepcheckout_coupon');
		}
		
		if (isset($this->request->post['module_stepcheckout_voucher'])) {
			$data['module_stepcheckout_voucher'] = $this->request->post['module_stepcheckout_voucher'];
		} else{
			$data['module_stepcheckout_voucher'] = $this->config->get('module_stepcheckout_voucher');
		} 
		
		if (isset($this->request->post['module_stepcheckout_productimage'])) {
			$data['module_stepcheckout_productimage'] = $this->request->post['module_stepcheckout_productimage'];
		} else{
			$data['module_stepcheckout_productimage'] = $this->config->get('module_stepcheckout_productimage');
		} 
		
		if (isset($this->request->post['module_stepcheckout_promoteproducts'])) {
			$data['module_stepcheckout_promoteproducts'] = $this->request->post['module_stepcheckout_promoteproducts'];
		} else{
			$data['module_stepcheckout_promoteproducts'] = $this->config->get('module_stepcheckout_promoteproducts');
		} 
		
		if (isset($this->request->post['module_stepcheckout_displaypanel'])) {
			$data['module_stepcheckout_displaypanel'] = $this->request->post['module_stepcheckout_displaypanel'];
		} elseif ($this->config->get('module_stepcheckout_displaypanel')) {
			$data['module_stepcheckout_displaypanel'] = $this->config->get('module_stepcheckout_displaypanel');
		} else {
			$data['module_stepcheckout_displaypanel'] = '1';
		}
		
		if (isset($this->request->post['module_stepcheckout_productimageheight'])) {
			$data['module_stepcheckout_productimageheight'] = $this->request->post['module_stepcheckout_productimageheight'];
		} elseif ($this->config->get('module_stepcheckout_productimageheight')) {
			$data['module_stepcheckout_productimageheight'] = $this->config->get('module_stepcheckout_productimageheight');
		} else {
			$data['module_stepcheckout_productimageheight'] = '';
		}
		
		if (isset($this->request->post['module_stepcheckout_productimagewidth'])) {
			$data['module_stepcheckout_productimagewidth'] = $this->request->post['module_stepcheckout_productimagewidth'];
		} elseif ($this->config->get('module_stepcheckout_productimagewidth')) {
			$data['module_stepcheckout_productimagewidth'] = $this->config->get('module_stepcheckout_productimagewidth');
		} else {
			$data['module_stepcheckout_productimagewidth'] = '';
		}
		
		if (isset($this->request->post['module_stepcheckout_promote_product_height'])) {
			$data['module_stepcheckout_promote_product_height'] = $this->request->post['module_stepcheckout_promote_product_height'];
		} elseif ($this->config->get('module_stepcheckout_promote_product_height')) {
			$data['module_stepcheckout_promote_product_height'] = $this->config->get('module_stepcheckout_promote_product_height');
		} else {
			$data['module_stepcheckout_promote_product_height'] = '';
		}
		
		if (isset($this->request->post['module_stepcheckout_promote_product_width'])) {
			$data['module_stepcheckout_promote_product_width'] = $this->request->post['module_stepcheckout_promote_product_width'];
		} elseif ($this->config->get('module_stepcheckout_promote_product_width')) {
			$data['module_stepcheckout_promote_product_width'] = $this->config->get('module_stepcheckout_promote_product_width');
		} else {
			$data['module_stepcheckout_promote_product_width'] = '';
		}
		
		if (isset($this->request->post['module_stepcheckout_termcondition'])) {
			$data['module_stepcheckout_termcondition'] = $this->request->post['module_stepcheckout_termcondition'];
		} elseif ($this->config->get('module_stepcheckout_termcondition')) {
			$data['module_stepcheckout_termcondition'] = $this->config->get('module_stepcheckout_termcondition');
		} else {
			$data['module_stepcheckout_termcondition'] = '';
		}
		
		if (isset($this->request->post['module_stepcheckout_autotermcondition'])) {
			$data['module_stepcheckout_autotermcondition'] = $this->request->post['module_stepcheckout_autotermcondition'];
		} else {
			$data['module_stepcheckout_autotermcondition'] = $this->config->get('module_stepcheckout_autotermcondition');
		}
		
		if (isset($this->request->post['module_stepcheckout_comment'])) {
			$data['module_stepcheckout_comment'] = $this->request->post['module_stepcheckout_comment'];
		} else {
			$data['module_stepcheckout_comment'] = $this->config->get('module_stepcheckout_comment');
		}
		
		if (isset($this->request->post['module_module_stepcheckout_shippingbutton'])) {
			$data['module_module_stepcheckout_shippingbutton'] = $this->request->post['module_module_stepcheckout_shippingbutton'];
		} else {
			$data['module_module_stepcheckout_shippingbutton'] = $this->config->get('module_module_stepcheckout_shippingbutton');
		}
		
		if (isset($this->request->post['module_stepcheckout_confirmbuttontrigger'])) {
			$data['module_stepcheckout_confirmbuttontrigger'] = $this->request->post['module_stepcheckout_confirmbuttontrigger'];
		} else {
			$data['module_stepcheckout_confirmbuttontrigger'] = $this->config->get('module_stepcheckout_confirmbuttontrigger');
		}
		
		if (isset($this->request->post['module_stepcheckout_successpage'])) {
			$data['module_stepcheckout_successpage'] = $this->request->post['module_stepcheckout_successpage'];
		} else {
			$data['module_stepcheckout_successpage'] = $this->config->get('module_stepcheckout_successpage');
		}
		
		if (isset($this->request->post['module_stepcheckout_customcss'])) {
			$data['module_stepcheckout_customcss'] = $this->request->post['module_stepcheckout_customcss'];
		} else {
			$data['module_stepcheckout_customcss'] = $this->config->get('module_stepcheckout_customcss');
		}
		
		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		if (isset($this->request->post['module_stepcheckout_customergroup'])) {
			$data['module_stepcheckout_customergroup'] = $this->request->post['module_stepcheckout_customergroup'];
		} else {
			$data['module_stepcheckout_customergroup'] = $this->config->get('module_stepcheckout_customergroup');
		}
		
		
		$this->load->model('setting/extension');
		
		$payment_methods = $this->model_setting_extension->getInstalled('payment');
		
		$data['payment_methods'] = array();
		foreach($payment_methods as $payment_method){
			if ($this->config->get('payment_'.$payment_method . '_status')) {
				 $this->load->language('extension/payment/'. $payment_method);
			
				
				if (isset($this->request->post['module_stepcheckout_payment'])) {
					$data['module_stepcheckout_payment'] = $this->request->post['module_stepcheckout_payment'];
				} else {
					$data['module_stepcheckout_payment'] = $this->config->get('module_stepcheckout_payment');
				}
				
				$data['payment_methods'][] = array(
					'name' 		=> strip_tags($this->language->get('heading_title')),
					'code'			=> $payment_method,
				);
			}
		}
		
		$shipping_methods = $this->model_setting_extension->getInstalled('shipping');
		
		foreach($shipping_methods as $shipping_method){
			if ($this->config->get('shipping_'.$shipping_method . '_status')) {
				if(version_compare(VERSION,'2.3.0.0','>=')){
				 $this->load->language('extension/shipping/'. $shipping_method);
				}else{
				 $this->load->language('shipping/'. $shipping_method);
				}
				
				if (isset($this->request->post['module_stepcheckout_shipping'])) {
				$data['module_stepcheckout_shipping'] = $this->request->post['module_stepcheckout_shipping'];
				} else {
					$data['module_stepcheckout_shipping'] = $this->config->get('module_stepcheckout_shipping');
				}
				$data['shipping_methods'][] = array(
					'name' 		=> strip_tags($this->language->get('heading_title')),
					'code'			=> $shipping_method,
				);
			}
		}
		
		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['module_stepcheckout_description'])) {
			$data['module_stepcheckout_description'] = $this->request->post['module_stepcheckout_description'];
		} elseif ($this->config->get('module_stepcheckout_description')) {
			$data['module_stepcheckout_description'] = $this->config->get('module_stepcheckout_description');
		} else {
			$data['module_stepcheckout_description'] = array();
		}
		
		if (isset($this->request->post['module_stepcheckout_account'])) {
			$data['module_stepcheckout_account'] = $this->request->post['module_stepcheckout_account'];
		} elseif ($this->config->get('module_stepcheckout_account')) {
			$data['module_stepcheckout_account'] = $this->config->get('module_stepcheckout_account');
		} else {
			$data['module_stepcheckout_account'] = array();
		}
		
		if (isset($this->request->post['module_stepcheckout_success_page'])) {
			$data['module_stepcheckout_success_page'] = $this->request->post['module_stepcheckout_success_page'];
		} elseif ($this->config->get('module_stepcheckout_success_page')) {
			$data['module_stepcheckout_success_page'] = $this->config->get('module_stepcheckout_success_page');
		} else {
			$data['module_stepcheckout_success_page'] = array();
		}
		
		if (isset($this->request->post['module_stepcheckout_sociallogin'])) {
			$data['module_stepcheckout_sociallogin'] = $this->request->post['module_stepcheckout_sociallogin'];
		} elseif ($this->config->get('module_stepcheckout_sociallogin')) {
			$data['module_stepcheckout_sociallogin'] = $this->config->get('module_stepcheckout_sociallogin');
		} else {
			$data['module_stepcheckout_sociallogin'] = array();
		}
		
		if (isset($this->request->post['module_stepcheckout_address'])) {
			$data['module_stepcheckout_address'] = $this->request->post['module_stepcheckout_address'];
		} elseif ($this->config->get('module_stepcheckout_address')) {
			$data['module_stepcheckout_address'] = $this->config->get('module_stepcheckout_address');
		} else {
			$data['module_stepcheckout_address'] = array();
		}
		
		if (isset($this->request->post['module_stepcheckout_paymentt'])) {
			$data['module_stepcheckout_paymentt'] = $this->request->post['module_stepcheckout_paymentt'];
		} elseif ($this->config->get('module_stepcheckout_paymentt')) {
			$data['module_stepcheckout_paymentt'] = $this->config->get('module_stepcheckout_paymentt');
		} else {
			$data['module_stepcheckout_paymentt'] = array();
		}
		
		if (isset($this->request->post['module_stepcheckout_shopping'])) {
			$data['module_stepcheckout_shopping'] = $this->request->post['module_stepcheckout_shopping'];
		} elseif ($this->config->get('module_stepcheckout_shopping')) {
			$data['module_stepcheckout_shopping'] = $this->config->get('module_stepcheckout_shopping');
		} else {
			$data['module_stepcheckout_shopping'] = '';
		}
		
		$this->load->model('catalog/product');

		$data['products'] = array();
		
		
		if (isset($this->request->post['module_stepcheckout_product'])) {
			$products = $this->request->post['module_stepcheckout_product'];
		} elseif ($this->config->get('module_stepcheckout_product')) {
			$products = $this->config->get('module_stepcheckout_product');
		} else {
			$products = array();
		}
		
		foreach ($products as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);

			if ($product_info) {
				$data['products'][] = array(
					'product_id' => $product_info['product_id'],
					'name'       => $product_info['name']
				);
			}
		}
		
		
	

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/stepcheckout', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/stepcheckout')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}