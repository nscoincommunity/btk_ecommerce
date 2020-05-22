<?php
class ControllerExtensionPaymentSnap extends Controller {

  private $error = array();

  public function index() {
    $this->load->language('extension/payment/snap');

    $this->document->setTitle($this->language->get('heading_title'));

    $this->load->model('setting/setting');
    $this->load->model('localisation/order_status');
	  $this->config->get('curency');


    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      $this->model_setting_setting->editSetting('payment_snap', $this->request->post);

      $this->session->data['success'] = $this->language->get('text_success');

      $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
    }

    if (isset($this->error['warning'])) {
      $data['error_warning'] = $this->error['warning'];
    } else {
      $data['error_warning'] = '';
    }

    if (isset($this->error['display_name'])) {
      $data['error_display_name'] = $this->error['display_name'];
    } else {
      $data['error_display_name'] = '';
    }
    
    if (isset($this->error['merchant_id'])) {
      $data['error_merchant_id'] = $this->error['merchant_id'];
    } else {
      $data['error_merchant_id'] = '';
    }

    if (isset($this->error['server_key'])) {
      $data['error_server_key'] = $this->error['server_key'];
    } else {
      $data['error_server_key'] = '';
    }

    if (isset($this->error['client_key'])) {
      $data['error_client_key'] = $this->error['client_key'];
    } else {
      $data['error_client_key'] = '';
    }

    if (isset($this->error['snap_currency_conversion'])) {
      $data['error_currency_conversion'] = $this->error['snap_currency_conversion'];
    } else {
      $data['error_currency_conversion'] = '';
    }


    $data['breadcrumbs'] = array();

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('text_home'),
      'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('text_extension'),
      'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('heading_title'),
      'href' => $this->url->link('extension/payment/snap', 'user_token=' . $this->session->data['user_token'], true)
    );

    $data['action'] = $this->url->link('extension/payment/snap', 'user_token=' . $this->session->data['user_token'], true);

    $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'].'&type=payment', true);

    $inputs = array(
      'payment_snap_display_name',
      'payment_snap_environment',
      'payment_snap_merchant_id',
      'payment_snap_server_key',
      'payment_snap_client_key',
      'payment_snap_oneclick',
      'payment_snap_order_status_id',
      'payment_snap_geo_zone_id',
      'payment_snap_sort_order',
      'payment_snap_3d_secure',
      'payment_snap_currency_conversion',
      'payment_snap_status',
      'payment_snap_expiry_duration',
      'payment_snap_expiry_unit',
      'payment_snap_custom_field1',
      'payment_snap_custom_field2',
      'payment_snap_custom_field3',
    );

    foreach ($inputs as $input) {
      if (isset($this->request->post[$input])) {
        $data[$input] = $this->request->post[$input];
      } else {
        $data[$input] = $this->config->get($input);
      }
    }

    $this->load->model('localisation/order_status');

    $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

    $this->load->model('localisation/geo_zone');

    $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
    
    $data['expiry'] = array();
    $unit1 = array('id' => 1,'unit' => "minutes");
    $unit2 = array('id' => 2,'unit' => "hours");
    $unit3 = array('id' => 3,'unit' => "days");
    array_push($data['expiry'] , $unit1, $unit2, $unit3);
    
    // $this->template = 'exension/payment/snap.tpl';
  	$data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
  	$data['footer'] = $this->load->controller('common/footer');
  	
	
	if(!$this->currency->has('IDR'))
	{
		$data['curr'] = true;
	}
	else
	{
		$data['curr'] = false;
	}

	$this->response->setOutput($this->load->view('extension/payment/snap',$data));
	
  }

  protected function validate() {

    if (!$this->user->hasPermission('modify', 'extension/payment/snap')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }

    // check for empty values
    if (!$this->request->post['payment_snap_display_name']) {
      $this->error['display_name'] = $this->language->get('error_display_name');
    }
    
      // default values
    if (!$this->request->post['payment_snap_environment'])
      $this->request->post['snap_environment'] = 1;

      // check for empty values
    if (!$this->request->post['payment_snap_merchant_id']) {
       $this->error['merchant_id'] = $this->language->get('error_merchant_id');
    }

      // check for empty values
    if (!$this->request->post['payment_snap_client_key']) {
       $this->error['client_key'] = $this->language->get('error_client_key');
    }

    if (!$this->request->post['payment_snap_server_key']) {
      $this->error['server_key'] = $this->language->get('error_server_key');
    }
    
    // currency conversion to IDR
    if (!$this->request->post['payment_snap_currency_conversion'] && !$this->currency->has('IDR'))
      $this->error['currency_conversion'] = $this->language->get('error_currency_conversion');

    return !$this->error;
  }
}
?>