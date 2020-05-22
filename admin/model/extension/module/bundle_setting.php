<?php
class ModelExtensionModuleBundleSetting extends Model {
    public function ifInstalled($type,$code) {
            $extension_data = array();

            $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->db->escape($type) . "' AND `code` = '" . $this->db->escape($code) . "' ");

            return (int)$query->row['total'];
        }
    
	public function editSetting($data, $store_id = 0) {
        // Shippings
        $shippings = array(
            'first_reg' => 'Regular Service', 
            'first_ons' => 'ONS Service', 
            'first_sds' => 'SDS Service',
            'idl_icon' => 'IDL Ekonomis (iCON)', 
            'idl_ireg' => 'IDL Regular (iREG)', 
            'idl_isds' => 'IDL Same Day Service (iSDS)', 
            'idl_ions' => 'IDL Overnight Service (iONS)', 
            'idl_iscf' => 'IDL Special Fleet (iSCF)',                
            'jne_oke'   => 'JNE OKE', 
            'jne_reg'   => 'JNE Regular', 
            'jne_yes'   => 'JNE YES', 
            'jnt_ez'        => 'J&T EZ (Regular Service)', 
            'ncs_nrs'   => 'NRS Reguler Service',
            'ncs_ons'   => 'ONS Overnight Service',
            'ncs_sds'   => 'SDS Same Day Service',            
            'ninja_standard' => 'Ninja Standard', 
            'ninja_nextday'  => 'Ninja Next Day',             
            'sicepat_reg'     => 'Sicepat Reguler', 
            'sicepat_best'     => 'Sicepat Best', 
            'pos_biasa'     => 'POS Biasa', 
            'pos_paket_kilat' => 'POS Paket Kilat', 
            'pos_express'   => 'POS Express', 
            'tiki_eco'      => 'TIKI Economy', 
            'tiki_reg'      => 'TIKI Regular', 
            'tiki_sds'      => 'TIKI Same Day Service', 
            'tiki_hds'      => 'TIKI Holiday Delivery Service', 
            'tiki_ons'      => 'TIKI One Night Service', 
            'pahala_express'    => 'Pahala Express (Regular Service)', 
            'pahala_ons'    => 'Pahala ONS (Over Night Service)', 
            'pahala_sds'    => 'Pahala SDS (Same Day Service)', 
            'wahana_des'    => 'Wahana DES (Regular Service)', 
            'jet_reg'       => 'JET Regular', 
            'jet_pri'       => 'JET Priority', 
            'jet_crg'       => 'JET Cargo',
            'lion_regpack'       => 'Lion Regular Service',
            'lion_onepack'       => 'Lion One Day Service',
            "rpx_sdp" => "SameDay Package",
            "rpx_mdp" => "MidDay Package",
            "rpx_ndp" => "Next Day Package",
            "rpx_rgp" => "Regular Package",
            "star_reguler" => "Star Reguler",
            "star_express" => "Star Express",
            "star_motor"   => "Star MOTOR",
            "star_motorcc"=> "Star MOTOR 150 - 250 CC"
            );

        foreach ($shippings as $shipping => $name) {
  
        $this->db->query("DELETE FROM " . DB_PREFIX . "extension WHERE `type` = 'shipping' AND `code` = '" . $this->db->escape($shipping) . "'");
            
        $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '" . (int)$store_id . "' AND `code` = 'shipping_" . $this->db->escape($shipping) . "'");
            
        }
        
        if(isset($data['module_bundle_preferred_shipping'])) {
            foreach ($data['module_bundle_preferred_shipping'] as $key) {            
                $this->db->query("INSERT INTO " . DB_PREFIX . "extension SET `type` = 'shipping', `code` = '" . $this->db->escape($key) . "'");

                $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '" . (int)$store_id . "' AND `code` = 'shipping_" . $this->db->escape($key) . "'");

                $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = 'shipping_" . $this->db->escape($key) . "', `key` = 'shipping_" . $this->db->escape($key) . "_status', `value` = '1'");
               }
        }   
             
        foreach($this->request->post as $item => $value) {
                if(substr($item,0,8) ==  'shipping') {
                $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = '".$item."'");

                $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET `code`='".substr($item,0,strlen($item)-11)."', `key` = '".$item."', `value` = '".$value."'");
                }    
        }
    
	}
    
    public function uninstallTable() {

    $this->cache->delete('country.admin');
    $this->cache->delete('country.catalog');
    $this->cache->delete('country');

    $sqls[]=" drop table IF EXISTS `" . DB_PREFIX . "zone`";
    $sqls[]=" drop table IF EXISTS `" . DB_PREFIX . "country`";
    $sqls[]=" drop table IF EXISTS `hp_subdistrict`";
        
    $sqls[]=" RENAME TABLE `" . DB_PREFIX . "country_hpwd` TO " . DB_PREFIX . "country"; 
    $sqls[]=" RENAME TABLE `" . DB_PREFIX . "zone_hpwd` TO " . DB_PREFIX . "zone"; 
    $sqls[]=" delete FROM `" . DB_PREFIX . "setting` where " . DB_PREFIX . "setting.code like '%bundle%'";

    $table_column['address'] = array("sub_district_id","city_id");
    $table_column['order'] = array(
        "payment_sub_district_id",
        "shipping_sub_district_id");

    foreach($table_column as $table => $columns) {
       foreach($columns as $column) {
            $query = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX.$table."` LIKE '%".$column."%';");
                if($query->num_rows) {
                    $sqls[]=" ALTER TABLE `" . DB_PREFIX.$table."` DROP ".$column."";
                }
            }
        }
        
    $error=0;
        
    foreach ($sqls as $sql){

        if(!$this->db->query($sql))  {
            $error++; 
               }
        sleep(0.2);
            }
      
    return $error ? false : true;
    }

    public function countRows($table) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "".$table."");
        return $query->row['total'];
        }
        public function tableExist($table) {
            $query = $this->db->query("SHOW TABLES LIKE '%".$table."%'");
            return $query->num_rows;
        }

    public function installTable() {

    $this->cache->delete('country.admin');
    $this->cache->delete('country.catalog');
    $this->cache->delete('country');

            $sqls=array();


    if($this->tableExist('country_hpwd') < 1) {
        $sqls[]="  RENAME TABLE " . DB_PREFIX . "country TO " . DB_PREFIX . "country_hpwd ; ";
        $sqls[]="  RENAME TABLE " . DB_PREFIX . "zone TO " . DB_PREFIX . "zone_hpwd ; ";
    }
        
    if($this->tableExist('country_hpwd')) {
        $sqls[]="DROP TABLE IF EXISTS " . DB_PREFIX . "country_hpwd";
        $sqls[]="DROP TABLE IF EXISTS " . DB_PREFIX . "zone_hpwd";
    }
    
    $table_column['address'] = array("sub_district_id","city_id");
    $table_column['order'] = array(
    "payment_sub_district_id",
    "shipping_sub_district_id");

    foreach($table_column as $table => $columns) {

       foreach($columns as $column) {
           $query = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX.$table."` LIKE '%".$column."%';");
        if(!$query->num_rows) {
            $sqls[]="ALTER TABLE `" . DB_PREFIX.$table."` ADD ".$column." INT(11) NOT NULL AFTER ".$table."_id";
            }
        } 
    }
            
    $sqls[]=" CREATE TABLE IF NOT EXISTS `oc_country` (
      `country_id` int(5) NOT NULL AUTO_INCREMENT,
      `name` varchar(30) NOT NULL,
      `code` varchar(10) NOT NULL,
      `status` enum('1','0') NOT NULL DEFAULT '1',
      `address_format` varchar(200) NOT NULL,
      `iso_code_2` varchar(5) NOT NULL,
      `iso_code_3` varchar(5) NOT NULL,
      `postcode_required` enum('1','0') NOT NULL DEFAULT '1',
      PRIMARY KEY (`country_id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ; ";
            
         $sqls[]=" CREATE TABLE IF NOT EXISTS `oc_zone` (
      `zone_id` int(11) NOT NULL AUTO_INCREMENT,
      `country_id` int(11) NOT NULL,
      `type` varchar(10) NOT NULL,
      `name` varchar(128) NOT NULL,
      `postal_code` int(11) NOT NULL,
      `code` varchar(5) NOT NULL DEFAULT '',
      `status` enum('1','0') NOT NULL DEFAULT '1',
      PRIMARY KEY (`zone_id`),
      KEY `country_id` (`country_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1 ; ";
            
        $sqls[]=" CREATE TABLE IF NOT EXISTS `hp_subdistrict` (
      `sub_district_id` int(11) NOT NULL AUTO_INCREMENT,
      `city_id` int(11) NOT NULL,
      `name` varchar(128) NOT NULL,
      `status` enum('1','0') NOT NULL DEFAULT '1',
      PRIMARY KEY (`sub_district_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1 ; ";

                
    $error=0;
            
    foreach ($sqls as $sql) {

        if(!$this->db->query($sql))  {
            $error++; 
               }
        sleep(0.2);
            }

    if($this->insertBasicRows()) {
        $error++;
    }        
          return ($error < 1) ? true : false;


    }
    public function insertBasicRows() {
        
    $sqls[]=" TRUNCATE " . DB_PREFIX . "geo_zone";
    $sqls[]=" TRUNCATE " . DB_PREFIX . "zone_to_geo_zone";

    // define correct separator
    $sep=(DIRECTORY_SEPARATOR == "/")  ? "/" : "\\";

    $error = 0;

    if(!$this->ImportSQL(file_get_contents(DIR_APPLICATION.'model'.$sep.'extension'.$sep.'module'.$sep.'basic_scheleton.sql'))) {
        $error++;
    }

    // rename table for custom prefix compatibility
    if(DB_PREFIX != 'oc_') {
        $sqls[]="  RENAME TABLE `oc_country` TO " . DB_PREFIX . "country ; ";
        $sqls[]="  RENAME TABLE `oc_zone` TO " . DB_PREFIX . "zone ; ";
    } 
        
    foreach($sqls as $sql) {
       if(!$this->db->query($sql))  
        $error++;
       }

      return $error ? false : true;
    }
    
    public function apiusage($val) {
        $this->db->query("UPDATE " . DB_PREFIX . "setting SET `value` = '".$val."' WHERE `key` = 'module_bundle_api_usage'");
        
        if($val) {
            $this->db->query("UPDATE " . DB_PREFIX . "setting SET store_id = '0', `code` = 'hpwd', `value` = SUBSTRING(`value`,1,32) WHERE `key` = 'hpwd_theq'");
        }  else {
            $this->db->query("UPDATE " . DB_PREFIX . "setting SET store_id = '0', `code` = 'hpwd', `value` = CONCAT(`value`,'".mt_rand(2,999)."') WHERE `key` = 'hpwd_theq'");            
        }      
    }
    
    public function ImportSQL($sql) {
		foreach (explode(";\n", $sql) as $sql) {
			$sql = trim($sql);

			if ($sql) {
				$this->db->query($sql);
			}
		}        
	}
}