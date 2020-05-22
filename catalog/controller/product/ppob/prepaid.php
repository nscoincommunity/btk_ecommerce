<?php

  class ControllerProductPpobPrepaid extends Controller
  {

    public function price_list(){
       $this->load->model('catalog/product');
  		// if (!$this->customer->isLogged()) {
  		// 	$this->session->data['redirect'] = $this->url->link('account/account', '', true);
      //
  		// 	$this->response->redirect($this->url->link('account/login', '', true));
  		// }

      // Get Product from MP
      $type = $this->request->post['type'];
  		$operator = $this->request->post['operator'];

  		$product_list = $this->ppob->price_list_prepaid($type, $operator);

      $product_available = [];

      foreach ($product_list["data"] as $pp => $data) {
        if ($type == "pulsa") {
          $code = $data['pulsa_code'];

          $product = $this->model_catalog_product->getPpobProduct($code);

          if (strpos($product['price'], '.') !== false) {
              $price_split = explode(".",$product['price']);
              $price = $price_split[0];
          }

          if ($product) {
            $products = array_merge($product, array(
              "pulsa_nominal" => (is_numeric($data["pulsa_nominal"]) ? number_format($data["pulsa_nominal"],0,'.','.') : $data["pulsa_nominal"]) ,
              "masaaktif" => $data["masaaktif"],
              "harga" => number_format($price,0,'.','.')
            ));

            array_push($product_available, $products);

          }
        }

      }

  		$this->response->addHeader('Content-Type: application/json');
  		$this->response->setOutput(json_encode($product_available));
  	}

  	public function topup(){
  		// if (!$this->customer->isLogged()) {
  		// 	$this->session->data['redirect'] = $this->url->link('account/account', '', true);
  		//
  		// 	$this->response->redirect($this->url->link('account/login', '', true));
  		// }
  		$ref_id 				= $this->request->post['ref_id'];
  		$inquiry_number = $this->request->post['inquiry_number']; //hp
  		$inquiry_code 	= $this->request->post['inquiry_code']; //pulsa_code

  		$result = $this->ppob->topup_prepaid($ref_id, $inquiry_number, $inquiry_code);

  		$this->response->addHeader('Content-Type: application/json');
  		$this->response->setOutput(json_encode($result));
  	}
  }


?>
