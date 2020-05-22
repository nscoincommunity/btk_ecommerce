<?php
class ModelExtensionCatalogProProduct extends Model {
    private function filterSql($data = array()) {
        if ($data === array())
            return "";

        $sql = "";
        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (!empty($data['filter_model'])) {
            $sql .= " AND p.model LIKE '%" . $this->db->escape($data['filter_model']) . "%'";
        }

        if (!empty($data['filter_sku'])) {
            $sql .= " AND p.sku LIKE '%" . $this->db->escape($data['filter_sku']) . "%'";
        }

        if (!empty($data['filter_price'])) {
            $sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
        }

        if (isset($data['filter_quantity']) && $data['filter_quantity'] !== '') {
            $sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
        }

        if (!empty($data['filter_product_id'])) {
            if (is_string($data['filter_product_id']))
                $sql .= " AND pd.product_id LIKE '%" . $this->db->escape($data['filter_product_id']) . "%'";
            else if (is_array($data['filter_product_id']))
                $sql .= " AND pd.product_id IN (" . implode(", ", $data['filter_product_id']) . ")";
        }

        if (!empty($data['filter_category'])) {
            $sql .= " AND pc.category_id in (" . $this->db->escape($data['filter_category']) . ")";
        }

        return $sql;
    }

    public function getProduct($product_id) {
        $sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' and p.product_id = '".((int) $product_id)."'";
        $query = $this->db->query($sql);

        $row = $query->row;
        $row['specials'] = $this->getProductSpecials(array($product_id));
        $row['images'] = $this->getProductImages(array($product_id));
        $categories = $this->getProductCategories(array($product_id));
        $row['categories'] = explode(",", ($categories === array()? "": $categories[0]['categories']));

        $row['filters'] = array_map(function($filter) {
            return $filter['filter_id'];
        }, $this->getProductFilters(array($product_id)));

        $row['stores'] = array_map(function($store) {
            return $store['store_id'];
        }, $this->getProductStores(array($product_id)));

        $row['downloads'] = array_map(function($download) {
            return $download['download_id'];
        }, $this->getProductDownloads(array($product_id)));

        $row['related'] = array_map(function($related) {
            return $related['related_id'];
        }, $this->getProductRelated(array($product_id)));

        $row['attributes'] = $this->getProductAttributes(array($product_id));
        $row['options'] = $this->getProductOptions(array($product_id));
        $row['discount'] = $this->getProductDiscount(array($product_id));
        $row['rewards'] = $this->getProductRewards(array($product_id));
        $row['seo'] = $this->getProductSeoUrls($product_id);

        return $row;
    }

