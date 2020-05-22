<?php

  class ControllerProductPpobPostpaid extends Controller
  {

    public function price_list(){
  		// if (!$this->customer->isLogged()) {
  		// 	$this->session->data['redirect'] = $this->url->link('account/account', '', true);
  		//
  		// 	$this->response->redirect($this->url->link('account/login', '', true));
  		// }

  		$type = $this->request->post['type'];

  		$result = $this->ppob->price_list_postpaid($type);

  		$this->response->addHeader('Content-Type: application/json');
  		$this->response->setOutput(json_encode($result));
  	}

  	public function inq_postpaid(){
  		// if (!$this->customer->isLogged()) {
  		// 	$this->session->data['redirect'] = $this->url->link('account/account', '', true);
  		//
  		// 	$this->response->redirect($this->url->link('account/login', '', true));
  		// }
  		try {
        $ref_id 				= $this->request->post['ref_id'];
    		$inquiry_number = $this->request->post['inquiry_number']; //hp
    		$inquiry_code 	= $this->request->post['inquiry_code']; //pulsa_code
        $month          = $this->request->post['inquiry_month'];

    		$result = $this->ppob->inq_postpaid($ref_id, $inquiry_code, $inquiry_number, $month);

        if ($result["data"]["response_code"] == "00") {
          $pay    = $this->ppob->pay_postpaid($result['data']['tr_id']);
        }

      } catch (\Exception $e) {
        $result = array(
          "response_code" => "01",
          "message" => "Error code"
        );
      }

  		$this->response->addHeader('Content-Type: application/json');
  		$this->response->setOutput(json_encode($result));
  	}


  }


?>
