<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class ModelSupplierSupplier extends Model {
	public function alterTables() {

  	$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "supplier` (`supplier_id` int(11) NOT NULL AUTO_INCREMENT,`name` varchar(255), `description` varchar(255), `sort_order` int(3) NOT NULL,PRIMARY KEY (`supplier_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4");

    $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "supplier_to_product` (`supplier_to_product_id` int(11) NOT NULL AUTO_INCREMENT,`supplier_id` int(11), `product_id` int(11),PRIMARY KEY (`supplier_to_product_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4");

	}

	public function addSupplier($data) {
    $this->db->query("INSERT INTO " . DB_PREFIX . "supplier SET name = '" . $this->db->escape($data['name_supplier']) . "', description = '". $this->db->escape($data['description']) ."'");

    $supplier_id = $this->db->getLastId();

		return $supplier_id;
	}

	public function editSupplier($supplier_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "supplier SET name = '" . $data['name_supplier'] . "', description = '".$data['description']."' WHERE supplier_id = '" . (int)$supplier_id . "'");
	}

	public function deleteSupplier($supplier_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "supplier` WHERE supplier_id = '" . (int)$supplier_id . "'");
	}

	public function getSupplier($supplier_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "supplier` WHERE supplier_id = '" . (int)$supplier_id . "'");

		return $query->row;
	}

	public function getSupplierByProduct($product_id){
		$query = $this->db->query("SELECT DISTINCT * from " . DB_PREFIX . "supplier s LEFT JOIN " . DB_PREFIX . "supplier_to_product sp on (s.supplier_id = sp.supplier_id) WHERE sp.product_id = '".(int)$product_id."'");

		return $query->row;
	}

	public function getSupplies($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "supplier afd";

		if (!empty($data['filter_supplier'])) {
			$sql .= " WHERE afd.name LIKE '%" . $this->db->escape($data['filter_supplier']) . "%'";
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

	public function getTotalSupplier() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "supplier`");

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

	public function getTotalProductsBySupplierId($supplier_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_additional_field WHERE additional_field_id = '" . (int)$additional_field_id . "'");

		return $query->row['total'];
	}
}