    public function getProductsByFilter($filter) {
        $sql = "SELECT 
		            p.*, pd.*
		        FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
                WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        $sql .= " AND (pd.name like '%".$this->db->escape($filter['q'])."%' or p.model like '%".$this->db->escape($filter['q'])."%' or p.sku like '%".$this->db->escape($filter['q'])."%')";

        if (isset($filter['ignore']) && $filter['ignore'] !== array())
            $sql .= " AND p.product_id not in (".implode(", ", $filter['ignore']).")";

        $sql .= " GROUP BY p.product_id";

        if (isset($data['sort']))
            $sql .= " ORDER BY pd.name ASC";

        if (isset($data['start']) || isset($data['limit'])) {
            if ($filter['start'] < 0) {
                $filter['start'] = 0;
            }

            if ($filter['limit'] < 1) {
                $filter['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$filter['start'] . "," . (int)$filter['limit'];
        }

        $query = $this->db->query($sql);

        $rows = array();
        foreach ($query->rows as $row)
            $rows[$row['product_id']] = $row;

        $ids = array_keys($rows);

        $specials = $this->getProductSpecials($ids);
        foreach ($specials  as $row) {
            if (!isset($rows[$row['product_id']]))
                $rows[$row['product_id']]['specials'] = array();

            $rows[$row['product_id']]['specials'][] = $row;
        }

        return $rows;
    }


    public function getProducts($data = array()) {
		$sql = "SELECT 
		            p.*, pd.*, pc.*, p.product_id
		        FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
                LEFT JOIN " . DB_PREFIX . "product_to_category pc ON (p.product_id = pc.product_id)
                WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        $sql .= $this->filterSql($data);


		$sql .= " GROUP BY p.product_id";

		if (isset($data['sort']))
			$sql .= " ORDER BY " . $data['sort']." ".$data['order'];

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

		$rows = array();
		foreach ($query->rows as $row)
		    $rows[$row['product_id']] = $row;

		$ids = array_keys($rows);

        $specials = $this->getProductSpecials($ids);
        foreach ($specials  as $row) {
            if (!isset($rows[$row['product_id']]))
                $rows[$row['product_id']]['specials'] = array();

            $rows[$row['product_id']]['specials'][] = $row;
        }

        $images = $this->getProductImages($ids);
        foreach ($images  as $row) {
            if (!isset($rows[$row['product_id']]))
                $rows[$row['product_id']]['images'] = array();

            $rows[$row['product_id']]['images'][] = $row;
        }

        $categories = $this->getProductCategories($ids);
        foreach ($categories  as $row) {
            if (!isset($rows[$row['product_id']]))
                $rows[$row['product_id']]['categories'] = array();

            $rows[$row['product_id']]['categories'] = explode(",", $row['categories']);
        }

        $filters = $this->getProductFilters($ids);
        foreach ($filters  as $row) {
            if (!isset($rows[$row['product_id']]))
                $rows[$row['product_id']]['filters'] = array();

            $rows[$row['product_id']]['filters'][] = $row['filter_id'];
        }

        $stores = $this->getProductStores($ids);
        foreach ($stores  as $row) {
            if (!isset($rows[$row['product_id']]))
                $rows[$row['product_id']]['filters'] = array();

            $rows[$row['product_id']]['stores'][] = $row['store_id'];
        }

        $downloads = $this->getProductDownloads($ids);
        foreach ($downloads  as $row) {
            if (!isset($rows[$row['product_id']]))
                $rows[$row['product_id']]['downloads'] = array();

            $rows[$row['product_id']]['downloads'][] = $row['download_id'];
        }

		return $rows;
	}


	public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category pc ON (p.product_id = pc.product_id)";

		$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        $sql .= $this->filterSql($data);

        $query = $this->db->query($sql);

		return $query->row['total'];
	}

    public function getProductSpecials($ids) {
        if ($ids === array())
            return array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id in (".implode(",", $ids).") ORDER BY priority, price");

        return $query->rows;
    }

    public function getProductDiscount($ids) {
        if ($ids === array())
            return array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id in (".implode(",", $ids).") ORDER BY priority, price");

        foreach ($query->rows as &$row) {
            if ($row['date_start'] == "0000-00-00")
                $row['date_start'] = null;
            if ($row['date_end'] == "0000-00-00")
                $row['date_end'] = null;
        }

        return $query->rows;
    }

    public function getProductRewards($ids) {
        if ($ids === array())
            return array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_reward WHERE product_id in (".implode(",", $ids).")");

        $return = array();

        foreach($query->rows as $row)
            $return[$row['customer_group_id']] = $row['points'];

        return $return;
    }

    public function getProductSeoUrls($product_id) {
        $return = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$product_id . "'");

        foreach ($query->rows as $row) {
            $return[$row['store_id']][$row['language_id']] = $row['keyword'];
        }

        return $return;
    }


    public function getProductImages($ids) {
        if ($ids === array())
            return array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id in (".implode(",", $ids).") ORDER BY sort_order asc");

        return $query->rows;
    }

    public function getProductCategories($ids) {
        if ($ids === array())
            return array();

        $query = $this->db->query("SELECT pc.product_id, group_concat(DISTINCT pc.category_id SEPARATOR ',') as categories FROM " . DB_PREFIX . "product_to_category pc, " . DB_PREFIX . "category c WHERE pc.product_id in (".implode(",", $ids).") and pc.category_id = c.category_id group by pc.product_id ORDER BY c.sort_order asc");

        return $query->rows;
    }

