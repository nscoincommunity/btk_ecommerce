<?php
class ControllerExtensionModuleBundleSetting extends Controller { 
	private $error         = array();
	private $v_d           = '';
	private $shippings     = array();
 
    public function index() {
            $this->language->load('extension/module/bundle_setting');
        
            $url = $this->request->get['route'];
        
            $this->rightman();
        
            if(!$this->validateTable()) { 
                if($_SERVER['SERVER_NAME'] != $this->v_d) {
                    $this->storeAuth();
                } else {
                
                $this->document->setTitle($this->language->get('error_database'));
                
                $data['install_database'] = $this->url->link('extension/module/bundle_setting/installDatabase', 'user_token=' . $this->session->data['user_token'], true);
                
                $data['text_install_message'] = $this->language->get('text_install_message');
                
                $data['text_upgrade'] = $this->language->get('text_upgrade');
                
                $data['error_database'] = $this->language->get('error_database');
                
                $data['breadcrumbs'] = array();

    	   		$data['breadcrumbs'][] = array(
    	       		'text'      => $this->language->get('text_home'),
    				'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
    	      		'separator' => false
    	   		);
                
                $data['header'] = $this->load->controller('common/header');
      		    $data['column_left'] = $this->load->controller('common/column_left');
                $data['footer'] = $this->load->controller('common/footer');
        
                $this->response->setOutput($this->load->view('extension/module/hpwd_notification', $data));
                
                }
                
            } else {
                 if($_SERVER['SERVER_NAME'] != $this->v_d) {
                    $this->storeAuth();
                } else {
                    $this->getData();
                 }
            }	
		}

    private function shippingservice() {
        // Shippings
        return  array(
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
            'lion_regpack'  => 'Lion Regular Package',
            'lion_onepack'  => 'Lion One Day Service',            
            "rpx_sdp" => "SameDay Package",
            "rpx_mdp" => "MidDay Package",
            "rpx_ndp" => "Next Day Package",
            "rpx_rgp" => "Regular Package",
            "star_reguler" => "Star Reguler",
            "star_express" => "Star Express",
            "star_motor"   => "Star MOTOR",
            "star_motorcc"=> "Star MOTOR 150 - 250 CC"
            );
        }
    
    public function storeAuth() {     
            $data['curl_status'] = $this->curlcheck();
                    
            $this->flushdata();  
            
            if(isset($this->error['warning'])) {
                $data['no_internet_access'] = $this->error['warning'];
            } else {
                $data['no_internet_access'] = '';   
            }
    
            $this->document->setTitle($this->language->get('text_validation'));

            $data['text_curl']                  = $this->language->get('text_curl');
            $data['text_disabled_curl']         = $this->language->get('text_disabled_curl');
     
            $data['text_validation']            = $this->language->get('text_validation');
            $data['text_validate_store']        = $this->language->get('text_validate_store');
            $data['text_information_provide']   = $this->language->get('text_information_provide');
            $data['domain_name'] = $this->language->get('text_validate_store');
            $data['domain_name'] = $_SERVER['SERVER_NAME'];

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text'      => $this->language->get('text_home'),
                'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
                'separator' => false
            );   		

