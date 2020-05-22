<?php
class ModelCatalogPpob extends Model {
	public function logging($response) {
    $this->db->query("INSERT INTO " . DB_PREFIX . "ppob_log SET response = '" . json_encode($response) . "'");

    $address_id = $this->db->getLastId();

		return $address_id;
	}
}
