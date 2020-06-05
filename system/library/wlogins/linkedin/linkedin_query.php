<?php
class ControllerCommonQuickLoginSocialLinkedinlinkedinQuery extends Controller {
	public function index(){
		$this->load->language('common/quick_login');
		$this->load->model('account/quick_login');
		$this->load->model('account/customer');
		require_once(DIR_APPLICATION. "controller/common/quick_login_social/linkedin/http.php"); //include linkedin SDK
		require_once(DIR_APPLICATION. "controller/common/quick_login_social/linkedin/oauth_client.php"); //include linkedin SDK
		
		######### linkedin API Configuration ##########
		
		if($this->config->get('quick_login_socialmedia')) {
			$quick_login_socialmedia_data = $this->config->get('quick_login_socialmedia');
		}else{
			$quick_login_socialmedia_data = array();
		}
		
		$appId = (!empty($quick_login_socialmedia_data['linkedin']['client_id'])) ? $quick_login_socialmedia_data['linkedin']['client_id'] : '';
		
		$appSecret = (!empty($quick_login_socialmedia_data['linkedin']['client_secret'])) ? $quick_login_socialmedia_data['linkedin']['client_secret'] : '';
		
		$homeurl = (!empty($quick_login_socialmedia_data['linkedin']['callbackdomain'])) ? $quick_login_socialmedia_data['linkedin']['callbackdomain'] : ''; //return to home
		$linkedinScope = 'r_basicprofile r_emailaddress';

		$client = new oauth_client_class;
		
		$client->debug = false;
		$client->debug_http = true;
		$client->redirect_uri = $homeurl;

		$client->client_id = $appId;
		$application_line = __LINE__;
		$client->client_secret = $appSecret;
		if (strlen($client->client_id) == 0 || strlen($client->client_secret) == 0)
			die('Please go to LinkedIn Apps page https://www.linkedin.com/secure/developer?newapp= , '.
			'create an application, and in the line '.$application_line.
			' set the client_id to Consumer key and client_secret with Consumer secret. '.
			'The Callback URL must be '.$client->redirect_uri).' Make sure you enable the '.
			'necessary permissions to execute the API calls your application needs.';
			
			$client->scope = $linkedinScope;
			
			if (($success = $client->Initialize())) {
			  if (($success = $client->Process())) {
				if (strlen($client->authorization_error)) {
				  $client->error = $client->authorization_error;
				  $success = false;
				} elseif (strlen($client->access_token)) {
				  $success = $client->CallAPI(
								'http://api.linkedin.com/v1/people/~:(id,email-address,first-name,last-name,location,picture-url,public-profile-url,formatted-name)', 
								'GET', array(
									'format'=>'json'
								), array('FailOnAccessError'=>true), $user);
				}
			  }
			  $success = $client->Finalize($success);
			}
			
			if ($client->exit) exit;
	}
}
?>