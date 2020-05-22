<?php
class ControllerAdditionalfieldAdditionalfield extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('additional_field/additional_field');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('additional_field/additional_field');
		
		$this->model_additional_field_additional_field->alterTables();

		if(VERSION >= '3.0.0.0') { 
			$token = $this->session->data['user_token'];
		} else {
			$token = $this->session->data['token'];
		}

		$url = '';
		if(VERSION >= '3.0.0.0') {
			$url .= 'user_token=' . $token;
		} else{
			$url .= 'token=' . $token;
		}

		$this->getList();
	}

	public function add() {
		$this->load->language('additional_field/additional_field');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('additional_field/additional_field');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_additional_field_additional_field->addAdditionalfield($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('additional_field/additional_field', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('additional_field/additional_field');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('additional_field/additional_field');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_additional_field_additional_field->editAdditionalfield($this->request->get['additional_field_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('additional_field/additional_field', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('additional_field/additional_field');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('additional_field/additional_field');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $additional_field_id) {
				$this->model_additional_field_additional_field->deleteAdditionalfield($additional_field_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('additional_field/additional_field', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'afd.name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', ''. $url, true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('additional_field/additional_field', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('additional_field/additional_field/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('additional_field/additional_field/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['additional_fields'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$additional_field_total = $this->model_additional_field_additional_field->getTotalAdditionalfields();

		$results = $this->model_additional_field_additional_field->getAdditionalfields($filter_data);

		foreach ($results as $result) {
			$data['additional_fields'][] = array(
				'additional_field_id'  => $result['additional_field_id'],
				'name'       => $result['name'],
				'sort_order' => $result['sort_order'],
				'edit'       => $this->url->link('additional_field/additional_field/edit', 'user_token=' . $this->session->data['user_token'] . '&additional_field_id=' . $result['additional_field_id'] . $url, true)
			);
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('additional_field/additional_field', 'user_token=' . $this->session->data['user_token'] . '&sort=afd.name' . $url, true);
		$data['sort_sort_order'] = $this->url->link('additional_field/additional_field', 'user_token=' . $this->session->data['user_token'] . '&sort=o.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $additional_field_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('additional_field/additional_field', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($additional_field_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($additional_field_total - $this->config->get('config_limit_admin'))) ? $additional_field_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $additional_field_total, ceil($additional_field_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('additional_field/additional_field_list', $data));
	}

	protected function getForm() {

		$data['text_form'] = !isset($this->request->get['additional_field_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', ''. $url, true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('additional_field/additional_field', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['additional_field_id'])) {
			$data['action'] = $this->url->link('additional_field/additional_field/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('additional_field/additional_field/edit', 'user_token=' . $this->session->data['user_token'] . '&additional_field_id=' . $this->request->get['additional_field_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('additional_field/additional_field', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['additional_field_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$additional_field_info = $this->model_additional_field_additional_field->getAdditionalfield($this->request->get['additional_field_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['mpoints_additional_field_description'])) {
			$data['mpoints_additional_field_description'] = $this->request->post['mpoints_additional_field_description'];
		} elseif (isset($this->request->get['additional_field_id'])) {
			$data['mpoints_additional_field_description'] = $this->model_additional_field_additional_field->getAdditionalfieldDescriptions($this->request->get['additional_field_id']);
		} else {
			$data['mpoints_additional_field_description'] = array();
		}
		
		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($additional_field_info)) {
			$data['sort_order'] = $additional_field_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('additional_field/additional_field_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'additional_field/additional_field')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['mpoints_additional_field_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 128)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}		

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'additional_field/additional_field')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		$this->load->model('additional_field/additional_field');

		foreach ($this->request->post['selected'] as $additional_field_id) {
			$product_total = $this->model_additional_field_additional_field->getTotalProductsByAdditionalFieldId($additional_field_id);

			if ($product_total) {
				$this->error['warning'] = sprintf($this->language->get('error_product'), $product_total);
			}
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->language('additional_field/additional_field');

			$this->load->model('additional_field/additional_field');

			$this->load->model('tool/image');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			);

			$additional_fields = $this->model_additional_field_additional_field->getAdditionalfields($filter_data);

			foreach ($additional_fields as $additional_field) {
				$json[] = array(
					'additional_field_id'    => $additional_field['additional_field_id'],
					'name'         => strip_tags(html_entity_decode($additional_field['name'], ENT_QUOTES, 'UTF-8')),
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}