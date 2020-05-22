<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class ModelKluKlu extends Model {
	public function alterTables() {

  	// $this->db->query("ALTER TABLE ". DB_PREFIX . "product ADD column IF NOT EXISTS klu VARCHAR(255) NOT NULL");

	}
}
