<?php
class ModelExtensionCatalogProCategory extends Model {
    public function getCategories() {
        $query = $this->db->query("SELECT DISTINCT *, (SELECT GROUP_CONCAT(cd1.name ORDER BY level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id AND cp.category_id != cp.path_id) WHERE cp.category_id = c.category_id AND cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY cp.category_id) AS path FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (c.category_id = cd2.category_id) WHERE cd2.language_id = '" . (int)$this->config->get('config_language_id') . "' order by c.sort_order asc");
        return $query->rows;
    }

    public function getTree() {
        $temp = array();

        $query = $this->db->query("SELECT c.category_id, c.parent_id, c.status, d.name, d.description, d.meta_title, d.meta_description, d.meta_keyword  FROM " . DB_PREFIX . "category c, " . DB_PREFIX . "category_description d  where c.category_id = d.category_id and d.language_id = '" . (int)$this->config->get('config_language_id') . "' order by c.parent_id, c.sort_order");

        foreach ($query->rows as $row) {
            $temp[$row['parent_id']][] = $row;
        }

        return $this->getTreeParent(0, $temp);
    }

    private function getTreeParent($parentId, $categories) {
        $result = array();
        foreach($categories[$parentId] as $row) {
            $temp = $row;
            if (isset($categories[$row['category_id']]))
                $temp['children'] = $this->getTreeParent($row['category_id'], $categories);
            $result[] = $temp;
        }

        return $result;
    }

    public function getDeTree() {
        $temp = array();

        $query = $this->db->query("SELECT c.category_id, c.parent_id, c.status, d.name, d.description, d.meta_title, d.meta_description, d.meta_keyword  FROM " . DB_PREFIX . "category c, " . DB_PREFIX . "category_description d  where c.category_id = d.category_id and d.language_id = '" . (int)$this->config->get('config_language_id') . "' order by c.parent_id, c.sort_order");

        foreach ($query->rows as $row) {
            $temp[$row['category_id']] = $row;
        }

        foreach ($temp as $categoryId => $category)
            $temp[$categoryId]['join_name'] = implode(" > ", $this->getDeTreeParent($categoryId, $temp));

        return $temp;
    }

    private function getDeTreeParent($categoryId, $categories) {
        $result = array();

        if ($categories[$categoryId]['parent_id'] != 0)
            $result = array_merge($result, $this->getDeTreeParent($categories[$categoryId]['parent_id'], $categories));

        $result[] = $categories[$categoryId]['name'];

        return $result;
    }
}
