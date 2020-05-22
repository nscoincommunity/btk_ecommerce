<?php
class ControllerExtensionModuleCodeManager extends Controller {

	private $data = array();
	private $error = array();
	private $version;
	private $module_path;
	private $extensions_link;
	private $language_variables;
	private $moduleModel;
	private $moduleName;
	private $call_model;

	public function __construct($registry){
		parent::__construct($registry);
		$this->load->config('isenselabs/codemanager');
		$this->moduleName = $this->config->get('codemanager_name');
		$this->call_model = $this->config->get('codemanager_model');
		$this->module_path = $this->config->get('codemanager_path');
		$this->version = $this->config->get('codemanager_version');
		$this->extensions_link = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'].'&type=module', 'SSL');


		$this->load->model($this->module_path);
		$this->moduleModel = $this->{$this->call_model};
    	$this->language_variables = $this->load->language($this->module_path);

    	//Loading framework models
	 	$this->load->model('setting/store');
		$this->load->model('setting/setting');
        $this->load->model('localisation/language');

		$this->document->addScript('view/javascript/summernote/summernote.js');
		$this->document->addStyle('view/javascript/summernote/summernote.css');

		$this->data['module_path']     = $this->module_path;
		$this->data['moduleName']      = $this->moduleName;
		$this->data['moduleNameSmall'] = $this->moduleName;
	}

