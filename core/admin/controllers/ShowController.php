<?php

namespace core\admin\controllers;

use core\base\settings\Settings;
use core\base\settings\ShopSettings;

class ShowController extends BaseAdmin{

    protected function inputData()
    {
        $this->execBase();
        $this->createTableData();
        $this->createData(['fields' => ['name', 'content']]);
        return $this->expansion(get_defined_vars());
    }

    protected function createData($arr = [])
    {
        $fields = [];
        $order = [];
        $order_direction = [];

        if (empty($this->columns['id_row'])) {
            return $this->data = [];
        }
        $fields[] = $this->columns['id_row'].' as id';
        if (!empty($this->columns['name'])) {
            $fileds['name'] = 'name';
        }
        if (!empty($this->columns['img'])) {
            $fileds['img'] = 'name';
        }
        if (count($fileds) < 3) {
            foreach ($this->columns as $key => $value) {
                if (empty($fileds['name']) && strpos($key, 'name') !== false) {
                    $fileds['name'] = $key.' as name';
                }
                if (empty($fileds['img']) && strpos($key, 'img') === 0) {
                    $fileds['img'] = $key.' as img';
                }
            }
        }
        if (!empty($arr['fields'])) {
            if (is_array($arr['fields'])) {
                $fields = Settings::instance()->arrayMergeRecursive($fields, $arr['fields']);
            } else {
                $fields[] = $arr['fields'];
            }
        }
        if (!empty($this->columns['parent_id'])) {
            if (!in_array('parent_id', $fields)) {
                $fields[] = 'parent_id';
                $order[] = 'parent_id';
            }
        }
        if (!empty($this->columns['menu'])) {
            $order[] = 'menu_position';
        } elseif (!empty($this->columns['date'])) {
            $order[] = 'date';
            if (isset($order)) {
                $order_direction = ['ASC', 'DESC'];
            } else {
                $order_direction[] = 'DESC';
            }
        }
        if (!empty($arr['order'])) {
            if (is_array($arr['order'])) {
                $order = Settings::instance()->arrayMergeRecursive($order, $arr['order']);
            } else {
                $order[] = $arr['order'];
            }
        }
        if (!empty($arr['order_direction'])) {
            if (is_array($arr['order_direction'])) {
                $order_direction = Settings::instance()->arrayMergeRecursive($order_direction, $arr['order_direction']);
            } else {
                $order_direction[] = $arr['order_direction'];
            }
        }

        $this->data = $this->model->get($this->table, [
            'fields' => $fields,
            'order' => $order,
            'order_direction' => $order_direction
        ]);
    }
}