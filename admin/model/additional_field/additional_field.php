<?php
class ModelAdditionalfieldAdditionalfield extends Model {
	public function alterTables() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mpoints_additional_field` (`additional_field_id` int(11) NOT NULL AUTO_INCREMENT,`sort_order` int(3) NOT NULL,PRIMARY KEY (`additional_field_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mpoints_additional_field_description` (`additional_field_id` int(11) NOT NULL,`language_id` int(11) NOT NULL,`name` varchar(128) NOT NULL,PRIMARY KEY (`additional_field_id`,`language_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "product_additional_field` (`product_additional_field_id` int(11) NOT NULL AUTO_INCREMENT,`product_id` int(11) NOT NULL,`additional_field_id` int(11) NOT NULL,`image` varchar(255) NOT NULL,`height` int(11) NOT NULL,`width` int(11) NOT NULL,PRIMARY KEY (`product_additional_field_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=123");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "product_additional_field_value` (`product_additional_field_value_id` int(11) NOT NULL AUTO_INCREMENT,`product_id` int(11) NOT NULL,`additional_field_id` int(11) NOT NULL,`language_id` int(11) NOT NULL,`value` text NOT NULL,PRIMARY KEY (`product_additional_field_value_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39");
	}
	
	public function addAdditionalfield($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "mpoints_additional_field` SET sort_order = '" . (int)$data['sort_order'] . "'");

		$additional_field_id = $this->db->getLastId();

		foreach ($data['mpoints_additional_field_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mpoints_additional_field_description SET additional_field_id = '" . (int)$additional_field_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}
		
		return $additional_field_id;
	}

	public function editAdditionalfield($additional_field_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "mpoints_additional_field` SET sort_order = '" . (int)$data['sort_order'] . "' WHERE additional_field_id = '" . (int)$additional_field_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "mpoints_additional_field_description WHERE additional_field_id = '" . (int)$additional_field_id . "'");

		foreach ($data['mpoints_additional_field_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mpoints_additional_field_description SET additional_field_id = '" . (int)$additional_field_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}
	}

	public function deleteAdditionalfield($additional_field_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "mpoints_additional_field` WHERE additional_field_id = '" . (int)$additional_field_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mpoints_additional_field_description WHERE additional_field_id = '" . (int)$additional_field_id . "'");		
	}

	public function getAdditionalfield($additional_field_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mpoints_additional_field` af LEFT JOIN " . DB_PREFIX . "mpoints_additional_field_description afd ON (af.additional_field_id = afd.additional_field_id) WHERE af.additional_field_id = '" . (int)$additional_field_id . "' AND afd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getAdditionalfields($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "mpoints_additional_field` af LEFT JOIN " . DB_PREFIX . "mpoints_additional_field_description afd ON (af.additional_field_id = afd.additional_field_id) WHERE afd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND afd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sort_data = array(
			'afd.name',
			'af.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY afd.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getAdditionalfieldDescriptions($additional_field_id) {
		$mpoints_additional_field_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpoints_additional_field_description WHERE additional_field_id = '" . (int)$additional_field_id . "'");

		foreach ($query->rows as $result) {
			$mpoints_additional_field_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $mpoints_additional_field_data;
	}

	public function getTotalAdditionalfields() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "mpoints_additional_field`");

		return $query->row['total'];
	}
	
	
	// Additional Work Starts
	public function getProductAdditionalfields($product_id) {
		$product_additional_field_data = array();

		$product_additional_field_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_additional_field` po LEFT JOIN `" . DB_PREFIX . "mpoints_additional_field` ad ON (po.additional_field_id = ad.additional_field_id) LEFT JOIN `" . DB_PREFIX . "mpoints_additional_field_description` adf ON (ad.additional_field_id = adf.additional_field_id) WHERE po.product_id = '" . (int)$product_id . "' AND adf.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($product_additional_field_query->rows as $product_additional_field) {
			$product_additional_field_description_data = array();
			
			$value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_additional_field_value WHERE product_id = '" . (int)$product_id . "' AND additional_field_id = '" . (int)$product_additional_field['additional_field_id'] . "'");

			$product_additional_field_description = array();
			foreach ($value_query->rows as $value) {
				$product_additional_field_description[$value['language_id']] = array('value' => $value['value']);
			}

			$product_additional_field_data[] = array(
				'additional_field_id'                  => $product_additional_field['additional_field_id'],
				'width'                 								 => $product_additional_field['width'],
				'height'                 								 => $product_additional_field['height'],
				'name'                 								 => $product_additional_field['name'],
				'image'                 							 => $product_additional_field['image'],
				'product_additional_field_description' => $product_additional_field_description
			);
		}
	
		return $product_additional_field_data;
	}
	// Additional Work Ends
	
	public function getTotalProductsByAdditionalFieldId($additional_field_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_additional_field WHERE additional_field_id = '" . (int)$additional_field_id . "'");

		return $query->row['total'];
	}
}