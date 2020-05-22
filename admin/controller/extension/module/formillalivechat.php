<?php
class ControllerExtensionModuleFormillalivechat extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/formillalivechat');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_formilla', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
	    $data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_chat_id'] = $this->language->get('entry_chat_id');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['help_code'] = $this->language->get('help_code');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['code'])) {
			$data['error_code'] = $this->error['code'];
		} else {
			$data['error_code'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/formillalivechat', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['action'] = $this->url->link('extension/module/formillalivechat', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', 'SSL');

		if (isset($this->request->post['module_formilla_chat_id'])) {
			$data['module_formilla_chat_id'] = $this->request->post['module_formilla_chat_id'];
		} else {
			$data['module_formilla_chat_id'] = $this->config->get('module_formilla_chat_id');
		}

		if (isset($this->request->post['module_formillalivechat_status'])) {
			$data['module_formillalivechat_status'] = $this->request->post['module_formillalivechat_status'];
		} else {
			$data['module_formillalivechat_status'] = $this->config->get('module_formillalivechat_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/formillalivechat', $data));
	}

	protected function validate() {

		if (!$this->request->post['module_formilla_chat_id']) {
			$this->error['code'] = $this->language->get('error_code');
		}

		return !$this->error;
	}
    
    public function uninstall() {
        $this->load->model('setting/setting');
        
        // When uninstalling the module, mark it as disabled to avoid rendering the script in the store
        $this->model_setting_setting->editSettingValue('module_formilla', 'module_formillalivechat_status', "0");
    }
}