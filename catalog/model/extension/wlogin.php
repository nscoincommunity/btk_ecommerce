<?php
class ModelModuleWlogin extends Model {
	public function addCustomer($data){
		if (isset($data['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($data['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $data['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$this->load->model('account/customer_group');

		$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);

		
		$sql = "INSERT INTO " . DB_PREFIX . "customer SET customer_group_id = '" . (int)$customer_group_id . "', store_id = '" . (int)$this->config->get('config_store_id') . "', email = '" . $this->db->escape($data['email']) . "', salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', status = '1', approved = '" . (int)!$customer_group_info['approval'] . "', date_added = NOW()";
		
		/* if(!empty($data['username'])){
			$sql .= ", username = '" . $this->db->escape($data['username']) . "'";
		} */
		
		if(!empty($data['firstname'])){
			$sql .= ", firstname = '" . $this->db->escape($data['firstname']) . "'";
		}
		
		if(!empty($data['lastname'])){
			$sql .= ", lastname = '" . $this->db->escape($data['lastname']) . "'";
		}
		
		if(!empty($data['telephone'])){
			$sql .= ", telephone = '" . $this->db->escape($data['telephone']) . "'";
		}
		
		if(!empty($data['fax'])){
			$sql .= ", fax = '" . $this->db->escape($data['fax']) . "'";
		}
		
		$this->db->query($sql);
		

		$customer_id = $this->db->getLastId();
		
		
		$sql1 = "INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$customer_id . "'";
		
		if(!empty($data['firstname'])){
			$sql1 .= ", firstname = '" . $this->db->escape($data['firstname']) . "'";
		}
		
		if(!empty($data['lastname'])){
			$sql1 .= ", lastname = '" . $this->db->escape($data['lastname']) . "'";
		}
		
		
		if(!empty($data['company'])){
			$sql1 .= ", company = '" . $this->db->escape($data['company']) . "'";
		}
		
		if(!empty($data['address_1'])){
			$sql1 .= ", address_1 = '" . $this->db->escape($data['address_1']) . "'";
		}
		
		if(!empty($data['address_2'])){
			$sql1 .= ", address_2 = '" . $this->db->escape($data['address_2']) . "'";
		}
		
		if(!empty($data['city'])){
			$sql1 .= ", city = '" . $this->db->escape($data['city']) . "'";
		}
		
		if(!empty($data['postcode'])){
			$sql1 .= ", postcode = '" . $this->db->escape($data['postcode']) . "'";
		}
		
		if(!empty($data['country_id'])){
			$sql1 .= ", country_id = '" . $this->db->escape($data['country_id']) . "'";
		}
		
		if(!empty($data['zone_id'])){
			$sql1 .= ", zone_id = '" . $this->db->escape($data['zone_id']) . "'";
		}
		
		$this->db->query($sql1);

		$this->load->language('mail/customer');

		$subject = sprintf($this->language->get('text_subject'), $this->config->get('config_name'));

		$message = sprintf($this->language->get('text_welcome'), $this->config->get('config_name')) . "\n\n";

		if (!$customer_group_info['approval']) {
			$message .= $this->language->get('text_login') . "\n";
		} else {
			$message .= $this->language->get('text_approval') . "\n";
		}

		$message .= $this->url->link('account/login', '', 'SSL') . "\n\n";
		$message .= $this->language->get('text_services') . "\n\n";
		$message .= $this->language->get('text_thanks') . "\n";
		$message .= $this->config->get('config_name');

		$mail = new Mail();
		$mail->setTo($data['email']);
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender($this->config->get('config_name'));
		$mail->setSubject($subject);
		$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
		$mail->send();

		// Send to main admin email if new account email is enabled
		if ($this->config->get('config_account_mail')) {
			$message  = $this->language->get('text_signup') . "\n\n";
			$message .= $this->language->get('text_website') . ' ' . $this->config->get('config_name') . "\n";
			if(!empty($data['firstname'])){
				$message .= $this->language->get('text_firstname') . ' ' . $data['firstname'] . "\n";
			}
			
			if(!empty($data['lastname'])) {
				$message .= $this->language->get('text_lastname') . ' ' . $data['lastname'] . "\n";
			}
			
			$message .= $this->language->get('text_customer_group') . ' ' . $customer_group_info['name'] . "\n";
			
			if(!empty($data['email'])){
				$message .= $this->language->get('text_email') . ' '  .  $data['email'] . "\n";
			}
			
			if(!empty($data['telephone'])) {
				$message .= $this->language->get('text_telephone') . ' ' . $data['telephone'] . "\n";
			}

			$mail->setTo($this->config->get('config_email'));
			$mail->setSubject(html_entity_decode($this->language->get('text_new_customer'), ENT_QUOTES, 'UTF-8'));
			$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();

			// Send to additional alert emails if new account email is enabled
			$emails = explode(',', $this->config->get('config_mail_alert'));
			if($emails) {
				foreach ($emails as $email) {
					if (utf8_strlen($email) > 0 && preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $email)) {
						$mail->setTo($email);
						$mail->send();
					}
				}
			}
		}

		return $customer_id;
	}
	
	public function getCustomer($customer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row;
	}
	
	public function checkUserBySocialMedia($type, $data){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($data['email'])) . "'");
		if($query->row) {
			 return array(
				'customer_id' =>$query->row['customer_id'],
				'customer' =>'exist',
			 );
		}else{
		 $customer_id = $this->addcustomerbysociallogin($type, $data);
		 return array(
				'customer_id' =>$customer_id,
				'customer' =>'register',
			 );
		}
	}
	
