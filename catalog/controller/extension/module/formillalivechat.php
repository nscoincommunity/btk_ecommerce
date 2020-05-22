<?php
class ControllerExtensionModuleFormillalivechat extends Controller {
	public function index() {
        // If the module is disabled, then don't render the chat script
        if(!$this->config->get('module_formillalivechat_status')) {
            return null;
        }
        
		$this->load->language('extension/module/formillalivechat');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['code'] = html_entity_decode($this->config->get('module_formilla_chat_id'));
        
        return $this->load->view('extension/module/formillalivechat', $data);
	}
}