            $data['breadcrumbs'][] = array(
                'text'      => $this->language->get('heading_title2'),
                'href'      => $this->url->link('extension/module/bundle_setting', 'user_token=' . $this->session->data['user_token'], true),
                'separator' => false
            );

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('extension/module/validation', $data));
    }
    
	public function getData() {            
		$this->load->language('extension/module/bundle_setting');

		$this->document->setTitle($this->language->get('heading_title2'));
		 
		$this->load->model('extension/module/bundle_setting');
		
        $this->document->addScript('view/javascript/bootstrap/js/bootstrap-checkbox.min.js');

        $url = "";
  
        $data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
        
	    $data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'].'&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/bundle_setting', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
		
		$data['user_token'] = $this->session->data['user_token'];			
		
        if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
        } else if (isset($this->request->get['install']) && $this->request->get['install'] == "true") {
			$data['success'] = $this->language->get('success_install');	
		} else if (isset($this->request->get['uninstall']) && $this->request->get['uninstall'] == "true") {
			$data['success'] = $this->language->get('success_uninstall');	
		} else {
			$data['success'] = '';
		}	
		
        $this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();      
        
	    $this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			
            $this->model_extension_module_bundle_setting->editSetting($this->request->post);

			$this->model_setting_setting->editSetting('module_bundle', $this->request->post);
            
			$this->session->data['success'] = $this->language->get('text_success');
            
			$this->response->redirect($this->url->link('extension/module/bundle_setting', 'user_token=' . $this->session->data['user_token'], true));	   
		}
	
		$data['heading_title'] = $this->language->get('heading_title2');
		$data['action'] = $this->url->link('extension/module/bundle_setting', 'user_token=' . $this->session->data['user_token'], true);
                
        $data['user_token'] = $this->session->data['user_token'];
            
        if($this->config->get('module_bundle_apitype') == "hpwdapi") {
            $eligible                = $this->VD(dirname(getcwd()).'/system/library/cache/hpsb_log');   
                       
            $data['eligible_status'] = $eligible['status'] ? $this->language->get('text_active') : $this->language->get('text_inactive');
            $data['eligible_date']   = date('d F Y',strtotime($eligible['date']));
            $this->model_extension_module_bundle_setting->apiusage( $eligible['status'] ? 1 : 0); 
            
            if(!$eligible['status']) {
                $this->error['warning'] = $this->language->get('text_limit_usage_warning');
            }
        } 
        
   // couriers
        $data['couriers']   = array(
        "first" => 
            array(
            'first_reg' => 'Regular Service', 
            'first_ons' => 'ONS Service', 
            'first_sds' => 'SDS Service'),          
        "idl" => 
            array(
            'idl_icon' => 'IDL Ekonomis (iCON)', 
            'idl_ireg' => 'IDL Regular (iREG)', 
            'idl_isds' => 'IDL Same Day Service (iSDS)', 
            'idl_ions' => 'IDL Overnight Service (iONS)', 
            'idl_iscf' => 'IDL Special Fleet (iSCF)'),        
        "jet" => 
            array(
            'jet_reg' => 'JET Regular', 
            'jet_pri'       => 'JET Priority', 
            'jet_crg'       => 'JET Cargo'),
        "jne" => array(
            'jne_oke'   => 'JNE OKE', 
            'jne_reg'   => 'JNE Regular', 
            'jne_yes'   => 'JNE YES'),        
        "ncs" => array(
            'ncs_nrs'    => 'NRS Reguler Service', 
            'ncs_ons'    => 'ONS Overnight Service',     
            'ncs_sds'    => 'SDS Same Day Service'),     
        "ninja" => array(
            'ninja_standard'   => 'Ninja Standard', 
            'ninja_nextday'    => 'Ninja Next Day'),
        "jnt"   => array('jnt_ez' => 'J&T EZ (Regular Service)'),
        "lion"  => array(
            'lion_regpack' => 'Regular Service',
            'lion_onepack' => 'One Day Service'),
        "pahala" => array(
                'pahala_express'    => 'Pahala Express (Regular Service)', 
                'pahala_ons'    => 'Pahala ONS (Over Night Service)', 
                'pahala_sds'    => 'Pahala SDS (Same Day Service)'),
        "pos" => array(
                 'pos_biasa'     => 'POS Biasa', 
                'pos_paket_kilat' => 'POS Paket Kilat', 
                'pos_express'   => 'POS Express'),
        "rpx" => array(
            "rpx_sdp" => "SameDay Package",
            "rpx_mdp" => "MidDay Package",
            "rpx_ndp" => "Next Day Package",
            "rpx_rgp" => "Regular Package"),            
        "sicepat" => array(
                'sicepat_reg'     => 'Sicepat Reguler', 
                'sicepat_best'     => 'Sicepat Best'),
        "tiki" => array(
                'tiki_eco'      => 'TIKI Economy', 
                'tiki_reg'      => 'TIKI Regular', 
                'tiki_sds'      => 'TIKI Same Day Service', 
                'tiki_hds'      => 'TIKI Holiday Delivery Service', 
                'tiki_ons'      => 'TIKI One Night Service'),
        "wahana" => array(
                'wahana_des'    => 'Wahana DES (Regular Service)'),
        "star" => array(
                'star_reguler' => 'Star Reguler', 
                'star_express'  => 'Star Express', 
                'star_motor'    => 'Star MOTOR',
                'star_motorcc' => 'Star MOTOR 150 - 250 CC'
            )
        );
        
		// Shippings
        $data['shippings'] = $this->shippingservice();
        
        $this->load->model('localisation/geo_zone');
		
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		$this->load->model('localisation/tax_class');
		
		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
        
        if (!$this->user->hasPermission('modify', 'extension/module/bundle_setting')) {
            $data['uninstall'] = $this->url->link('extension/module/bundle_setting/uninstallDatabase', 'user_token=' . $this->session->data['user_token'], true);
        } else {
            $data['uninstall'] = $this->url->link('extension/module/bundle_setting','user_token=' . $this->session->data['user_token'], true);
        }
        
        foreach($data['couriers'] as $code => $val) {
            
            if (isset($this->request->post['module_bundle_total_shipping_'.$code])) {
                $data['module_bundle_total_shipping_'.$code] = $this->request->post['module_bundle_total_shipping_'.$code];
            } else {
                $data['module_bundle_total_shipping_'.$code] = $this->config->get('module_bundle_total_shipping_'.$code);
            }

            if (isset($this->request->post['module_bundle_geo_zone_id_'.$code])) {
                $data['module_bundle_geo_zone_id_'.$code] = $this->request->post['module_bundle_geo_zone_id_'.$code];
            } else {
                $data['module_bundle_geo_zone_id_'.$code] = $this->config->get('module_bundle_geo_zone_id_'.$code);
            }	

            if (isset($this->request->post['module_bundle_tax_class_id_'.$code])) {
                $data['module_bundle_tax_class_id_'.$code] = $this->request->post['module_bundle_tax_class_id_'.$code];
            } else {
                $data['module_bundle_tax_class_id_'.$code] = $this->config->get('module_bundle_tax_class_id_'.$code);
            }
        }
        
		if (isset($this->request->post['module_bundle_preferred_shipping'])) {
			$data['module_bundle_preferred_shipping'] = $this->request->post['module_bundle_preferred_shipping'];
		} else if($this->config->get('module_bundle_preferred_shipping')) {
			$data['module_bundle_preferred_shipping'] = $this->config->get('module_bundle_preferred_shipping');      
        } else {
			$data['module_bundle_preferred_shipping'] = array();
		}
        
        $sort_orders = array();
        
        // Get shippings sort order
            foreach($data['module_bundle_preferred_shipping'] as $shipping) {
            $sort_orders[$shipping] = array(
                'name' => $data['shippings'][$shipping],
                'value' => $this->config->get($shipping.'_sort_order'));
            }
        
        $sort_order = array();

			foreach ($sort_orders as $key => $value) {
				$sort_order[$key] = $value['value'];
			}

        array_multisort($sort_order, SORT_ASC, $sort_orders);
        
        
        $data['sort_orders'] = $sort_orders;

        if (isset($this->request->post['module_bundle_handling_fee'])) {
			$data['module_bundle_handling_fee'] = $this->request->post['module_bundle_handling_fee'];
		} else {
			$data['module_bundle_handling_fee'] = $this->config->get('module_bundle_handling_fee');
		}
        
        if (isset($this->request->post['module_bundle_username'])) {
			$data['module_bundle_username'] = $this->request->post['module_bundle_username'];
		} else {
			$data['module_bundle_username'] = $this->config->get('module_bundle_username');
		} 
        
        if (isset($this->request->post['module_bundle_order_id'])) {
			$data['module_bundle_order_id'] = $this->request->post['module_bundle_order_id'];
		} else {
			$data['module_bundle_order_id'] = $this->config->get('module_bundle_order_id');
		}  
        
        if (isset($this->request->post['module_bundle_rajaongkirapi'])) {
			$data['module_bundle_rajaongkirapi'] = $this->request->post['module_bundle_rajaongkirapi'];
		} else {
			$data['module_bundle_rajaongkirapi'] = $this->config->get('module_bundle_rajaongkirapi');
		}    
        
        if (isset($this->request->post['module_bundle_apitype'])) {
			$data['module_bundle_apitype'] = $this->request->post['module_bundle_apitype'];
		} else {
			$data['module_bundle_apitype'] = $this->config->get('module_bundle_apitype');
		}
        
		if (isset($this->request->post['module_bundle_handling_fee_mode'])) {
			$data['module_bundle_handling_fee_mode'] = $this->request->post['module_bundle_handling_fee_mode'];
		} else {
			$data['module_bundle_handling_fee_mode'] = $this->config->get('module_bundle_handling_fee_mode');
		}
        
        if (isset($this->request->post['module_bundle_province_id'])) {
			$data['module_bundle_province_id'] = $this->request->post['module_bundle_province_id'];
		} else {
			$data['module_bundle_province_id'] = $this->config->get('module_bundle_province_id');
		}    
        
        if (isset($this->request->post['module_bundle_city_id'])) {
			$data['module_bundle_city_id'] = $this->request->post['module_bundle_city_id'];
		} else {
			$data['module_bundle_city_id'] = $this->config->get('module_bundle_city_id');
		}        
        
        if (isset($this->request->post['module_bundle_sub_district_id'])) {
			$data['module_bundle_sub_district_id'] = $this->request->post['module_bundle_sub_district_id'];
		} else {
			$data['module_bundle_sub_district_id'] = $this->config->get('module_bundle_sub_district_id');
		}
        
         if (isset($this->request->post['module_bundle_wooden_package'])) {
			$data['module_bundle_wooden_package'] = $this->request->post['module_bundle_wooden_package'];
		} else {
			$data['module_bundle_wooden_package'] = $this->config->get('module_bundle_wooden_package');
		}
        
        if (isset($this->request->post['module_bundle_assurance'])) {
			$data['module_bundle_assurance'] = $this->request->post['module_bundle_assurance'];
		} else if($this->config->get('module_bundle_assurance')) {
			$data['module_bundle_assurance'] = $this->config->get('module_bundle_assurance');
		} else {
            $data['module_bundle_assurance'] = 0;
        }
        
        if (isset($this->request->post['module_bundle_total_assurance'])) {
			$data['module_bundle_total_assurance'] = $this->request->post['module_bundle_total_assurance'];
		} else {
			$data['module_bundle_total_assurance'] = $this->config->get('module_bundle_total_assurance');
		}
        
        if (isset($this->request->post['module_bundle_assurance_percentage'])) {
			$data['module_bundle_assurance_percentage'] = $this->request->post['module_bundle_assurance_percentage'];
		} else {
			$data['module_bundle_assurance_percentage'] = $this->config->get('module_bundle_assurance_percentage');
		}
    
        if (isset($this->request->post['module_bundle_shipping_from'])) {
			$data['module_bundle_shipping_from'] = $this->request->post['module_bundle_shipping_from'];
		} else {
			$data['module_bundle_shipping_from'] = $this->config->get('module_bundle_shipping_from');
		} 
        
        if (isset($this->request->post['module_bundle_shipping_available'])) {
			$data['module_bundle_shipping_available'] = $this->request->post['module_bundle_shipping_available'];
		} else {
			$data['module_bundle_shipping_available'] = $this->config->get('module_bundle_shipping_available');
		} 
        
        if (isset($this->request->post['module_bundle_display_image_orderinfo'])) {
			$data['module_bundle_display_image_orderinfo'] = $this->request->post['module_bundle_display_image_orderinfo'];
		} else {
			$data['module_bundle_display_image_orderinfo'] = $this->config->get('module_bundle_display_image_orderinfo');
		}         
        
        if (isset($this->request->post['module_bundle_display_image_ordertotal'])) {
			$data['module_bundle_display_image_ordertotal'] = $this->request->post['module_bundle_display_image_ordertotal'];
		} else {
			$data['module_bundle_display_image_ordertotal'] = $this->config->get('module_bundle_display_image_ordertotal');
		} 
            
 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
            $data['error_warning'] = '';
        }
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
			}
		else if (isset($this->request->get['install']) && $this->request->get['install'] == "true") {
			$data['success'] = $this->language->get('success_install');	
		} 
		else if (isset($this->request->get['uninstall']) && $this->request->get['uninstall'] == "true") {
			$data['success'] = $this->language->get('success_uninstall');	
		}		
		else {
			$data['success'] = '';
		}
		
	$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
				
  $this->response->setOutput($this->load->view('extension/module/bundle_setting', $data));
	}


	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/bundle_setting')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
			
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
    