    public function getProductFilters($ids) {
        if ($ids === array())
            return array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_filter where product_id in (".implode(", ", $ids).")");

        return $query->rows;
    }

    public function getProductStores($ids) {
        if ($ids === array())
            return array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store where product_id in (".implode(", ", $ids).")");

        return $query->rows;
    }

    public function getProductDownloads($ids) {
        if ($ids === array())
            return array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download where product_id in (".implode(", ", $ids).")");

        return $query->rows;
    }

    public function getProductRelated($ids) {
        if ($ids === array())
            return array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related where product_id in (".implode(", ", $ids).")");

        return $query->rows;
    }

    public function getProductDescriptions($product_id) {
        $product_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");

        foreach ($query->rows as $result) {
            $product_description_data[$result['language_id']] = array(
                'name'             => $result['name'],
                'description'      => $result['description'],
                'meta_title'       => $result['meta_title'],
                'meta_description' => $result['meta_description'],
                'meta_keyword'     => $result['meta_keyword'],
                'tag'              => $result['tag']
            );
        }

        return $product_description_data;
    }

    public function getProductAttributes($ids) {
        $attributes = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id in (".implode(", ", $ids).")");

        foreach ($query->rows as $result) {
            $attributes[$result['attribute_id']][$result['language_id']] = $result['text'];
        }

        return $attributes;
    }

    public function getProductOptions($ids) {
        $options = array();

        $query = $this->db->query("SELECT
                                        ov.*,
                                        o.product_option_id,
                                        o.option_id,
                                        o.value,
                                        o.required,
                                        op.type
                                   FROM " . DB_PREFIX . "product_option o
                                   LEFT JOIN " . DB_PREFIX . "product_option_value ov on (o.product_option_id = ov.product_option_id)
                                   RIGHT JOIN " . DB_PREFIX . "option op on (op.option_id = o.option_id)
                                   WHERE o.product_id in (".implode(", ", $ids).")");

        foreach ($query->rows as $row) {
            if (!isset($options[$row['product_option_id']]))
                $options[$row['product_option_id']] = array (
                    'option_id' => $row['option_id'],
                    'type' => $row['type'],
                    'value' => $row['value'],
                    'required' => $row['required'],
                );

            $options[$row['product_option_id']]['children'][] = $row;
        }

        return $options;
    }


    public function saveProductDescriptions($product_id, $language_id, $values) {
        $sql = array();
        foreach ($values as $field => $value) {
            $sql[] = "`{$field}` = '".$this->db->escape($value)."'";
        }

        $this->db->query("UPDATE " . DB_PREFIX . "product_description set ".implode(",", $sql)." WHERE product_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
        $this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
        return;
    }

    public function saveProduct($product_id, $values) {
        $sql = array();
        foreach ($values as $field => $value) {
            $sql[] = "`{$field}` = '".$this->db->escape($value)."'";
        }

        $this->db->query("UPDATE " . DB_PREFIX . "product SET ".implode(",", $sql).", date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
        return;
    }

    public function saveProductSpecial($product_id, $specials) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");

        $priority = 0;
        foreach ($specials as $special) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$special['customer_group_id'] . "', priority = '" . (int)$priority . "', price = '" . (float)$special['price'] . "', date_start = '" . $this->db->escape($special['date_start']) . "', date_end = '" . $this->db->escape($special['date_end']) . "'");
            $priority++;
        }

        $this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
        return;
    }