    public function index() {
		foreach ($this->language_variables as $code => $languageVariable) {
		    $this->data[$code] = $languageVariable;
		}

		if ($this->user->hasPermission('access', $this->module_path)) {
			$user_id = $this->user->getId();
			$user_name = $this->user->getUserName();
			$cookie_expiration = time()+3600;
			setcookie('user_id', $user_id, $cookie_expiration);
			setcookie('user', $user_name, $cookie_expiration);
			setcookie($this->moduleName, true, $cookie_expiration);
			setcookie('OC_VERSION',VERSION);
			$this->data['usable'] = true;
		} else {
			$this->data['usable'] = false;
		}

		if ($this->user->hasPermission('modify', $this->module_path)) {
			$this->data['buttons'] = true;
		} else {
			$this->data['buttons'] = false;
		}

	    $catalogURL = $this->getCatalogURL();
	    $this->document->addStyle('view/stylesheet/'.$this->moduleName.'/'.$this->moduleName.'.css');
	    $this->document->setTitle($this->language->get('heading_title') . ' ' . $this->version);

	    if(!isset($this->request->get['store_id'])) {
	        $this->request->get['store_id'] = 0;
	    }

		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "user_group WHERE name = '".$this->moduleName."'");
		if (!$query->rows) {
			$permissions = array();
			$permissions["access"][] = 'extension/module';
			$permissions["access"][] = $this->module_path;
			$this->db->query("INSERT INTO " . DB_PREFIX . "user_group SET name = '" . $this->db->escape($this->moduleName) . "', permission = '" . (isset($permissions) ? serialize($permissions) : '') . "'");
		}
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "user_group WHERE name = '".$this->moduleName."'");
		$this->data['UserGroupID'] = $query->row['user_group_id'];

	    $store = $this->getCurrentStore($this->request->get['store_id']);

	    if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
	        if (!$this->user->hasPermission('modify', $this->module_path)) {
	            $this->redirect($this->extensions_link);
	        }

	        if (!empty($_POST['OaXRyb1BhY2sgLSBDb21'])) {
	            $this->request->post[$this->moduleName]['LicensedOn'] = $_POST['OaXRyb1BhY2sgLSBDb21'];
	        }

	        if (!empty($_POST['cHRpbWl6YXRpb24ef4fe'])) {
	            $this->request->post[$this->moduleName]['License'] = json_decode(base64_decode($_POST['cHRpbWl6YXRpb24ef4fe']), true);
	        }

	        $this->model_setting_setting->editSetting($this->moduleName, $this->request->post, $this->request->post['store_id']);
	        $this->session->data['success'] = $this->language->get('text_success');
	        $this->response->redirect($this->url->link($this->module_path, 'store_id='.$this->request->post['store_id'] . '&user_token=' . $this->session->data['user_token'], 'SSL'));
	    }

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

	    $this->data['breadcrumbs']   = array();
	    $this->data['breadcrumbs'][] = array(
	        'text' => $this->language->get('text_home'),
	        'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL'),
	    );
	    $this->data['breadcrumbs'][] = array(
	        'text' => $this->language->get('text_module'),
	        'href' => $this->extensions_link,
	    );
	    $this->data['breadcrumbs'][] = array(
	        'text' => $this->language->get('heading_title') . ' ' . $this->version,
	        'href' => $this->url->link($this->module_path, 'user_token=' . $this->session->data['user_token'], 'SSL'),
	    );

		$this->data['heading_title']			= $this->language->get('heading_title') . ' ' . $this->version;

	    $this->data['stores']					= array_merge(array(0 => array('store_id' => '0', 'name' => $this->config->get('config_name') . ' (' . $this->data['text_default'].')', 'url' => HTTP_SERVER, 'ssl' => HTTPS_SERVER)), $this->model_setting_store->getStores());
	    $this->data['languages']              = $this->model_localisation_language->getLanguages();
	    $this->data['store']                  = $store;
	    $this->data['user_token']                  = $this->session->data['user_token'];
	    $this->data['action']                 = $this->url->link($this->module_path, 'user_token=' . $this->session->data['user_token'], 'SSL');
	    $this->data['cancel']                 = $this->extensions_link;
	    $this->data['moduleSettings']			= $this->model_setting_setting->getSetting($this->moduleName, $store['store_id']);
	    $this->data['catalog_url']			= $catalogURL;

		$this->data['moduleData'] = (isset($this->data['moduleSettings'][$this->moduleName])) ? $this->data['moduleSettings'][$this->moduleName] : '';

		$this->data['header']					= $this->load->controller('common/header');
		$this->data['column_left']			= $this->load->controller('common/column_left');
		$this->data['footer']					= $this->load->controller('common/footer');
			//load the variables used in the tab_editor twig
		$current_dir_name = basename(DIR_APPLICATION);
		$this->data['current_dir_name'] = $current_dir_name;
		$this->data['dir_app'] = dirname(DIR_APPLICATION);
		$this->data['timezone'] = date_default_timezone_get();
		$this->data['path'] = (dirname(DIR_APPLICATION)).'/'.$current_dir_name.'/view/javascript/'.$this->moduleName;
		$this->data['workspace'] = is_writable($this->data['path'] . "/workspace");
		$this->data['data_tab_editor'] = is_writable($this->data['path']  . "/data");
		$this->data['plugins'] = is_writable($this->data['path']  . "/plugins");
		$this->data['themes'] = is_writable($this->data['path'] . "/themes");
		$this->data['workspace'] = is_writable( $this->data['path'] . "/workspace");
		$this->data['project_path'] = is_writable(dirname(DIR_APPLICATION));
		$this->data['path_writable'] = is_writable((dirname(DIR_APPLICATION)).'/'.$current_dir_name.'/view/javascript/'.$this->moduleName);

		$this->data['conf'] = $this->data['path'] . '/config.php';

		$this->data['config'] = is_writable(file_exists($this->data['conf']) ? $this->data['conf'] : $this->data['path']);

		// Check if the module is installed
		$this->data['users'] = file_exists($this->data['path'] . "/data/users.php");
		$this->data['projects'] = file_exists($this->data['path'] . "/data/projects.php");
		$this->data['active'] = file_exists($this->data['path'] . "/data/active.php");

			//the twig files variables
		$twigFiles = array(
			'tab_editor'
			);
		 foreach ($twigFiles as $twigFile) {
            $this->data[$twigFile] = $this->load->view('extension/module/codemanager/'.$twigFile, $this->data);
        }

		$this->response->setOutput($this->load->view($this->module_path . '/' . $this->moduleName, $this->data));
    }
		// Events
	public function injectAdminMenuItemCM($eventRoute, &$data) {
			 if ($this->user->hasPermission('access', $this->module_path)) {
					 $this->load->language($this->module_path);

					 foreach ($data["menus"] as &$menu) {
							 if ($menu["id"] == "menu-system") {
									 $menu["children"][] = array(
											 'name'	   => $this->language->get('menu_title'),
											 'href'     => $this->url->link($this->module_path, 'user_token=' . $this->session->data['user_token'], true),
											 'children' => array()
									 );
							 }
					 }
			 }
	 }
    private function getCatalogURL() {
        if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
            $storeURL = HTTPS_CATALOG;
        } else {
            $storeURL = HTTP_CATALOG;
        }
        return $storeURL;
    }

    private function getServerURL() {
        if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
            $storeURL = HTTPS_SERVER;
        } else {
            $storeURL = HTTP_SERVER;
        }
        return $storeURL;
    }

    private function getCurrentStore($store_id) {
        if($store_id && $store_id != 0) {
            $store = $this->model_setting_store->getStore($store_id);
        } else {
            $store['store_id'] = 0;
            $store['name'] = $this->config->get('config_name');
            $store['url'] = $this->getCatalogURL();
        }
        return $store;
    }

    public function install() {
		 $this->load->model("setting/event");
	   $this->moduleModel->install();
		 $this->load->model($this->module_path);
		 $this->model_extension_module_codemanager->setupEventHandlers();
    }

    public function uninstall() {
		$this->model_setting_setting->deleteSetting($this->moduleName,0);
		$stores=$this->model_setting_store->getStores();
		foreach ($stores as $store) {
			$this->model_setting_setting->deleteSetting($this->moduleName, $store['store_id']);
		}

		$files = array('active.php', 'projects.php', 'settings.php', 'users.php', 'version.php');
		$dir_folder = dirname(DIR_APPLICATION).'/admin/view/javascript/codemanager/data/';

		foreach ($files as $file) {
			 if (is_file($dir_folder.$file)) {
				unlink($dir_folder.$file);
			}
		}
		$this->load->model("setting/event");
		$this->load->model($this->module_path);
		$this->model_extension_module_codemanager->removeEventHandlers();
    $this->moduleModel->uninstall();
    }

	public function givecredentials() {
		$this->load->model('user/user');
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "user_group WHERE name = '".$this->moduleName."'");
		$this->data['user_group_id'] = $query->row['user_group_id'];
		$this->data['username'] = $this->generateRandomUsername();
		$this->data['password'] = $this->generateRandomPassword();
		$this->data['email'] = $this->generateRandomEmail();
		$this->data['user_token'] = $this->session->data['user_token'];

		$this->db->query("INSERT INTO `" . DB_PREFIX . "user`
			SET
			username = '" . $this->db->escape($this->data['username']) . "',
			salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "',
			password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($this->data['password'])))) . "',
			firstname = '" . $this->db->escape($this->data['username']) . "',
			lastname = '" . $this->db->escape($this->data['username']) . "',
			email = '" . $this->db->escape($this->data['email']) . "',
			user_group_id = '" . (int)$this->data['user_group_id'] . "',
			status = '1',
			date_added = NOW()");

		$this->response->setOutput($this->load->view($this->module_path . '/user_data', $this->data));
	}

	public function showusers() {
		$this->data['moduleNameSmall'] = $this->moduleName;
		$this->data['results'] = $this->getUsersByGroup();
		$this->data['user_token'] = $this->session->data['user_token'];
		$this->response->setOutput($this->load->view($this->module_path .'/users', $this->data));
	}

	public function removeuser() {
		if (isset($_POST['user_id'])) {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "user` WHERE user_id = '" . (int)$_POST['user_id'] . "'");
		}
	}
	private function getUsersByGroup() {
		$queryFirst = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "user_group WHERE name = '".$this->moduleName."'");
		$user_group_id = $queryFirst->row['user_group_id'];
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "user` WHERE user_group_id = '" . $this->db->escape($user_group_id) . "'");
		return $query->rows;
	}

	private function generateRandomUsername($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}

	private function generateRandomPassword($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz!@#$%';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
	private function getFiles($path) {
		if (is_dir($path)) {
			$files = array();
			$path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
			$dh = opendir($path);


			while (($entry = readdir($dh)) !== false) {
				if (in_array($entry, array('.','..'))) continue;

				$entry = $path.$entry;
				if (is_dir($entry)) {
					$files = array_merge($files, $this->getFiles($entry));
				} else {
					if (!preg_match('/([\.\/]{1}cache($|\/)|error_log|\.git|vqmod\/logs|error\.txt)/', $entry)) {
						$files[] = $entry;
					}
				}

				$filemtimes = array();
				foreach($files as $file) {
					$filemtimes[] = filemtime($file);
				}
				array_multisort($filemtimes, SORT_DESC, SORT_REGULAR, $files);
				$files = array_slice($files, 0, 15);
			}
			closedir($dh);
			return $files;
		}
	}

	public function lastModifiedFiles() {
		$this->data['files'] = $this->getFiles(dirname(DIR_APPLICATION));
		foreach ($this->data['files'] as $key => $value) {
			$this->data['file_names'][] =str_replace(dirname(DIR_APPLICATION), '', $value);
			$this->data['file_created_dates'][] = date('[Y-m-d H:i:s]', filemtime($value));
		}

		$this->response->setOutput($this->load->view($this->module_path.'/pro_last_modified_files', $this->data));
	}
	private function generateRandomEmail($length = 7) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString."@test.example";
	}

}

?>