public function uninstall() {
    if($this->validateTable()) {
             $this->language->load('extension/module/bundle_setting');
                
                $this->document->setTitle($this->language->get('error_database'));
                
                $data['install_database'] = $this->url->link('extension/module/bundle_setting/uninstallDatabase', 'user_token=' . $this->session->data['user_token'], true);
                
                $data['text_install_message'] = $this->language->get('text_uninstall_message');
                
                $data['text_upgrade'] = $this->language->get('text_downgrade');
                
                $data['error_database'] = $this->language->get('text_found_database');
                
                $data['breadcrumbs'] = array();

    	   		$data['breadcrumbs'][] = array(
    	       		'text'      => $this->language->get('text_home'),
    				'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
    	      		'separator' => false
    	   		);
        
        	   $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_shipping'),
                'href' => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'].'&type=shipping', true)
                );
                
                $data['header'] = $this->load->controller('common/header');
      		    $data['column_left'] = $this->load->controller('common/column_left');
                $data['footer'] = $this->load->controller('common/footer');
        
                $this->response->setOutput($this->load->view('extension/module/hpwd_notification', $data));
    }
}
    
public function uninstallDatabase() {
 		$this->load->model('extension/module/bundle_setting');
 		if($this->model_extension_module_bundle_setting->uninstallTable())
		 {
			$this->response->redirect($this->url->link('extension/module/bundle_setting', 'user_token=' . $this->session->data['user_token']."&uninstall=true", true));	
		 		}
		 else {
			$this->response->redirect($this->url->link('extension/module/bundle_setting', 'user_token=' . $this->session->data['user_token']."&uninstall=false", true));			 
		 }
}
        
