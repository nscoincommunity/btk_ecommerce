<?php
class ModelLocalisationSubdistrict extends Model {
	public function getSubdistrict($subdistrict_id) {
		$query = $this->db->query("SELECT * FROM hp_subdistrict WHERE subdistrict_id = '" . (int)$subdistrict_id . "' AND status = '1'");

		return $query->row;
	}	

     // for local version
	public function getSubdistrictsByZoneId($zone_id) {
		$subdistrict_data = $this->cache->get('subdistrict.' . (int)$zone_id);

		if (!$subdistrict_data) {
			$query = $this->db->query("SELECT * FROM hp_subdistrict WHERE city_id = '" . (int)$zone_id . "' AND status = '1' ORDER BY name ASC");

			$subdistrict_data = $query->rows;

			$this->cache->set('subdistrict.' . (int)$zone_id, $subdistrict_data);
		}

		return $subdistrict_data;
	}	
 
}