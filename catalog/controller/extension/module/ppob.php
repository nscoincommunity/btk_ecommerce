<?php
class ControllerExtensionModulePpob extends Controller {
	public function index($setting) {
		static $module = 0;


		return $this->load->view('extension/module/ppob', $data);
	}
}
