<?php
class ControllerExtensionModuleCatalogProConfiguration extends Controller {
	private $error = array();

	public function index() {
        $this->load->language('extension/module/catalog_pro_default');
		$this->load->language('extension/module/catalog_pro_configuration');

		$this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/catalog_pro/default');

		$this->getConfigForm();
	}

	protected function getConfigForm() {
	    $url = "";

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('menu_parent'),
            'href' => $this->url->link('extension/module/catalog_pro_configuration', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('submenu_config'),
            'href' => $this->url->link('extension/module/catalog_pro_configuration', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

		$data['user_token'] = $this->session->data['user_token'];

        $default = $this->model_extension_catalog_pro_default->getConfig();

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if (file_exists(DIR_APPLICATION.'controller/extension/module/catalog_pro.json'))
                $config = json_decode(file_get_contents(DIR_APPLICATION.'controller/extension/module/catalog_pro.json'), true);

            if (!isset($config) || $config === null)
                $config = $default;

            $columns = [];
            foreach ($this->request->post['columns'] as $column => $visible) {
                $columns[$column] = isset($config['columns'][$column])? $config['columns'][$column]: $default['columns'][$column];
                $columns[$column]['visible'] = $visible['visible'];
            }

            $config['columns'] = $columns;

            try {
                $fp = @fopen(DIR_APPLICATION . 'controller/extension/module/catalog_pro.json', "w");
                if ($fp === false)
                    throw new Exception($this->language->get('text_not_permission'));
                fwrite($fp, json_encode($config));
                fclose($fp);

                $data['success'] = $this->language->get('text_success');
            }
            catch (Exception $exception) {
                $data['error_warning'] = $exception->getMessage();
            }
        }

        if (file_exists(DIR_APPLICATION.'controller/extension/module/catalog_pro.json')) {
            $config = json_decode(file_get_contents(DIR_APPLICATION . 'controller/extension/module/catalog_pro.json'), true);
            foreach ($default['columns'] as $field => $column)
                if (!isset($config['columns'][$field]))
                    $config['columns'][$field] = $column;
        }
        else
            $config = $default;

		$data['columns'] = array();

		foreach ($config['columns'] as $column => $value)
            $data['columns'][] =
                array(
                    "caption" => $this->language->get('column_'.$column),
                    "name" => "columns[{$column}][visible]",
                    "value" => $value['visible'],
                );

        $data['text_limit'] = $this->language->get('limit');
        $data['limit'] = $config['limit'];
        $data['limit_rows'] = array(
            10, 25, 50, 100
        );

        $data['action'] = $this->url->link('extension/module/catalog_pro_configuration', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('extension/module/catalog_pro_configuration', 'user_token=' . $this->session->data['user_token'], true);

        $data['table_name'] = $this->language->get('table_name');
        $data['table_visible'] = $this->language->get('table_visible');
        $data['table_value'] = $this->language->get('table_value');


        $data['visible_yes'] = $this->language->get('visible_yes');
        $data['visible_no'] = $this->language->get('visible_no');


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/catalog_pro/configuration', $data));
	}
}