	public function addcustomerbysociallogin($type, $data){
		$customer_group_id = $this->config->get('config_customer_group_id');
		if($this->config->get('quick_login_socialmedia')) {
			$quick_login_socialmedia = $this->config->get('quick_login_socialmedia');
			if(!empty($quick_login_socialmedia[$type]['customer_group_id'])) {
				$customer_group_id = $quick_login_socialmedia[$type]['customer_group_id'];
			}
		}

		$this->load->model('account/customer_group');

		$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);

		
		$sql = "INSERT INTO " . DB_PREFIX . "customer SET customer_group_id = '" . (int)$customer_group_id . "', store_id = '" . (int)$this->config->get('config_store_id') . "', email = '" . $this->db->escape($data['email']) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', status = '1', approved = '" . (int)!$customer_group_info['approval'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', date_added = NOW()";
		
		$this->db->query($sql);
		
		$customer_id = $this->db->getLastId();
		
		/* Address Id Insert Starts */ 
		$address_sql = "INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$customer_id . "'";
		
		if(!empty($data['firstname'])){
			$address_sql .= ", firstname = '" . $this->db->escape($data['firstname']) . "'";
		}
		
		if(!empty($data['lastname'])){
			$address_sql .= ", lastname = '" . $this->db->escape($data['lastname']) . "'";
		}
		
		$this->db->query($address_sql);
		
		$address_id = $this->db->getLastId();
		
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
		/* Address Id Insert Ends */ 

		$this->load->language('mail/customer');

		$subject = sprintf($this->language->get('text_subject'), $this->config->get('config_name'));

		$message = sprintf($this->language->get('text_welcome'), $this->config->get('config_name')) . "\n\n";

		if (!$customer_group_info['approval']) {
			$message .= $this->language->get('text_login') . "\n";
		} else {
			$message .= $this->language->get('text_approval') . "\n";
		}

		$message .= $this->url->link('account/login', '', 'SSL') . "\n\n";
		$message .= $this->language->get('text_services') . "\n\n";
		$message .= $this->language->get('text_thanks') . "\n";
		$message .= $this->config->get('config_name');

		$mail = new Mail();
		$mail->setTo($data['email']);
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender($this->config->get('config_name'));
		$mail->setSubject($subject);
		$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
		$mail->send();

		// Send to main admin email if new account email is enabled
		if ($this->config->get('config_account_mail')) {
			$message  = $this->language->get('text_signup') . "\n\n";
			$message .= $this->language->get('text_website') . ' ' . $this->config->get('config_name') . "\n";
			if(!empty($data['firstname'])){
				$message .= $this->language->get('text_firstname') . ' ' . $data['firstname'] . "\n";
			}
			
			if(!empty($data['lastname'])) {
				$message .= $this->language->get('text_lastname') . ' ' . $data['lastname'] . "\n";
			}
			
			$message .= $this->language->get('text_customer_group') . ' ' . $customer_group_info['name'] . "\n";
			
			if(!empty($data['email'])){
				$message .= $this->language->get('text_email') . ' '  .  $data['email'] . "\n";
			}
			
			if(!empty($data['telephone'])) {
				$message .= $this->language->get('text_telephone') . ' ' . $data['telephone'] . "\n";
			}

			$mail->setTo($this->config->get('config_email'));
			$mail->setSubject(html_entity_decode($this->language->get('text_new_customer'), ENT_QUOTES, 'UTF-8'));
			$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();

			// Send to additional alert emails if new account email is enabled
			$emails = explode(',', $this->config->get('config_mail_alert'));
			if($emails) {
				foreach ($emails as $email) {
					if (utf8_strlen($email) > 0 && preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $email)) {
						$mail->setTo($email);
						$mail->send();
					}
				}
			}
		}

		return $customer_id;
	}
	
	public function editToken($customer_id, $token) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET token = '" . $this->db->escape($token) . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}
}