public function validateTable() {               
    		$queries[] = $this->db->query("SHOW TABLES LIKE '".DB_PREFIX."country_hpwd'");
    		$queries[] = $this->db->query("SHOW TABLES LIKE '".DB_PREFIX."zone_hpwd'");
    		$queries[] = $this->db->query("SHOW TABLES LIKE 'hp_subdistrict'");
            
            $error = 0;
    
            foreach($queries as $query) {
                $error += ($query->num_rows) ? 0 : 1;
            }
    
            return $error ? false : true;
        }
        
         public function installDatabase() {
            		$this->load->model('extension/module/bundle_setting');

	       $error=0;
		 	
            if($this->model_extension_module_bundle_setting->installTable()) {
			     $error++;
		 		}
             
		 	if($error < 1)	
  		        $this->response->redirect($this->url->link('extension/module/bundle_setting', 'user_token=' . $this->session->data['user_token']."&install=true", true));	
   		   else 
		 	    $this->response->redirect($this->url->link('extension/module/bundle_setting', 'user_token=' . $this->session->data['user_token']."&install=false", true));	
             
                $this->language->load('extension/module/bundle_setting');
            
                $this->load->model('extension/module/bundle_setting');

                $this->model_extension_module_bundle_setting->installTable();

                $this->session->data['success'] = $this->language->get('text_success_installed');

                $route = $this->request->get['url'];

                $this->response->redirect($this->url->link($route, 'user_token=' . $this->session->data['user_token'], true));
         }
    
    public function getTheQ($username,$order_id) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://api.hpwebdesign.id/rest/".$username."/".$order_id,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_SSL_VERIFYPEER => false,               
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_POSTFIELDS => "module_name=bundle",
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
              echo "cURL Error #:" . $err;
            } else {
                $result = json_decode($response);
            }    
                if($result)
                return $result->results->shared_api_key;
                else 
                return '';
    }
    
    public function validateOrder() {
        $this->load->language('extension/module/bundle_setting');

        $json = array();

        if (!$this->user->hasPermission('modify', 'extension/module/bundle_setting')) {
            $json['error']    = $this->language->get('error_permission');
        } else {    
            if(isset($this->request->get['username'])) {
                $username = $this->request->get['username'];
            } else {
                $username = '';
            } 

            if(isset($this->request->get['order_id'])) {
                $order_id = $this->request->get['order_id'];
            } else {
                $order_id = 0;
            }

            $q = $this->getTheQ($username,$order_id);

            if($q != '' && strlen($q) == 32) {

                $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' AND `code` = 'hpwd'");
                $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' AND `key` = 'module_bundle_username'");
                $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' AND `key` = 'module_bundle_order_id'");

                $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'hpwd', `key` = 'hpwd_theq', `value` = '".$q."'");
                $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'bundle', `key` = 'module_bundle_username', `value` = '".$username."'");
                $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'bundle', `key` = 'module_bundle_order_id', `value` = '".$order_id."'");

                $json['success'] = $this->language->get('success_order_validation');

            $this->load->model('setting/setting');                   
            $this->model_setting_setting->editSettingValue('module_bundle','bundle_username',$username);
            $this->model_setting_setting->editSettingValue('module_bundle','bundle_order_id',$order_id);

            } else {
                $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' AND `code` = 'hpwd'");
                $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' AND `key` = 'module_bundle_username'");
                $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' AND `key` = 'module_bundle_order_id'");
                $json['error'] = $this->language->get('error_order_validation');            
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
     public function applyApi() {
        $this->load->language('extension/module/bundle_setting');
        
        $json = array();
        
        if(isset($this->request->get['apikey'])) {
            $apikey = $this->request->get['apikey'];
        } else {
            $apikey = '';
        } 
         
        // check if API KEY is valid
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://pro.rajaongkir.com/api/subdistrict?city=39&id=2103",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "key: ".$apikey
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          $response = $err;
        } else {
          $response = $response;
        }

        $response = json_decode($response);

        if($response->rajaongkir->status->code == 200) {
            $json['success'] = $this->language->get('success_valid_api');
            
            $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'module_bundle_rajaongkirapi' AND `value` = '".$apikey."'");
            
            $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'hpwd', `key` = 'module_bundle_rajaongkirapi', `value` = '".$apikey."'");
                        
        } else {
            $json['error']   = $response->rajaongkir->status->description;            
        } 
        
         // delete country and zone cache
         $this->cache->delete('admin.country');
         $this->cache->delete('catalog.country');
         $this->cache->delete('zone');
         
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
     public function clearApi() {
        $this->load->language('extension/module/bundle_setting');
        
        $json = array();
        
        if(isset($this->request->get['apikey'])) {
            $apikey = $this->request->get['apikey'];
        } else {
            $apikey = '';
        } 

        $json['success'] = $this->language->get('success_clear_api');

        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'bundle_rajaongkirapi' AND `value` = '".$apikey."'");
               
         // delete country and zone cache
         $this->cache->delete('admin.country');
         $this->cache->delete('catalog.country');
         $this->cache->delete('zone');
         
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
      
    public function sortorder() {
      $this->load->language('extension/module/bundle_setting');
        $json = array();        
        
		if (!$this->user->hasPermission('modify', 'extension/module/bundle_setting')) {
            $json['error']    = $this->language->get('error_permission');
        } else {
            $this->load->language('extension/module/bundle_setting');

            $this->load->model('extension/module/bundle_setting');
            $this->load->model('setting/setting');

            $json = array();

            $json['success'] = $this->language->get('success_save_sortorder');

            foreach($this->request->post as $item => $value) {
                    if(substr($item,0,8) ==  'shipping') {
                    $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = '".$item."'");
                    $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET `code`='".substr($item,0,strlen($item)-11)."', `key` = '".$item."', `value` = '".$value."'");
                    }    
            }
        }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    } 
    
    public function shipping() {
        $this->load->language('extension/module/bundle_setting');
        $json = array();        
        
		if (!$this->user->hasPermission('modify', 'extension/module/bundle_setting')) {
            $json['error']    = $this->language->get('error_permission');
        } else {
            if($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->load->model('extension/module/bundle_setting');
            $this->load->model('setting/setting');
            
            $json['success']    = $this->language->get('success_save_shipping');

            foreach ($this->shippingservice() as $shipping => $name) {

            $this->db->query("DELETE FROM " . DB_PREFIX . "extension WHERE `type` = 'shipping' AND `code` = '" . $this->db->escape($shipping) . "'");

            $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `code` = 'shipping_" . $this->db->escape($shipping) . "'");
            }
                
            $shippings = array();
            
            foreach($this->request->post['module_bundle_preferred_shipping'] as $item) {
                    $shippings[] = $item;
                   //  $expedition = explode("_",$item);
                
                    $this->db->query("INSERT INTO " . DB_PREFIX . "extension SET `type` = 'shipping', `code` = '" . $this->db->escape($item) . "'");

                    $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `code` = 'shipping_" . $this->db->escape($item) . "'");
                
                    $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET `code` = 'shipping_" . $this->db->escape($item) . "', `key` = 'shipping_" . $this->db->escape($item) . "_status', `value` = '1'");
                
                
//                    $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `key` = 'module_bundle_total_shipping_" . $this->db->escape($shipping[0]) . "'");
//                    $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET `code` = 'module_bundle', `key` = 'module_bundle_total_shipping_" . $this->db->escape($shipping[0]) . "', `value` = '".(int)$this->request->post['module_bundle_total_shipping_'.$shipping[0]]."'");
//                
//                    $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `key` = 'module_bundle_tax_class_id_" . $this->db->escape($shipping[0]) . "'");
//                    $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET `code` = 'module_bundle', `key` = 'module_bundle_tax_class_id_" . $this->db->escape($shipping[0]) . "', `value` = '".(int)$this->request->post['module_bundle_tax_class_id_'.$shipping[0]]."'");
//                
//                
//                    $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `key` = 'module_bundle_geo_zone_id_" . $this->db->escape($shipping[0]) . "'");
//                    $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET `code` = 'module_bundle', `key` = 'module_bundle_geo_zone_id_" . $this->db->escape($shipping[0]) . "', `value` = '".(int)$this->request->post['module_bundle_geo_zone_id_'.$shipping[0]]."'");
                
                
                } 
                    
                $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `key` = 'module_bundle_preferred_shipping'");
                
                $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET `code` = 'module_bundle', `key` = 'module_bundle_preferred_shipping', `value` = '".json_encode($shippings)."', serialized = '1'");
                
            }    
        }
     
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    protected function rightman() {
        if(file_exists(dirname(getcwd()).'/system/library/cache/hpsb_log')) {
            $this->v_d = $this->VS(dirname(getcwd()).'/system/library/cache/hpsb_log');
            if($this->v_d != $_SERVER['SERVER_NAME']) {          
                if($this->internetAccess()) {
                 $data = $this->get_remote_data('https://api.hpwebdesign.id/hpsb.txt');
                if(strpos($data,$_SERVER['SERVER_NAME']) !== false) {
                $eligible = $this->VD(dirname(getcwd()).'/system/library/cache/hpsb_log');
                $this->hpsb(1,$eligible['date']);
                $this->response->redirect($this->url->link('extension/module/bundle_setting', 'user_token=' . $this->session->data['user_token'], true));                                    
                }
            } else {
                $this->error['warning'] = $this->language->get('error_no_internet_access');      
                }    
            }
        } else {
            if($this->internetAccess()) {
            $data = $this->get_remote_data('https://api.hpwebdesign.id/hpsb.txt');
            if(strpos($data,$_SERVER['SERVER_NAME']) !== false) {
                $this->hpsb(1);
                $this->response->redirect($this->url->link('extension/module/bundle_setting', 'user_token=' . $this->session->data['user_token'], true));                
                }   
          } else {
                $this->error['warning'] = $this->language->get('error_no_internet_access');                      
            }
        } 
    }
    
protected function hpsb($ref = 0, $date = NULL) {
    $pf = dirname(getcwd()).'/system/library/cache/hpsb_log';
        if(!file_exists($pf)) {
            fopen($pf,'w'); 
        }
        $fh = fopen($pf,'r');

    if(!fgets($fh) || $ref = 1) {
        $fh = fopen($pf, "wb");
        if(!$fh) {
            chmod($pf,644);
            }
        fwrite($fh, "// HPWD -> Dilarang mengedit isi file ini untuk tujuan cracking validasi atau tindakan terlarang lainnya".PHP_EOL);
        $date = $date ? $date : date("d-m-Y",strtotime(date("d-m-Y").' + 1 year'));
        fwrite($fh, $date.PHP_EOL);
        fwrite($fh, $_SERVER['SERVER_NAME'].PHP_EOL);
    }

    fclose($fh);  
    } 
        
    private function VD($path) {
        $data = array();
        $source = @fopen($path,'r');    
        $i  = 0;
        if($source) {
        while ($line = fgets($source)) {
            $line = trim($line);
            if($i == 1) {
                $diff = strtotime(date("d-m-Y")) - strtotime($line);
                    if(floor($diff / (24 * 60 * 60 ) > 0)) {
                      $data['status'] = 0; 
                    } else {
                      $data['status'] = 1; 
                    }
                $data['date'] = $line;
                }
            $i++;
            }
        return $data;
        }
    }

    private function VS($path) {
        $source = @fopen($path,'r');    
        $i  = 0;
        if($source) {
        while ($line = fgets($source)) {
            $line = trim($line);
            if($i == 2) {
               return $line;
            }
            $i++;
                }  
            }
        }
    
public function flushdata() {
     $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `code` LIKE '%module_bundle%'");
  }
    
 public function curlcheck() {
     return in_array ('curl', get_loaded_extensions()) ? true : false;
    }     
    
function get_remote_data($url, $post_paramtrs=false) {
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    if($post_paramtrs)
    {
        curl_setopt($c, CURLOPT_POST,TRUE);
        curl_setopt($c, CURLOPT_POSTFIELDS, "var1=bla&".$post_paramtrs );
    }
    curl_setopt($c, CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:33.0) Gecko/20100101 Firefox/33.0");
    curl_setopt($c, CURLOPT_COOKIE, 'CookieName1=Value;');
    curl_setopt($c, CURLOPT_MAXREDIRS, 10);
    $follow_allowed= ( ini_get('open_basedir') || ini_get('safe_mode')) ? false:true;
    if ($follow_allowed)
    {
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
    }
    curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($c, CURLOPT_REFERER, $url);
    curl_setopt($c, CURLOPT_TIMEOUT, 60);
    curl_setopt($c, CURLOPT_AUTOREFERER, true);
    curl_setopt($c, CURLOPT_ENCODING, 'gzip,deflate');
    $data=curl_exec($c);
    $status=curl_getinfo($c);
    curl_close($c);
    if($status['http_code']==200)
    {
        return $data;
    }
    elseif($status['http_code']==301 || $status['http_code']==302)
    {
        if (!$follow_allowed)
        {
            if (!empty($status['redirect_url']))
            {
                $redirURL=$status['redirect_url'];
            }
            else
            {
                preg_match('/href\=\"(.*?)\"/si',$data,$m);
                if (!empty($m[1]))
                {
                    $redirURL=$m[1];
                }
            }
            if(!empty($redirURL))
            {
                return  call_user_func( __FUNCTION__, $redirURL, $post_paramtrs);
            }
        }
    }
    return "ERRORCODE22 with $url!!<br/>Last status codes<b/>:".json_encode($status)."<br/><br/>Last data got<br/>:$data";
    }
    
private function internetAccess() {
//  $connected = @fopen("http://google.com","r");
  //return $connected ? true : false;
    return true;
    }    
}