    public function saveProductInvertStatus($product_id) {
        $this->db->query("UPDATE " . DB_PREFIX . "product SET `status` = 1 - `status`, date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
        return;
    }

    public function saveProductImage($product_id, $mainImage, $images) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");

        $sort_order = 0;
        if ($images !== array())
            foreach ($images as $image) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($image['image']) . "', sort_order={$sort_order};");
                $sort_order++;
            }

        $this->db->query("UPDATE " . DB_PREFIX . "product SET `image` = ".($mainImage == ""? null: "'".$this->db->escape($mainImage)."'").", date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
        return;
    }

    public function saveProductCategory($product_id, $categories) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

        if ($categories !== array())
            foreach ($categories as $category) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category . "';");
            }

        $this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
        return;
    }

    public function saveProductFilter($product_id, $filters) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");

        if ($filters !== array())
            foreach ($filters as $filter) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter . "';");
            }

        $this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
        return;
    }

    public function saveProductStore($product_id, $stores) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

        if ($stores !== array())
            foreach ($stores as $store) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store . "';");
            }

        $this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
        return;
    }

    public function saveProductDownload($product_id, $downloads) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");

        if ($downloads !== array())
            foreach ($downloads as $download) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download . "';");
            }

        $this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
        return;
    }

    public function saveProductRelated($product_id, $related) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");

        if ($related !== array())
            foreach ($related as $r) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$r . "';");
            }

        $this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
        return;
    }

    public function saveProductAttributes($product_id, $attributes) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");

        if ($attributes !== array())
            foreach ($attributes as $a) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . $a['attribute_id'] . "', language_id = '" . $a['language_id'] . "', text = '".$this->db->escape($a['text'])."';");
            }

        $this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
        return;
    }

    public function saveProductOptions($product_id, $options) {

        $this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");

        if ($options !== array())
            foreach ($options as $o) {

                $this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . $o['option_type'] . "', value = '" . (isset($o['value'])? $this->db->escape($o['value']): "") . "', required = '".$o['required']."';");

                if (isset($o['values']) && $o['values'] !== array()) {
                    $insertId = $this->db->getLastId();

                    foreach($o['values'] as $row) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET
                                                product_option_id = '" . $insertId . "',  
                                                product_id = '" . (int)$product_id . "', 
                                                option_id = '" . $o['option_type'] . "', 
                                                option_value_id = '" . $row['option'] . "', 
                                                quantity = '".$row['quantity']."',
                                                subtract = '".$row['substract']."',
                                                price = '".$row['price']."',
                                                price_prefix = '".$row['price_prefix']."',
                                                points = '".$row['points']."',
                                                points_prefix = '".$row['points_prefix']."',
                                                weight = '".$row['weight']."',
                                                weight_prefix = '".$row['weight_prefix']."';
                        ");
                    }
                }

            }

        $this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
        return;
    }


    public function saveProductDiscount($product_id, $discount) {

        $this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");

        $position = 0;
        if ($discount !== array())
            foreach ($discount as $d) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', 
                                        customer_group_id = '" . $d['customer_group_id'] . "', 
                                        quantity = '" . $d['quantity'] . "',
                                        priority = '" . $position++ . "',
                                        price = '" . $d['price'] . "',
                                        date_start = '" . ($d['date_start'] != ""? $d['date_start']: "0000-00-00") . "',
                                        date_end = '" . ($d['date_end'] != ""? $d['date_end']: "0000-00-00") . "'
                                        ");
            }

        $this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
        return;
    }

    public function saveProductRewards($product_id, $rewards) {

        $this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");

        $position = 0;
        if ($rewards !== array())
            foreach ($rewards as $r) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', 
                                        customer_group_id = '" . $r['customer_group_id'] . "', 
                                        points = '" . $r['points'] . "'
                                        ");
            }

        $this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
        return;
    }

    public function saveProductSEO($product_id, $seo) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$product_id . "'");

            foreach ($seo as $url) {
                if ($url['keyword'] == "")
                    continue;

                $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET 
                                        store_id = '" . $url['store_id'] . "', 
                                        language_id = '" . $url['language_id'] . "',
                                        query = 'product_id=" . (int)$product_id . "',
                                        keyword = '" . $url['keyword'] . "'
                                        ");
            }

        $this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
        return;
    }

}
