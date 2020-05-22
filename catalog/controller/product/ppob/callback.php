<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

class ControllerProductPpobCallback extends Controller {
	private $error = array();

	public function index() {

		try {
			$response = json_decode(file_get_contents('php://input'), true);
			$response = $this->request->post['data'];

			if (isset($response)) {
				$this->load->language('product/ppob');

		    $this->load->model('catalog/ppob');

				$insert_log = $this->model_catalog_ppob->logging($response);

				if ($insert_log) {
					$json = "success";
				} else {
					$json = "failed to insert log";
				}
			} else {
				$json = "response failed";
			}

		} catch (\Exception $e) {

			$json = "failed";

		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

	private function validateCallback($cbext){
		try {
			$scCbExy = "B7kM3@xsaaERASRd$#%&#@";

			if ($cbext == $scCbExy) {
				return true;
			} else {
				return false;
			}
		} catch (\Exception $e) {
				return false;
		}

	}




}
