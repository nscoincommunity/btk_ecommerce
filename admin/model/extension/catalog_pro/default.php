<?php
class ModelExtensionCatalogProDefault {
    private $config = array (
        'columns' => array(
            'product_id' => array(
                'visible' => 0,
                'sort' => 1,
                'width' => 50,
                'className' => 'dt-body-right cell-id',
                'type' => 'text',
            ),
            'category' => array(
                'visible' => 1,
                'sort' => 0,
                'width' => null,
                'className' => 'dt-body-left cell-category',
                'type' => 'select-choice',
            ),
            'image' => array(
                'visible' => 1,
                'sort' => 0,
                'width' => 50,
                'className' => 'dt-body-center cell-image',
            ),
            'name' => array(
                'visible' => 1,
                'sort' => 1,
                'width' => null,
                'className' => 'dt-body-left cell-name',
                'type' => 'text',
            ),
            'model' => array(
                'visible' => 1,
                'sort' => 1,
                'width' => null,
                'className' => 'dt-body-left cell-model',
                'type' => 'text',
            ),
            'sku' => array(
                'visible' => 1,
                'sort' => 1,
                'width' => null,
                'className' => 'dt-body-left cell-sku',
                'type' => 'text',
            ),
            'price' => array(
                'visible' => 1,
                'sort' => 1,
                'width' => null,
                'className' => 'dt-body-right cell-price',
                'type' => 'text',
            ),
            'quantity' => array(
                'visible' => 1,
                'sort' => 1,
                'width' => null,
                'className' => 'dt-body-center cell-quantity',
                'type' => 'text',
            ),
            'status' => array(
                'visible' => 1,
                'sort' => 1,
                'width' => 100,
                'className' => 'dt-body-center cell-status',
                'type' => 'select',
            ),
            'actions' => array(
                'visible' => 1,
                'sort' => 0,
                'width' => 20,
                'className' => 'dt-body-center cell-status',
            ),
        ),
        'limit' => 25,
    );

    public function getConfig() {
        return $this->config;
    }
}
