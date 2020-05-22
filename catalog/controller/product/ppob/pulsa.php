<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class ControllerProductPpobPulsa extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('product/ppob');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);


		$this->document->setTitle($this->language->get('title_pulsa'));
		// $this->document->setDescription($product_info['meta_description']);
		// $this->document->setKeywords($product_info['meta_keyword']);
		// $this->document->addLink($this->url->link('product/product', 'product_id=' . $this->request->get['product_id']), 'canonical');
		$this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
		$this->document->addStyle('catalog/view/theme/so-bestshop/css/ppob.css');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

		$data['heading_title'] = $this->language->get('caption_pulsa');

		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['base_url'] = $this->config->get('config_url');

		$this->config->set('template_cache', false);
		$this->response->setOutput($this->load->view('product/ppob/pulsa', $data));
	}

	private function searchForId($search_value, $array) {

    // Iterating over main array
    foreach ($array as $key1 => $val1) {


        // Adding current key to search path

        // Check if this value is an array
        // with atleast one element
        if(is_array($val1) and count($val1)) {

            // Iterating over the nested array
            foreach ($val1 as $key2 => $val2) {

                if($val2 == $search_value) {

                    return $key1;
                }
            }
        }

        elseif($val1 == $search_value) {
            return $val1;
        }
    }

    return null;
}

	public function operator_check(){
		// INDOSAT ( hindosat, isatdata )	0814,0815,0816,0855,0856,0857,0858
		// XL ( xld, xldata )
		// AXIS ( haxis, axisdata )	0838,0837,0831,0832
		// TELKOMSEL ( htelkomsel, tseldata)	0812,0813,0852,0853,0821,0823,0822,0851
		// SMARTFREN ( hsmart )	0881,0882,0883,0884, 0885,0886,0887,0888
		// THREE ( hthree, threedata)	0896,0897,0898,0899,0895
		try {
			$number = $this->request->post['number'];
			$ss = str_split($number);

			$request = $ss[0].$ss[1].$ss[2].$ss[3];

			$operator_list = array(
				"indosat" => array("0814","0815","0816","0855","0856","0857","0858"),
				"xl" 			=> array("0817","0818","0819","0859","0878","0877"),
				"axis"		=> array("0838","0837","0831","0832"),
				"telkomsel"=> array("0812","0813","0852","0853","0821","0823","0822","0851"),
				"smartfren"=> array("0881","0882","0883","0884","0885","0886","0887","0888"),
				"three"=> array("0896","0897","0898","0899","0895")
			);

			$result = $this->searchForId($request,$operator_list);

			$responses = array(
				"operator" => $result
			);
		} catch (\Exception $e) {
			$responses = array(
				"operator" => null
			);
		}


		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($responses));

	}



}
