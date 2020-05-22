<?php
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

class ControllerExtensionModuleCatalogProProduct extends Controller {
	private $error = array();

	private function loadLanguage() {
        $this->load->language('extension/module/catalog_pro_default');
        $this->load->language('extension/module/catalog_pro_product');
    }

	public function index() {
        $this->loadLanguage();
        $this->load->model('tool/image');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->getList();
	}

	private function getColumnValues($field) {
        switch ($field) {
            case 'status': return array (
                                        "0" => $this->language->get('filter_status_no'),
                                        "1" => $this->language->get('filter_status_yes')
                                    );
                break;
        }
    }

    private function getColumnContent($field) {
        switch ($field) {
            case 'category':
                return $this->load->view('extension/module/catalog_pro/filter_categories', array(
                    "more_two_categories" => $this->language->get('text_more_two_categories'),
                    "categories" => $this->model_extension_catalog_pro_category->getCategories())
                );
                break;
        }
    }


    protected function getList() {
        $this->load->model('extension/catalog_pro/category');

	    unset($data);

        if (!file_exists(DIR_APPLICATION.'controller/extension/module/catalog_pro.json'))
            $data['error_warning'] = $this->language->get('text_config_not_found');
        else
            $config = json_decode(file_get_contents(DIR_APPLICATION.'controller/extension/module/catalog_pro.json'), true);

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
            'text' => $this->language->get('submenu_product'),
            'href' => $this->url->link('extension/module/catalog_pro_product', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['user_token'] = $this->session->data['user_token'];

        if (isset($config)) {
			$data['columns'] = array();
            foreach($config['columns'] as $field => $value) {
                if ($value['visible'] == 1) {
                    $data['columns'][] = [
                        'title' => $this->language->get('column_'.$field),
                        'data' => $field,
                        'width' => $value['width'],
                        'className' => ($field != 'status' && $field != 'product_id' && $field != 'actions'? 'cell ': ''). $value['className'],
                        'orderable' => $value['sort'] == 1? true: false,
                        'type' => isset($value['type'])? $value['type']: null,
                        'values' => isset($value['type'])? ($value['type'] == "select"? $this->getColumnValues($field): null): null,
                        'content' => isset($value['type'])? ($value['type'] == "select-choice"? $this->getColumnContent($field): null): null,
                    ];
                }
            }

			$data['columns'] = json_encode($data['columns']);
			$data['limit'] = $config['limit'];
        }



        $data['data'] = str_replace("&amp;", "&", $this->url->link('extension/module/catalog_pro_product/data', 'user_token=' . $this->session->data['user_token'], true));
        $data['edit'] = str_replace("&amp;", "&", $this->url->link('extension/module/catalog_pro_product/edit', 'user_token=' . $this->session->data['user_token'], true));
        $data['save'] = str_replace("&amp;", "&", $this->url->link('extension/module/catalog_pro_product/save', 'user_token=' . $this->session->data['user_token'], true));
        $data['edit_data'] = str_replace("&amp;", "&", $this->url->link('extension/module/catalog_pro_product/editdata', 'user_token=' . $this->session->data['user_token'], true));
        $data['save_data'] = str_replace("&amp;", "&", $this->url->link('extension/module/catalog_pro_product/savedata', 'user_token=' . $this->session->data['user_token'], true));
        $data['active'] = str_replace("&amp;", "&", $this->url->link('extension/module/catalog_pro_product/active', 'user_token=' . $this->session->data['user_token'], true));

        $data['noimage'] = $this->model_tool_image->resize('no_image.png', 60, 60);

        $data['table_name'] = $this->language->get('table_name');
        $data['table_visible'] = $this->language->get('table_visible');

        $data['visible_yes'] = $this->language->get('visible_yes');
        $data['visible_no'] = $this->language->get('visible_no');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['datatable']['empty_table'] = $this->language->get('datatable_empty_table');
        $data['datatable']['info'] = $this->language->get('datatable_info');
        $data['datatable']['info_empty'] = $this->language->get('datatable_info_empty');
        $data['datatable']['info_filtered'] = $this->language->get('datatable_info_filtered');
        $data['datatable']['info_post_fix'] = $this->language->get('datatable_info_post_fix');
        $data['datatable']['thousands'] = $this->language->get('datatable_thousands');
        $data['datatable']['length_menu'] = $this->language->get('datatable_length_menu');
        $data['datatable']['loading'] = $this->language->get('datatable_loading');
        $data['datatable']['processing'] = $this->language->get('datatable_processing');
        $data['datatable']['search'] = $this->language->get('datatable_search');
        $data['datatable']['zero_records'] = $this->language->get('datatable_zero_records');
        $data['datatable']['sort_asc'] = $this->language->get('datatable_sort_asc');
        $data['datatable']['sort_desc'] = $this->language->get('datatable_sort_desc');
        $data['eLang'] = $this->language->get('edit');

        $this->response->setOutput($this->load->view('extension/module/catalog_pro/product', $data));
	}

	public function data() {
        $this->loadLanguage();

        $config = json_decode(file_get_contents(DIR_APPLICATION.'controller/extension/module/catalog_pro.json'), true);

        $this->load->model('extension/catalog_pro/product');
        $this->load->model('extension/catalog_pro/category');
        $this->load->model('tool/image');

        $deTree = $this->model_extension_catalog_pro_category->getDeTree();

        $filter_data = array(
            'start'           => $this->request->get['start'],
            'limit'           => $this->request->get['length'],
        );

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
            $request = $this->request->get['columns'];
            $field = $request[$order[0]['column']]['data'];

            $filter_data['order'] = $order[0]['dir'];

            switch ($field) {
                case 'name': $filter_data['sort'] = "pd.name"; break;
                case 'model': $filter_data['sort'] = "p.model"; break;
                case 'sku': $filter_data['sort'] = "p.sku"; break;
                case 'price': $filter_data['sort'] = "p.price"; break;
                case 'quantity': $filter_data['sort'] = "p.quantity"; break;
                case 'status': $filter_data['sort'] = "p.status"; break;
                default: $filter_data['sort'] = "p.product_id"; $filter_data['order'] = "desc"; break;
            }
        }

        $search = array();
        foreach ($this->request->get['columns'] as $column) {
            if ($column['search']['value'] != "")
                $filter_data['filter_'.$column['data']] = $column['search']['value'];
        }


        $recordsTotal = $this->model_extension_catalog_pro_product->getTotalProducts($filter_data);
        $recordsFiltered = $recordsTotal;

        $data = array();
        foreach($this->model_extension_catalog_pro_product->getProducts($filter_data) as $row) {
            $temp = array();

            foreach ($config['columns'] as $field => $value)
                if ($value['visible'] == 1)
                    if ($field == "image") {
                        if (is_file(DIR_IMAGE . $row[$field]))
                            $temp[$field] = $this->model_tool_image->resize($row[$field], 40, 40);
                        else
                            $temp[$field] = $this->model_tool_image->resize('no_image.png', 40, 40);

                        $temp[$field] = "<img src=\"{$temp[$field]}\" />";
                    }
                    else if ($field == "status")
                        $temp[$field] = '<button class="btn btn-xs btn-'.($row[$field] == 1? "success": "danger").' product-active" title="'.($row[$field] == 1? $this->language->get('status_no'): $this->language->get('status_yes')).'"><i class="fa fa-power-off" aria-hidden="true"></i></button>';
                    else if ($field == "price") {
                        if (isset($row['specials']))
                            $temp[$field] = "<del class=\"text-danger\">{$row[$field]}</del><br/>".$row['specials'][0]['price'];
                        else
                            $temp[$field] = $row[$field];
                    }
                    else if ($field == "category") {
                        $cTemp = array();

                        if (isset($row['categories']))
                            foreach ($row['categories'] as $c) {
                                $cTemp[] = '<span class="label label-info">'.$deTree[$c]['join_name'].'</span>';
                            }

                        $temp[$field] = implode(" ", $cTemp);
                    }
                    else if ($field == "quantity") {
                        if ($row[$field] == 0)
                            $temp[$field] = "<span class=\"label label-danger\">{$row[$field]}</span>";
                        elseif ($row[$field] < 5)
                            $temp[$field] = "<span class=\"label label-warning\">{$row[$field]}</span>";
                        elseif ($row[$field] < 20)
                            $temp[$field] = "<span class=\"label label-info\">{$row[$field]}</span>";
                        else
                            $temp[$field] = "<span class=\"label label-success\">{$row[$field]}</span>";
                    }
                    else if ($field == "actions") {
                        $temp[$field] = '<button class="btn btn-primary btn-xs show-actions data-placement="auto-bottom" data-content="'.htmlspecialchars($this->load->view('extension/module/catalog_pro/action_buttons', array(
                                "product_id" => $row['product_id'],
                                "lang" => $this->language->get('action_buttons'),
                            ))).'" type="button"><i class="fa fa-cog" aria-hidden="true"></i></button>';
                    }
                    else
                        $temp[$field] = isset($row[$field])? $row[$field]: "";

            $temp['DT_RowId'] = $row['product_id'];
            $data[] = $temp;
            unset($temp);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            "data" => $data,
            "draw" => time(),
            "recordsFiltered" => $recordsFiltered,
            "recordsTotal" => $recordsTotal,
        ]));
    }

    private function returnError($title, $message) {
        $this->response->addHeader('HTTP/1.0 422 Unprocessable Entity');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            "title" => $title,
            "message" => is_array($message)? "<ul>".implode("", array_map(function ($line) { return "<li>{$line}</li>"; }, $message))."</ul>": $message,
        ]));
    }

    public function save() {
        $this->loadLanguage();
        $this->load->model('extension/catalog_pro/product');
        $this->load->model('localisation/language');

        $eLang = $this->language->get('edit');
        $eValidation = $this->language->get('validate');

        $post = $this->request->post['data'];
        $languages = $this->model_localisation_language->getLanguages(array());

        if (!isset($post['action']) || !in_array($post['action'], array('name', 'model', 'sku', 'quantity', 'price', 'image', 'category'))) {
            $this->returnError($eValidation['title'], $eValidation['action']);
            return;
        }


        $item = $this->model_extension_catalog_pro_product->getProduct($post['id']);
        if ($item === array()) {
            $this->returnError($eValidation['title'], $eValidation['id']);
            return;
        }

        switch ($post['action']) {
            case 'name':
                $languages = $this->model_localisation_language->getLanguages(array());

                $errors = array();

                foreach ($languages as $language) {
                    if (!isset($post[$post['action'].".".$language['language_id']])) {
                        $this->returnError($eValidation['title'], $eValidation['action']);
                        return;
                    }

                    $validator = Validation::createValidator();
                    $violations = $validator->validate($post[$post['action'].".".$language['language_id']], array(
                        new Length([
                            'min' => 2,
                            'max' => 255,
                            'minMessage' => $eValidation['name.min'],
                            'maxMessage' => $eValidation['name.max'],
                        ]),
                        new NotBlank([
                            'message' => $eValidation['name.required']
                        ]),
                    ));

                    if (0 !== count($violations)) {
                        // there are errors, now you can show them
                        foreach ($violations as $violation) {
                            $errors[] = $violation->getMessage();
                        }
                    }
                }

                if ($errors !== array()) {
                    $this->returnError($eValidation['title'], $errors);
                    return;
                }

                foreach ($languages as $language) {
                    $this->model_extension_catalog_pro_product->saveProductDescriptions($post['id'], $language['language_id'], array('name' => $post[$post['action'].".".$language['language_id']]));
                }

                break;
            case 'model':
                $errors = array();


                $validator = Validation::createValidator();
                $violations = $validator->validate($post['model'], array(
                    new Length([
                        'min' => 1,
                        'max' => 255,
                        'minMessage' => $eValidation['model.min'],
                        'maxMessage' => $eValidation['model.max'],
                    ]),
                    new NotBlank([
                        'message' => $eValidation['model.required']
                    ]),
                ));

                if (0 !== count($violations)) {
                    // there are errors, now you can show them
                    foreach ($violations as $violation) {
                        $errors[] = $violation->getMessage();
                    }
                }

                if ($errors !== array()) {
                    $this->returnError($eValidation['title'], $errors);
                    return;
                }

                $this->model_extension_catalog_pro_product->saveProduct($post['id'], array("model" => $post['model']));

                break;
            case 'sku':
                $errors = array();


                $validator = Validation::createValidator();
                $violations = $validator->validate($post['sku'], array(
                    new Length([
                        'max' => 64,
                        'maxMessage' => $eValidation['sku.max'],
                    ])
                ));

                if (0 !== count($violations)) {
                    foreach ($violations as $violation) {
                        $errors[] = $violation->getMessage();
                    }
                }

                if ($errors !== array()) {
                    $this->returnError($eValidation['title'], $errors);
                    return;
                }

                $this->model_extension_catalog_pro_product->saveProduct($post['id'],  array("sku" => $post['sku']));

                break;
            case 'quantity':
                $errors = array();

                $validator = Validation::createValidator();
                $violations = $validator->validate($post['quantity'], array(
                    new Range([
                        'min' => 0,
                        'minMessage' => $eValidation['quantity.min'],
                        'invalidMessage' => $eValidation['quantity.invalid'],
                    ]),
                    new NotBlank([
                        'message' => $eValidation['quantity.required']
                    ]),
                ));

                if (0 !== count($violations)) {
                    foreach ($violations as $violation) {
                        $errors[] = $violation->getMessage();
                    }
                }

                if ($errors !== array()) {
                    $this->returnError($eValidation['title'], $errors);
                    return;
                }

                $this->model_extension_catalog_pro_product->saveProduct($post['id'],  array("quantity" => $post['quantity']));

                break;

            case 'price':
                $errors = array();

                $validator = Validation::createValidator();
                $violations = $validator->validate($post['price'], array(
                    new Range([
                        'min' => 0,
                        'minMessage' => $eValidation['price.min'],
                        'invalidMessage' => $eValidation['price.invalid'],
                    ]),
                    new NotBlank([
                        'message' => $eValidation['price.required']
                    ]),
                ));

                if (0 !== count($violations)) {
                    foreach ($violations as $violation) {
                        $errors[] = $violation->getMessage();
                    }
                }

                $specials = array();
                $regular = '/(\w+)\.(\d+)\.(\w+)/m';
                foreach ($post as $field => $value) {
                    if (strpos($field, "special.") !== false) {
                        preg_match_all($regular, $field, $matches, PREG_SET_ORDER, 0);
                        $specials[$matches[0][2]][$matches[0][3]] = $value;
                    }
                }

                if ($specials !== array() && count($specials) == 1) {
                    $tempKey = array_keys($specials);
                    $tempKey = $tempKey[0];

                    $temp = $specials[$tempKey];
                    unset($temp['customer_group_id']);
                    try {
                        foreach ($temp as $t)
                            if ($t != "")
                                throw new Exception('');

                        unset($specials[$tempKey]);
                    }
                    catch (Exception $exception) {

                    }
                }

                foreach ($specials as $special) {
                    // price
                    $violations = $validator->validate($special['price'], array(
                        new Range([
                            'min' => 0.01,
                            'minMessage' => $eValidation['price.min'],
                            'invalidMessage' => $eValidation['price.invalid'],
                        ]),
                        new NotBlank([
                            'message' => $eValidation['price.required']
                        ]),
                    ));

                    if (0 !== count($violations)) {
                        foreach ($violations as $violation) {
                            $errors[] = $violation->getMessage();
                        }
                    }

                    // date_start
                    if ($special['date_start'] != "") {
                        $violations = $validator->validate($special['date_start'], array(
                            new Date([
                                'message' => $eValidation['date_start.invalid'],
                            ])
                        ));

                        if (0 !== count($violations)) {
                            foreach ($violations as $violation) {
                                $errors[] = $violation->getMessage();
                            }
                        }

                        if ($special['date_end'] != "") {
                            $violations = $validator->validate(new DateTime($special['date_start']), array(
                                new Assert\Range([
                                    'max' => new DateTime($special['date_end']),
                                    'maxMessage' => $eValidation['date_start.max'],
                                    'invalidMessage' => $eValidation['date_start.invalid'],
                                ])
                            ));

                            if (0 !== count($violations)) {
                                foreach ($violations as $violation) {
                                    $errors[] = $violation->getMessage();
                                }
                            }
                        }
                    }

                    if ($special['date_end'] != "") {
                        $violations = $validator->validate($special['date_end'], array(
                            new Date([
                                'message' => $eValidation['date_start.invalid'],
                            ])
                        ));

                        if (0 !== count($violations)) {
                            foreach ($violations as $violation) {
                                $errors[] = $violation->getMessage();
                            }
                        }

                        if ($special['date_start'] != "") {
                            $violations = $validator->validate(new DateTime($special['date_end']), array(
                                new Assert\Range([
                                    'min' => new DateTime($special['date_start']),
                                    'minMessage' => $eValidation['date_end.min'],
                                    'invalidMessage' => $eValidation['date_end.invalid'],
                                ])
                            ));

                            if (0 !== count($violations)) {
                                foreach ($violations as $violation) {
                                    $errors[] = $violation->getMessage();
                                }
                            }
                        }

                    }
                }

                if ($errors !== array()) {
                    $this->returnError($eValidation['title'], $errors);
                    return;
                }

                $this->model_extension_catalog_pro_product->saveProduct($post['id'], array("price" => $post['price']));
                $this->model_extension_catalog_pro_product->saveProductSpecial($post['id'],  $specials);

                break;


            case 'image':
                $errors = array();

                $images = array();
                $regular = '/(\w+)\.(\d+)\.(\w+)/m';
                foreach ($post as $field => $value) {
                    if (strpos($field, "image.") !== false) {
                        preg_match_all($regular, $field, $matches, PREG_SET_ORDER, 0);
                        $images[$matches[0][2]][$matches[0][3]] = $value;
                    }
                }

                if ($images !== array() && count($images) == 1) {
                    $tempKey = array_keys($images);
                    $tempKey = $tempKey[0];

                    $temp = $images[$tempKey];
                    try {
                        foreach ($temp as $t)
                            if ($t != "")
                                throw new Exception('');

                        unset($images[$tempKey]);
                    }
                    catch (Exception $exception) {

                    }
                }

                $mainImage = $images[0]['image'];
                unset($images[0]);

                $this->model_extension_catalog_pro_product->saveProductImage($post['id'], $mainImage, $images);

                break;

            case 'category':
                $this->model_extension_catalog_pro_product->saveProductCategory($post['id'], $post['category']);

                break;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            "title" => $this->language->get('text_success_save_title'),
            "message" => $this->language->get('text_success_save')
        ]));
    }

    public function active() {
        $this->loadLanguage();
        $this->load->model('extension/catalog_pro/product');

        $id = $this->request->post['id'];
        $eValidation = $this->language->get('validate');

        $item = $this->model_extension_catalog_pro_product->getProduct($id);
        if ($item === array()) {
            $this->returnError($eValidation['title'], $eValidation['id']);
            return;
        }

        $this->model_extension_catalog_pro_product->saveProductInvertStatus($id);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            "title" => $this->language->get('text_success_save_title'),
            "message" => $this->language->get('text_success_save')
        ]));
    }

    public function edit() {
        $this->loadLanguage();
        $this->load->model('extension/catalog_pro/product');
        $this->load->model('localisation/language');

        $eLang = $this->language->get('edit');

        $item = $this->model_extension_catalog_pro_product->getProduct($this->request->post['id']);

	    $title = "";
	    $content = "";
	    $width = 250;

	    switch ($this->request->post['action']) {
            case 'name':
                $title = $eLang['title']['name'];
                $content = $this->editName($eLang, $item);
                $width = 400;
                break;
            case 'model':
                $title = $eLang['title']['model'];
                $content = $this->editModel($eLang, $item);
                $width = 400;
                break;
            case 'sku':
                $title = $eLang['title']['sku'];
                $content = $this->editSku($eLang, $item);
                $width = 400;
                break;
            case 'price':
                $title = $eLang['title']['price'];
                $content = $this->editPrice($eLang, $item);
                $width = 700;
                break;
            case 'quantity':
                $title = $eLang['title']['quantity'];
                $content = $this->editQuantity($eLang, $item);
                $width = 400;
                break;
            case 'image':
                $title = $eLang['title']['image'];
                $content = $this->editImage($eLang, $item);
                $width = 500;
                break;
            case 'category':
                $title = $eLang['title']['category'];
                $content = $this->editCategory($eLang, $item);
                $width = 400;
                break;
        }


        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            "title" => $title,
            "content" => $content,
            "width" => $width,
        ]));
    }

    private function editButtons($eLang) {
        $content = "<div class=\"pull-right\">";
        $content .= "<button class=\"btn btn-danger btn-xs popover-hide\" type=\"button\">".$eLang['buttons']['cancel']."</button>&nbsp;";
        $content .= "<button class=\"btn btn-success btn-xs edit-save\" type=\"button\">".$eLang['buttons']['save']."</button>";
        $content .= "</div>";

        return $content;
    }

    private function editName($eLang, $item) {
        $content = "<form>";

        $languages = $this->model_localisation_language->getLanguages(array());

        $names = $this->model_extension_catalog_pro_product->getProductDescriptions($item['product_id']);

        foreach ($languages as $language) {
            $content .= '<div class="input-group">
                          <span class="input-group-addon"><img src="language/'.$language['code'].'/'.$language['code'].'.png" title="'.$language['name'].'"/></span>
                          <input type="text" class="form-control" name="name.'.$language['language_id'].'" value="'.$names[$language['language_id']]['name'].'">
                        </div>';
        }

        $content .= $this->editButtons($eLang);
        $content .= '<input type="hidden" name="action" value="name" />';
        $content .= '<input type="hidden" name="id" value="'.$item['product_id'].'" />';
        $content .= "</form>";


        return $content;
    }

    private function editModel($eLang, $item) {
        $content = "<form>";

        $content .= '<div class="form-group"><input type="text" class="form-control" name="model" value="'.$item['model'].'"></div>';

        $content .= $this->editButtons($eLang);
        $content .= '<input type="hidden" name="action" value="model" />';
        $content .= '<input type="hidden" name="id" value="'.$item['product_id'].'" />';
        $content .= "</form>";

        return $content;
    }

    private function editSku($eLang, $item) {
        $content = "<form>";

        $content .= '<div class="form-group"><input type="text" class="form-control" name="sku" value="'.$item['sku'].'"></div>';

        $content .= $this->editButtons($eLang);
        $content .= '<input type="hidden" name="action" value="sku" />';
        $content .= '<input type="hidden" name="id" value="'.$item['product_id'].'" />';
        $content .= "</form>";

        return $content;
    }

    private function editPriceAddSpecial($customers, $special) {
        $content = '<li class="special">
                <div class="row">
                    <div class="col-xs-4"><i class="fa fa-arrows sortable pull-left" aria-hidden="true"></i><select name="special.'.$special['product_special_id'].'.customer_group_id" class="form-control input-sm">';
        foreach ($customers as $customer)
            $content .= '<option value="'.$customer['customer_group_id'].'" '.($customer['customer_group_id'] == $special['customer_group_id']? "SELECTED": "").'>'.$customer['name'].'</option>';
        $content .= '</select></div>
                     <div class="col-xs-2"><input type="text" class="form-control input-sm" name="special.'.$special['product_special_id'].'.price" value="'.$special['price'].'" /></div>
                     <div class="col-xs-3">
                         <div class="input-group date">
                            <input type="text" name="special.'.$special['product_special_id'].'.date_start" data-date-format="YYYY-MM-DD" class="form-control input-sm" value="'.($special['date_start'] != "0000-00-00"? $special['date_start']: "").'" />
                            <span class="input-group-btn">
                            <button class="btn btn-default btn-sm" type="button"><i class="fa fa-calendar"></i></button>
                            </span>
                        </div>
                     </div>
                     <div class="col-xs-3">
                        <button type="button" class="btn btn-sm btn-danger special-remove pull-right"><i class="fa fa-trash-o" aria-hidden="true"></i></button>

                        <div class="input-group date">
                            <input type="text" name="special.'.$special['product_special_id'].'.date_end" data-date-format="YYYY-MM-DD" class="form-control input-sm" value="'.($special['date_end'] != "0000-00-00"? $special['date_end']: "").'" />
                            <span class="input-group-btn">
                                <button class="btn btn-default btn-sm" type="button"><i class="fa fa-calendar"></i></button>
                                
                            </span>
                        </div>
                        
                        
                     </div>
                </div>
            </div>';

        return $content;
    }

    private function editPrice($eLang, $item) {
        $this->load->model('customer/customer_group');

        $customers = $this->model_customer_customer_group->getCustomerGroups();

        $content = "<form>";

        $content .= '<div class="form-group" style="padding: 0"><label>'.$eLang['price']['current'].'</label><input type="text" class="form-control" name="price" value="'.$item['price'].'"></div>';
        $content .= '<hr/>';

        $content .= '<div class="pull-right"><button type="button" class="btn btn-xs btn-success special-add">'.$eLang['price']['special_add'].'</button></div>';
        $content .= '<label>'.$eLang['price']['specials'].'</label>';

        $content .= '<div class="row" style="margin-top: 20px"><div class="col-xs-4"><b>'.$eLang['price']['table']['group'].'</b></div><div class="col-xs-2"><b>'.$eLang['price']['table']['price'].'</b></div><div class="col-xs-3"><b>'.$eLang['price']['table']['date_from'].'</b></div><div class="col-xs-3"><b>'.$eLang['price']['table']['date_to'].'</b></div></div>';
        $content .= '<ul id="specials">';

        if ($item['specials'] !== array()) {
            foreach ($item['specials'] as $special) {
                $content .= $this->editPriceAddSpecial($customers, $special);
            }
        }
        else
            $content .= $this->editPriceAddSpecial($customers, array(
                "product_special_id" => time().mt_rand(1000, 9999),
                "customer_group_id" => "",
                "priority" => "",
                "price" => "",
                "date_start" => "",
                "date_end" => "",
            ));
        $content .= '</ul>';

        $content .= $this->editButtons($eLang);
        $content .= '<input type="hidden" name="action" value="price" />';
        $content .= '<input type="hidden" name="id" value="'.$item['product_id'].'" />';
        $content .= "</form>";

        return $content;
    }

    private function editQuantity($eLang, $item) {
        $content = "<form>";

        $content .= '<div class="form-group"><input type="text" class="form-control" name="quantity" value="'.$item['quantity'].'"></div>';

        $content .= $this->editButtons($eLang);
        $content .= '<input type="hidden" name="action" value="quantity" />';
        $content .= '<input type="hidden" name="id" value="'.$item['product_id'].'" />';
        $content .= "</form>";

        return $content;
    }

    private function editImageAddImage($eLang, $image) {
        if (is_file(DIR_IMAGE . $image['image']))
            $imageHtml = $this->model_tool_image->resize($image['image'], 60, 60);
        else
            $imageHtml = $this->model_tool_image->resize('no_image.png', 60, 60);

        $imageHtml = "<div id=\"image{$image['product_image_id']}\">
                <img data=\"{$image['product_image_id']}\" src=\"{$imageHtml}\" class=\"update-image\" />
                <input type=\"hidden\" name=\"image.{$image['product_image_id']}.image\" value=\"{$image['image']}\" id=\"image-{$image['product_image_id']}-image\" />
            </div>";

        $content = '<div class="col-xs-3 image">
                <div class="panel panel-default">
                    <div class="panel-body">
                        '.$imageHtml.'
                    </div>
                    <div class="panel-footer">
                        <button type="button" class="btn btn-danger btn-xs image-remove btn-block"><i class="fa fa-trash-o" aria-hidden="true"></i> '.$eLang['buttons']['remove'].'</button>
                    </div>
                </div>
            </div>';

        return $content;
    }

    private function editImage($eLang, $item) {
        $this->load->model('tool/image');

        $content = "<form>";

        if (is_file(DIR_IMAGE . $item['image']))
            $image = $this->model_tool_image->resize($item['image'], 100, 100);
        else
            $image = $this->model_tool_image->resize('no_image.png', 100, 100);

        $image = "<div id=\"image0\">
                <img data=\"0\" src=\"{$image}\" class=\"update-image\" />
                <input type=\"hidden\" name=\"image.0.image\" value=\"{$item['image']}\" id=\"image-{$item['product_id']}-image\" />
            </div>";

        $content .= '<div class="form-group" style="padding: 0">'.$image.'</div>';
        $content .= '<hr/>';

        $content .= '<div class="pull-right"><button type="button" class="btn btn-xs btn-success image-add">'.$eLang['image']['image_add'].'</button></div>';
        $content .= '<label>'.$eLang['image']['additional'].'</label>';

        $content .= '<div id="images" class="row">';
        if ($item['images'] !== array()) {
            foreach ($item['images'] as $image) {
                $content .= $this->editImageAddImage($eLang, $image);
            }
        }

        $content .= '</div>';

        $content .= '<div class="alert alert-warning">'.$this->language->get('text_image_note').'</div>';

        $content .= $this->editButtons($eLang);
        $content .= '<input type="hidden" name="action" value="image" />';
        $content .= '<input type="hidden" name="id" value="'.$item['product_id'].'" />';
        $content .= "</form>";

        return $content;
    }

    private function editCategory($eLang, $item) {
        $this->load->model('extension/catalog_pro/category');

        $content = "<form>";

        $content .= '<ul id="listCategoryEdit" class="ztree"></ul>';

        $content .= '<script>
            var settingEdit = {check: {enable: true,chkboxType: { "Y" : "", "N" : "" }},view: {dblClickExpand: false},data: {simpleData: {enable: true}}};';

        $content .= 'var zCategoriesEditNodes =['."\n";
        foreach ($this->model_extension_catalog_pro_category->getCategories() as $category)
            $content .= '{id:'.$category['category_id'].', pId:'.$category['parent_id'].', name:"'.$category['name'].'", checked: '.(in_array($category['category_id'], $item['categories']) !== false? "true": "false").'},'."\n";
        $content .= "];\n";


        $content .= 'var categoryEditTree = $.fn.zTree.init($("#listCategoryEdit"), settingEdit, zCategoriesEditNodes);';

        $content .= '</script>';

        $content .= $this->editButtons($eLang);
        $content .= '<input type="hidden" name="action" value="category" />';
        $content .= '<input type="hidden" name="id" value="'.$item['product_id'].'" />';
        $content .= "</form>";

        return $content;
    }



    public function editData() {
        $this->loadLanguage();
        $this->load->model('extension/catalog_pro/product');
        $this->load->model('localisation/language');

        $eModal = $this->language->get('modal');

        $item = $this->model_extension_catalog_pro_product->getProduct($this->request->post['id']);

        $title = "";
        $content = "";

        switch ($this->request->post['action']) {
            case 'main':
                $title = $eModal['title']['main'];
                $content = $this->editDataMain($eModal, $item);
                break;
            case 'data':
                $title = $eModal['title']['data'];
                $content = $this->editDataData($eModal, $item);
                break;
            case 'link':
                $title = $eModal['title']['link'];
                $content = $this->editDataLinks($eModal, $item);
                break;
            case 'attrs':
                $title = $eModal['title']['attrs'];
                $content = $this->editDataAttrs($eModal, $item);
                break;
            case 'options':
                $title = $eModal['title']['options'];
                $content = $this->editDataOptions($eModal, $item);
                break;
            case 'discount':
                $title = $eModal['title']['discount'];
                $content = $this->editDataDiscount($eModal, $item);
                break;
            case 'bonus':
                $title = $eModal['title']['bonus'];
                $content = $this->editDataBonus($eModal, $item);
                break;
            case 'seo':
                $title = $eModal['title']['seo'];
                $content = $this->editDataSEO($eModal, $item);
                break;
            default:
                $eValidation = $this->language->get('validate');
                return $this->returnError($eValidation['title'], $eValidation['action']);
                return;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            "title" => $title,
            "content" => $content
        ]));
    }

    private function editDataMain($eModal, $item) {
        $languages = $this->model_localisation_language->getLanguages(array());

        $names = $this->model_extension_catalog_pro_product->getProductDescriptions($item['product_id']);

        return $this->load->view(
            'extension/module/catalog_pro/edit_product/block_general',
            array(
                "languages" => $languages,
                "names" => $names,
                "modal" => $eModal,
            )
        );
    }

    private function editDataData($eModal, $item) {
        $this->load->model('localisation/tax_class');
        $this->load->model('localisation/stock_status');
        $this->load->model('localisation/length_class');
        $this->load->model('localisation/weight_class');

        return $this->load->view(
            'extension/module/catalog_pro/edit_product/block_data',
            array(
                "modal" => $eModal,
                "item" => $item,
                'dict' => array (
                    'tax' => $this->model_localisation_tax_class->getTaxClasses(),
                    'subtract' => array (
                        array("value" => 0, "title" => $eModal['text']['no']),
                        array("value" => 1, "title" => $eModal['text']['yes']),
                    ),
                    'shipping' => array (
                        array("value" => 0, "title" => $eModal['text']['no']),
                        array("value" => 1, "title" => $eModal['text']['yes']),
                    ),
                    'stock_status' => $this->model_localisation_stock_status->getStockStatuses(),
                    'length_class' => $this->model_localisation_length_class->getLengthClasses(),
                    'weight_class' => $this->model_localisation_weight_class->getWeightClasses(),
                    'status' => array (
                        array("value" => 0, "title" => $eModal['text']['status_no']),
                        array("value" => 1, "title" => $eModal['text']['status_yes']),
                    ),
                ),
            )
        );
    }

    private function editDataLinks($eModal, $item) {
        $this->load->model('catalog/manufacturer');
        $this->load->model('catalog/filter');
        $this->load->model('setting/store');
        $this->load->model('catalog/download');
        $this->load->model('extension/catalog_pro/category');

        $related = array();
        if (isset($item['related']) && $item['related'] !== array()) {
            $filter_data = array(
                'start'           => 0,
                'limit'           => 1000,
                'filter_product_id' => $item['related'],
            );
            $temp = $this->model_extension_catalog_pro_product->getProducts($filter_data);
            foreach ($temp as $p) {
                $related[] = array(
                    "id" => $p['product_id'],
                    "text" => $this->getProductNameForAjax($p, $eModal),
                );
            }
        }

        $manufacturers = $this->model_catalog_manufacturer->getManufacturers(array());
        $categories = $this->model_extension_catalog_pro_category->getCategories();
        $filters = array();
        foreach ($this->model_catalog_filter->getFilters(array()) as $temp)
            $filters[$temp['group']][] = $temp;

        $stores = array(
            array(
                'store_id' => 0,
                'name'     => $this->language->get('text_default')
            )
        );

        foreach ($this->model_setting_store->getStores() as $store) {
            $stores[] = array(
                'store_id' => $store['store_id'],
                'name'     => $store['name']
            );
        }

        $downloads = array();

        foreach ($this->model_catalog_download->getDownloads(array()) as $download)
            $downloads[] = array(
                'download_id' => $download['download_id'],
                'name'        => $download['name']
            );

        $ajaxProducts = str_replace("&amp;", "&", $this->url->link('extension/module/catalog_pro_product/ajaxProducts', 'user_token=' . $this->session->data['user_token'], true));

        return $this->load->view(
            'extension/module/catalog_pro/edit_product/block_links',
            array(
                "item" => $item,
                "modal" => $eModal,
                "manufacturers" => $manufacturers,
                "categories" => $categories,
                "filters" => $filters,
                "stores" => $stores,
                "downloads" => $downloads,
                "ajaxProducts" => $ajaxProducts,
                "related" => $related,
            )
        );
    }

    private function editDataAttrs($eModal, $item) {
        $this->load->model('catalog/attribute');
        $attributes = $this->model_catalog_attribute->getAttributes();
        $languages = $this->model_localisation_language->getLanguages(array());

        $eEdit = $this->language->get('edit');

        return $this->load->view(
            'extension/module/catalog_pro/edit_product/block_attrs',
            array(
                "item" => $item,
                "modal" => $eModal,
                "attributes" => $attributes,
                "languages" => $languages,
                "edit" => $eEdit,
            )
        );
    }

    private function editDataOptions($eModal, $item) {
        $this->load->language('catalog/option');
        $this->load->model('catalog/option');
        $options = $this->model_catalog_option->getOptions();
        $optionAdd = array();

        foreach ($options as &$option) {
            $values = $this->model_catalog_option->getOptionValues($option['option_id']);
            $option['values'] = $values;

            if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox') {
                $type = $this->language->get('text_choose');
            }
            elseif ($option['type'] == 'text' || $option['type'] == 'textarea') {
                $type = $this->language->get('text_input');
            }
            elseif ($option['type'] == 'file') {
                $type = $this->language->get('text_file');
            }
            elseif ($option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
                $type = $this->language->get('text_date');
            }

            $optionAdd[$type][] = $option;
        }

        $languages = $this->model_localisation_language->getLanguages(array());

        $eEdit = $this->language->get('edit');

        return $this->load->view(
            'extension/module/catalog_pro/edit_product/block_options',
            array(
                "item" => $item,
                "modal" => $eModal,
                "options" => $options,
                "optionAdd" => $optionAdd,
                "languages" => $languages,
                "edit" => $eEdit,
            )
        );
    }

    private function editDataDiscount($eModal, $item) {
        $this->load->language('customer/customer_group');
        $this->load->model('customer/customer_group');
        $groups = $this->model_customer_customer_group->getCustomerGroups();

        $eEdit = $this->language->get('edit');

        return $this->load->view(
            'extension/module/catalog_pro/edit_product/block_discount',
            array(
                "item" => $item,
                "modal" => $eModal,
                "groups" => $groups,
                "edit" => $eEdit,
            )
        );
    }

    private function editDataBonus($eModal, $item) {
        $this->load->language('customer/customer_group');
        $this->load->model('customer/customer_group');
        $groups = $this->model_customer_customer_group->getCustomerGroups();

        $eEdit = $this->language->get('edit');

        return $this->load->view(
            'extension/module/catalog_pro/edit_product/block_rewards',
            array(
                "item" => $item,
                "modal" => $eModal,
                "groups" => $groups,
                "edit" => $eEdit,
            )
        );
    }

    private function editDataSEO($eModal, $item) {
        $this->load->model('setting/store');
        $this->load->model('localisation/language');

        $languages = $this->model_localisation_language->getLanguages(array());

        $stores = array(
            array(
                'store_id' => 0,
                'name'     => $this->language->get('text_default')
            )
        );

        foreach ($this->model_setting_store->getStores() as $store) {
            $stores[] = array(
                'store_id' => $store['store_id'],
                'name'     => $store['name']
            );
        }

        $eEdit = $this->language->get('edit');

        return $this->load->view(
            'extension/module/catalog_pro/edit_product/block_seo',
            array(
                "item" => $item,
                "modal" => $eModal,
                "stores" => $stores,
                "languages" => $languages,
                "edit" => $eEdit,
            )
        );
    }


    public function saveData() {
        $this->loadLanguage();
        $this->load->model('extension/catalog_pro/product');
        $this->load->model('localisation/language');

        $eLang = $this->language->get('edit');
        $eValidation = $this->language->get('validate');

        $post = $this->request->post['data'];
        $action = $this->request->post['action'];
        $id = $this->request->post['id'];
        $languages = $this->model_localisation_language->getLanguages(array());

        if (!in_array($action, array('main', 'data', 'link', 'attrs', 'options', 'discount', 'bonus', 'seo'))) {
            $this->returnError($eValidation['title'], $eValidation['action']);
            return;
        }

        $item = $this->model_extension_catalog_pro_product->getProduct($id);
        if ($item === array()) {
            $this->returnError($eValidation['title'], $eValidation['id']);
            return;
        }

        switch ($action) {
            case 'main':
                $languages = $this->model_localisation_language->getLanguages(array());

                $errors = array();

                $fields = array();
                $regular = '/(\w+)\.(\d+)/m';
                foreach ($post as $field => $value) {
                    preg_match_all($regular, $field, $matches, PREG_SET_ORDER, 0);
                    $fields[$matches[0][2]][$matches[0][1]] = $value;
                }

                $validator = Validation::createValidator();
                $groups = new Assert\GroupSequence(['Default']);

                foreach ($languages as $language) {
                    $prefix = "[{$language['name']}] ";

                    $constraint = new Assert\Collection(array(
                        "name" => array(
                            new Assert\Length([
                                'min' => 2,
                                'max' => 255,
                                'minMessage' => $prefix.$eValidation['name.min'],
                                'maxMessage' => $prefix.$eValidation['name.max'],
                            ]),
                            new Assert\NotBlank([
                                'message' => $prefix.$eValidation['name.required']
                            ]),
                        ),
                        "description" => array(
                            new Assert\Type(array('type' => 'string'))
                        ),
                        "meta_title" => array(
                            new Assert\Length(array(
                                'min' => 2,
                                'max' => 255,
                                'minMessage' => $prefix.$eValidation['meta_title.min'],
                                'maxMessage' => $prefix.$eValidation['meta_title.max'],
                            )),
                            new Assert\NotBlank(array(
                                'message' => $prefix.$eValidation['meta_title.required']
                            )),
                        ),
                        "meta_description" => array(
                            new Assert\Length(array(
                                'max' => 255,
                                'maxMessage' => $prefix.$eValidation['meta_description.max'],
                            )),
                        ),
                        "meta_keyword" => array(
                            new Assert\Length(array(
                                'max' => 255,
                                'maxMessage' => $prefix.$eValidation['meta_keyword.max'],
                            )),
                        ),
                        "tag" => array(
                            new Assert\Type(array('type' => 'string'))
                        ),
                    ));


                    $violations = $validator->validate($fields[$language['language_id']], $constraint);

                    if (0 !== count($violations)) {
                        // there are errors, now you can show them
                        foreach ($violations as $violation) {
                            $errors[] = $violation->getMessage();
                        }
                    }
                }

                if ($errors !== array()) {
                    $this->returnError($eValidation['title'], $errors);
                    return;
                }

                foreach ($languages as $language) {
                    $this->model_extension_catalog_pro_product->saveProductDescriptions($id, $language['language_id'], $fields[$language['language_id']]);
                }

                break;

            case 'data':
                $this->load->model('localisation/tax_class');
                $this->load->model('localisation/stock_status');
                $this->load->model('localisation/length_class');
                $this->load->model('localisation/weight_class');

                $errors = array();
                $dict = array ();
                foreach ($this->model_localisation_tax_class->getTaxClasses() as $d)
                    $dict['tax'][] = $d['tax_class_id'];
                $dict['subtract'] = [0, 1];
                $dict['shipping'] = [0, 1];
                foreach ($this->model_localisation_stock_status->getStockStatuses() as $d)
                    $dict['stock_status'][] = $d['stock_status_id'];
                foreach ($this->model_localisation_length_class->getLengthClasses() as $d)
                    $dict['length_class'][] = $d['length_class_id'];
                foreach ($this->model_localisation_weight_class->getWeightClasses() as $d)
                    $dict['weight_class'][] = $d['weight_class_id'];
                $dict['status'] = [0, 1];


                $validator = Validation::createValidator();

                $constraint = new Assert\Collection([
                    "model" => array(
                        new Assert\Length(array(
                            'max' => 64,
                            'maxMessage' => $eValidation['model.max'],
                        )),
                        new Assert\NotBlank(array(
                            'message' => $eValidation['model.required']
                        )),
                    ),
                    "sku" => array(
                        new Length(array(
                            'max' => 64,
                            'maxMessage' => $eValidation['sku.max'],
                        ))
                    ),
                    "upc" => array(
                        new Length(array(
                            'max' => 12,
                            'maxMessage' => $eValidation['upc.max'],
                        ))
                    ),
                    "ean" => array(
                        new Length(array(
                            'max' => 14,
                            'maxMessage' => $eValidation['ean.max'],
                        ))
                    ),
                    "jan" => array(
                        new Length(array(
                            'max' => 13,
                            'maxMessage' => $eValidation['jan.max'],
                        ))
                    ),
                    "isbn" => array(
                        new Length(array(
                            'max' => 17,
                            'maxMessage' => $eValidation['isbn.max'],
                        ))
                    ),
                    "mpn" => array(
                        new Length(array(
                            'max' => 64,
                            'maxMessage' => $eValidation['mpn.max'],
                        ))
                    ),
                    "location" => array(
                        new Length(array(
                            'max' => 128,
                            'maxMessage' => $eValidation['location.max'],
                        ))
                    ),
                    "price" => array (
                        new Range(array(
                            'min' => 0,
                            'minMessage' => $eValidation['price.min'],
                            'invalidMessage' => $eValidation['price.invalid'],
                        )),
                        new NotBlank(array(
                            'message' => $eValidation['price.required']
                        )),
                    ),
                    "tax_class_id" => array (
                        new Assert\Choice(array(
                            'choices' => $dict['tax'],
                            'message' => $eValidation['tax_class_id.in']
                        )),
                    ),
                    "quantity" => array(
                        new Range(array(
                            'min' => 0,
                            'minMessage' => $eValidation['quantity.min'],
                            'invalidMessage' => $eValidation['quantity.invalid'],
                        )),
                        new NotBlank(array(
                            'message' => $eValidation['quantity.required']
                        )),
                    ),
                    "subtract" => array (
                        new Assert\Choice(array(
                            'choices' => $dict['subtract'],
                            'message' => $eValidation['tax_class_id.in']
                        )),
                    ),
                    "minimum" => array(
                        new Range(array(
                            'min' => 1,
                            'minMessage' => $eValidation['minimum.min'],
                            'invalidMessage' => $eValidation['minimum.invalid'],
                        )),
                        new NotBlank(array(
                            'message' => $eValidation['minimum.required']
                        )),
                    ),
                    "stock_status_id" => array (
                        new Assert\Choice(array(
                            'choices' => $dict['stock_status'],
                            'message' => $eValidation['stock_status_id.in']
                        )),
                    ),
                    "shipping" => array (
                        new Assert\Choice(array(
                            'choices' => $dict['shipping'],
                            'message' => $eValidation['shipping.in']
                        )),
                    ),
                    "date_available" => array(
                        new Date(array(
                            'message' => $eValidation['date_available.invalid'],
                        ))
                    ),
                    "length" => array(
                        new Range(array(
                            'min' => 0,
                            'minMessage' => $eValidation['length.min'],
                            'invalidMessage' => $eValidation['length.invalid'],
                        ))
                    ),
                    "width" => array(
                        new Range(array(
                            'min' => 0,
                            'minMessage' => $eValidation['width.min'],
                            'invalidMessage' => $eValidation['width.invalid'],
                        ))
                    ),
                    "height" => array(
                        new Range(array(
                            'min' => 0,
                            'minMessage' => $eValidation['height.min'],
                            'invalidMessage' => $eValidation['height.invalid'],
                        ))
                    ),
                    "length_class_id" => array (
                        new Assert\Choice(array(
                            'choices' => $dict['length_class'],
                            'message' => $eValidation['length_class_id.in']
                        )),
                    ),
                    "weight" => array(
                        new Range(array(
                            'min' => 0,
                            'minMessage' => $eValidation['weight.min'],
                            'invalidMessage' => $eValidation['weight.invalid'],
                        ))
                    ),
                    "weight_class_id" => array (
                        new Assert\Choice(array(
                            'choices' => $dict['weight_class'],
                            'message' => $eValidation['weight_class_id.in']
                        )),
                    ),
                    "status" => array (
                        new Assert\Choice(array(
                            'choices' => $dict['status'],
                            'message' => $eValidation['status.in']
                        )),
                    ),
                    "sort_order" => array (
                        new Range(array(
                            'min' => 0,
                            'minMessage' => $eValidation['sort_order.min'],
                            'invalidMessage' => $eValidation['sort_order.invalid'],
                        ))
                    ),
                ]);


                $violations = $validator->validate($post, $constraint);

                if (0 !== count($violations)) {
                    // there are errors, now you can show them
                    foreach ($violations as $violation) {
                        $errors[] = $violation->getMessage();
                    }
                }


                if ($errors !== array()) {
                    $this->returnError($eValidation['title'], $errors);
                    return;
                }

                $this->model_extension_catalog_pro_product->saveProduct($id, $post);

                break;


            case 'link':
                $this->load->model('catalog/manufacturer');
                $this->load->model('catalog/filter');
                $this->load->model('setting/store');
                $this->load->model('catalog/download');
                $this->load->model('extension/catalog_pro/category');

                $errors = array();
                $dict = array ();

                $dict['manufacturers'] = array_map(function($manufacturer) {
                    return $manufacturer['manufacturer_id'];
                }, $this->model_catalog_manufacturer->getManufacturers(array()));

                $dict['categories'] = array_map(function($category) {
                    return $category['category_id'];
                }, $this->model_extension_catalog_pro_category->getCategories());

                $dict['filters'] = array_map(function($filter) {
                    return $filter['filter_id'];
                }, $this->model_catalog_filter->getFilters(array()));

                $dict['stores'] = array(0);
                $dict['stores'] = array_merge($dict['stores'], array_map(function($store) {
                    return $store['store_id'];
                }, $this->model_setting_store->getStores()));

                $dict['downloads'] = array_map(function($download) {
                    return $download['download_id'];
                }, $this->model_catalog_download->getDownloads(array()));

                $validator = Validation::createValidator();

                $constraint = new Assert\Collection([
                    "manufacturer_id" => array (
                        new Assert\Choice(array(
                            'choices' => $dict['manufacturers'],
                            'message' => $eValidation['manufacturer.in']
                        )),
                    ),
                    "filters" => array(
                        new Assert\Optional(
                            array(
                                new Assert\Choice(array(
                                    'multiple' => true,
                                    'choices' => $dict['filters'],
                                    'message' => $eValidation['filters.in']
                                )),
                            )
                        )
                    ),
                    "stores" => array(
                        new Assert\Optional(
                            array(
                                new Assert\Choice(array(
                                    'multiple' => true,
                                    'choices' => $dict['stores'],
                                    'message' => $eValidation['stores.in']
                                )),
                            )
                        )
                    ),
                    "downloads" => array(
                        new Assert\Optional(
                            array(
                                new Assert\Choice(array(
                                    'multiple' => true,
                                    'choices' => $dict['downloads'],
                                    'message' => $eValidation['downloads.in'],
                                )),
                            )
                        )
                    ),
                    "category" => array(
                        new Assert\Optional(
                            array(
                                new Assert\Choice(array(
                                    'multiple' => true,
                                    'choices' => $dict['categories'],
                                    'message' => $eValidation['category.in'],
                                )),
                            )
                        )
                    ),
                    "related" => array(
                        new Assert\Optional(
                            array(
                                new Assert\Type(array(
                                    'type' => 'array'
                                )),
                            )
                        )
                    ),
                ]);


                $violations = $validator->validate($post, $constraint);

                if (0 !== count($violations)) {
                    foreach ($violations as $violation) {
                        $errors[] = $violation->getMessage();
                    }
                }


                if ($errors !== array()) {
                    $this->returnError($eValidation['title'], $errors);
                    return;
                }

                $this->model_extension_catalog_pro_product->saveProduct($id, array("manufacturer_id" => $post['manufacturer_id']));
                $this->model_extension_catalog_pro_product->saveProductCategory($id, isset($post['category'])? $post['category']: array());
                $this->model_extension_catalog_pro_product->saveProductFilter($id, isset($post['filters'])? $post['filters']: array());
                $this->model_extension_catalog_pro_product->saveProductStore($id, isset($post['stores'])? $post['stores']: array());
                $this->model_extension_catalog_pro_product->saveProductDownload($id, isset($post['downloads'])? $post['downloads']: array());
                $this->model_extension_catalog_pro_product->saveProductRelated($id, isset($post['related'])? $post['related']: array());

                break;

            case 'attrs':
                $this->load->model('catalog/attribute');

                $languages = $this->model_localisation_language->getLanguages(array());
                $attributesId = array_map(function($a) { return $a['attribute_id']; }, $this->model_catalog_attribute->getAttributes());
                $languagesId = array_map(function($a) { return $a['language_id']; }, $languages);

                $attrs = array();

                $unique = array();

                foreach ($post['attribute'] as $key => $attribute) {
                    $temp = "";
                    foreach ($languages as $language)
                        $temp .= $post['attribute_text.' . $language['language_id']][$key];

                    if ($temp != "") {
                        foreach ($languages as $language) {
                            $attrs[] = [
                                'product_id' => (int)$id,
                                'attribute_id' => $attribute,
                                'language_id' => $language['language_id'],
                                'text' => $post['attribute_text.' . $language['language_id']][$key]
                            ];
                        }

                        $unique[] = $attribute;
                    }
                }

                $errors = array();

                if (count($unique) !== count(array_unique($unique)))
                    $errors[] = $eValidation['attribute_id.unique'];

                $validator = Validation::createValidator();

                $constraint = new Assert\Collection([
                    'product_id' => array (
                        new Assert\NotBlank(array('message' => $eValidation['product_id.required'])),
                        new Range([
                            'min' => 1,
                            'minMessage' => $eValidation['product_id.integer'],
                            'invalidMessage' => $eValidation['product_id.integer'],
                        ]),
                    ),
                    'attribute_id' => array (
                        new Assert\NotBlank(array('message' => $eValidation['attribute_id.required'])),
                        new Assert\Choice(array(
                            'choices' => $attributesId,
                            'message' => $eValidation['attribute_id.in'],
                        )),
                    ),
                    'language_id' => array (
                        new Assert\NotBlank(array('message' => $eValidation['language_id.required'])),
                        new Assert\Choice(array(
                            'choices' => $languagesId,
                            'message' => $eValidation['language_id.in'],
                        )),
                    ),
                    'text' => array (
                        new Assert\NotBlank(array('message' => $eValidation['attribute_text.required'])),
                    )
                ]);

                foreach ($attrs as $attr) {
                    $violations = $validator->validate($attr, $constraint);

                    if (0 !== count($violations)) {
                        foreach ($violations as $violation) {
                            $errors[] = $violation->getMessage();
                        }
                    }
                }

                if ($errors !== array()) {
                    $this->returnError($eValidation['title'], $errors);
                    return;
                }

                $this->model_extension_catalog_pro_product->saveProductAttributes($id, $attrs);

                break;

            case 'options':
                $regular = '/(\w+)(\.(\w+))?(\.(\w+))?\.(\d+)/m';
                $options = array();
                foreach ($post as $field => $value) {
                    preg_match_all($regular, $field, $matches, PREG_SET_ORDER, 0);
                    $m = $matches[0];

                    if ($m[3] != "")
                        $options[$m[6]][$m[1]][$m[5]][$m[3]] = in_array($m[3], array("quantity", "price", "points", "weight")) !== false? $value + 0: $value;
                    else
                        $options[$m[6]][$m[1]] = $value;
                }

                $this->model_extension_catalog_pro_product->saveProductOptions($id, $options);

                break;

            case 'discount':
                $this->load->model('customer/customer_group');
                $this->load->model('extension/catalog_pro/category');

                $errors = array();
                $dict = array ();

                $groups = array_map(function($group) {
                    return $group['customer_group_id'];
                }, $this->model_customer_customer_group->getCustomerGroups(array()));


                $regular = '/(\w+)\.(\d+)/m';
                $postData = array();

                foreach ($post as $field => $value) {
                    preg_match_all($regular, $field, $matches, PREG_SET_ORDER, 0);
                    $m = $matches[0];
                    $postData[$m[2]][$m[1]] = $value;
                }

                $constraint = new Assert\Collection(
                    array(
                        "customer_group_id" => array (
                            new Assert\Choice(array(
                                'choices' => $groups,
                                'message' => $eValidation['customer_group_id.in']
                            )),
                        ),
                        "quantity" => array(
                            new Range(array(
                                'min' => 1,
                                'minMessage' => $eValidation['quantity.min'],
                                'invalidMessage' => $eValidation['quantity.invalid'],
                            ))
                        ),
                        "price" => array(
                            new Range(array(
                                'min' => 0.01,
                                'minMessage' => $eValidation['price.min'],
                                'invalidMessage' => $eValidation['price.invalid'],
                            ))
                        ),
                        "date_start" => array(
                            new Date([
                                'message' => $eValidation['date_start.invalid'],
                            ]),
                        ),
                        "date_end" => array(
                            new Date([
                                'message' => $eValidation['date_start.invalid'],
                            ]),
                        ),
                    )
                );

                $validator = Validation::createValidator();
                foreach ($postData as $data) {
                    $violations = $validator->validate($data, $constraint);

                    if (0 !== count($violations)) {
                        foreach ($violations as $violation) {
                            $errors[] = $violation->getMessage();
                        }
                    }
                }

                if ($errors !== array()) {
                    $this->returnError($eValidation['title'], $errors);
                    return;
                }

                $this->model_extension_catalog_pro_product->saveProductDiscount($id, $postData);

                break;

            case 'bonus':
                $this->load->model('customer/customer_group');
                $this->load->model('extension/catalog_pro/category');

                $errors = array();

                $groups = array_map(function($group) {
                    return $group['customer_group_id'];
                }, $this->model_customer_customer_group->getCustomerGroups(array()));

                $regular = '/(\w+)\.(\d+)/m';
                $postData = array();

                foreach ($post as $field => $value) {
                    if (strpos($field, ".") !== false) {
                        preg_match_all($regular, $field, $matches, PREG_SET_ORDER, 0);
                        $m = $matches[0];
                        $postData['reward_points'][] = array (
                            "customer_group_id" => $m[2],
                            "points" => $value,
                        );
                    }
                    else
                        $postData[$field] = $value;
                }

                $constraint = new Assert\Collection(
                    array(
                        "points" => array (
                            new Range(array(
                                'min' => 0,
                                'minMessage' => $eValidation['points.min'],
                                'invalidMessage' => $eValidation['points.invalid'],
                            ))
                        ),
                        "reward_points" => array (
                            new Assert\Type('array'),
                            new Assert\All(
                                array(
                                    new Assert\Collection(
                                        array(
                                            "customer_group_id" => array (
                                                new Assert\Choice(array(
                                                    'choices' => $groups,
                                                    'message' => $eValidation['customer_group_id.in']
                                                )),
                                            ),
                                            "points" => array (
                                                new Range(array(
                                                    'min' => 0,
                                                    'minMessage' => $eValidation['points_reward.min'],
                                                    'invalidMessage' => $eValidation['points_reward.invalid'],
                                                ))
                                            ),
                                        )
                                    )
                                )
                            ),
                        ),
                    )
                );

                $validator = Validation::createValidator();
                $violations = $validator->validate($postData, $constraint);

                if (0 !== count($violations)) {
                    foreach ($violations as $violation) {
                        $errors[] = $violation->getMessage();
                    }
                }

                if ($errors !== array()) {
                    $this->returnError($eValidation['title'], $errors);
                    return;
                }

                $this->model_extension_catalog_pro_product->saveProduct($id, array("points" => $postData['points']));
                $this->model_extension_catalog_pro_product->saveProductRewards($id, $postData['reward_points']);

                break;


            case 'seo':

                $regular = '/(\w+)\.(\d+)\.(\d+)/m';
                $postData = array();
                $errors = array();

                foreach ($post as $field => $value) {
                    preg_match_all($regular, $field, $matches, PREG_SET_ORDER, 0);
                    $m = $matches[0];
                    $postData[] = array(
                        "store_id" => $m[2],
                        "language_id" => $m[3],
                        "keyword" => $value
                    );
                }

                $this->load->model('setting/store');
                $this->load->model('localisation/language');
                $this->load->model('design/seo_url');

                $languages = array();
                foreach ($this->model_localisation_language->getLanguages(array()) as $l)
                    $languages[] = $l['language_id'];

                $stores[] = 0;

                foreach ($this->model_setting_store->getStores() as $store) {
                    $stores[] = $store['store_id'];
                }

                $constraint = new Assert\Collection(
                    array(
                        "store_id" => array (
                            new Assert\Choice(array(
                                'choices' => $stores,
                                'message' => $eValidation['stores.in']
                            )),
                        ),
                        "language_id" => array (
                            new Assert\Choice(array(
                                'choices' => $languages,
                                'message' => $eValidation['language_id.in']
                            )),
                        ),
                        "keyword" => array (
                            new Assert\Type(array('type' => 'string'))
                        ),
                    )
                );

                $validator = Validation::createValidator();

                foreach ($postData as $data) {
                    $violations = $validator->validate($data, $constraint);

                    if (0 !== count($violations)) {
                        foreach ($violations as $violation) {
                            $errors[] = $violation->getMessage();
                        }
                    }

                    $urls = $this->model_design_seo_url->getSeoUrlsByQuery($data['keyword']);
                    if ($urls !== array()) {
                        foreach ($urls as $url) {
                            if ($url['query'] != "product_id=".$id)
                                $errors[] = $this->language->get('validate')['url.unique'];
                        }
                    }
                }


                if ($errors !== array()) {
                    $this->returnError($eValidation['title'], $errors);
                    return;
                }


                $this->model_extension_catalog_pro_product->saveProductSEO($id, $postData);

                break;

        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            "title" => $this->language->get('text_success_save_title'),
            "message" => $this->language->get('text_success_save')
        ]));
    }

    public function ajaxProducts() {
        $this->loadLanguage();

        $this->load->model('extension/catalog_pro/product');
        $this->load->model('localisation/currency');

        $eModal = $this->language->get('modal');

        $query = $this->request->get['q']['term'];
        $filter_data = array(
            'start'             => 0,
            'limit'             => 10,
            'q'                 => $query,
            'ignore'            => isset($this->request->get['ignore'])? $this->request->get['ignore']: array()
        );

        $products = array();
        $temp = $this->model_extension_catalog_pro_product->getProductsByFilter($filter_data);

        foreach ($temp as $p) {
            $products[] = [
                "id" => $p['product_id'],
                "text" => $this->getProductNameForAjax($p, $eModal),
            ];
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode(
            array(
                "results" => $products,
                "pagination" => array (
                    "more" => false,
                )
            )
        ));
    }

    private function getProductNameForAjax($p, $eModal) {
        $additional = array();

        if ($p['model'] != "")
            $additional[] = $eModal['fields']['model'].": ".$p['model'];
        if ($p['sku'] != "")
            $additional[] = $eModal['fields']['sku'].": ".$p['sku'];

        return $p['name'].($additional !== array()? ". <span style='color: #ccc'>".implode(", ", $additional)."</span>": "");
    }
}
