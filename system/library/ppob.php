<?php
final class Ppob {

	public function __construct($registry) {
		$this->registry = $registry;

		// Credential
		$this->username = "081310902580";
		$this->apiKey		= "2565ce7e4420d8e3";
		$this->sign 		= "8d2190e012baed857fb9d6d0ec8146e8";
		$this->signature= md5($this->username.$this->apiKey.'bl');

		// API mobilepulsa
		$this->baseUrl =	"https://testprepaid.mobilepulsa.net/v1/legacy/index";
		$this->baseUrlPostpaid = "https://testpostpaid.mobilepulsa.net/api/v1/bill/check";
	}

	public function curl($url, $commands) {
    $ch  = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($commands));
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
	}

	function isJson($string) {
	 json_decode($string, true);
	 return (json_last_error() == JSON_ERROR_NONE);
	}

  public function getBalance(){
		$commands = array(
			"commands"=>"balance",
			"username"=>$this->username,
			"sign"=>$this->signature
		);

    $url = $this->baseUrl;

		$checkJson = $this->isJson($this->curl($url, $commands));
		$result = json_decode($this->curl($url, $commands), true);

    if ($checkJson) {
    	return number_format($result['data']['balance'],0,",",".");;
    }
  }

	public function price_list_prepaid($type, $operator){
		$commands = array(
			"commands" => "pricelist",
			"username" => $this->username,
			"sign"     => md5($this->username.$this->apiKey.'pl'),
		  "status"   => "all"
		);

		$url = $this->baseUrl."/".$type."/".$operator;

		$checkJson = $this->isJson($this->curl($url, $commands));
		$result = json_decode($this->curl($url, $commands), true);

		if ($checkJson) {
    	return $result;
    }
	}

	public function topup_prepaid($ref_id, $inquiry_number, $inquiry_code){
		$commands = array(
			"commands"   => "topup",
			"username"   => $this->username,
			"ref_id"     => $ref_id,
			"hp"         => $inquiry_number,
			"pulsa_code" => $inquiry_code,
			"sign"       => md5($this->username.$this->apiKey.$ref_id)
		);

		$checkJson = $this->isJson($this->curl($this->baseUrl, $commands));
		$result = json_decode($this->curl($this->baseUrl, $commands), true);

		if ($checkJson) {
    	return $result;
    }
	}

	public function price_list_postpaid($type){
		$commands = array(
			"commands"   => "pricelist-pasca",
			"username"   => $this->username,
			"status" 		 => "active",
			"sign"       => md5($this->username.$this->apiKey.'pl')
		);

		$url = $this->baseUrlPostpaid."/".$type;

		$checkJson = $this->isJson($this->curl($url, $commands));
		$result = json_decode($this->curl($url, $commands), true);

		if ($checkJson) {
    	return $result;
    }
	}

	public function inq_postpaid($ref_id,$inq_code,$inq_user,$inq_month){
		$commands = array(
			"commands" => "inq-pasca",
	    "username" => $this->username,
	    "code"     => $inq_code,
	    "hp"       => $inq_user,
	    "ref_id"   => $ref_id,
	    "sign"     => md5($this->username.$this->apiKey.$ref_id),
	    "month"    => $inq_month
		);

		$checkJson = $this->isJson($this->curl($this->baseUrlPostpaid, $commands));
		$result = json_decode($this->curl($this->baseUrlPostpaid, $commands), true);

		if ($checkJson) {
    	return $result;
    }
	}

	public function pay_postpaid($tr_id){
		$commands = array(
			"commands" => "pay-pasca",
	    "username" => $this->username,
	    "tr_id"    => $tr_id,
	    "sign"     => md5($this->username.$this->apiKey.$tr_id)
		);

		$checkJson = $this->isJson($this->curl($this->baseUrlPostpaid, $commands));
		$result = json_decode($this->curl($this->baseUrlPostpaid, $commands), true);

		if ($checkJson) {
    	return $result;
    }
	}
}
