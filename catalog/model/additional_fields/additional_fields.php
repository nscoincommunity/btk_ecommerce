<?php
class ModelAdditionalFieldsAdditionalFields extends Model {
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
	
	// Additional Field Work Start
	public function getProductAdditionalFields($product_id) {
		$field_data = array();
		
		$this->load->model('tool/image');
		
		$product_additional_field_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_additional_field pad LEFT JOIN `" . DB_PREFIX . "mpoints_additional_field` af ON (pad.additional_field_id = af.additional_field_id) LEFT JOIN " . DB_PREFIX . "mpoints_additional_field_description afd ON (af.additional_field_id = afd.additional_field_id) WHERE pad.product_id = '" . (int)$product_id . "' AND afd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY af.sort_order ASC");

		foreach ($product_additional_field_query->rows as $field) {
			$value_data = array();

			$value_info = $this->db->query("SELECT value FROM " . DB_PREFIX . "product_additional_field_value WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "' AND additional_field_id = '". (int)$field['additional_field_id']."'")->row;

			if(!empty($value_info['value'])) {
				if ((int)$field['width']) {
					$width = $field['width'];
				}else{
					$width = 74;
				}
				
				if ((int)$field['height']) {
					$height = $field['height'];
				}else{
					$height = 74;
				}
				 
				if ($field['image']) {
					$image = $this->model_tool_image->resize($field['image'], $width, $height);
				} else {
					$image = '';
				}
				
				$field_data[] = array(
					'name'            => $field['name'],
					'value' 					=> $value_info['value'],
					'image' 					=> $image,
				);
			}
		}
				
		return $field_data;
	}
	// Additional Field Work Ends
}